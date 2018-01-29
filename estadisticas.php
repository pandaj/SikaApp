<?php
include 'php__header.php';


// Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// No tienes permiso
if ($user['acceso'] < 3) die('<script> alert("No tienes permiso para ingresar a esta página."); top.location.href = "home.php"; </script>');


// Fecha actual
$fechaFin = date("Y-m-d");


// Regiones
$regionNames = array(
	"metropolitana"	=> array(
		'nombre'	=> 'Región Metropolitana',
		'num'		=> 0
	),
	"region1"		=> array(
		'nombre'	=> 'I Región',
		'num'		=> 0
	),
	"region2"		=> array(
		'nombre'	=> 'II Región',
		'num'		=> 0
	),
	"region3"		=> array(
		'nombre'	=> 'III Región',
		'num'		=> 0
	),
	"region4"		=> array(
		'nombre'	=> 'IV Región',
		'num'		=> 0
	),
	"region5"		=> array(
		'nombre'	=> 'V Región',
		'num'		=> 0
	),
	"region6"		=> array(
		'nombre'	=> 'VI Región',
		'num'		=> 0
	),
	"region7"		=> array(
		'nombre'	=> 'VII Región',
		'num'		=> 0
	),
	"region8"		=> array(
		'nombre'	=> 'VIII Región',
		'num'		=> 0
	),
	"region9"		=> array(
		'nombre'	=> 'IX Región',
		'num'		=> 0
	),
	"region10"		=> array(
		'nombre'	=> 'X Región',
		'num'		=> 0
	),
	"region11"		=> array(
		'nombre'	=> 'XI Región',
		'num'		=> 0
	),
	"region12"		=> array(
		'nombre'	=> 'XII Región',
		'num'		=> 0
	),
	"region14"		=> array(
		'nombre'	=> 'XIV Región',
		'num'		=> 0
	),
	"region15"		=> array(
		'nombre'	=> 'XV Región',
		'num'		=> 0
	),
	"fuera"			=> array(
		'nombre'	=> 'Fuera de Chile',
		'num'		=> 0
	)
);


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Clientes del Usuario
$tablaClien = new Zend_Db_Table('clientes');
$queryClien = $tablaClien->select();
$clientes = $tablaClien->fetchAll($queryClien);
$clientesTotal = count($clientes);


// Carga Lugares
$tablaLugar = new Zend_Db_Table('lugares');
$queryLugar = $tablaLugar->select();
$lugares = $tablaLugar->fetchAll($queryLugar);
$lugaresTotal = count($lugares);


// Carga Campañas
$tablaCampa = new Zend_Db_Table('campas');
$queryCampa = $tablaCampa->select();
$campas = $tablaCampa->fetchAll($queryCampa);
$campasTotal = count($campas);


// Carga Usuarios
$tablaUsers = new Zend_Db_Table('users');
$queryUsers = $tablaUsers->select();
$usuarios = $tablaUsers->fetchAll($queryUsers);
$usersTotal = count($usuarios);


// Cierra Conexion DB
$conectDB->closeConnection();


// Datos Lugares
$lugarNames = array();
foreach ($lugares as $lugar) :
	$lugarNames[ $lugar['lugar_id'] ] = array(
		'nombre'	=> $lugar['nombre'],
		'region'	=> $lugar['region'],
		'ciudad'	=> $lugar['ciudad'],
		'num'		=> 0
	);
endforeach;
unset($tablaLugar);
unset($queryLugar);
unset($lugares);


// Datos Campañas
$campaNames = array();
foreach ($campas as $campa) :
	$campaNames[ $campa['campa_id'] ] = array(
		'nombre'	=> $campa['nombre'],
		'activa'	=> $campa['activa'],
		'num'		=> 0
	);
endforeach;
unset($tablaCampa);
unset($queryCampa);
unset($campas);


// Datos Usuarios
$usersNames = array();
foreach ($usuarios as $usuario) :
	$usersNames[ $usuario['user_id'] ] = array(
		'nombre'	=> $usuario['nombre'],
		'foto'		=> $usuario['foto'],
		'num'		=> 0
	);
endforeach;
unset($tablaUsers);
unset($queryUsers);
unset($usuarios);


// Temporal
$campaNamesTemp = $campaNames;


// Cruza Campaña x Lugares
foreach ($campaNames as $key => $lugar) :
	$campaNames[$key]['lugar'] = $lugarNames;
endforeach;


// Cruza Lugares x Usuarios
foreach ($lugarNames as $key => $lugar) :
	$lugarNames[$key]['users'] = $usersNames;
endforeach;


// Cruza Usuarios y Campañas
foreach ($usersNames as $key => $usero) :
	$usersNames[$key]['campa'] = $campaNamesTemp;
endforeach;
unset($campaNamesTemp);


// Cuenta Clientes
$unicosEmail = array();
foreach ($clientes as $cliente) :
	$elMail = $cliente['email'];
	if (!in_array($elMail, $unicosEmail) ) :
		$unicosEmail[] = $elMail;
	endif;

	$lugarID = $cliente['lugar_id'];
	$lugarNames[$lugarID]['num']++;
	
	$campaID = $cliente['campa_id'];
	$campaNames[$campaID]['num']++;
	
	$usersID = $cliente['user_id'];
	$usersNames[$usersID]['num']++;
	
	$regionID = $cliente['region'];
	$regionNames[$regionID]['num']++;
	
	// Cuenta Cruces
	$usersNames[$usersID]['campa'][$campaID]['num']++;
	$lugarNames[$lugarID]['users'][$usersID]['num']++;
	$campaNames[$campaID]['lugar'][$lugarID]['num']++;
endforeach;

$unicosTotal = count($unicosEmail);
?>
<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">

	<?php include 'php/header.php'; ?>

	<!-- Contenido -->
	<div class="app-body">

		<?php include 'php/menu.php'; ?>

		<!-- Main content -->
		<main class="main">

			<!-- Breadcrumb -->
			<ol class="breadcrumb">
				<li class="breadcrumb-item active">Estadisticas</li>
			</ol>

			<!-- Contenedor -->
			<div class="container-fluid">
				<div class="animated fadeIn">

					<!-- Datos Globales -->
					<div class="row">
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-primary">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponePuntos($clientesTotal); ?> clientes | <small><?php echo ponePuntos($unicosTotal); ?> únicos</small></h4>
									<p>Clientes Registrados</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-success">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponePuntos($campasTotal); ?></h4>
									<p>Campañas Realizadas</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-warning">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponePuntos($usersTotal); ?></h4>
									<p>Usuarios Activos</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-danger">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponePuntos($lugaresTotal); ?></h4>
									<p>Lugares de Campaña</p>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN Datos Globales -->

					<!-- Grafico -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-sm-5">
									<h4 class="card-title mb-0">Clientes Registrados</h4>
									<div class="small text-muted">[ <?php echo ponePuntos($clientesTotal); ?> clientes ]</div>
								</div>
							</div>
							<div class="chart-wrapper" style="height:300px; margin-top:40px;">
								<canvas id="main-chart" class="chart" height="300"></canvas>
							</div>
						</div>
					</div>
					<!-- FIN Grafico -->

					<!-- Bloque Datos 1 -->
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									Detalle de Estadisticas
								</div>
								<div class="card-body">
									<div class="row">

										<!-- Detalle por Campaña -->
										<div class="col-sm-12 col-lg-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-success">
														<small class="text-muted">CAMPAÑAS</small><br>
														<strong class="h4"><?php echo ponePuntos($campasTotal); ?> <small>campañas</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($campaNames as $campa) :
													$elNum = $campa['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													$activa = ' <span class="badge badge-success">ACTIVA</span>';
													if ($campa['activa'] == 0) :
														$activa = ' <span class="badge badge-danger">INACTIVA</span>';
													endif;
													?>
													<li>
														<i class="icon-star"></i>
														<span class="title"><?php echo $campa['nombre'].$activa; ?></span>
														<span class="value"><?php echo $elNum; ?> clientes 
															<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
														</span>
														<div class="bars">
															<div class="progress progress-xs">
																<div 
																	class="progress-bar bg-success" 
																	role="progressbar" 
																	style="width:<?php echo $elPor; ?>%" 
																	aria-valuenow="<?php echo $elNum; ?>" 
																	aria-valuemin="0" 
																	aria-valuemax="<?php echo $clientesTotal; ?>">
																</div>
															</div>
														</div>
													</li>
													<?php
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Campaña -->

										<!-- Detalle por Usuario -->
										<div class="col-sm-12 col-lg-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-warning">
														<small class="text-muted">USUARIOS-REGISTRADORES</small><br>
														<strong class="h4"><?php echo ponePuntos($usersTotal); ?> <small>usuarios</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($usersNames as $usero) :
													$elNum = $usero['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													?>
													<li>
														<i class="icon-location-pin"></i>
														<span class="title"><?php echo $usero['nombre']; ?></span>
														<span class="value"><?php echo $elNum; ?> clientes 
															<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
														</span>
														<div class="bars">
															<div class="progress progress-xs">
																<div 
																	class="progress-bar bg-warning" 
																	role="progressbar" 
																	style="width:<?php echo $elPor; ?>%" 
																	aria-valuenow="<?php echo $elNum; ?>" 
																	aria-valuemin="0" 
																	aria-valuemax="<?php echo $clientesTotal; ?>">
																</div>
															</div>
														</div>
													</li>
													<?php
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Usuario -->

										<!-- Detalle por Region -->
										<div class="col-sm-12 col-lg-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-primary">
														<small class="text-muted">REGIÓN DONDE VIVEN LOS CLIENTES</small><br>
														<strong class="h4"><?php echo ponePuntos($clientesTotal); ?> <small>clientes</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($regionNames as $region) :
													$elNum = $region['num'];
													if ($elNum > 0) :
														$elPor = round(100 * ($elNum / $clientesTotal) );
														?>
														<li>
															<i class="icon-map"></i>
															<span class="title"><?php echo $region['nombre']; ?></span>
															<span class="value"><?php echo $elNum; ?> clientes 
																<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
															</span>
															<div class="bars">
																<div class="progress progress-xs">
																	<div 
																		class="progress-bar bg-info" 
																		role="progressbar" 
																		style="width:<?php echo $elPor; ?>%" 
																		aria-valuenow="<?php echo $elNum; ?>" 
																		aria-valuemin="0" 
																		aria-valuemax="<?php echo $clientesTotal; ?>">
																	</div>
																</div>
															</div>
														</li>
														<?php
													endif;
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Region -->

										<!-- Detalle por Lugar -->
										<div class="col-sm-12 col-lg-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-danger">
														<small class="text-muted">LUGARES DE CAMPAÑA</small><br>
														<strong class="h4"><?php echo ponePuntos($lugaresTotal); ?> <small>lugares</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($lugarNames as $lugar) :
													$elNum = $lugar['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													?>
													<li>
														<i class="icon-location-pin"></i>
														<span class="title">
															<?php echo $lugar['nombre']; ?> | 
															<small><?php echo $lugar['ciudad'].', '.$regionNames[ $lugar['region'] ]['nombre']; ?></small>
														</span>
														<span class="value"><?php echo $elNum; ?> clientes 
															<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
														</span>
														<div class="bars">
															<div class="progress progress-xs">
																<div 
																	class="progress-bar bg-danger" 
																	role="progressbar" 
																	style="width:<?php echo $elPor; ?>%" 
																	aria-valuenow="<?php echo $elNum; ?>" 
																	aria-valuemin="0" 
																	aria-valuemax="<?php echo $clientesTotal; ?>">
																</div>
															</div>
														</div>
													</li>
													<?php
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Lugar -->

									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN Bloque Datos 1 -->

					<!-- Bloque Datos 2 -->
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									Estadisticas Cruzadas
								</div>
								<div class="card-body">
									<div class="row">

										<!-- Detalle por Campaña x Usuarios -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-success">
														<small class="text-muted">USUARIOS x CAMPAÑA</small><br>
														<strong class="h4"><?php echo ponePuntos($usersTotal); ?> <small>usuarios</small></strong> x 
														<strong class="h4"><?php echo ponePuntos($campasTotal); ?> <small>campañas</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<?php
											foreach ($usersNames as $usero) :
												?>
												<ul class="horizontal-bars type-2">
													<li>
														<div class="avatar">
															<img src="img/avatares/<?php echo $usero['foto']; ?>" class="img-avatar">
														</div>
														<span class="title"><?php echo $usero['nombre']; ?></span>
													</li>
													<?php
													$n = 0;
													foreach ($usero['campa'] as $campa) :
														$elNum = $campa['num'];
														if ($elNum > 0) :
															$n++;
															$elPor = round(100 * ($elNum / $clientesTotal) );
															$activa = ' <span class="badge badge-success">ACTIVA</span>';
															if ($campa['activa'] == 0) :
																$activa = ' <span class="badge badge-danger">INACTIVA</span>';
															endif;
															?>
															<li>
																<span class="title"><?php echo $campa['nombre'].$activa; ?></span>
																<span class="value"><?php echo $elNum; ?>
																	<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
																</span>
																<div class="bars">
																	<div class="progress progress-xs">
																		<div 
																			class="progress-bar bg-success" 
																			role="progressbar" 
																			style="width:<?php echo $elPor; ?>%" 
																			aria-valuenow="<?php echo $elNum; ?>" 
																			aria-valuemin="0" 
																			aria-valuemax="<?php echo $clientesTotal; ?>">
																		</div>
																	</div>
																</div>
															</li>
															<?php
														endif;
													endforeach;
													if ($n == 0) :
														?><li><span class="title">[ sin datos ]</span></li><?php
													endif;
													?>
												</ul>
												<hr class="mt-0" style="border:0;">
												<?php
											endforeach;
											?>
										</div>
										<!-- FIN Detalle por Campaña x Usuarios -->

										<!-- Detalle por Campaña x Lugar -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-warning">
														<small class="text-muted">CAMPAÑA x LUGARES</small><br>
														<strong class="h4"><?php echo ponePuntos($campasTotal); ?> <small>campañas</small></strong> x 
														<strong class="h4"><?php echo ponePuntos($lugaresTotal); ?> <small>lugares</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<?php
											foreach ($campaNames as $campa) :
												$activa = ' <span class="badge badge-success">ACTIVA</span>';
												if ($campa['activa'] == 0) :
													$activa = ' <span class="badge badge-danger">INACTIVA</span>';
												endif;
												?>
												<ul class="horizontal-bars type-2">
													<li>
														<i class="icon-star"></i>
														<span class="title"><?php echo $campa['nombre'].$activa; ?></span>
													</li>
													<?php
													$n = 0;
													foreach ($campa['lugar'] as $lugar) :
														$elNum = $lugar['num'];
														if ($elNum > 0) :
															$n++;
															$elPor = round(100 * ($elNum / $clientesTotal) );
															?>
															<li>
																<span class="title"><?php echo $lugar['nombre']; ?> | 
																	<small><?php echo $lugar['ciudad'].', '.$regionNames[ $lugar['region'] ]['nombre']; ?></small>
																</span>
																<span class="value"><?php echo $elNum; ?>
																	<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
																</span>
																<div class="bars">
																	<div class="progress progress-xs">
																		<div 
																			class="progress-bar bg-warning" 
																			role="progressbar" 
																			style="width:<?php echo $elPor; ?>%" 
																			aria-valuenow="<?php echo $elNum; ?>" 
																			aria-valuemin="0" 
																			aria-valuemax="<?php echo $clientesTotal; ?>">
																		</div>
																	</div>
																</div>
															</li>
															<?php
														endif;
													endforeach;
													if ($n == 0) :
														?><li><span class="title">[ sin datos ]</span></li><?php
													endif;
													?>
												</ul>
												<hr class="mt-0" style="border:0;">
												<?php
											endforeach;
											?>
										</div>
										<!-- FIN Detalle por Campaña x Lugar -->

										<!-- Detalle por Usuarios x Lugar -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-danger">
														<small class="text-muted">LUGARES x USUARIOS</small><br>
														<strong class="h4"><?php echo ponePuntos($lugaresTotal); ?> <small>lugares</small></strong> x 
														<strong class="h4"><?php echo ponePuntos($usersTotal); ?> <small>usuarios</small></strong>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<?php
											foreach ($lugarNames as $lugar) :
												?>
												<ul class="horizontal-bars type-2">
													<li>
														<i class="icon-location-pin"></i>
														<span class="title"><?php echo $lugar['nombre']; ?> | 
															<small><?php echo $lugar['ciudad'].', '.$regionNames[ $lugar['region'] ]['nombre']; ?></small>
														</span>
													</li>
													<?php
													$n = 0;
													foreach ($lugar['users'] as $usero) :
														$elNum = $usero['num'];
														if ($elNum > 0) :
															$n++;
															$elPor = round(100 * ($elNum / $clientesTotal) );
															?>
															<li>
																<span class="title"><?php echo $usero['nombre']; ?></span>
																<span class="value"><?php echo $elNum; ?>
																	<span class="text-muted small">(<?php echo $elPor; ?>%)</span>
																</span>
																<div class="bars">
																	<div class="progress progress-xs">
																		<div 
																			class="progress-bar bg-danger" 
																			role="progressbar" 
																			style="width:<?php echo $elPor; ?>%" 
																			aria-valuenow="<?php echo $elNum; ?>" 
																			aria-valuemin="0" 
																			aria-valuemax="<?php echo $clientesTotal; ?>">
																		</div>
																	</div>
																</div>
															</li>
															<?php
														endif;
													endforeach;
													if ($n == 0) :
														?><li><span class="title">[ sin datos ]</span></li><?php
													endif;
													?>
												</ul>
												<hr class="mt-0" style="border:0;">
												<?php
											endforeach;
											?>
										</div>
										<!-- FIN Detalle por Campaña x Usuarios -->

									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN Bloque Datos 2 -->

				</div>
			</div>
			<!-- /.conainer-fluid -->
		</main>

  </div>

	<?php $conectDB->closeConnection(); ?>

	<?php include 'php/footer.php'; ?>

	<script src="library/pace/pace.min.js"></script>
	<script src="library/chart/Chart.min.js"></script>
	<!--<script src="js/views/main.js"></script>-->

	<script>
		//convert Hex to RGBA
		function convertHex(hex,opacity){
			hex = hex.replace('#','');
			var r = parseInt(hex.substring(0,2), 16);
			var g = parseInt(hex.substring(2,4), 16);
			var b = parseInt(hex.substring(4,6), 16);
			var result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
			return result;
		}
		//Random Numbers
		function random(min,max) {
			return Math.floor(Math.random()*(max-min+1)+min);
		}
		
		// Carga Datos
		var datosArray = new Array();
		var lasFechas = new Array();
		var fechaIni = '';
		var fechaFin = '<?php echo $fechaFin; ?>';
		var totalMax = <?php echo $clientesTotal; ?>;
		var maxDay = 0;
		function cargaDatosAsync(num) {
			$.ajax({
				url	 : 'adminAsync.php',
				type : 'POST',
				data : {
					tabla	: 'clientes',
					pag		: num
				}
			}).done(function(txt) {
				
				// Si carga Datos
				if (txt.substr(0,5) != 'Error' && txt != 'FIN') {
					
					// Guarda Datos
					var datosA = txt.split('<xxx>');
					for (var a = 0; a < datosA.length; a++) {
						var datosB = datosA[a].split('<xx>');
						var newDato = new Array();
						for (var b = 0; b < datosB.length; b++) {
							var datosC = datosB[b].split('<x>');
							if (datosC[0] == 'fecha' || datosC[0] == 'email') {
								newDato[ datosC[0] ] = datosC[1];
								if (datosC[0] == 'fecha' && fechaIni == '') {
									fechaIni = datosC[1].substr(0,10);
								}
							}
						}
						datosArray.push(newDato);
					}
					
					// Muestra Porcentaje
					var porciento = Math.round( (datosArray.length / totalMax) * 100 );
					$('#carga').html(' [ Carga Datos : '+ porciento +'% ] ');
					
					// Reinicia Ciclo
					setTimeout(function() {
						cargaDatosAsync(num + 1);
					}, 500);
					
				// Termina de cargar datos
				} else if (txt == 'FIN') {
					$('#carga').html(' [ DATOS CARGADOS ] ');
					lasFechas = calculaFechas(fechaIni, fechaFin);
					cuentaDatos();

				// Error
				} else {
					alert(txt);
				}
			});
		}
		
		
		// Cuenta Datos
		function cuentaDatos() {
			
			// Crea Array para Datos del Grafico
			var datosGraf = new Array();
			for (var f = 0; f < lasFechas.length; f++) {
				datosGraf[ lasFechas[f] ] = 0;
			}
			
			// Lee los datos cargados previamente
			for (var s = 0; s < datosArray.length; s++) {
				
				// Filtros para Gráfico
				var elFecha = datosArray[s]['fecha'].substr(0,10);
				datosGraf[ elFecha ]++;
				if (datosGraf[ elFecha ] > maxDay) {
					maxDay = datosGraf[ elFecha ];
				}
			}
			
			// Limpia los Datos
			var datosFinGraf = new Array();
			for (key in datosGraf) {
				datosFinGraf.push(datosGraf[key]);
			}
			crearGrafico(datosFinGraf);
		}
		
		
		// Calcula set de fechas
		function calculaFechas(fechaIni, fechaFin) {
			var d1 = new Date(fechaIni)*1 + 1*24*3600*1000;
			var d2 = new Date(fechaFin)*1 + 2*24*3600*1000;
			var dates = new Array();
			
			var oneDay = 24*3600*1000;
			for (var ms = d1*1,last = d2*1; ms < last; ms += oneDay) {
				var fechaTemp = new Date(ms);
				var dia = fechaTemp.getDate();
				if (dia < 10) dia = '0'+ dia;
				var mes = fechaTemp.getMonth() + 1;
				if (mes < 10) mes = '0'+ mes;
				var year = fechaTemp.getFullYear();
				dates.push(year +'-'+ mes +'-'+ dia);
			}
			return dates;
		}
		
		// Crrea Grafico
		function crearGrafico(datos) {
			var grafiData = {
				labels	: lasFechas,
				datasets: [
					{
						label						: 'Clientes',
						backgroundColor				: convertHex($.brandInfo, 10),
						borderColor					: $.brandInfo,
						pointHoverBackgroundColor	: '#fff',
						borderWidth					: 2,
						data						: datos
					}/*,
					{
						label: 'My Second dataset',
						backgroundColor: 'transparent',
						borderColor: $.brandSuccess,
						pointHoverBackgroundColor: '#fff',
						borderWidth: 2,
						data: data2
					},
					{
						label: 'My Third dataset',
						backgroundColor: 'transparent',
						borderColor: $.brandDanger,
						pointHoverBackgroundColor: '#fff',
						borderWidth: 1,
						borderDash: [8, 5],
						data: data3
					}*/
				]
			};
		
			var grafiOpcion = {
				maintainAspectRatio	: false,
				legend				: { display : false },
				scales				: {
					xAxes : [{
						gridLines: {
							drawOnChartArea : false,
						}
					}],
					yAxes : [{
						ticks: {
							beginAtZero		: true,
							maxTicksLimit	: 1,
							stepSize		: Math.round(maxDay/5),
							max				: maxDay
						}
					}]
				},
				elements : {
					point : {
						radius				: 0,
						hitRadius			: 10,
						hoverRadius			: 4,
						hoverBorderWidth	: 3
					}
				}
			};
			var ctx = $('#main-chart');
			var mainChart = new Chart(ctx, {
				type	: 'line',
				data	: grafiData,
				options	: grafiOpcion
			});
		}


		// Grafico
		$(document).ready(function(e) {
			cargaDatosAsync(1);
			
		});
	</script>

</body>
</html>