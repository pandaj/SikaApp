//URL Global
window.dataURL = 'http://appsredon.cl/apps/sikaform/connect/';


// Detecta si hay conexion a internet o no
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
	if (conexion == 'No network') {
		return false;
	} else {
		return true;
	}
}
//window.internet = detectaConexion();
window.internet = true;


// Valida Login
window.elUsuario = false;
function loginRedirige(pag, num) {
	var datosUsuario = JSON.parse( window.localStorage.getItem("usuario") );
	
	// Si esta logueado y vuelve al index por error
	if (num == 0) {
		if (datosUsuario) {
			alert('Ya estás Logueado.');
			location.href = pag +'.html';
		}

	// Si ingresa a otra pagina
	} else {
		if (datosUsuario) {
			if (datosUsuario.acceso < num) {
				alert('No tienes acceso a esta página.');
				location.href = pag +'.html';
			}
			window.elUsuario = datosUsuario;

		// Si no hay datos del usuario
		} else {
			alert('No estás logueado.');
			location.href = 'index.html';
		}
	}
}


// Accesos Menu
function loadMenu() {
	// Si hay datos guardados de un usuario
	if (window.elUsuario) {
		$('#side-menu').html(
			'<nav class="sidebar-nav">'+
				'<ul class="nav">'+
					'<li class="nav-item">'+
						'<a id="btn-1" class="nav-link" href="home.html">'+
							'<i class="icon-star"></i> Mis Estadisticas'+
						'</a>'+
					'</li>'+
					'<li class="nav-item">'+
						'<a id="btn-2" class="nav-link" href="registro.html">'+
							'<i class="icon-plus"></i> Registrar Cliente'+
						'</a>'+
					'</li>'+
					'<li class="nav-item acceso3">'+
						'<a id="btn-3" class="nav-link" href="estadisticas.html">'+
							'<i class="icon-chart"></i> Estadisticas Generales'+
						'</a>'+
					'</li>'+
					'<li class="nav-title acceso3">'+
						'Datos Detallados'+
					'</li>'+
					'<li class="nav-item acceso3">'+
						'<a id="btn-4" class="nav-link" href="datos_clientes.html">'+
							'<i class="icon-list"></i> Clientes Únicos'+
						'</a>'+
					'</li>'+
					'<li class="nav-item acceso3">'+
						'<a id="btn-5" class="nav-link" href="datos_clientes_cmp.html">'+
							'<i class="icon-list"></i> Clientes por Campaña'+
						'</a>'+
					'</li>'+
					'<li class="nav-item acceso3">'+
						'<a id="btn-6" class="nav-link" href="datos_clientes_lgr.html">'+
							'<i class="icon-list"></i> Clientes por Lugar'+
						'</a>'+
					'</li>'+
					'<li class="nav-item acceso3">'+
						'<a id="btn-7" class="nav-link" href="datos_clientes_usr.html">'+
							'<i class="icon-list"></i> Clientes por Usuario'+
						'</a>'+
					'</li>'+
					'<li class="nav-title acceso5">'+
						'Administración'+
					'</li>'+
					'<li class="nav-item acceso7">'+
						'<a id="btn-8" class="nav-link" href="edit_users.html">'+
							'<i class="icon-people"></i> Admin. Usuarios'+
						'</a>'+
					'</li>'+
					'<li class="nav-item acceso5">'+
						'<a id="btn-9" class="nav-link" href="edit_campa.html">'+
							'<i class="icon-settings"></i> Admin. Campañas'+
						'</a>'+
					'</li>'+
				'</ul>'+
			'</nav>'+
			'<button class="sidebar-minimizer brand-minimizer" type="button"></button>'
		);

		// menus de nivel 7
		if (window.elUsuario.acceso < 7) {
			$('#side-menu .acceso7').each(function() {
				$(this).remove();
			});
		}

		// menus de nivel 5
		if (window.elUsuario.acceso < 5) {
			$('#side-menu .acceso5').each(function() {
				$(this).remove();
			});
		}

		// menus de nivel 3
		if (window.elUsuario.acceso < 3) {
			$('#side-menu .acceso3').each(function() {
				$(this).remove();
			});
		}
	}
}


// Log Out
function logout() {
	var confirma = confirm("¿ Realmente quieres salir ?");
	if (confirma == true) {
		//window.localStorage.removeItem("usuario");
		window.localStorage.clear();
		setTimeout(function() {
			location.href = 'index.html';
		}, 500);
	}
}


// Carga Header
function loadHeader() {
	if (window.elUsuario) {
		$('#header').html(
			'<button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">'+
				'<span class="navbar-toggler-icon"></span>'+
			'</button>'+
			'<a class="navbar-brand" href="home.html">'+
				'<img src="img/logo-sika.png" width="100%" />'+
			'</a>'+
			'<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">'+
				'<span class="navbar-toggler-icon"></span>'+
			'</button>'+
			'<ul class="nav navbar-nav ml-auto">'+
				'<li class="nav-item dropdown">'+
					'<div class="nav-link dropdown-toggle nav-link">'+
						'<span id="userdata-nombre">'+ window.elUsuario.nombre +'</span> ' +
						'<img src="img/avatares/'+ window.elUsuario.foto +'" class="img-avatar" />'+
					'</div>'+
				'</li>'+
				'<li class="nav-item d-md-down-none">'+
					'<a class="nav-link" href="javascript: logout();">'+
						'<i class="fa fa-lock"></i> Logout'+
					'</a>'+
				'</li>'+
				'<li class="nav-item d-md-down-none"></li>'+
			'</ul>'
		);
	}
}