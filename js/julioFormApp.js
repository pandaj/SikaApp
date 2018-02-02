/***** JULIO FORM ver. 1.30 (26-dic-2016) *****/
/***** requiere validaDatos.js y definir la funcion "alertas" *****/
/*
Ej :
<html>
	<input type="text" id="formName" name="nombre" placeholder="NOMBRE" maxlength="100" />
	<input type="text" id="formMail" name="email" placeholder="E-MAIL" maxlength="100" />
	<input type="checkbox" id="formCheck" name="check" />
</html>
var losDatos = new Array(
	{
		input		: $('#formName'),
		tipo		: 'nombre',
		minText		: 2,
		aleCompleta	: 'Debes escribir tu nombre.',
		aleValida	: 'Debes escribir tu nombre sin números ni simbolos.'
	},{
		input		: $('#formEmail'),
		tipo		: 'email',
		minText		: 5,
		aleCompleta	: 'Debes escribir tu email.',
		aleValida	: 'Debes escribir un email válido.'
	},{
		input		: $('#formCheck'),
		tipo		: 'checkbox',
		minText		: 0,
		aleCompleta	: 'Debes ser mayor de edad y aceptar las bases.'
	}
);
validaJulioform (losDatos, 'guardaDatos.php', function(txt) {
	alert(txt); //txt = respuesta del PHP
});

tipos :
- email
- numero
- fono
- nombre
- alfanum (solo letras y numeros, sin simbolos, acentos ni espacios)
- checkbox
- rut (requiere julioRut.js)
*/

var activaJulioForm = true;
function validaJulioform (datos, resultado) {
	if (activaJulioForm) {
		
		//Valida Datos
		var enviar = true;
		var dataProce = new Object();
		for (var d = 0; d < datos.length; d++) {
			var elDiv = datos[d].input;
			var elNombre = elDiv.attr('name');
			
			var elDato = '';
			if (datos[d].tipo == 'email') elDiv.val( elDiv.val().toLowerCase() );
			if (datos[d].tipo != 'checkbox') elDato = elDiv.val();
			
			//Guarda Datos Procesados
			dataProce[elNombre] = elDato;
			
			//Revisa si el dato existe
			if (typeof elDato === 'undefined') {
				alert('La variable '+ (d+1) +' no está definida.');
			
			//Si el dato existe
			} else {
				//Revisa si el campo esta lleno
				if (datos[d].minText > 0) {
					if (elDato.length < datos[d].minText && enviar) {
						alertas(datos[d].aleCompleta);
						enviar = false;
					}
				}
				//Valida si es true o false
				if (datos[d].tipo == 'checkbox' && enviar) {
					if (elDiv.is(':checked') == false) {
						alertas(datos[d].aleCompleta);
						enviar = false;
					}
				}
				//Valida Emails
				if (datos[d].tipo == 'email' && enviar) {
					if (validaEmail(elDato) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
				}
				//Valida Números
				if (datos[d].tipo == 'numero' && enviar) {
					if (validaNum(elDato) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
				}
				//Valida Teléfonos
				if (datos[d].tipo == 'fono' && enviar) {
					if (validaFono(elDato) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
				}
				//Valida Nombres
				if (datos[d].tipo == 'nombre' && enviar) {
					if (validaNombre(elDato) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
				}
				//Valida Textos Alfa-numéricos
				if (datos[d].tipo == 'alfanum' && enviar) {
					if (validaAlfa(elDato) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
				}
				//Valida Rut
				if (datos[d].tipo == 'rut' && enviar) {
					if (elDiv.val().indexOf('-') < 0) {
						alertas(datos[d].aleValida);
						enviar = false;
					} else if (validarRut(elDiv) == false) {
						alertas(datos[d].aleValida);
						enviar = false;
					}
					dataProce[elNombre] = elDiv.val();
				}
			}
		}
		
		//Envia Datos
		if (enviar) {
			resultado(dataProce);
		}
	}
}