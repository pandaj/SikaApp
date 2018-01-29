<?php
include 'php__header.php';


// Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// No tienes permiso
if ($user['acceso'] < 3) die('<script> alert("No tienes permiso para ingresar a esta página."); top.location.href = "home.php"; </script>');


// Regiones
$regiones = array(
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


// Campos
$campos = array(
	'ID',
	'Nombre',
	'Apellido',
	'Email',
	'Teléfono',
	'Empresa',
	'Actividad',
	'Región',
	'Ciudad',
	'Campaña',
	'Registrado Por',
	'Lugar Registro',
	'Fecha Registro'
);


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Clientes
$tablaClien = new Zend_Db_Table('clientes');
$queryClien = $tablaClien->select();
$clientes = $tablaClien->fetchAll($queryClien);
$clientesTotal = count($clientes);


// Carga Usuarios
$tablaUsers = new Zend_Db_Table('users');
$queryUsers = $tablaUsers->select();
$usuarios = $tablaUsers->fetchAll($queryUsers);


// Carga Lugares
$tablaLugar = new Zend_Db_Table('lugares');
$queryLugar = $tablaLugar->select();
$lugares = $tablaLugar->fetchAll($queryLugar);


// Carga Campañas
$tablaCampa = new Zend_Db_Table('campas');
$queryCampa = $tablaCampa->select();
$campas = $tablaCampa->fetchAll($queryCampa);


// Cierra Conexion DB
$conectDB->closeConnection();
$configDB = NULL;
$conectDB = NULL;


// Datos Lugares
$lugarNames = array();
foreach ($lugares as $lugar) :
	$lugarNames[ $lugar['lugar_id'] ] = array(
		'nombre'	=> $lugar['nombre']
	);
endforeach;
$tablaLugar = NULL;
$queryLugar = NULL;
$lugares = NULL;


// Datos Campañas
$campaNames = array();
foreach ($campas as $campa) :
	$campaNames[ $campa['campa_id'] ] = array(
		'campa_id'	=> $campa['campa_id'],
		'nombre'	=> $campa['nombre'],
		'clientes'	=> array( $campos )
	);
endforeach;
$tablaCampa = NULL;
$queryCampa = NULL;
$campas = NULL;


// Datos Usuarios
$usersNames = array();
foreach ($usuarios as $usuario) :
	$usersNames[ $usuario['user_id'] ] = array(
		'nombre'	=> $usuario['nombre']
	);
endforeach;
$tablaUsers = NULL;
$queryUsers = NULL;
$usuarios = NULL;


// Datos Clientes
foreach ($clientes as $cliente) :
	$campaID = $cliente['campa_id'];
	$campaNames[ $campaID ]['clientes'][] = array(
		$cliente['cliente_id'],
		str_replace("'", "´", $cliente['nombre']),
		str_replace("'", "´", $cliente['apellido']),
		$cliente['email'],
		$cliente['fono'],
		str_replace("'", "´", $cliente['empresa']),
		str_replace("'", "´", $cliente['actividad']),
		$regiones[ $cliente['region'] ],
		str_replace("'", "´", $cliente['ciudad']),
		str_replace("'", "´", $campaNames[ $cliente['campa_id'] ]['nombre']),
		str_replace("'", "´", $usersNames[ $cliente['user_id'] ]['nombre']),
		str_replace("'", "´", $lugarNames[ $cliente['lugar_id'] ]['nombre']),
		$cliente['fecha']
	);
endforeach;
$tablaClien = NULL;
$queryClien = NULL;
$clientes = NULL;
?>
<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<style>
	.nav-tabs .nav-item.active {
		background:#ffc107;
	}
	.nav-tabs .nav-item.active a {
		color:#FFF;
	}
</style>

	<?php include 'php/header.php'; ?>

	<!-- Contenido -->
	<div class="app-body">

		<?php include 'php/menu.php'; ?>

		<!-- Main content -->
		<main class="main">

			<!-- Breadcrumb -->
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Home</li>
				<li class="breadcrumb-item">Datos Detallados</li>
				<li class="breadcrumb-item active">Clientes Por Campaña</li>
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
					</div>
					<!-- FIN Datos Globales -->

					<!-- Datos -->
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									Datos de los clientes
								</div>
								<div class="card-body" id="tabla-datos">

									<!-- Menu Tab -->
									<ul class="nav nav-tabs" role="tablist">
										<?php
										$n = 0;
										foreach ($campaNames as $campa) :
											if (count($campa['clientes']) > 1) :
												$n++;
												$activo = '';
												if ($n == 1) $activo = ' active';
												$elName = 'campa'.$campa['campa_id'];
												?>
												<li class="nav-item<?php echo $activo; ?>">
													<a class="nav-link" data-toggle="tab" href="#<?php echo $elName; ?>" role="tab" aria-controls="<?php echo $elName; ?>">
														<?php echo $campa['nombre']; ?>
													</a>
												</li>
												<?php
											endif;
										endforeach;
										?>
									</ul>
									<!-- FIN Menu Tab -->

									<!-- Tablas -->
									<div class="tab-content">
										<?php
										$n = 0;
										foreach ($campaNames as $campa) :
											if (count($campa['clientes']) > 1) :
												$n++;
												$activo = '';
												if ($n == 1) $activo = ' active';
												$elName = 'campa'.$campa['campa_id'];
												?>
												<div class="tab-pane<?php echo $activo; ?>" id="<?php echo $elName; ?>" role="tabpanel">
                                                	<div class="card-header">
														Campaña : <b><?php echo $campa['nombre']; ?></b>
													</div>
													<form class="card-body" style="padding-bottom:0;" method="post" action="adminExcel.php">
														<input type="hidden" name="datos" value='<?php echo json_encode($campa['clientes']); ?>' />
														<input type="hidden" name="nombre" value='<?php echo $elName; ?>' />
														<button type="button" class="btn btn-danger btn-lg" onClick="$('#<?php echo $elName; ?> form').submit();">Exportar datos para EXCEL</button>
													</form>
													<table class="table table-responsive-sm table-striped">
														<?php
														$n = 0;
														foreach ($campa['clientes'] as $cliente) :
															$n++;
															if ($n == 1) :
																?>
																<thead><tr>
																	<?php foreach ($cliente as $dato) : ?>
																		<th><?php echo $dato; ?></th>
																	<?php endforeach; ?>
																</tr></thead>
																<?php
															else :
																?>
																<tbody><tr>
																	<?php foreach ($cliente as $dato) : ?>
																		<td><?php echo $dato; ?></td>
																	<?php endforeach; ?>
																</tr></tbody>
																<?php
															endif;
														endforeach;
														?>
													</table>
												</div>
												<?php
											endif;
										endforeach;
										?>
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

	<?php include 'php/footer.php'; ?>

	<script src="library/pace/pace.min.js"></script>
	<script src="library/chart/Chart.min.js"></script>
	<!--<script src="js/views/main.js"></script>-->

</body>
</html>