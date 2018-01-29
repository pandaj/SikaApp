<?php
include 'php__header.php';


//Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Datos Campañas
$tablaCampa = new Zend_Db_Table('campas');
$queryCampa = $tablaCampa->select();
$datosCampa = $tablaCampa->fetchAll($queryCampa);


// Carga Datos Usuarios
$tablaUsers = new Zend_Db_Table('users');
$queryUsers = $tablaUsers->select();
$datosUsers = $tablaUsers->fetchAll($queryUsers);


// Obtiene Creador por ID
function obtieneNombre($id) {
	global $datosUsers;
	$nombre = '';
	foreach ($datosUsers as $user) :
		if ($user['user_id'] == $id) $nombre = $user['nombre'];
	endforeach;
	return $nombre;
}


// Total Clientes
$clienTotal = cuentaDatos($conectDB, 'clientes');
$conectDB->closeConnection();
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
				<li class="breadcrumb-item">Administración</li>
				<li class="breadcrumb-item active">Admin. Campañas</li>
			</ol>

			<!-- Contenedor -->
			<div class="container-fluid">
				<div class="animated fadeIn">
					<div class="row">
						<div class="col-md-12">

							<!--- Box Usuarios -->
							<div class="card">
								<div class="card-header">
									Campañas Creadas
								</div>
								<div class="alert alert-danger" style="display:none;">
									<strong>...</strong>
								</div>
								<form class="card-body" style="padding-bottom:0;" id="form_nuevo" method="post" action="edit_campa_registro.php">
									<input type="hidden" name="id" value="0" />
									<input type="hidden" name="tipo" value="nuevo" />
									<button type="button" class="btn btn-warning btn-lg" onClick="$('#form_nuevo').submit();">+ Nueva Campaña</button>
								</form>
								<div class="card-body">
									<table class="table table-responsive-sm table-hover table-outline mb-0">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Nombre</th>
												<th>Estado</th>
												<th>Creada Por</th>
												<th>Clientes Registrados</th>
												<th>Fecha Creación</th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($datosCampa as $campa) :
												$total = 10;
												$activa = ' <span class="badge badge-success">ACTIVA</span>';
												if ($campa['activa'] == 0) :
													$activa = ' <span class="badge badge-danger">INACTIVA</span>';
												endif;
												?>
												<tr>
													<td><div><?php echo $campa['campa_id']; ?></div></td>
													<td><div><?php echo $campa['nombre']; ?></div></td>
													<td><div><?php echo $activa; ?></div></td>
													<td><div id="nombre-<?php echo $campa['campa_id']; ?>"><?php echo obtieneNombre($campa['creador']); ?></div></td>
													<td class="barra-cliente" id="data-<?php echo $campa['campa_id']; ?>">
														<div class="clearfix">
															<div class="float-left">
																<strong>...</strong>
															</div>
														</div>
														<div class="progress progress-xs progress-black">
															<!-- Aqui va la barra -->
														</div>
													</td>
													<td><div><?php echo $campa['fecha']; ?></div></td>
													<td>
														<form id="form_campa-<?php echo $campa['campa_id']; ?>" method="post" action="edit_campa_registro.php">
															<input type="hidden" name="id" value="<?php echo $campa['campa_id']; ?>" />
															<input type="hidden" name="tipo" value="edita" />
															<button type="button" class="btn btn-warning" onClick="$('#form_campa-<?php echo $campa['campa_id']; ?>').submit();">Editar Campaña</button> 
															<button type="button" style="display:none;" class="btn btn-danger" onClick="borraCampa(<?php echo $campa['campa_id']; ?>);">Borrar Campaña</button>
														</form>
													</td>
												</tr>
												<?php
											endforeach;
											?>
										</tbody>
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

	<script>
		$(document).ready(function(e) {
			var n = 0;
			$('.barra-cliente').each(function() {
				n++;
				var elID = $(this).attr('id');
				elID = elID.replace('data-', '');
				elID = parseInt(elID);
				setTimeout(function() {
					buscaDatos(elID);
				}, 500 * n);
			});
        });
		
		function buscaDatos(id) {
			$.ajax({
				url : 'adminQuerys.php',
				type: 'POST',
				data: {
					tabla	: 'clientes',
					tipo	: 'campa_clientes',
					dato	: id
				}
			}).done(function(txt) {
				if ( !isNaN(txt) ) {
					cargaBarraDatos(id, parseInt(txt) );
				} else {
					alert(txt);
				}
			});
		}
	
		var clienTotal = <?php echo $clienTotal; ?>;
		function cargaBarraDatos(id, num) {
			var div = '#data-'+ id;
			var porciento = Math.round( (num / clienTotal) * 100);
			$(div +' .float-left strong').html(num +' clientes | '+ porciento +'%');
			$(div +' .progress').html('<div class="progress-bar bg-danger" role="progressbar" style="width:'+ porciento +'%" aria-valuenow="'+ num +'" aria-valuemin="0" aria-valuemax="'+ clienTotal +'"></div>');
			if (num == 0) {
				$('#form_campa-'+ id +' .btn-danger').show();
			}
		}
		
		
		var activaTodo = true;
		function borraCampa(elID) {
			if (activaTodo) {
				var confirma = confirm("¿ Estás seguro de querer eliminar para siempre a "+ $('#nombre-'+ elID).html() +" ?");
				
				if (confirma == true) {
					activaTodo = false;
					$.ajax({
						url : 'adminBorraCampa.php',
						type: 'POST',
						data: {
							id : elID
						}
					}).done(function(txt) {
						if (txt == 'Exito') {
							$('.card .alert').removeClass('alert-danger');
							$('.card .alert').addClass('alert-success');
							$('.card .alert strong').html('Campaña eliminada correctamente.');
							$('.card .alert').fadeIn(300);
							setTimeout(function() {
								$('.card .alert').fadeOut(300);
							}, 2300);
							setTimeout(function() {
								top.location.href = 'edit_campa.php';
							}, 2600);
						} else {
							activaTodo = true;
							$('.card .alert strong').html(txt);
							$('.card .alert').fadeIn(300);
						}
					});
				}
			}
		}
	</script>

</body>
</html>