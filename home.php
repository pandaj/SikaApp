<?php
include 'php__header.php';


// Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// Nombre Regiones
$regionNames = array(
	"metropolitana"	=> 'Región Metropolitana',
	"region1"		=> 'I Región',
	"region2"		=> 'II Región',
	"region3"		=> 'III Región',
	"region4"		=> 'IV Región',
	"region5"		=> 'V Región',
	"region6"		=> 'VI Región',
	"region7"		=> 'VII Región',
	"region8"		=> 'VIII Región',
	"region9"		=> 'IX Región',
	"region10"		=> 'X Región',
	"region11"		=> 'XI Región',
	"region12"		=> 'XII Región',
	"region14"		=> 'XIV Región',
	"region15"		=> 'XV Región',
	"fuera"			=> 'Fuera de Chile'
);


// Nombres Accesos
$accesoNames = array(
	1	=> 'Solo Ingresa Datos',
	3	=> 'Ingresa Datos + Ver Registros',
	5	=> 'Ingresa Datos + Registros + Campañas',
	7	=> 'Control Total',
	9	=> 'ADMIN MASTER'
);


// Fecha actual
$fechaFin = date("Y-m-d");


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Clientes del Usuario
$tablaClien = new Zend_Db_Table('clientes');
$queryClien = $tablaClien->select()->where('user_id = ?', $user['id']);
$clientes = $tablaClien->fetchAll($queryClien);
$clientesTotal = count($clientes);


// Busca Lugares
$tablaLugar = new Zend_Db_Table('lugares');
function datosLugar($id) {
	global $tablaLugar;
	$queryLugar = $tablaLugar->select()->where('lugar_id = ?', $id);
	$lugarData = $tablaLugar->fetchRow($queryLugar);
	return $lugarData;
}


// Carga Campañas
$tablaCampa = new Zend_Db_Table('campas');
function datosCampa($id) {
	global $tablaCampa;
	$queryCampa = $tablaCampa->select()->where('campa_id = ?', $id);
	$campaData = $tablaCampa->fetchRow($queryCampa);
	return $campaData;
}


// Conteos de lugares, campañas y regiones
$lugaresID = array();
$lugaresData = array();
$campasID = array();
$campasData = array();
$regionID = array();
$regionData = array();
foreach ($clientes as $cliente) :

	// Lugares
	$lugarID = intval($cliente['campa_id']);
	if (in_array($lugarID, $lugaresID) ) :
		$lugaresData[ $lugarID ]['num']++;
	else :
		$lugaresID[] = $lugarID;
		$lugaresData[ $lugarID ] = array(
			'num'	=> 1,
			'data'	=> datosLugar($lugarID)
		);
	endif;

	// Campañas
	$campaID = intval($cliente['campa_id']);
	if (in_array($campaID, $campasID) ) :
		$campasData[ $campaID ]['num']++;
	else :
		$campasID[] = $campaID;
		$campasData[ $campaID ] = array(
			'num'	=> 1,
			'data'	=> datosCampa($campaID)
		);
	endif;

	// Regiones
	$regID = $cliente['region'];
	if (in_array($regID, $regionID) ) :
		$regionData[ $regID ]['num']++;
	else :
		$regionID[] = $regID;
		$regionData[ $regID ] = array(
			'num'		=> 1,
			'nombre'	=> $regionNames[$regID]
		);
	endif;
endforeach;
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
				<li class="breadcrumb-item active">Mis Estadisticas</li>
			</ol>

			<!-- Contenedor -->
			<div class="container-fluid">
				<div class="animated fadeIn">

					<!-- Datos Globales -->
					<div class="row">
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-primary">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponePuntos($clientesTotal); ?></h4>
									<p>Clientes Registrados</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-success">
								<div class="card-body pb-0">
									<h4 class="mb-0" id="campa-all"><?php echo $accesoNames[ $user['acceso'] ]; ?></h4>
									<p>Nivel de Acceso <?php echo $user['acceso']; ?></p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-warning">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponepuntos( count($campasData) ); ?></h4>
									<p>Campañas Participadas</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="card text-white bg-danger">
								<div class="card-body pb-0">
									<h4 class="mb-0"><?php echo ponepuntos( count($lugaresData) ); ?></h4>
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
									<div class="small text-muted">[ <?php echo ponePuntos($clientesTotal); ?> ]</div>
								</div>
							</div>
							<div class="chart-wrapper" style="height:300px;margin-top:40px;">
								<canvas id="main-chart" class="chart" height="300"></canvas>
							</div>
						</div>
					</div>
					<!-- FIN Grafico -->

					<!-- -->
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									Detalle de Estadisticas
								</div>
								<div class="card-body">
									<div class="row">

										<!-- Detalle por Campaña -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-warning">
														<small class="text-muted">CAMPAÑAS</small>
														<br>
														<strong class="h4"><?php echo ponepuntos( count($campasData) ); ?></strong>
														<div class="chart-wrapper">
															<canvas id="sparkline-chart-3" width="100" height="30"></canvas>
														</div>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($campasData as $campa) :
													$elNum = $campa['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													$activa = ' <span class="badge badge-success">ACTIVA</span>';
													if ($campa['data']['activa'] == 0) :
														$activa = ' <span class="badge badge-danger">INACTIVA</span>';
													endif;
													?>
													<li>
														<i class="icon-globe"></i>
														<span class="title"><?php echo $campa['data']['nombre'].$activa; ?></span>
														<span class="value"><?php echo $elNum; ?>
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
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Campaña -->

										<!-- Detalle por Region -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-warning">
														<small class="text-muted">CLIENTES POR REGIÓN DONDE VIVEN</small>
														<br>
														<strong class="h4"><?php echo ponePuntos($clientesTotal); ?> <small>clientes</small></strong>
														<div class="chart-wrapper">
															<canvas id="sparkline-chart-3" width="100" height="30"></canvas>
														</div>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($regionData as $region) :
													$elNum = $region['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													?>
													<li>
														<i class="icon-globe"></i>
														<span class="title"><?php echo $region['nombre']; ?></span>
														<span class="value"><?php echo $elNum; ?>
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
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Region -->

										<!-- Detalle por Lugar -->
										<div class="col-sm-12 col-lg-4">
											<div class="row">
												<div class="col-sm-6">
													<div class="callout callout-warning">
														<small class="text-muted">LUGARES DE CAMPAÑA</small>
														<br>
														<strong class="h4"><?php echo ponepuntos( count($lugaresData) ); ?></strong>
														<div class="chart-wrapper">
															<canvas id="sparkline-chart-3" width="100" height="30"></canvas>
														</div>
													</div>
												</div>
											</div>
											<hr class="mt-0">
											<ul class="horizontal-bars type-2">
												<?php 
												foreach ($lugaresData as $lugar) :
													$elNum = $lugar['num'];
													$elPor = round(100 * ($elNum / $clientesTotal) );
													?>
													<li>
														<i class="icon-globe"></i>
														<span class="title">
															<?php echo $lugar['data']['nombre']; ?> | 
															<small><?php echo $lugar['data']['ciudad'].', '.$regionNames[ $lugar['data']['region'] ]; ?></small>
														</span>
														<span class="value"><?php echo $elNum; ?>
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
												endforeach;
												?>
											</ul>
										</div>
										<!-- FIN Detalle por Region -->

									</div>
								</div>
							</div>
						</div>
						<!--/.col-->
					</div>
					<!--/.row-->
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
		var tempArray;
		
		function cargaDatosAsync(num) {
			<?php
			$n = 0;
			echo 'fechaIni = "'.substr($clientes[0]['fecha'], 0, 10).'"; ';
			foreach ($clientes as $cliente) :
				echo 'tempArray = new Array(); ';
				echo 'tempArray["email"] = "'.$cliente['email'].'"; ';
				echo 'tempArray["fecha"] = "'.$cliente['fecha'].'"; ';
				echo 'datosArray.push(tempArray); ';
			endforeach;
			?>
			lasFechas = calculaFechas(fechaIni, fechaFin);
			cuentaDatos();
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