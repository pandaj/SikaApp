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
if (!isset($post['tabla']) || !isset($post['tipo']) || !isset($post['dato']) ) :
	$mensaje = 'Ha ocurrido un error con los datos ingresados.';
else :
	if (strlen($post['tabla']) < 2 || strlen($post['tipo']) < 2 || strlen($post['dato']) < 1) :
		$mensaje = 'Ha ocurrido un problema con los datos ingresados.';
	endif;
endif;


// Valida Sesion
if ($mensaje == 'Exito') :
	session_start();
	if (!isset($_SESSION['bloqueo']) && isset($_SESSION['user_login']) && isset($_SESSION['user_datos']) ) :
		if ($_SESSION['user_login'] == base64_encode( $fechaLogin ) && !empty($_SESSION["user_datos"]) ) :
			//OK
		else :
			$mensaje = 'Ha ocurrido un problema con tu sesión. Por favor, vuelve a ingresar.';
		endif;
	else :
		$mensaje = 'Ha ocurrido un error con tu sesión. Por favor, vuelve a ingresar.';
	endif;
endif;


// Sesion Exitosa
if ($mensaje == 'Exito') :


	// Carga Base de Datos
	$configDB = require_once 'config/db.php';
	$conectDB = Zend_Db::factory('Mysqli', $configDB);
	Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


	// Consulta segun Tipo
	$queryData = '';
	
	// Clientes por Usuario
	if ($post['tipo'] == 'users_clientes') :
		$queryData = ' WHERE user_id = '.$post['dato'];

	// Clientes por Campaña
	elseif ($post['tipo'] == 'campa_clientes') :
		$queryData = ' WHERE campa_id = '.$post['dato'];
	endif;
	
	
	// Carga los datos
	$query = $conectDB->query('SELECT COUNT(*) FROM '.$post['tabla'].$queryData); 
	$rows = $query->fetchAll();
	$valor = intval($rows[0]['COUNT(*)']);
endif;


// Cierra Conexion
if (isset($conectDB) ) :
	$conectDB->closeConnection();
endif;


// Guarda Errores
if ($mensaje != 'Exito') :
	$log->emerg('adminQuerys : '.$mensaje);
	echo $mensaje;
else :
	echo $valor;
endif;
?>