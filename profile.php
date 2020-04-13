<?php 
	session_start();
	$pageTitle = 'Profile';
	include "init.php";
	if($session != '') {
		$getUser = $con->prepare('SELECT * FROM users WHERE Username = ?');
		$getUser->execute(array($session));
		$info = $getUser->fetch();
		$userid = $info['UserID'];
?>
	<h1 class='text-center'><?php echo $_SESSION['NormalUser'] ?> Profile</h1>

	<div class='information block'>
		<div class='container'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
					My information
				</div>
				<div class='panel-body'>
					<ul class="list-unstyled">
						<li>
							<i class='fa fa-unlock fa-fw'></i>
							<span>Name</span>: <?php echo $info['Username'] ?>
						</li>
						<li>
							<i class='fa fa-envelope-o fa-fw'></i>
							<span>Email</span>: <?php echo $info['Email'] ?>
						</li>
						<li>
							<i class='fa fa-user fa-fw'></i>
							<span>Full name</span>: <?php echo $info['FullName'] ?>
						</li>
						<li>
							<i class='fa fa-calendar fa-fw'></i>
							<span>Register date</span>: <?php echo $info['Date'] ?>
						</li>
						<li>
							<i class='fa fa-tags fa-fw'></i>
							<span>Favorite category</span>:
						</li>
					</ul>
					<a href="#" class='btn btn-default my-btn'>Edit info</a>
				</div>
			</div>
		</div>
	</div>
	<div id='my-ads' class='my-ads block'>
		<div class='container'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>My ads</div>
				<div class='panel-body'>
					<?php 
						$items = getRecords('*', 'items', "WHERE MemberID = $userid", "", "ID");
						if(!empty($items)){
							echo "<div class='row'>";
								$approve = '';
								foreach($items as $item) {
									if($item['Approve'] == 1){
										$approve = 'Waiting for approval';
									}
									echo "<div class='col-sm-6 col-md-3'>
											<div class='thumbnail item-box'>";
											if($item['Approve'] == 0){
												echo "<span class='approve-status'>Waiting for approval</span>";
											}
											echo "<span class='price-tag'>$" . $item['Price'] . " </span>
												<img class='img-responsive' src='layout/images/avatar.jpg' alt='Proudct image' />
												<div class='caption'>
													<h3><a href='items.php?itemid=" . $item['ID'] . "'>" . $item['Name'] . "</a></h3>
													<p> " . $item['Description'] . " </p>
													<div class='date'> " . $item['Date'] . " </div>
												</div>
											</div>
										  </div>";
								}
							echo "</div>";
						} else {
							echo "There's no ads to show, Create <a href='newAd.php'>new ad</a>";
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class='my-comments block'>
		<div class='container'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>My comments</div>
				<div class='panel-body'>
					<?php 
						$comments = getRecords("Comment", "comments", "WHERE UserID = $userid", "", "ID");

						if(!empty($comments)) {
							foreach($comments as $comment) {
								echo "<p> " . $comment['Comment'] . "</p>";
							}
						} else {
							echo "There's no comments to show!";
						}
					?>
				</div>
			</div>
		</div>
	</div>
<?php
	} else {
		header('Location: login.php');
		exit();
	}
	include $tpl . "footer.php"; 
?>