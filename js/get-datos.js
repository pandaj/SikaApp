function obtenerUsuarios() {
	// Revisa si estan los datos del usuario
	var datosUsuario = window.localStorage.getItem("usuario");
	if (datosUsuario) {

		// Si los datos ya estan cargados
		window.datosLosUsers = window.localStorage.getItem("users_data");

		// Si los datos aun no estan cargados
		if (!window.datosLosUsers) {
			$.ajax({
				url : window.dataURL + 'users.php',
				type: 'GET',
				data: {
					usuario : datosUsuario['email'],
					password: datosUsuario['pass']
				}
			}).done(function(txt) {
				if (txt.subtr(0,5) == 'Exito') {
					var newDatosUsers = new Array();
					var tempDatos = txt.split('<-xx_xx->');
					for (var t = 1; t < tempDatos.length; t++) {
						var tempDatitos = tempDatos[t].split('<-x_x->');
						var newDato = new Array();
						for (var tt = 0; tt < tempDatitos.length; tt++) {
							var tempLeDatos = tempDatitos[tt].split('<-x->');
							newDato[ tempLeDatos[0] ] = tempLeDatos[1];
						}
						newDatosUsers.push(newDato);
					}
					window.localStorage.setItem("users_data", newDatosUsers);
					window.datosLosUsers = newDatosUsers;
				} else {
					alert(txt);
				}
			});
		}
	}
}