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
if (!isset($post['id']) || !isset($post['nombre']) || !isset($post['email']) || !isset($post['password']) || !isset($post['acceso']) || !isset($post['foto']) ) :
	$mensaje = 'Ha ocurrido un error con los datos ingresados.';
else :
	if (strlen($post['id']) < 1 || strlen($post['nombre']) < 2 || strlen($post['email']) < 5 || strlen($post['password']) < 5 || strlen($post['acceso']) < 1 || strlen($post['foto']) < 2) :
		$mensaje = 'Ha ocurrido un problema con los datos ingresados.';
	endif;
endif;


// Valida Sesion
if ($mensaje == 'Exito') :
	session_start();
	if (!isset($_SESSION['bloqueo']) && isset($_SESSION['user_login']) && isset($_SESSION['user_datos']) ) :
		if ($_SESSION['user_login'] == base64_encode( $fechaLogin ) && !empty($_SESSION["user_datos"]) ) :
			$userID = $_SESSION["user_datos"]['id'];
		else :
			$mensaje = 'Ha ocurrido un problema con tu sesión. Por favor, vuelve a ingresar.';
		endif;
	else :
		sleep(5);
		$mensaje = 'Ha ocurrido un error con tu sesión. Por favor, vuelve a ingresar.';
	endif;
endif;


// Obtiene Datos del Lugar
if ($mensaje == 'Exito') :


	// Carga Base de Datos
	$config = require_once 'config/db.php';
	$db = Zend_Db::factory('Mysqli', $config);
	Zend_Db_Table_Abstract::setDefaultAdapter($db);

	$tablaUsers = new Zend_Db_Table('users');


	// Si se modifica un usuario
	if (intval($post['id']) > 1) :
		$queryUsers = $tablaUsers->select()->where('user_id = ?', $post['id']);
		$datosUsers = $tablaUsers->fetchRow($queryUsers);
		if (isset($datosUsers['user_id']) ) :
			$postUpdate = array(
				'fecha_mod'	=> $fecha
			);
			if ($post['nombre'] != $datosUsers['nombre'])		$postUpdate['nombre'] = $post['nombre'];
			if ($post['email'] != $datosUsers['email'])			$postUpdate['email'] = $post['email'];
			if ($post['password'] != $datosUsers['password'])	$postUpdate['password'] = $post['password'];
			if ($post['acceso'] != $datosUsers['acceso'])		$postUpdate['acceso'] = $post['acceso'];
			if ($post['foto'] != $datosUsers['foto'])			$postUpdate['foto'] = $post['foto'];
			
			$whereUpdate = $tablaUsers->getAdapter()->quoteInto('user_id=?', $post['id']);
			$inserUpdate = $tablaUsers->update($postUpdate, $whereUpdate);
			if (!$inserUpdate) :
				$mensaje = 'Ha ocurrido un error al actualizar los datos del usuario.';
			endif;
		else :
			$mensaje = 'Ha ocurrido un problema con el usuario a editar.';
		endif;


	// Guarda Nuevo Usuario
	else :
		$postDatos = array(
			'nombre'	=> $post['nombre'],
			'email'		=> $post['email'],
			'password'	=> $post['password'],
			'acceso'	=> $post['acceso'],
			'foto'		=> $post['foto'],
			'creador'	=> $userID,
			'fecha'		=> $fecha
		);
		$newUser = $tablaUsers->insert($postDatos);
		if (!$newUser) :
			$mensaje = 'Ha ocurrido un error al guardar tus datos.';
		endif;
	endif;
endif;


// Cierra Conexion
if (isset($db) ) :
	$db->closeConnection();
endif;

if ($mensaje != 'Exito') :
	$log->emerg('SaveUser : '.$mensaje);
endif;

echo $mensaje;
?>