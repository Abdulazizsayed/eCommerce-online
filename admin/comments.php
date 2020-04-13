<?php 
	session_start();

	$pageTitle = 'Comments';
	if(isset($_SESSION['Username'])){
		include 'init.php';
		isset($_GET['do']) ? $do = $_GET['do'] : $do = 'manage';
		if($do == 'manage') { // manage page 
			// get all users from DB
			$stmt = $con->prepare("SELECT 
										c.*, i.Name AS ItemName, u.Username AS Username
									FROM
										comments AS c, items AS i, users AS u
									WHERE 
										c.ItemID = i.ID AND c.UserID = u.UserID");
			$stmt->execute();
			$table = $stmt->fetchAll();
			echo "<h1 class='text-center'>Manage comments</h1>
				  <div class='container'>";

			if(!empty($table)){
			?>
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>#ID</td>
							<td>Comment</td>
							<td>Item name</td>
							<td>Username</td>
							<td>Date</td>
							<td>Control</td>
						</tr>
						<?php 
							foreach($table as $row) {
								echo "
									<tr>
										<td>" . $row['ID'] . "</td>
										<td>" . $row['Comment'] . "</td>
										<td>" . $row['ItemName'] . "</td>
										<td>" . $row['Username'] . "</td>
										<td>" . $row['Date'] . "</td>
										<td>
											<a href='?do=edit&commentid=" . $row['ID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
											<a href='?do=delete&commentid=" . $row['ID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a> ";
										if($row['Status'] == 0) {
											echo "<a href='?do=approve&commentid=" . $row['ID'] . "' class='btn btn-info'><i class='fa fa-check'></i> Approve</a>";
										}
								echo "	</td>
									</tr>
								";
							}
						?>
					</table>
			</div>
		<?php 
			} else {
				echo "<div class='alert alert-info'> There's no records to show!</div>";
			}
			echo "</div>"; 
		} elseif($do == 'edit') { // edit page

			$commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
			
			// check if the user is in the DB
			$stmt = $con->prepare("SELECT * FROM comments WHERE ID = ?");
			$stmt->execute(array($commentid));
			$row = $stmt->fetch();
			$count = $stmt->rowCount();

			if($count > 0){ ?>

				<h1 class="text-center">Edit comment</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=update" method="POST">
						<input type="hidden" name="commentid" value="<?php echo $commentid; ?>" />
						<!-- start comment field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Comment</label>
							<div class="col-sm-10 col-md-4">
								<textarea class="form-control" name="comment"><?php echo $row['Comment']; ?></textarea>
							</div>
						</div>
						<!-- end comment field -->
						<!-- start submit field -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save" class="btn btn-primary btn-lg" />
							</div>
						</div>
						<!-- end submit field -->
					</form>
				</div>
			<?php 
			} else {
				echo "<div class='container'>";
				redirectHome("<div class='alert alert-danger'>there's no such id</div>", null, 5);
				echo "</div>";
			}
		} elseif($do == "update") { // update page
			echo "<h1 class='text-center'>Update comment</h1>";
			echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$id = $_POST['commentid'];
				$comment = $_POST['comment'];
				
				// update DB with this info
				$stmt = $con->prepare("UPDATE comments SET Comment = ? WHERE ID = ?");
				$stmt->execute(array($comment, $id));

				// echo success msg
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>", 'back', 5);
			} else {
				echo "<div class='container'>";
				redirectHome("<div class'alert alert-danger'>Sorry you are not allowed to view this page!</div>", null, 5);
				echo "</div>";
			}
			echo "</div>";
		} elseif($do == 'delete') {// delete page
			echo "<h1 class='text-center'>Delete comment</h1>";
			echo "<div class='container'>";

			$commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
			
			// check if the user is in the DB
			$count = checkExistance('ID', 'comments', $commentid);

			if($count > 0){

				$stmt = $con->prepare("DELETE FROM comments WHERE ID = :commentid");
				$stmt->bindParam(":commentid", $commentid);
				$stmt->execute();
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record deleted </div>", 'back', 5);
			} else {
				
				redirectHome("<div class='alert alert-danger'>this id is not exist.</div>");
			}
			echo "</div>";
		} elseif($do == 'approve') { // approve member
			echo "<h1 class='text-center'>Approve member</h1>";
			echo "<div class='container'>";

			$commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
			
			// check if the user is in the DB
			$count = checkExistance('ID', 'comments', $commentid);

			if($count > 0){

				$stmt = $con->prepare("UPDATE comments SET Status = 1 WHERE ID = :commentid");
				$stmt->bindParam(":commentid", $commentid);
				$stmt->execute();
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " member activated </div>", 'back', 5);
			} else {
				
				redirectHome("<div class='alert alert-danger'>this id is not exist.</div>");
			}
			echo "</div>";
		}
		include $tpl . 'footer.php';
	} else {
		header('Location: index.php');
		exit();
	}