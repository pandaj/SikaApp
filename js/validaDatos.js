/***** JULIO VALIDA DATOS ver. 1.50 (28-abr-2017) *****/
/*---------------------------------------------------------------------
			NOTA : NO ES COMPATIBLE CON IE 8 o Inferior
---------------------------------------------------------------------*/


/*----- Validador para Emails -----*/
var charsEmail = Array(
	"@",".","-","_", //simbolos
	"0","1","2","3","4","5","6","7","8","9", //numeros
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", //minusculas
	"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z" //letras en baja
);
function validaEmail(elMail) {
	var respuesta = true;
	for (var c = 0; c < elMail.length; c++) {
		var letra = elMail.substr(c, 1);
		if (charsEmail.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	if (elMail.indexOf('@') < 1 || elMail.indexOf('.') < 1) {
		respuesta = false;
	} else {
		if (elMail.indexOf('@') != elMail.lastIndexOf('@') ) {
			respuesta = false;
		} else {
			var numA = elMail.lastIndexOf('@');
			var numP = elMail.lastIndexOf('.');
			if ( (numA+1) >= numP) {
				respuesta = false;
			} else {
				if (elMail.indexOf('@.') >= 0 || elMail.indexOf('@-') >= 0 || elMail.indexOf('@_') >= 0) {
					respuesta = false;
				} else {
					var elMailData = elMail.split('@');
					if (elMailData[1].length < 4) {
						respuesta = false;
					}
				}
			}
		}
	}
	return respuesta;
}



/*----- Validador Solo Numeros -----*/
var charsNum = Array( "0","1","2","3","4","5","6","7","8","9" );
function validaNum(elNum) {
	var respuesta = true;
	for (var c = 0; c < elNum.length; c++) {
		var letra = elNum.substr(c, 1);
		if (charsNum.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	return respuesta;
}


/*----- Validador Numeros de Teléfono -----*/
var charsFono = Array("0","1","2","3","4","5","6","7","8","9"," ","-","+");
//var charsFono = Array("0","1","2","3","4","5","6","7","8","9","+");
function validaFono(elNum) {
	var respuesta = true;
	for (var c = 0; c < elNum.length; c++) {
		var letra = elNum.substr(c, 1);
		if (charsFono.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	return respuesta;
}


/*----- Validador Códigos Alfa-Numericos -----*/
var charsAlfa = Array(
	"0","1","2","3","4","5","6","7","8","9", //numeros
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", //minusculas
	"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z" //letras en baja
);
function validaAlfa(elNum) {
	var respuesta = true;
	for (var c = 0; c < elNum.length; c++) {
		var letra = elNum.substr(c, 1);
		if (charsAlfa.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	return respuesta;
}


/*----- Validador Nombres -----*/
var charsNombre = Array(
	"´","'",".","-","°","~"," ", //Simbolos
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", //minusculas
	"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z", //letras en baja
	"á","é","í","ó","ú","ä","ë","ï","ö","ü","à","è","ì","ò","ù","ñ", //minusculas latinas
	"Á","É","Í","Ó","Ú","Ä","Ë","Ï","Ö","Ü","À","È","Ì","Ò","Ù","Ñ" //minusculas latinas
);
function validaNombre(elNum) {
	var respuesta = true;
	for (var c = 0; c < elNum.length; c++) {
		var letra = elNum.substr(c, 1);
		if (charsNombre.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	return respuesta;
}


/*----- Validador AlfaText -----*/
var charsAText = Array(
	"´","'",".","-","°","~"," ", //Simbolos
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", //minusculas
	"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z", //letras en baja
	"á","é","í","ó","ú","ä","ë","ï","ö","ü","à","è","ì","ò","ù","ñ", //minusculas latinas
	"Á","É","Í","Ó","Ú","Ä","Ë","Ï","Ö","Ü","À","È","Ì","Ò","Ù","Ñ", //minusculas latinas
	"1","2","3","4","5","6","7","8","9","0" //numeros
);
function validaAlfaText(elNum) {
	var respuesta = true;
	for (var c = 0; c < elNum.length; c++) {
		var letra = elNum.substr(c, 1);
		if (charsAText.indexOf(letra) < 0) {
			respuesta = false;
		}
	}
	return respuesta;
}