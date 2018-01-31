// Regiones
var regionNames = new Array();
regionNames["metropolitana"]= 'Región Metropolitana';
regionNames["region1"]		= 'I Región';
regionNames["region2"]		= 'II Región';
regionNames["region3"]		= 'III Región';
regionNames["region4"]		= 'IV Región';
regionNames["region5"]		= 'V Región';
regionNames["region6"]		= 'VI Región';
regionNames["region7"]		= 'VII Región';
regionNames["region8"]		= 'VIII Región';
regionNames["region9"]		= 'IX Región';
regionNames["region10"]		= 'X Región';
regionNames["region11"]		= 'XI Región';
regionNames["region12"]		= 'XII Región';
regionNames["region14"]		= 'XIV Región';
regionNames["region15"]		= 'XV Región';
regionNames["fuera"]		= 'Fuera de Chile';

// Accesos
var accesoNames = new Array();
accesoNames[1]	= 'Solo Ingresa Datos';
accesoNames[3]	= 'Ingresa Datos + Ver Registros';
accesoNames[5]	= 'Ingresa Datos + Registros + Campañas';
accesoNames[7]	= 'Control Total';
accesoNames[9]	= 'ADMIN MASTER';

// convert Hex to RGBA
function convertHex(hex,opacity){
	hex = hex.replace('#','');
	var r = parseInt(hex.substring(0,2), 16);
	var g = parseInt(hex.substring(2,4), 16);
	var b = parseInt(hex.substring(4,6), 16);
	var result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
	return result;
}

// Random Numbers
function random(min,max) {
	return Math.floor(Math.random()*(max-min+1)+min);
}

// Fecha hoy
function getFechaHoy() {
	var date = new Date();
	var mm = date.getMonth() + 1;
  	var dd = date.getDate();
  	var laFecha = date.getFullYear() +'-'+ (mm > 9 ? '' : '0') + mm +'-'+ (dd > 9 ? '' : '0') + dd;
  	return laFecha;
}