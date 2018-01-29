<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


$a1 = array("red","green");
$a2 = array("blue","yellow");
$a12 = array_merge($a1,$a2);
print_r($a12);






die('');
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

$post = array('id'	=> 3);


// Obtiene Datos del Lugar
if ($mensaje == 'Exito') :


	// Carga Base de Datos
	$config = require_once 'config/db.php';
	$db = Zend_Db::factory('Mysqli', $config);
	Zend_Db_Table_Abstract::setDefaultAdapter($db);

	$tablaUsers = new Zend_Db_Table('users');


	// Si se modifica un usuario
	if (intval($post['id']) > 0) :
$tablaUsers = new Zend_Db_Table('users');
$queryUsers = $tablaUsers->select()->where('user_id = ?', $post['id']);
$datosUsers = $tablaUsers->fetchRow($queryUsers);
$borraUsers = $datosUsers->delete();
		
		if (isset($datosUsers['user_id']) ) :
			
			if ($borraUsers) :
				echo 'Exito';
			else :
				echo 'Error 2';
			endif;
		else :
			echo 'Error 1';
		endif;
	endif;
endif;


// Cierra Conexion
if (isset($db) ) :
	$db->closeConnection();
endif;
?>