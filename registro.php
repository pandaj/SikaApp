<?php
include 'php__header.php';


//Valida Login
global $login,$get,$extra,$user;
if (!$login) :
	sleep(3);
	devuelveHome();
endif;


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Datos de Campañas
$tablaCampa = new Zend_Db_Table('campas');
$queryCampa = $tablaCampa->select()->where('activa = ?', 1);
$datosCampa = $tablaCampa->fetchAll($queryCampa);


// Carga Datos de Lugares
$tablaLugar = new Zend_Db_Table('lugares');
$queryLugar = $tablaLugar->select();
$datosLugar = $tablaLugar->fetchAll($queryLugar);
?>
<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">

	<?php include 'php/header.php'; ?>

	<!-- Contenido -->
	<div class="app-body">

		<?php include 'php/menu.php'; ?>

		<!-- Main content -->
		<main class="main row justify-content-center">
			<div class="col-md-6">
				<div class="card mx-4">
					<div id="formbox-1" class="card-body p-4">
						<h1>Asocia una Campaña</h1>
						<p class="text-muted">Selecciona una campaña para asociarla al registro de clientes.</p>
						<div class="alert alert-danger" style="display:none;">
							<strong>Danger!</strong>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-badge"></i></span>
							<select class="form-control" name="campa">
								<option value="">[ Elige Campaña ]</option>
								<?php 
								foreach ($datosCampa as $campa) :
									echo '<option value="'.$campa['campa_id'].'">'.$campa['nombre'].'</option>';
								endforeach;
								?>
							</select>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-map"></i></span>
							<select class="form-control" name="camp_region">
								<option value="">[ Región de Campaña ]</option>
								<option value="metropolitana">Región Metropolitana</option>
								<option value="region1">I Región</option>
								<option value="region2">II Región</option>
								<option value="region3">III Región</option>
								<option value="region4">IV Región</option>
								<option value="region5">V Región</option>
								<option value="region6">VI Región</option>
								<option value="region7">VII Región</option>
								<option value="region8">VIII Región</option>
								<option value="region9">IX Región</option>
								<option value="region10">X Región</option>
								<option value="region11">XI Región</option>
								<option value="region12">XII Región</option>
								<option value="region14">XIV Región</option>
								<option value="region15">XV Región</option>
							</select>
						</div>

						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-map"></i></span>
							<select class="form-control" name="camp_ciudad">
								<option value="">[ Ciudad o Comuna ]</option>
							</select>
						</div>

						<div class="input-group mb-4">
							<span class="input-group-addon"><i class="icon-location-pin"></i></span>
							<select class="form-control" name="camp_lugar">
								<option value="">[ Lugar de Campaña ]</option>
							</select>
							<input type="text" class="form-control" name="new_lugar" placeholder="Nuevo Lugar" style="display:none;" maxlength="100" />
						</div>
						<button type="button" class="btn btn-block btn-success" onClick="asociarCamp();">Asociar Campaña</button>
					</div>
					<div id="formbox-2" class="card-footer p-4" style="display:none;">
						<h1>Registro de Cliente</h1>
						<p class="text-muted">Ingresa los datos del cliente.</p>
						<div class="alert alert-danger" style="display:none;">
							<strong>Danger!</strong>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<input type="text" class="form-control" name="nombre" placeholder="Nombre" maxlength="50" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<input type="text" class="form-control" name="apellido" placeholder="Apellidos" maxlength="50" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon">@</span>
							<input type="email" class="form-control" name="email" placeholder="Email" maxlength="100" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-phone"></i></span>
							<input type="text" class="form-control" name="fono" placeholder="Teléfono (opcional) (debe incluir +56)" maxlength="12" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-people"></i></span>
							<input type="text" class="form-control" name="empresa" placeholder="Empresa (opcional)" maxlength="100" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-wrench"></i></span>
							<select class="form-control" name="actividad">
								<option value="">[ Actividad ]</option>
								<option>Productor/Fabricante</option>
								<option>Contratista</option>
								<option>Mayorista/Distribuidor</option>
								<option>Propietario</option>
								<option>Especificador</option>
								<option>Arquitecto</option>
								<option>Ingeniero</option>
								<option>Topógrafo</option>
								<option>Maestro/Aplicador</option>
								<option>Autoridad</option>
								<option>Hágalo Ud. Mismo</option>
								<option>Otros</option></select>
							</select>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-map"></i></span>
							<select class="form-control" name="user_region">
								<option value="">[ Región ]</option>
								<option value="metropolitana">Región Metropolitana</option>
								<option value="region1">I Región</option>
								<option value="region2">II Región</option>
								<option value="region3">III Región</option>
								<option value="region4">IV Región</option>
								<option value="region5">V Región</option>
								<option value="region6">VI Región</option>
								<option value="region7">VII Región</option>
								<option value="region8">VIII Región</option>
								<option value="region9">IX Región</option>
								<option value="region10">X Región</option>
								<option value="region11">XI Región</option>
								<option value="region12">XII Región</option>
								<option value="region14">XIV Región</option>
								<option value="region15">XV Región</option>
								<option value="fuera">Fuera de Chile</option>
							</select>
						</div>
						<div class="input-group mb-4">
							<span class="input-group-addon"><i class="icon-location-pin"></i></span>
							<select class="form-control" name="user_ciudad">
								<option value="">[ Ciudad o Comuna ]</option>
							</select>
						</div>

						<button type="button" class="btn btn-block btn-success" onClick="registraCliente();">Registrar Cliente</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include 'php/footer.php'; ?>

	<!-- Script Propios -->
	<script src="js/comunasChile.js"></script>
	<script src="js/validaDatos.js"></script>
	<script src="js/julioForm.js"></script>
	<script>
		var lasKeys = new Array('0','1','2','3','4','5','6','7','8','9','+');
	
		$(document).ready(function(e) {
			$('select[name=camp_region]').change(function() {
				cambioRegion( $(this).val(), 'select[name=camp_ciudad]');
			});
			$('select[name=user_region]').change(function() {
				cambioRegion( $(this).val(), 'select[name=user_ciudad]');
			});
			$('select[name=camp_ciudad]').change(function() {
				cambiaLugares( $(this).val(), 'select[name=camp_lugar]');
			});
			$('select[name=camp_lugar]').change(function() {
				if ($(this).val() == 'new_place') {
					$(this).animate({opacity:0}, 500);
					$(this).attr('disabled', 'disabled');
					setTimeout(function() {
						$('select[name=camp_lugar]').animate({opacity:1}, 500);
						$('input[name=new_lugar]').fadeIn(500);
					}, 500);
				}
			});
			
			$('input[name=fono]').keypress(function(evt) {
				console.log(evt.key);
				if (lasKeys.indexOf(evt.key) < 0) {
					return false;
				} else if ( $('input[name=fono]').val().indexOf('+') >= 0 && evt.key == '+') {
					return false;
				}
			});
		});

		// Cambia Region / Comuna
		function cambioRegion(regionID, elDiv) {
			if (regionID != '') {
				if (regionID == 'fuera')				regionID = 0;
				else if (regionID == 'metropolitana')	regionID = 13;
				else									regionID = parseInt( regionID.replace('region','') );
			
				$(elDiv).html('<option value="">[ Ciudad o Comuna ]</option>');
				$(elDiv).val('');
				$(elDiv).trigger('change');
				
				for (var c = 0; c < ciudadesChile[regionID].length; c++){
					var comuna = ciudadesChile[regionID][c];
					$(elDiv).append('<option value="'+ comuna +'">'+ comuna +'</option>');
				}
				if (elDiv == 'select[name=camp_ciudad]' && $('input[name=new_lugar]').is(':visible') ) {
					$('select[name=camp_lugar]').val('');
					$('select[name=camp_lugar]').trigger('change');
					$('input[name=new_lugar]').val('');
					$('input[name=new_lugar]').fadeOut(500);
					$('select[name=camp_lugar]').animate({opacity:0}, 500);
					setTimeout(function() {
						$('select[name=camp_lugar]').removeAttr('disabled');
						$('select[name=camp_lugar]').animate({opacity:1}, 500);
					}, 500);
				}
			}
		}
		
		// Muestra Lugares
		var losLugares = new Array();
		<?php 
		$comunas = array();
		foreach ($datosLugar as $lugar) :
			if (!in_array($lugar['ciudad'], $comunas) ) :
				echo 'losLugares["'.$lugar['ciudad'].'"] = new Array(); ';
				$comunas[] = $lugar['ciudad'];
			endif;
			echo 'losLugares["'.$lugar['ciudad'].'"].push({nombre:"'.$lugar['nombre'].'",id:'.$lugar['lugar_id'].'}); ';
		endforeach;
		?>
		function cambiaLugares(comuna, elDiv) {
			if (comuna != '') {
				$(elDiv).html('<option value="">[ Lugar de Campaña ]</option>');
				$(elDiv).val('');
				$(elDiv).trigger('change');
				if (typeof(losLugares[comuna]) !== 'undefined') {
					for (var c = 0; c < losLugares[comuna].length; c++){
						var lugar = losLugares[comuna][c];
						$(elDiv).append('<option value="'+ lugar.id +'">'+ lugar.nombre +'</option>');
					}
				}
				$(elDiv).append('<option value="new_place">[ Nuevo Lugar ]</option>');
				
				if ($('input[name=new_lugar]').is(':visible') ) {
					$('input[name=new_lugar]').val('');
					$('input[name=new_lugar]').fadeOut(500);
					$('select[name=camp_lugar]').animate({opacity:0}, 500);
					setTimeout(function() {
						$('select[name=camp_lugar]').removeAttr('disabled');
						$('select[name=camp_lugar]').animate({opacity:1}, 500);
					}, 500);
				}
			}
		}
		
		var activaTodo = true;
		function asociarCamp() {
			if (activaTodo) {
				if ( $('select[name=campa]').val() != '' && $('select[name=camp_region]').val() != '' && $('select[name=camp_ciudad]').val() != '' && $('select[name=camp_lugar]').val() != '') {
					if ( $('select[name=camp_lugar]').val() == 'new_place' && $('input[name=new_lugar]').val().length < 2) {
						alertas('Debes ingresar el nombre del nuevo lugar.');
					} else {
						activaTodo = false;
						$('#formbox-1').animate({opacity:0}, 500);
						setTimeout(function() {
							$('#formbox-1').hide();
							$('#formbox-2').fadeIn(500);
						}, 500);
						setTimeout(function() {
							activaTodo = true;
						}, 1000);
					}
				} else {
					alertas('Debes completar todos los datos.');
				}
			}
		}
		
		function alertas(txt) {
			
			// Alertas Form 2
			if ($('#formbox-2').is(':visible') ) {
				$('#formbox-2 .alert strong').html(txt);
				if ($('#formbox-2 .alert').hasClass('alert-success') ) {
					$('#formbox-2 .alert').removeClass('alert-success');
					$('#formbox-2 .alert').addClass('alert-danger');
				}
				$('#formbox-2 .alert').fadeIn(300);
			
			// Alertas Form 1
			} else {
				$('#formbox-1 .alert strong').html(txt);
				$('#formbox-1 .alert').fadeIn(300);
			}
		}
		
		function registraCliente() {
			if (activaTodo) {
				var losDatos = new Array(
					{
						input		: $('select[name=campa]'),
						tipo		: '',
						minText		: 1,
						aleCompleta	: 'Error #001. Ha ocurrido un error con los datos de campaña. Por favor, actualice esta página.'
					},{
						input		: $('select[name=camp_region]'),
						tipo		: '',
						minText		: 2,
						aleCompleta	: 'Error #002. Ha ocurrido un error con los datos de campaña. Por favor, actualice esta página.'
					},{
						input		: $('select[name=camp_ciudad]'),
						tipo		: '',
						minText		: 2,
						aleCompleta	: 'Error #003. Ha ocurrido un error con los datos de campaña. Por favor, actualice esta página.'
					},{
						input		: $('select[name=camp_lugar]'),
						tipo		: '',
						minText		: 1,
						aleCompleta	: 'Error #003. Ha ocurrido un error con los datos de campaña. Por favor, actualice esta página.'
					},{
						input		: $('input[name=new_lugar]'),
						tipo		: '',
						minText		: 0
					},{
						input		: $('input[name=nombre]'),
						tipo		: 'nombre',
						minText		: 2,
						aleCompleta	: 'Debes ingresar el nombre del cliente.',
						aleValida	: 'Debes ingresar un nombre válido, sin símbolos extraños ni números.'
					},{
						input		: $('input[name=apellido]'),
						tipo		: 'nombre',
						minText		: 2,
						aleCompleta	: 'Debes ingresar los apellido del cliente.',
						aleValida	: 'Debes ingresar unos apellido válidos, sin símbolos extraños ni números.'
					},{
						input		: $('input[name=email]'),
						tipo		: 'email',
						minText		: 5,
						aleCompleta	: 'Debes ingresar el email del cliente.',
						aleValida	: 'Debes ingresar un email válido, sin tildes, espacios ni símbolos extraños.'
					},{
						input		: $('input[name=fono]'),
						tipo		: 'fono',
						minText		: 8,
						aleCompleta	: 'Debes ingresar un teléfono de al menos 8 dígitos.',
						aleValida	: 'Debes ingresar un teléfono válido, sin letras ni símbolos extraños.'
					},{
						input		: $('input[name=empresa]'),
						tipo		: '',
						minText		: 0
					},{
						input		: $('select[name=actividad]'),
						tipo		: '',
						minText		: 2,
						aleCompleta	: 'Debes indicar la actividad del cliente.'
					},{
						input		: $('select[name=user_region]'),
						tipo		: '',
						minText		: 2,
						aleCompleta	: 'Debes indicar la región del cliente.'
					},{
						input		: $('select[name=user_ciudad]'),
						tipo		: '',
						minText		: 2,
						aleCompleta	: 'Debes indicar la ciudad o comuna deñ cliente.'
					}
				);
				
				validaJulioform(losDatos, 'adminSaveCliente.php', function(txt) {
					if (txt == 'Exito') {
						activaTodo = false;
						$('#formbox-2 .alert strong').html('El cliente se ha registrado correctamente.');
						if ($('#formbox-2 .alert').hasClass('alert-danger') ) {
							$('#formbox-2 .alert').removeClass('alert-danger');
							$('#formbox-2 .alert').addClass('alert-success');
						}
						$('#formbox-2 .alert').fadeIn(300);
						$('#formbox-2 input').each(function() {
							$(this).val('');
						});
						$('#formbox-2 select').each(function() {
							$(this).val('');
							$(this).trigger('change');
						});
						setTimeout(function() {
							$('#formbox-2 .alert').fadeOut(300);
						}, 1300);
						setTimeout(function() {
							activaTodo = true;
						}, 1600);
					} else {
						alertas(txt);
					}
				});
			}
		}
	</script>

</body>
</html>