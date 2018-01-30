<?php
include 'php__header.php';


//Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// Carga Base de Datos
$configDB = require_once 'config/db.php';
$conectDB = Zend_Db::factory('Mysqli', $configDB);
Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);


// Carga Datos Usuarios
$tablaUsers = new Zend_Db_Table('users');
$queryUsers = $tablaUsers->select();
$datosUsers = $tablaUsers->fetchAll($queryUsers);


// Total Clientes
$clienTotal = cuentaDatos($conectDB, 'clientes');
$conectDB->closeConnection();


// Obtiene Creadores
$creaNames = array();
foreach ($datosUsers as $elUser) :
	$creaNames[ $elUser['user_id'] ] = $elUser['nombre'];
endforeach;


// Niveles de Acceso
$accesoNames = array(
	1	=> 'Solo Ingresa Datos',
	3	=> 'Ingresa Datos + Ver Registros',
	5	=> 'Ingresa Datos + Registros + Campañas',
	7	=> 'Control Total',
	9	=> 'ADMIN MASTER'
);
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
				<li class="breadcrumb-item active">Admin. Usuarios</li>
			</ol>

			<!-- Contenedor -->
			<div class="container-fluid">
				<div class="animated fadeIn">
					<div class="row">
						<div class="col-md-12">

							<!--- Box Usuarios -->
							<div class="card">
								<div class="card-header">
									Usuarios Promotores <?php echo count($datosUsers); ?>
								</div>
								<div class="alert alert-danger" style="display:none;">
									<strong>...</strong>
								</div>
								<form class="card-body" style="padding-bottom:0;" id="form_nuevo" method="post" action="edit_user_registro.php">
									<input type="hidden" name="id" value="0" />
									<input type="hidden" name="tipo" value="nuevo" />
									<button type="button" class="btn btn-warning btn-lg" onClick="$('#form_nuevo').submit();">+ Nuevo Usuario</button>
								</form>
								<div class="card-body">
									<table class="table table-responsive-sm table-hover table-outline mb-0">
										<thead class="thead-light">
											<tr>
												<th class="text-center"><i class="icon-people"></i></th>
												<th>Nombre</th>
												<th>Email</th>
												<th>Clientes Registrados</th>
												<th>Tipo de Usuario</th>
												<th>Fecha Creación</th>
												<th>Último Acceso</th>
												<th>Último Ingreso de Datos</th>
												<th>Creado Por</th>
												<th> </th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($datosUsers as $elUser) :
												?>
												<tr>
													<td class="text-center">
														<div class="avatar">
															<img src="img/avatares/<?php echo $elUser['foto']; ?>" class="img-avatar">
														</div>
													</td>
													<td><div id="nombre-<?php echo $elUser['user_id']; ?>"><?php echo $elUser['nombre']; ?></div></td>
													<td><div><?php echo $elUser['email']; ?></div></td>
													<td class="barra-cliente" id="data-<?php echo $elUser['user_id']; ?>">
														<div class="clearfix">
															<div class="float-left">
																<strong>...</strong>
															</div>
														</div>
														<div class="progress progress-xs progress-black">
															<!-- Aqui va la barra -->
														</div>
													</td>
													<td><div><?php echo $accesoNames[ $elUser['acceso'] ]; ?></div></td>
													<td><div><?php echo $elUser['fecha']; ?></div></td>
													<td><div><?php echo $elUser['fecha_login']; ?></div></td>
													<td><div><?php echo $elUser['fecha_add']; ?></div></td>
													<?php 
													if (intval($elUser['user_id']) > 1) :
														?>
														<td><div><?php echo $creaNames[ $elUser['creador'] ]; ?></div></td>
														<td>
															<form id="form_user-<?php echo $elUser['user_id']; ?>" method="post" action="edit_user_registro.php">
																<input type="hidden" name="id" value="<?php echo $elUser['user_id']; ?>" />
																<input type="hidden" name="tipo" value="edita" />
																<button type="button" class="btn btn-warning" onClick="$('#form_user-<?php echo $elUser['user_id']; ?>').submit();">Editar Usuario</button> | 
                                                                <button type="button" class="btn btn-danger" onClick="borrarUsuario(<?php echo $elUser['user_id']; ?>);">Borrar Usuario</button>
															</form>
														</td>
													<?php else : ?>
														<td>...</td>
														<td></td>
													<?php endif; ?>
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
					tipo	: 'users_clientes',
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
		}
		
		var activaTodo = true;
		function borrarUsuario(id) {
			if (activaTodo) {
				var confirma = confirm("¿ Estás seguro de querer eliminar para siempre a "+ $('#nombre-'+ id).html() +" ?");
				
				if (confirma == true) {
					activaTodo = false;
					$.ajax({
						url : 'adminQuerys.php',
						type: 'POST',
						data: {
							tabla	: 'clientes',
							tipo	: 'cuenta_clientes',
							dato	: id
						}
					}).done(function(txt) {
						if (txt == 'Exito') {
							$('.card .alert').removeClass('alert-danger');
							$('.card .alert').addClass('alert-success');
							$('.card .alert strong').html('Usuario eliminado correctamente.');
							$('.card .alert').fadeIn(300);
							setTimeout(function() {
								$('.card .alert').fadeOut(300);
							}, 2300);
							setTimeout(function() {
								top.location.href = 'edit_users.php';
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