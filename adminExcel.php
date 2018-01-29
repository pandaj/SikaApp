<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 9999);
ini_set('max_input_time', 9999);
ini_set('memory_limit', '2048M');

date_default_timezone_set('America/Santiago');

//Solo ejecutar desde browser
if (PHP_SAPI == 'cli') die('No puedes ejecutar este archivo fuera del admin.');


//Valida Datos
if (!isset($_POST['datos']) || !isset($_POST['nombre']) ) :
	die('Hay un problema con los datos.');
else :
	$datosExcel = json_decode($_POST['datos']);
	if (count($datosExcel) < 2) :
		die('Hay un error con los datos exportados.');
	endif;
endif;


/*----- Include PHPExcel -----*/
require_once 'PHPExcel/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("AdminWeb")
							 ->setLastModifiedBy("AdminWeb")
							 ->setTitle("Datos Admin")
							 ->setSubject("Datos Admin")
							 ->setDescription("Datos Admin, by AdminWeb.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Datos Admin");

/*----- Datos Importados -----*/
$letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$num = 0;
foreach ($datosExcel as $dato) :
	$num++;
	for ($i = 0; $i < count($dato); $i++) :
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letras[$i].$num, $dato[$i]);
	endfor;
endforeach;

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Datos');
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$_POST['nombre'].'.xls"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>