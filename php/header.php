<?php global $user; ?>

	<!-- Header -->
	<header class="app-header navbar">
		<button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
			<span class="navbar-toggler-icon"></span>
		</button>
		<a class="navbar-brand" href="dashboard.php">
			<img src="img/logo-sika.png" width="100%" />
		</a>
		<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
			<span class="navbar-toggler-icon"></span>
		</button>
		<ul class="nav navbar-nav ml-auto">
			<li class="nav-item dropdown">
				<div class="nav-link dropdown-toggle nav-link">
					<span><?php echo $user['nombre']; ?></span> 
					<img src="img/avatares/<?php echo $user['foto']; ?>" class="img-avatar" />
				</div>
			</li>
			<li class="nav-item d-md-down-none">
				<a class="nav-link" href="logout.php">
					<i class="fa fa-lock"></i> Logout
				</a>
			</li>
			<li class="nav-item d-md-down-none"></li>
		</ul>
	</header>
	<!-- FIN Header -->