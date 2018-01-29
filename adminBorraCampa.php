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
	$userID = $post['id'];


	// Carga Base de Datos
	$config = require_once 'config/db.php';
	$db = Zend_Db::factory('Mysqli', $config);
	Zend_Db_Table_Abstract::setDefaultAdapter($db);
	
	
	// Revisa que la campaña este vacia
	$tablaClien = new Zend_Db_Table('clientes');
	$queryClien = $tablaClien->select()->where('campa_id = ?', $userID);
	$datosClien = $tablaClien->fetchRow($queryClien);
	if (isset($datosClien['campa_id']) ) :
		$mensaje = 'La campaña no está vacia y no se puede borrar.';
	endif;


	// Si se modifica un usuario
	if ($mensaje == 'Exito') :
		$tablaUsers = new Zend_Db_Table('campas');
		$queryUsers = $tablaUsers->select()->where('campa_id=?', $userID);
		$datosUsers = $tablaUsers->fetchRow($queryUsers);
		if (isset($datosUsers['campa_id']) ) :
			$borraUsers = $datosUsers->delete();
			if (!$borraUsers) :
				$mensaje = 'Ha ocurrido un error al borrar la campaña.';
			endif;
		else :
			$mensaje = 'Ha ocurrido un problema con la campaña a borrar.';
		endif;
	endif;
endif;


// Cierra Conexion
if (isset($db) ) :
	$db->closeConnection();
endif;

if ($mensaje != 'Exito') :
	$log->emerg('BorraCampa : '.$mensaje);
endif;

echo $mensaje;
?>