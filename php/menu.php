<?php global $user; ?>

		<!-- Menu Lateral -->
		<div class="sidebar">
			<nav class="sidebar-nav">
				<ul class="nav">
					<li class="nav-item">
						<a id="btn-1" class="nav-link" href="home.php">
							<i class="icon-star"></i> Mis Estadisticas
						</a>
					</li>
					<li class="nav-item">
						<a id="btn-2" class="nav-link" href="registro.php">
							<i class="icon-plus"></i> Registrar Cliente
						</a>
					</li>
					<?php if ($user['acceso'] >= 3) : ?>
						<li class="nav-item">
							<a id="btn-3" class="nav-link" href="estadisticas.php">
								<i class="icon-chart"></i> Estadisticas Generales
							</a>
						</li>
					<?php endif; ?>
					<?php if ($user['acceso'] >= 3) : ?>
						<li class="nav-title">
							Datos Detallados
						</li>
						<li class="nav-item">
							<a id="btn-4" class="nav-link" href="datos_clientes.php">
								<i class="icon-list"></i> Clientes Únicos
							</a>
						</li>
						<li class="nav-item">
							<a id="btn-5" class="nav-link" href="datos_clientes_cmp.php">
								<i class="icon-list"></i> Clientes por Campaña
							</a>
						</li>
						<li class="nav-item">
							<a id="btn-6" class="nav-link" href="datos_clientes_lgr.php">
								<i class="icon-list"></i> Clientes por Lugar
							</a>
						</li>
						<li class="nav-item">
							<a id="btn-7" class="nav-link" href="datos_clientes_usr.php">
								<i class="icon-list"></i> Clientes por Usuario
							</a>
						</li>
					<?php endif; ?>
					<?php if ($user['acceso'] >= 5) : ?>
						<li class="nav-title">
							Administración
						</li>
						<?php if ($user['acceso'] >= 7) : ?>
							<li class="nav-item">
								<a id="btn-8" class="nav-link" href="edit_users.php">
									<i class="icon-people"></i> Admin. Usuarios
								</a>
							</li>
						<?php endif; ?>
						<li class="nav-item">
							<a id="btn-9" class="nav-link" href="edit_campa.php">
								<i class="icon-settings"></i> Admin. Campañas
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</nav>
			<button class="sidebar-minimizer brand-minimizer" type="button"></button>
		</div>
		<!-- FIN Menu Lateral -->