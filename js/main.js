//URL Global
window.dataURL = 'http://appsredon.cl/apps/sikaform/connect/';


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
	
	// Si esta logueado y vuelve al index por error
	if (num == 0 && datosUsuario) {
		location.href = pag +'.html';

	// Si ingresa a otra pagina
	} else {
		if (datosUsuario) {
			if (datosUsuario['acceso'] < num) location.href = pag +'.html';

		// Si no hay datos del usuario
		} else {
			location.href = 'index.html';
		}
	}
}


// Accesos Menu
function accesosMenu() {
	var datosUsuario = window.localStorage.getItem("usuario");
	
	// Si hay datos guardados de un usuario
	if (datosUsuario) {

		// Borra los menus de nivel 7
		if (datosUsuario['acceso'] < 7) {
			$('.acceso7').each(function() {
				$(this).remove();
			});
		}

		// Borra los menus de nivel 5
		if (datosUsuario['acceso'] < 5) {
			$('.acceso5').each(function() {
				$(this).remove();
			});
		}

		// Borra los menus de nivel 3
		if (datosUsuario['acceso'] < 3) {
			$('.acceso3').each(function() {
				$(this).remove();
			});
		}
	
	// Si no hay usuario
	} else {
		location.href = 'index.html';
	}
}