<?php 
	session_start();
	$pageTitle = 'Show items';
	include "init.php";
	
	$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
	
	// check if the user is in the DB
	$stmt = $con->prepare("SELECT 
								items.*, 
								categories.Name AS CatName,
								users.Username
							 FROM 
							 	items
							 INNER JOIN
							 	categories
							 ON
							 	categories.ID = items.CatID
							 INNER JOIN
							 	users
							 ON
							 	users.UserID = items.MemberID 
							 WHERE 
							 	items.ID = ?
							 AND 
							 	Approve = 1");
	$stmt->execute(array($itemid));

	if($stmt->rowCount()) {

		$item = $stmt->fetch();
?>
	<h1 class='text-center'><?php echo $item['Name']; ?></h1>

	<div class="container">
		<div class='row'>
			<div class='col-md-3'>
				<img class='img-responsive img-thumbnail center-block' src='layout/images/avatar.jpg' alt='Proudct image' />
			</div>
			<div class='col-md-9 item-info'>
				<h2><?php echo $item['Name'] ?></h2>
				<p><?php echo $item['Description'] ?></p>
				<ul class='list-unstyled'>
					<li>
						<i class='fa fa-calendar fa-fw'></i>
						<span>Date</span>: <?php echo $item['Date'] ?>
					</li>
					<li>
						<i class='fa fa-money fa-fw'></i>
						<span>Price</span>: <?php echo $item['Price'] ?>
					</li>
					<li>
						<i class='fa fa-building fa-fw'></i>
						<span>Made in</span>: <?php echo $item['MadeIn'] ?>
					</li>
					<li>
						<i class='fa fa-tags fa-fw'></i>
						<span>Category</span>: <a href='categories.php?id=<?php echo $item['CatID'] ?>'><?php echo $item['CatName'] ?></a>
					</li>
					<li>
						<i class='fa fa-user fa-fw'></i>
						<span>Publisher</span>: <a href='#'><?php echo $item['Username'] ?></a>
					</li>
					<li>
						<i class='fa fa-user fa-fw'></i>
						<span>Tags</span>: 
						<?php 
							$allTags = explode(",", $item['Tags']);
							foreach($allTags as $tag) {
								$tag = str_replace(' ', '', $tag);
								$tagLink = strtolower($tag);
								if(!empty($tag)){
									echo "<a class='tag-name' href='tags.php?name={$tagLink}'>" . $tag . "</a>";
								}
							}
						?>
					</li>
				</ul>
			</div>
		</div>
		<hr class='custom-hr'>
		<?php  if($session != '') { ?>
			<!-- start add comment -->
			<div class='row'>
				<div class='col-md-offset-3'>
					<div class='add-comment'>
						<h3>Add your comment</h3>
						<form method='POST' action='<?php echo $_SERVER['PHP_SELF'] . "?itemid=" . $item['ID'] ?>'>
							<textarea class='form-control' name='comment' required></textarea>
							<input class='btn btn-primary' type="submit" value="Comment" />
						</form>
						<?php 
							if($_SERVER['REQUEST_METHOD'] == 'POST') {
								$comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
								$userid = $item['MemberID'];
								$itemid = $item['ID'];

								if(!empty($comment)) {
									$stmt = $con->prepare("INSERT INTO 
															comments(Comment, Status, Date, ItemID, UserID)
														   VALUES 
														   	(:comment, 0, now(), :itemid, :userid)");
									$stmt->execute(array(
										':comment' => $comment,
										':itemid' => $itemid,
										':userid' => $_SESSION['NormalUserID'],
									));

									if($stmt) {
										echo "<div class='alert alert-success'>Comment added <strong>successfully</strong>!</div>";
									}
								}
							}
						?>
					</div>
				</div>
			</div>
			<!-- end add comment -->
		<?php } else {
			echo "<a href='login.php'>Login</a> or <a href='login.php'>register</a> to add comment";
		} ?>
		<hr class='custom-hr'>
		<?php 
					// get all comments from DB
					$stmt = $con->prepare("SELECT 
												c.*, u.Username AS Username
											FROM
												comments AS c, users AS u
											WHERE 
												c.UserID = u.UserID
											AND 
												c.ItemID = ?
											AND 
												c.Status = 1");
					$stmt->execute(array($itemid));
					$table = $stmt->fetchAll();

				foreach($table as $comment) { ?>
					<div class='comment-box'>
						<div class='row'>
							<div class='col-sm-2 text-center'>
								<img class='img-responsive img-thumbnail img-circle center-block' src='layout/images/avatar.jpg' alt='Proudct image' />
								<?php echo $comment['Username'] ?>
							</div> 
							<div class='col-sm-10'>
								<p class='lead'><?php echo $comment['Comment'] ?></p>
							</div>
						</div>
					</div>
					<hr class='custom-hr'>
			<?php } ?>
			
	</div>
<?php
	} else {
		echo "<div class='container'><div class='alert alert-danger'>There's no such id or waiting for approval!</div></div>";
	}
	include $tpl . "footer.php"; 
?>