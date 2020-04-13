<?php 
	session_start();

	if(isset($_SESSION['Username'])){
		$pageTitle = 'Dashborad';
		include 'init.php';

		// start dashboard page

		$latestUsers = getLatest('*', 'users', 'UserID', 5);
		$latestItems = getLatest('*', 'items', 'ID', 5);

		?>

		<div class="home-stats">
			<div class="container text-center">
				<h1>Dashboard</h1>
				<div class="row">
					<div class="col-md-3">
						<div class="stat members">
							<i class='fa fa-users'></i>
							<div class="info">
								Total Members
								<span><a href='members.php'><?php echo countItems('UserID', 'users'); ?></a></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="stat pending">
							<i class="fa fa-user-plus"></i>
							<div class="info">
								Pending Members
								<span><a href='members.php'><?php echo checkExistance('RegStatus', 'users', '0'); ?></a></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="stat items">
							<i class="fa fa-tag"></i>
							<div class="info">
								Total Items
								<span><a href='items.php'><?php echo countItems('ID', 'items'); ?></a></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="stat comments">
							<i class="fa fa-comments"></i>
							<div class="info">
								Total Comments
								<span><a href='comments.php'><?php echo countItems('ID', 'comments'); ?></a></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="latest">
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-users"></i> Latest registered users
								<span class="toggle-info pull-right">
									<i class="fa fa-plus fa-lg"></i>
								</span>
							</div>
							<div class="panel-body">
								<ul class='list-unstyled latest-users'>
									<?php
										if(!empty($latestUsers)){
											foreach($latestUsers as $lUser){
												if($lUser['GroupID'] == 1) continue;
												echo "<li>"
													.$lUser['Username'] . "
													<a href='members.php?do=activate&userid= " . $lUser['UserID'] . "'>
														";
														if($lUser['RegStatus'] == 0) {
															echo "<a href='members.php?do=activate&userid=" . $lUser['UserID'] . "' class='btn btn-info pull-right'><i class='fa fa-check'></i> Activate</a>";
														}
														 echo "
													</a>
												</li>";
											}
										} else {
											echo "There's no records to show!";
										}
									?>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-tag"></i> Latest Items
								<span class="toggle-info pull-right">
									<i class="fa fa-plus fa-lg"></i>
								</span>
							</div>
							<div class="panel-body">
								<ul class='list-unstyled latest-items'>
									<?php
										if(!empty($latestItems)){
											foreach($latestItems as $lItems){
												echo "<li>"
													.$lItems['Name'] . "
													<a href='items.php?do=approve&itemid= " . $lItems['ID'] . "'>
														";
														if($lItems['Approve'] == 0) {
															echo "<a href='items.php?do=approve&itemid=" . $lItems['ID'] . "' class='btn btn-info pull-right'><i class='fa fa-check'></i> Approve</a>";
														}
														 echo "
													</a>
												</li>";
											}
										} else {
											echo "There's no records to show!";
										}
									?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<!-- Start latest comments -->
				<div class="row">
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-comment-o"></i> Latest comments
								<span class="toggle-info pull-right">
									<i class="fa fa-plus fa-lg"></i>
								</span>
							</div>
							<div class="panel-body">
								<?php 
									$stmt = $con->prepare("SELECT 
											c.*, u.Username AS Username
										FROM
											comments AS c
										INNER JOIN
											users AS u
										ON 
											c.UserID = u.UserID
										ORDER BY 
											c.ID DESC ");
									$stmt->execute();
									$table = $stmt->fetchAll();

									if(!empty($table)){
										foreach($table as $row) {
											echo "<div class='comment-box'>";
												echo "<span class='comment-owner text-center'><a href='members.php?do=edit&userid=" . $row['UserID'] . "'>" . $row['Username'] . "</a></span>";
												echo "<p class='comment'>" . $row['Comment'] . "</p>";
											echo "</div>";
										}
									} else {
										echo "There's no records to show!";
									}
								?>
							</div>
						</div>
					</div>
				</div>
				<!-- End latest comments -->
			</div>
		</div>

		<?php

		// end dashboard page

		include $tpl . 'footer.php';
	} else {
		header('Location: index.php');
		exit();
	}


?>