<?php 
	session_start();

	$pageTitle = 'Members';
	if(isset($_SESSION['Username'])){
		include 'init.php';
		isset($_GET['do']) ? $do = $_GET['do'] : $do = 'manage';
		if($do == 'manage') { // manage page 
			// get all users from DB
			$stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1");
			$stmt->execute();
			$table = $stmt->fetchAll();
				echo "<h1 class='text-center'>Manage member</h1>
						<div class='container'>";
			if(!empty($table)){
				?>
					<div class="table-responsive">
						<table class="main-table manage-members text-center table table-bordered">
							<tr>
								<td>#ID</td>
								<td>Avatar</td>
								<td>Username</td>
								<td>Email</td>
								<td>Full name</td>
								<td>Registered date</td>
								<td>Control</td>
							</tr>
							<?php 
								foreach($table as $row) {
									echo "
										<tr>
											<td>" . $row['UserID'] . "</td>
											<td>";
											if(empty($row['Avatar'])) {
												$av = 'layout/images/avatar.jpg';
											} else {
												$av = 'uploads/avatars/' . $row['Avatar'];
											}
											echo "<img src='" . $av . "' alt='User image' /></td>
											<td>" . $row['Username'] . "</td>
											<td>" . $row['Email'] . "</td>
											<td>" . $row['FullName'] . "</td>
											<td>" . $row['Date'] . "</td>
											<td>
												<a href='?do=edit&userid=" . $row['UserID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
												<a href='?do=delete&userid=" . $row['UserID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a> ";
											if($row['RegStatus'] == 0) {
												echo "<a href='?do=activate&userid=" . $row['UserID'] . "' class='btn btn-info'><i class='fa fa-check'></i> Activate</a>";
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
					echo "<a href='members.php?do=add' class='btn btn-primary'><i class='fa fa-plus'></i> Add new member</a>";
				echo "</div>";
		} elseif($do == 'add') { // add page ?>
				<h1 class="text-center">Add new member</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=insert" method="POST" enctype="multipart/form-data">
						<!-- start username field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Username</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="username" class="form-control" autocomplete="off" placeholder="Username" required/>
							</div>
						</div>
						<!-- end username field -->
						<!-- start Password field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Password</label>
							<div class="col-sm-10 col-md-4">
								<input type="password" name="password" class="form-control" autocomplete="new_password" placeholder="Password" required />
								<i class="show-pass fa fa-eye fa-2x"></i>
							</div>
						</div>
						<!-- end Password field -->
						<!-- start Email field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-10 col-md-4">
								<input type="email" name="email" class="form-control" placeholder="Email" required/>
							</div>
						</div>
						<!-- end Email field -->
						<!-- start Full name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Full name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="full" class="form-control" placeholder="Full name" required/>
							</div>
						</div>
						<!-- end Full name field -->
						<!-- start Avatar field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Profile image</label>
							<div class="col-sm-10 col-md-4">
								<input type="file" name="avatar" class="form-control" required/>
							</div>
						</div>
						<!-- end Avatar field -->
						<!-- start submit field -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Add" class="btn btn-primary btn-lg" />
							</div>
						</div>
						<!-- end submit field -->
					</form>
				</div>
		<?php 
		} elseif($do == 'insert') { // insert page

			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				echo "<h1 class='text-center'>Add new member</h1>";
				echo "<div class='container'>";

				// upload variable

				$avatar = $_FILES['avatar'];

				$avatarName = $_FILES['avatar']['name'];
				$avatarSize = $_FILES['avatar']['size'];
				$avatarTmp = $_FILES['avatar']['tmp_name'];
				$avatarType = $_FILES['avatar']['type'];

				$avatarAllowedExtensions = array("jpeg", "jpg", "png", "gif");

				$nameArray = explode('.', $avatarName);
				$avatarExtension = strtolower(end($nameArray));

				// from variables

				$user = $_POST['username'];
				$pass = $_POST['password'];
				$email = $_POST['email'];
				$name = $_POST['full'];

				$hashedPass = sha1($pass);

				// validate form
				$formErrors = array();

				if(empty($user)) {
					$formErrors[] = "Username can't be <strong>empty</strong>!";
				} elseif(strlen($user) < 4) {
					$formErrors[] = "Username can't be less than <strong>4 characters</strong>!";
				} elseif(strlen($user) > 20) {
					$formErrors[] = "Username can't be more than <strong>20 characters</strong>!";
				}

				if(empty($pass)) {
					$formErrors[] = "Password can't be <strong>empty</strong>!";
				}

				if(empty($email)) {
					$formErrors[] = "Email can't be <strong>empty</strong>!";
				}	

				if(empty($name)) {
					$formErrors[] = "FullName can't be <strong>empty</strong>!";
				}

				if(!empty($avatarName) && !in_array($avatarExtension, $avatarAllowedExtensions)) {
					$formErrors[] = "This extension is not <strong>allowed</strong>!";
				}

				if(empty($avatarName)) {
					$formErrors[] = "You should upload an <strong>image</strong>!";
				}

				if($avatarSize > 4194304) {
					$formErrors[] = "Image can't be larger than <strong>4 megabytes</strong>!";
				}

				if(empty($formErrors)){

					$dbAvatar = rand(0, 10000000000) . '_' . $avatarName;

					move_uploaded_file($avatarTmp, "uploads\avatars\\" . $dbAvatar);

					// check if the user is already in DB
					if(checkExistance('Username', 'users', $user)) {
						redirectHome("<div class='alert alert-danger'>Username used before!</div>", 5);
					}

					// Insert new user in DB
					
					$stmt = $con->prepare("INSERT INTO 
											users(Username, Password, Email, FullName, RegStatus, Date, Avatar)
										   VALUES(:user, :pass, :email, :name, 1, now(), :avatar)");
					$stmt->execute(array(
						':user' => $user,
						':pass' => $hashedPass,
						':email' => $email,
						':name' => $name,
						':avatar' => $dbAvatar,
					));

					// echo success msg

					redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record inserted </div>", 'back', 5);
				} else {
					foreach($formErrors as $error){
						echo "<div class='alert alert-danger'>" . $error . "</div>";
					}
				}
			} else {
				 echo "<div class='container'>";
				 redirectHome('<div class="alert alert-danger"> sorry you are not allowed to view this page!</div>', null, 5);
				 echo "</div>";
			}
			echo "</div>";

		} elseif($do == 'edit') { // edit page

			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
			
			// check if the user is in the DB
			$stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
			$stmt->execute(array($userid));
			$row = $stmt->fetch();
			$count = $stmt->rowCount();

			if($count > 0){ ?>

				<h1 class="text-center">Edit member</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=update" method="POST">
						<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
						<!-- start username field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Username</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="username" value="<?php echo $row['Username']; ?>" class="form-control" autocomplete="off" required/>
							</div>
						</div>
						<!-- end username field -->
						<!-- start Password field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Password</label>
							<div class="col-sm-10 col-md-4">
								<input type="hidden" name="oldpassword" value="<?php echo $row['Password']; ?>" />
								<input type="password" name="newpassword" class="form-control" autocomplete="new_password" />
							</div>
						</div>
						<!-- end Password field -->
						<!-- start Email field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-10 col-md-4">
								<input type="email" name="email" value="<?php echo $row['Email']; ?>" class="form-control" required/>
							</div>
						</div>
						<!-- end Email field -->
						<!-- start Full name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Full name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="full" value="<?php echo $row['FullName']; ?>" class="form-control" required/>
							</div>
						</div>
						<!-- end Full name field -->
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
			echo "<h1 class='text-center'>Update member</h1>";
			echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$id = $_POST['userid'];
				$user = $_POST['username'];
				$pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);
				$email = $_POST['email'];
				$name = $_POST['full'];

				// validate form
				$formErrors = array();

				if(empty($user)) {
					$formErrors[] = "Username can't be <strong>empty</strong>!";
				} elseif(strlen($user) < 4) {
					$formErrors[] = "Username can't be less than <strong>4 characters</strong>!";
				} elseif(strlen($user) > 20) {
					$formErrors[] = "Username can't be more than <strong>20 characters</strong>!";
				}

				if(empty($email)) {
					$formErrors[] = "Email can't be <strong>empty</strong>!";
				}	

				if(empty($name)) {
					$formErrors[] = "FullName can't be <strong>empty</strong>!";
				}

				if(empty($formErrors)){
					$stmt2 = $con->prepare('SELECT Username FROM users WHERE Username = ? AND UserID != ?');
					$stmt2->execute(array($user, $id));
					if($stmt2->rowCount() > 0) {
						redirectHome("<div class='alert alert-danger'>This username is <strong>used before</strong>!</div>", 'back', 5);
					} else {
						// update DB with this info
						$stmt = $con->prepare("UPDATE users SET Username = ?, Password = ?, Email = ?, FullName = ? WHERE UserID = ?");
						$stmt->execute(array($user, $pass, $email, $name, $id));

						// echo success msg
						
						redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>", 'back', 5);
					}
				} else {
					foreach($formErrors as $error){
						echo "<div class='alert alert-danger'>" . $error . "</div>";
					}
				}
			} else {
				echo "<div class='container'>";
				redirectHome("<div class'alert alert-danger'>sorry you are not allowed to view this page!</div>", null, 5);
				echo "</div>";
			}
			echo "</div>";
		} elseif($do == 'delete') {// delete page
			echo "<h1 class='text-center'>Delete member</h1>";
			echo "<div class='container'>";

			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
			
			// check if the user is in the DB
			$count = checkExistance('UserID', 'users', $userid);

			if($count > 0){

				$stmt = $con->prepare("DELETE FROM users WHERE UserID = :userid");
				$stmt->bindParam(":userid", $userid);
				$stmt->execute();
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record deleted </div>", 'back', 5);
			} else {
				
				redirectHome("<div class='alert alert-danger'>this id is not exist.</div>");
			}
			echo "</div>";
		} elseif($do == 'activate') { // activate member
			echo "<h1 class='text-center'>Activate member</h1>";
			echo "<div class='container'>";

			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
			
			// check if the user is in the DB
			$count = checkExistance('UserID', 'users', $userid);

			if($count > 0){

				$stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = :userid");
				$stmt->bindParam(":userid", $userid);
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