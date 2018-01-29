<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 9999);
ini_set('max_input_time', 9999);
ini_set('memory_limit', '2048M');

session_start();


global $login,$error,$post,$get,$extra,$pagina,$user,$fecha;
$login = false;
$error = false;
$extra = include 'config/extra.php';
include 'php/funciones.php';


//Fecha Actual
date_default_timezone_set( $extra['timezone'] );
$fechaLogin = date("Y-m-d");
$fecha = date("Y-m-d H:i:s");


// CARGA EL ZEND LOADER
//set_include_path(get_include_path().PATH_SEPARATOR.$extra['ruta_zend']);
require 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();


// DATOS POR POST
$request = new Zend_Controller_Request_Http();
$post = $request->getPost();
$get = $request->getQuery();


// Valida Login
if (isset($post["usuario"]) && isset($post["password"]) && !isset($_SESSION['bloqueo']) ) :
	$post["usuario"] = strtolower($post["usuario"]);
	$post["password"] = strtolower($post["password"]);

	// Carga Base de Datos
	$configDB = require_once 'config/db.php';
	$conectDB = Zend_Db::factory('Mysqli', $configDB);
	Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);

	// Comprueba datos de Login
	$tablaUsers = new Zend_Db_Table('users');
	$queryUsers = $tablaUsers->select()->where('email=?', $post["usuario"])->where('password=?', $post["password"]);
	$datosUsers = $tablaUsers->fetchRow($queryUsers);

	// Login Correcto
	if (isset($datosUsers['email']) ) :
		$_SESSION['user_login'] = base64_encode( $fechaLogin );
		$_SESSION['user_datos'] = array(
			'id'		=> $datosUsers['user_id'],
			'nombre'	=> $datosUsers['nombre'],
			'email'		=> $datosUsers['email'],
			'foto'		=> $datosUsers['foto'],
			'acceso'	=> intval($datosUsers['acceso'])
		);
		
		// Actualiza Datos Usuario
		$datosUp = array('fecha_login' => $fecha);
		$whereUp = $tablaUsers->getAdapter()->quoteInto('email=?', $datosUsers['email']);
		$inserUp = $tablaUsers->update($datosUp, $whereUp);
		if (!$inserUp) :
			$mensaje = 'Ha ocurrido un error al actualizar el producto canjeado.';
		endif;
		$conectDB->closeConnection();
		unset($configDB);
		unset($conectDB);

	// Login Incorrecto
	else :
		if (isset($_SESSION['fallos']) ) :
			$_SESSION['fallos']++;
			if ($_SESSION['fallos'] == 5) :
				$_SESSION['bloqueo'] = true;
			endif;
		else :
			$_SESSION['fallos'] = 1;
		endif;
		$error = true;
		sleep(3);
	endif;
endif;


// Valida Usuario
if (!$error && !isset($_SESSION['bloqueo']) && isset($_SESSION['user_login']) && isset($_SESSION['user_datos']) ) :
	if ($_SESSION['user_login'] == base64_encode( $fechaLogin ) && !empty($_SESSION["user_datos"]) ) :
		$login = true;
		$user = $_SESSION["user_datos"];
	endif;
endif;
?>
<!--
 * CoreUI - Open Source Bootstrap Admin Template
 * @version v1.0.6
 * @link http://coreui.io
 * Copyright (c) 2017 creativeLabs Łukasz Holeczek
 * @license MIT
-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="googlebot" content="noindex" />
	<meta name="robots" content="noindex, nofollow" />
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW" />
	
	<link rel="shortcut icon" href="img/favicon.png">
	<title><?php echo $extra['admin_titulo']; ?></title>

	<link href="library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="library/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

	<link href="css/style.css?v=<?php echo rand(0,99999); ?>" rel="stylesheet">
</head>