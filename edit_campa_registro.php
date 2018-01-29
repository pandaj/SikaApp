<?php
include 'php__header.php';


//Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// No tienes permiso
if ($user['acceso'] < 5) die('<script> alert("No tienes permiso para ingresar a esta página."); top.location.href = "home.php"; </script>');


// DATOS POR POST
$request = new Zend_Controller_Request_Http();
$post = $request->getPost();


// Valida Datos Post
$mensaje = 'Exito';
if (!isset($post['tipo']) || !isset($post['id']) ) :
	$mensaje = 'Ha ocurrido un error con los datos ingresados.';
else :
	if (strlen($post['tipo']) < 2 || strlen($post['id']) < 1) :
		$mensaje = 'Ha ocurrido un problema con los datos ingresados.';
	endif;
endif;


// Valida Usuario Editado
$edita = false;
$datosUsers = array(
	'campa_id'	=> 0,
	'nombre'	=> '',
	'activa'	=> 1
);
if ($mensaje == 'Exito') :
	if ($post['tipo'] == 'edita') :

		// Carga Base de Datos
		$configDB = require_once 'config/db.php';
		$conectDB = Zend_Db::factory('Mysqli', $configDB);
		Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);
		
		// Busca Usuario
		$tablaUsers = new Zend_Db_Table('campas');
		$queryUsers = $tablaUsers->select()->where('campa_id = ?', $post['id']);
		$datosUsers = $tablaUsers->fetchRow($queryUsers);
		if (isset($datosUsers['campa_id']) ) :
			$edita = true;
		else :
			$mensaje = 'Ha ocurrido un problema con la campaña a editar.';
		endif;
		
		$conectDB->closeConnection();
	endif;
endif;


// Muestra Errores
if ($mensaje != 'Exito') :
	die('<script> alert("'.$mensaje.'"); top.location.href = "edit_campa.php"; </script>');
endif;
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
					<div id="formbox-1" class="card-footer p-4">
						<input type="hidden" name="id" value="<?php echo $datosUsers['campa_id']; ?>" />
						<h1>
							<?php 
							if ($edita)	echo 'Editar Campaña #'.$post['id'];
							else		echo 'Crear Nueva Campaña';
							?>
						</h1>
						<p class="text-muted">
							<?php
							if ($edita)	echo 'Modifica los datos de la campaña.';
							else		echo 'Ingresa los datos de la campaña.';
							?>
						</p>
						<div class="alert alert-danger" style="display:none;">
							<strong>...</strong>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<input type="text" class="form-control" name="nombre" placeholder="Nombre Campaña" maxlength="100" value="<?php echo $datosUsers['nombre']; ?>" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<select class="form-control" name="activa">
								<option value="1">Campaña Activa</option>
								<option value="0">Campaña Desactivada</option>
							</select>
						</div>

						<button type="button" class="btn btn-block btn-danger" onClick="actualizaUsuario();">Guardar Campaña</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include 'php/footer.php'; ?>

	<!-- Script Propios -->
	<script src="js/validaDatos.js"></script>
	<script src="js/julioForm.js"></script>
	<script>
		$(document).ready(function(e) {
			$('#btn-9').addClass('active');
			<?php
			if ($edita) :
				?>
				$('select[name=activa]').val(<?php echo $datosUsers['activa']; ?>);
				$('select[name=activa]').trigger('change');
				<?php 
			endif;
			?>
		});
		
		// Alertas
		function alertas(txt) {
			$('#formbox-1 .alert strong').html(txt);
			$('#formbox-1 .alert').fadeIn(300);
		}
		
		
		// Guarda Usuario
		var activaTodo = true;
		function actualizaUsuario() {
			if (activaTodo) {
				var losDatos = new Array(
					{
						input		: $('input[name=id]'),
						tipo		: '',
						minText		: 1,
						aleCompleta	: 'Error #001. Ha ocurrido un error con los datos del usuario. Por favor, regrese a la página anterior.'
					},{
						input		: $('input[name=nombre]'),
						tipo		: '',
						minText		: 3,
						aleCompleta	: 'Debes ingresar el nombre de la campaña.'
					},{
						input		: $('select[name=activa]'),
						tipo		: '',
						minText		: 0
					}
				);
				validaJulioform(losDatos, 'adminSaveCampa.php', function(txt) {
					if (txt == 'Exito') {
						activaTodo = false;
						$('#formbox-1 .alert strong').html('La campaña ha sido guardada correctamente.');
						if ($('#formbox-1 .alert').hasClass('alert-danger') ) {
							$('#formbox-1 .alert').removeClass('alert-danger');
							$('#formbox-1 .alert').addClass('alert-success');
						}
						$('#formbox-1 .alert').fadeIn(300);
						$('#formbox-1 input').each(function() {
							$(this).val('');
						});
						setTimeout(function() {
							$('#formbox-1 .alert').fadeOut(300);
						}, 2300);
						setTimeout(function() {
							top.location.href = 'edit_campa.php';
						}, 2600);
					} else {
						alertas(txt);
					}
				});
			}
		}
	</script>

</body>
</html>