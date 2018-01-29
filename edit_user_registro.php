<?php
include 'php__header.php';


//Valida Login
global $login,$get,$extra,$user;
if (!$login) devuelveHome();


// No tienes permiso
if (intval($user['acceso']) < 7) :
	die('<script> alert("No tienes permiso para ingresar a esta página."); top.location.href = "index.php"; </script>');
endif;


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
	'user_id'	=> 0,
	'nombre'	=> '',
	'email'		=> '',
	'password'	=> '',
	'foto'		=> '',
	'acceso'	=> ''
);
if ($mensaje == 'Exito') :
	if ($post['tipo'] == 'edita' && intval($post['id']) > 1) :

		// Carga Base de Datos
		$configDB = require_once 'config/db.php';
		$conectDB = Zend_Db::factory('Mysqli', $configDB);
		Zend_Db_Table_Abstract::setDefaultAdapter($conectDB);
		
		// Busca Usuario
		$tablaUsers = new Zend_Db_Table('users');
		$queryUsers = $tablaUsers->select()->where('user_id = ?', $post['id']);
		$datosUsers = $tablaUsers->fetchRow($queryUsers);
		if (isset($datosUsers['user_id']) ) :
			$edita = true;
		else :
			$mensaje = 'Ha ocurrido un problema con el usuario a editar.';
		endif;
		
		$conectDB->closeConnection();
	endif;
endif;


// Muestra Errores
if ($mensaje != 'Exito') :
	die('<script> alert("'.$mensaje.'"); top.location.href = "edit_users.php"; </script>');
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
						<input type="hidden" name="id" value="<?php echo $datosUsers['user_id']; ?>" />
						<h1>
							<?php 
							if ($edita)	echo 'Edita Usuario #'.$post['id'];
							else		echo 'Crear Nuevo Usuario';
							?>
						</h1>
						<p class="text-muted">
							<?php
							if ($edita)	echo 'Modifica los datos del usuario.';
							else		echo 'Ingresa los datos del usuario.';
							?>
						</p>
						<div class="alert alert-danger" style="display:none;">
							<strong>...</strong>
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<input type="text" class="form-control" name="nombre" placeholder="Nombre" maxlength="100" value="<?php echo $datosUsers['nombre']; ?>" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon">@</span>
							<input type="email" class="form-control" name="email" placeholder="Email" maxlength="100" value="<?php echo $datosUsers['email']; ?>" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-lock"></i></span>
							<input type="password" class="form-control" name="password" placeholder="Contraseña" maxlength="40" value="<?php echo $datosUsers['password']; ?>" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-lock"></i></span>
							<input type="password" class="form-control" name="password2" placeholder="Repite Contraseña" maxlength="40" value="<?php echo $datosUsers['password']; ?>" />
						</div>
						<div class="input-group mb-3">
							<span class="input-group-addon"><i class="icon-star"></i></span>
							<select class="form-control" name="acceso">
								<option value="">[ Nivel de Acceso ]</option>
								<option value="1">Solo Ingresa Datos</option>
								<option value="3">Ingresa Datos + Ver Registros</option>
								<option value="5">Ingresar + Registros + Crear Campañas</option>
								<option value="7">Control Total</option>
							</select>
						</div>
						<p class="text-muted">
							Avatar del Usuario
						</p>
						<div class="input-group mb-3 btnfotos">
							<span class="input-group-addon"><i class="icon-symbol-female"></i></span>
							<input type="hidden" class="form-control" name="foto" value="<?php echo $datosUsers['foto']; ?>"  />
							<?php
							$n = 0;
							while ($n < 12) :
								$n++;
								if ($n < 10)	$foto = '00'.$n;
								else			$foto = '0'.$n;
								echo'<button id="btnfoto-'.$foto.'" type="button" class="btn btn-lg" onClick="cambiaFoto('."'".$foto."'".');" style="width:16%;">'.
										'<img src="img/avatares/'.$foto.'.png" width="100%" />'.
									'</button>';
								if ($n == 6) echo '</div><div class="input-group mb-3 btnfotos"><span class="input-group-addon"><i class="icon-symbol-male"></i></span>';
							endwhile;
							?>
						</div>

						<button type="button" class="btn btn-block btn-danger" onClick="actualizaUsuario();">Guardar Usuario</button>
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
		$(document).ready(function(e) {
			$('#btn-8').addClass('active');
			<?php
			if ($edita) :
				$foto = str_replace('.png', '', $datosUsers['foto']);
				?>
				$('select[name=acceso]').val(<?php echo $datosUsers['acceso']; ?>);
				$('select[name=acceso]').trigger('change');
				$('input[name=foto]').val('<?php echo $datosUsers['foto']; ?>');
				$('#btnfoto-<?php echo $foto; ?>').addClass('btn-warning');
				<?php 
			endif;
			?>
		});
		
		
		// Elige Foto
		function cambiaFoto(indica) {
			$('.btnfotos button').removeClass('btn-warning');
			$('#btnfoto-'+ indica).addClass('btn-warning');
			$('input[name=foto]').val(indica +'.png');
		}
		
		
		// Alertas
		function alertas(txt) {
			$('#formbox-1 .alert strong').html(txt);
			$('#formbox-1 .alert').fadeIn(300);
		}
		
		
		// Guarda Usuario
		var activaTodo = true;
		function actualizaUsuario() {
			if (activaTodo) {
				if ( $('input[name=password]').val().length > 5 && $('input[name=password2]').val().length > 5 && $('input[name=password]').val() != $('input[name=password2]').val() ) {
					alertas('Repetir Contraseña no es igual a la primera contraseña ingresada.');
				} else {
					var losDatos = new Array(
						{
							input		: $('input[name=id]'),
							tipo		: '',
							minText		: 1,
							aleCompleta	: 'Error #001. Ha ocurrido un error con los datos del usuario. Por favor, regrese a la página anterior.'
						},{
							input		: $('input[name=nombre]'),
							tipo		: 'nombre',
							minText		: 2,
							aleCompleta	: 'Debes ingresar el nombre del usuario.',
							aleValida	: 'Debes ingresar un nombre válido, sin símbolos extraños ni números.'
						},{
							input		: $('input[name=email]'),
							tipo		: 'email',
							minText		: 5,
							aleCompleta	: 'Debes ingresar el email del usuario.',
							aleValida	: 'Debes ingresar un email válido, sin tildes, espacios ni símbolos extraños.'
						},{
							input		: $('input[name=password]'),
							tipo		: '',
							minText		: 5,
							aleCompleta	: 'Debes ingresar una contraseña.'
						},{
							input		: $('input[name=password2]'),
							tipo		: '',
							minText		: 5,
							aleCompleta	: 'Debes repetir la contraseña.'
						},{
							input		: $('select[name=acceso]'),
							tipo		: '',
							minText		: 1,
							aleCompleta	: 'Debes elegir el nivel de acceso que tendrá el usuario.'
						},{
							input		: $('input[name=foto]'),
							tipo		: '',
							minText		: 2,
							aleCompleta	: 'Debes elegir la foto de avatar para el usuario.'
						}
					);
					validaJulioform(losDatos, 'adminSaveUser.php', function(txt) {
						if (txt == 'Exito') {
							activaTodo = false;
							$('#formbox-1 .alert strong').html('El usuario ha sido guardado correctamente.');
							if ($('#formbox-1 .alert').hasClass('alert-danger') ) {
								$('#formbox-1 .alert').removeClass('alert-danger');
								$('#formbox-1 .alert').addClass('alert-success');
							}
							$('#formbox-1 .alert').fadeIn(300);
							$('#formbox-1 input').each(function() {
								$(this).val('');
							});
							$('#formbox-1 select').each(function() {
								$(this).val('');
								$(this).trigger('change');
							});
							setTimeout(function() {
								$('#formbox-1 .alert').fadeOut(300);
							}, 2300);
							setTimeout(function() {
								top.location.href = 'edit_users.php';
							}, 2600);
						} else {
							alertas(txt);
						}
					});
				}
			}
		}
	</script>

</body>
</html>