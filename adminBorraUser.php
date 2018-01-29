<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$mensaje = 'Exito';
$extra = include 'config/extra.php';


// Fecha Actual
date_default_timezone_set( $extra['timezone'] );
$fechaLogin = date("Y-m-d");
$fecha = date("Y-m-d H:i:s");


// Inicializo ZEND
//set_include_path(get_include_path().PATH_SEPARATOR.$extra['ruta_zend']);
require 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();


// DATOS POR POST
$request = new Zend_Controller_Request_Http();
$post = $request->getPost();


// Inicializo Log
$log = new Zend_Log();
$writer = new Zend_Log_Writer_Stream('error.log');
$log->addWriter($writer);


// Validacion inicial
if (!isset($post['id']) ) :
	$mensaje = 'Ha ocurrido un error con los datos ingresados.';
else :
	if (strlen($post['id']) < 1) :
		$mensaje = 'Ha ocurrido un problema con los datos ingresados.';
	endif;
endif;


// Valida Sesion
if ($mensaje == 'Exito') :
	session_start();
	if (!isset($_SESSION['bloqueo']) && isset($_SESSION['user_login']) && isset($_SESSION['user_datos']) ) :
		if ($_SESSION['user_login'] == base64_encode( $fechaLogin ) && !empty($_SESSION["user_datos"]) ) :
			// Login OK
		else :
			$mensaje = 'Ha ocurrido un problema con tu sesión. Por favor, vuelve a ingresar.';
		endif;
	else :
		$mensaje = 'Ha ocurrido un error con tu sesión. Por favor, vuelve a ingresar.';
	endif;
endif;


// Obtiene Datos del Lugar
if ($mensaje == 'Exito') :


	// Carga Base de Datos
	$config = require_once 'config/db.php';
	$db = Zend_Db::factory('Mysqli', $config);
	Zend_Db_Table_Abstract::setDefaultAdapter($db);


	// Si se modifica un usuario
	if (intval($post['id']) > 1) :
		$userID = $post['id'];
		$tablaUsers = new Zend_Db_Table('users');
		$queryUsers = $tablaUsers->select()->where('user_id = ?', $userID);
		$datosUsers = $tablaUsers->fetchRow($queryUsers);
		if (isset($datosUsers['user_id']) ) :
			$borraUsers = $datosUsers->delete();
			if (!$borraUsers) :
				$mensaje = 'Ha ocurrido un error al borrar al usuario.';
			endif;
		else :
			$mensaje = 'Ha ocurrido un problema con el usuario a editar.';
		endif;
	else :
		$mensaje = 'Hay un error con el usuario a borrar.';
	endif;
endif;


// Mueve Clientes Asociados al Usuario
if ($mensaje == 'Exito') :


	// Busca clientes asociados al usuario borrado
	$tablaClien = new Zend_Db_Table('clientes');
	$queryClien = $tablaClien->select()->where('user_id = ?', $userID);
	$datosClien = $tablaClien->fetchAll($queryClien);
	
	
	// Reemplaza el ID del usuario eliminado por el del admin
	foreach ($datosClien as $client) :
		$postUpdate = array('user_id' => 1);
		$whereUpdate = $tablaClien->getAdapter()->quoteInto('cliente_id=?', $client['cliente_id'] );
		$inserUpdate = $tablaClien->update($postUpdate, $whereUpdate);
		if (!$inserUpdate) :
			$mensaje = 'Ha ocurrido un error al actualizar el producto canjeado.';
		endif;
	endforeach;
endif;


// Cierra Conexion
if (isset($db) ) :
	$db->closeConnection();
endif;

if ($mensaje != 'Exito') :
	$log->emerg('BorraUser : '.$mensaje);
endif;

echo $mensaje;
?>