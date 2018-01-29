// Detecta si hay conexion a internet o no
window.internet = false;
function detectaConexion() {
	var networkState = navigator.network.connection.type;
	var states = new Array();
	states[Connection.UNKNOWN]  = 'Unknown';
	states[Connection.ETHERNET] = 'Ethernet';
	states[Connection.WIFI]     = 'WiFi';
	states[Connection.CELL_2G]  = 'Cell 2G';
	states[Connection.CELL_3G]  = 'Cell 3G';
	states[Connection.CELL_4G]  = 'Cell 4G';
	states[Connection.NONE]     = 'No network';
	var conexion = states[networkState];
	if (conexion = 'No network') {
		return false;
	} else {
		return true;
	}
}
$(document).ready(function(e) {
	window.internet = detectaConexion();
});


// Valida Login
function loginRedirige(pag, num) {
	var datosUsuario = window.localStorage.getItem("usuario");
	
	// Si hay datos guardados de un usuario
	if (datosUsuario) {
		if (datosUsuario['acceso'] < num) location.href = pag +'.html';
	}
}