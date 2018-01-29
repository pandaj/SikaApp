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
if (!isset($post['campa']) || !isset($post['camp_region']) || !isset($post['camp_ciudad']) || !isset($post['camp_lugar']) || !isset($post['new_lugar']) || !isset($post['nombre']) || !isset($post['apellido']) || 
	!isset($post['email']) || !isset($post['fono']) || !isset($post['empresa']) || !isset($post['actividad']) || !isset($post['user_region']) || !isset($post['user_ciudad']) ) :
	$mensaje = 'Ha ocurrido un error con los datos ingresados.';
else :
	if (strlen($post['campa']) < 1 || strlen($post['camp_region']) < 2 || strlen($post['camp_ciudad']) < 2 || strlen($post['camp_lugar']) < 1 || strlen($post['nombre']) < 2 || strlen($post['apellido']) < 2 || 
	strlen($post['email']) < 4 || strlen($post['fono']) < 2 || strlen($post['actividad']) < 2 || strlen($post['user_region']) < 2 || strlen($post['user_ciudad']) < 2) :
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


	// Guarda Lugar Nuevo
	if ($post['camp_lugar'] == 'new_place') :
		if (strlen($post['new_lugar']) > 1) : 
			$tablaLugar = new Zend_Db_Table('lugares');
			$postLugar = array(
				'nombre'	=> $post['new_lugar'],
				'region'	=> $post['camp_region'],
				'ciudad'	=> $post['camp_ciudad'],
				'fecha'		=> $fecha
			);
			$lugarID = $tablaLugar->insert($postLugar);
			if (!$lugarID) :
				$mensaje = 'Ha ocurrido un error al guardar tus datos.';
			endif;
		else :
			$mensaje = 'Ha ocurrido un error con el nuevo lugar creado.';
		endif;

	// Si es un lugar Existente
	else :
		$lugarID = intval($post['camp_lugar']);
	endif;
endif;


// Guarda Usuarios Nuevo
if ($mensaje == 'Exito') :
	$tablaClien = new Zend_Db_Table('clientes');
	$postClien = array(
		'nombre'	=> $post['nombre'],
		'apellido'	=> $post['apellido'],
		'email'		=> $post['email'],
		'fono'		=> $post['fono'],
		'empresa'	=> $post['empresa'],
		'actividad'	=> $post['actividad'],
		'region'	=> $post['user_region'],
		'ciudad'	=> $post['user_ciudad'],
		'campa_id'	=> $post['campa'],
		'user_id'	=> $userID,
		'lugar_id'	=> $lugarID,
		'fecha'		=> $fecha
	);
	$clienNew = $tablaClien->insert($postClien);
	if (!$clienNew) :
		$mensaje = 'Ha ocurrido un error al guardar tus datos.';
	endif;
endif;


// Actualiza Datos del Usuario
if ($mensaje == 'Exito') :
	$tablaUsers = new Zend_Db_Table('users');
	$postUpdate = array('fecha_add'	=> $fecha);
	$whereUpdate = $tablaUsers->getAdapter()->quoteInto('user_id=?', $userID);
	$inserUpdate = $tablaUsers->update($postUpdate, $whereUpdate);
	if (!$inserUpdate) :
		$mensaje = 'Ha ocurrido un error al actualizar el producto canjeado.';
	endif;
endif;

if (isset($db) ) :
	$db->closeConnection();
endif;

if ($mensaje != 'Exito') :
	$log->emerg('SaveCliente : '.$mensaje);
endif;

echo $mensaje;
?>