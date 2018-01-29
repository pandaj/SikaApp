<?php
ini_set('max_execution_time', 999);
ini_set('max_input_time', 999);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set("display_errors", 1);

$extra = include 'config/extra.php';
$login = false;
$mensaje = 'Exito';


// Valida Usuario
session_start();
$fechaLogin = date("Y-m-d");
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


// CARGA EL ZEND LOADER
//set_include_path(get_include_path().PATH_SEPARATOR.$extra['ruta_zend']);
require 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();


// DATOS POR POST
$request = new Zend_Controller_Request_Http();
$post = $request->getPost();


// Validacion Datos POST
if (!isset($post['pag']) || !isset($post['tabla']) ) :
	$mensaje = 'Ha ocurrido un error con sus datos.';
else :
	if (strlen($post['pag']) < 1 || strlen($post['tabla']) < 2) :
		$mensaje = 'Ha ocurrido un problema con sus datos.';
	endif;
endif;


// Carga Datos
if ($mensaje == 'Exito') :

	// Carga Base de Datos
	$config = require_once 'config/db.php';
	$db = Zend_Db::factory('Mysqli', $config);
	Zend_Db_Table_Abstract::setDefaultAdapter($db);

	//Query
	$perPag = 5000;
	$pagIni = (intval($post['pag']) - 1) * $perPag;
	$queryDB = "SELECT * FROM ".$post['tabla']." LIMIT ".$pagIni.",".$perPag;
	$datosDB = $db->query($queryDB); 
	$datosData = $datosDB->fetchAll();
	$db->closeConnection();

	if (count($datosData) > 0) :
		$n = 0;
		foreach ($datosData as $row) :
			if ($n != 0) :
				echo '<xxx>';
			endif;
			$k = 0;
			foreach ($row as $key => $dato) :
				if ($k != 0) :
					echo '<xx>';
				endif;
				echo $key.'<x>'.$dato;
				$k++;
			endforeach;
			$n++;
		endforeach;
	else :
		echo 'FIN';
	endif;
endif;
if ($mensaje != 'Exito') :
	echo 'Error: '.$mensaje;
endif;
?>