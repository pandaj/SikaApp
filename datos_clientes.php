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
	'Campañas',
	'Registradores',
	'Lugares Registro',
	'Fecha 1er Registro'
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


// Datos Campañas
$campaNames = array();
foreach ($campas as $campa) :
	$campaNames[ $campa['campa_id'] ] = $campa['nombre'];
endforeach;
$tablaCampa = NULL;
$queryCampa = NULL;
$campas = NULL;


// Datos Usuarios
$usersNames = array();
foreach ($usuarios as $usuario) :
	$usersNames[ $usuario['user_id'] ] = $usuario['nombre'];
endforeach;
$tablaUsers = NULL;
$queryUsers = NULL;
$usuarios = NULL;


// Datos Lugares
$lugarNames = array();
foreach ($lugares as $lugar) :
	$lugarNames[ $lugar['lugar_id'] ] = $lugar['nombre'];
endforeach;
$tablaLugar = NULL;
$queryLugar = NULL;
$lugares = NULL;


// Datos Clientes
$unicoMail = array();
$unicoClientes = array();
foreach ($clientes as $cliente) :
	$email = $cliente['email'];

	// Si es dato nuevo
	if (!in_array($email, $unicoMail) ) :
		$unicoMail[] = $cliente['email'];
		$unicoClientes[] = array(
			$cliente['cliente_id'],
			str_replace("'", "´", $cliente['nombre']),
			str_replace("'", "´", $cliente['apellido']),
			$cliente['email'],
			$cliente['fono'],
			
			str_replace("'", "´", $cliente['empresa']),
			str_replace("'", "´", $cliente['actividad']),
			$regiones[ $cliente['region'] ],
			str_replace("'", "´", $cliente['ciudad']),
			array( $cliente['campa_id'] ), //9
			
			array( $cliente['user_id'] ), //10
			array( $cliente['lugar_id'] ), //11
			$cliente['fecha']
		);

	// Si ya existe
	else :
		$pos = array_search($email, $unicoMail);

		// Nueva Campa ID
		if (!in_array($cliente['campa_id'], $unicoClientes[ $pos ][9]) ) :
			$unicoClientes[ $pos ][9][] = $cliente['campa_id'];
		endif;
		
		// Nuevo User ID
		if (!in_array($cliente['user_id'], $unicoClientes[ $pos ][10]) ) :
			$unicoClientes[ $pos ][10][] = $cliente['user_id'];
		endif;

		// Nuevo Lugar ID
		if (!in_array($cliente['lugar_id'], $unicoClientes[ $pos ][11]) ) :
			$unicoClientes[ $pos ][11][] = $cliente['lugar_id'];
		endif;
	endif;
endforeach;
$tablaClien = NULL;
$queryClien = NULL;
$clientes = NULL;
$unicoMail = NULL;


// Reemplaza IDs por Nombres
foreach ($unicoClientes as $keyA => $cliente) :

	// Remplaza Campañas
	foreach ($cliente[9] as $keyC => $campa) :
		$unicoClientes[ $keyA ][9][ $keyC ] = $campaNames[ $campa ];
	endforeach;
	$unicoClientes[ $keyA ][9] = implode(', ', $unicoClientes[ $keyA ][9]);
	
	// Reemplaza Usuarios
	foreach ($cliente[10] as $keyU => $usero) :
		$unicoClientes[ $keyA ][10][ $keyU ] = $usersNames[ $usero ];
	endforeach;
	$unicoClientes[ $keyA ][10] = implode(', ', $unicoClientes[ $keyA ][10]);
	
	// Reemplaza Lugares
	foreach ($cliente[11] as $keyL => $lugar) :
		$unicoClientes[ $keyA ][11][ $keyL ] = $lugarNames[ $lugar ];
	endforeach;
	$unicoClientes[ $keyA ][11] = implode(', ', $unicoClientes[ $keyA ][11]);
	
endforeach;


$clienteCampos = array( $campos );
$unicoClientes = array_merge($clienteCampos, $unicoClientes);
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
				<li class="breadcrumb-item">Home</li>
				<li class="breadcrumb-item">Datos Detallados</li>
				<li class="breadcrumb-item active">Clientes Únicos</li>
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
									<h4 class="mb-0"><?php echo ponePuntos( count($unicoClientes) - 1 ); ?></h4>
									<p>Clientes Únicos</p>
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
									<form class="card-body" style="padding-bottom:0;" method="post" action="adminExcel.php">
										<input type="hidden" name="datos" value='<?php echo json_encode($unicoClientes); ?>' />
										<input type="hidden" name="nombre" value='clientes_unicos' />
										<button type="button" class="btn btn-danger btn-lg" onClick="$('#tabla-datos form').submit();">Exportar datos para EXCEL</button>
									</form>
									<table class="table table-responsive-sm table-striped">
										<?php
										$n = 0;
										foreach ($unicoClientes as $cliente) :
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