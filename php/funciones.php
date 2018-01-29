<?php
function ponePuntos($num) {
	$finNum = ''.$num;
	$len = strlen($finNum);	
	if ($len > 3 && $len <= 6) :
		$finNum = substr($finNum, 0, ($len - 3)).'.'.substr($finNum, ($len - 3), 3);
	elseif ($len > 6 && $len <= 9) :
		$finNum = substr($finNum, 0, ($len - 6)).'.'.substr($finNum, ($len - 6), 3).'.'.substr($finNum, ($len - 3), 3);
	elseif ($len > 9 && $len <= 12) :
		$finNum = substr($finNum, 0, ($len - 9)).'.'.substr($finNum, ($len - 9), 3).'.'.substr($finNum, ($len - 6), 3).'.'.substr($finNum, ($len - 3), 3);
	endif;
	return $finNum;
}

function devuelveHome() {
	sleep(3);
	die('<script> alert("Debes volver a ingresar"); top.location.href = "index.php"; </script>');
}

function cuentaDatos($db, $tabla, $datosWhere='') {
	if ( $datosWhere != '') :
		$query = $db->query( 'SELECT COUNT(*) FROM '.$tabla.' WHERE '.$datosWhere ); 
	else :
		$query = $db->query( 'SELECT COUNT(*) FROM '.$tabla); 
	endif;
	$rows = $query->fetchAll();
	$valor = intval($rows[0]['COUNT(*)']);
	return $valor;
}
?>