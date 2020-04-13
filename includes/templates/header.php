<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo getTitle(); ?></title>
		<link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css" />
		<link rel="stylesheet" href="<?php echo $css; ?>font-awesome.min.css" />
		<link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css" />
		<link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css" />
		<link rel="stylesheet" href="<?php echo $css; ?>frontend.css" />
	</head>
	<body>
		<div class='upper-bar'>
			<div class='container'>
				<?php
					if($_SESSION['NormalUser'] != ''){ ?>

						<img class='img-circle' src='layout/images/avatar.jpg' alt='Proudct image' />
						<div class='btn-group my-info'>
							<span class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
								<?php echo $session; ?>
								<span class='caret'></span>
							</span>
							<ul class='dropdown-menu'>
								<li><a href='profile.php'>My profile</a></li>
								<li><a href='newAd.php'>New item</a></li>
								<li><a href='profile.php#my-ads'>My items</a></li>
								<li><a href='logout.php'>Logout</a></li>
							</ul>
						</div>
				<?php 
					}
				?>
			</div>
		</div>
		<nav class="navbar navbar-inverse">
		  <div class="container">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="index.php">Home</a>
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="app-nav">
		      <ul class="nav navbar-nav navbar-right">
		      	<?php
			        foreach(getRecords("*", "categories", "WHERE Parent = 0", '', "ID") as $cat) {
						echo "<li>
								<a href='categories.php?id=" . $cat['ID'] . "'>
								 " . $cat['Name'] . "
								</a>
							</li>";
					}
				?>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>