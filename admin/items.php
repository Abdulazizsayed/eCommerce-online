<?php 
	session_start();

	$pageTitle = 'Items';
	if(isset($_SESSION['Username'])){
		include 'init.php';
		isset($_GET['do']) ? $do = $_GET['do'] : $do = 'manage';
		if($do == 'manage') { // manage page 
			// get all users from DB
			$stmt = $con->prepare("SELECT 
										i.ID, i.Name, i.Description, i.Price, i.Date, i.Approve, cat.Name AS catName, u.Username AS username
								   FROM 
								   		items AS i, categories AS cat, users AS u
								   WHERE 
								   		i.CatID = cat.ID
								   AND 
								   		i.MemberID = u.UserID");
			$stmt->execute();
			$table = $stmt->fetchAll();
			echo "<h1 class='text-center'>Manage items</h1>
				  <div class='container'>";

			if(!empty($table)){
			?>
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>#ID</td>
							<td>Name</td>
							<td>Description</td>
							<td>Price</td>
							<td>Date</td>
							<td>Category</td>
							<td>Publisher</td>
							<td>Control</td>
						</tr>
						<?php 
							foreach($table as $row) {
								echo "
									<tr>
										<td>" . $row['ID'] . "</td>
										<td>" . $row['Name'] . "</td>
										<td>" . $row['Description'] . "</td>
										<td>" . $row['Price'] . "</td>
										<td>" . $row['Date'] . "</td>
										<td>" . $row['catName'] . "</td>
										<td>" . $row['username'] . "</td>
										<td>
											<a href='?do=edit&itemid=" . $row['ID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
											<a href='?do=delete&itemid=" . $row['ID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a> ";
										if($row['Approve'] == 0) {
										echo "<a href='?do=approve&itemid=" . $row['ID'] . "' class='btn btn-info'><i class='fa fa-check'></i> Approve</a>";
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
				echo "<a href='?do=add' class='btn btn-primary'><i class='fa fa-plus'></i> Add new item</a>";
			echo "</div>";
		} elseif($do == 'add') { // add page ?>
			<h1 class="text-center">Add new item</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=insert" method="POST">
						<!-- start name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="name" class="form-control" placeholder="Name" required/>
							</div>
						</div>
						<!-- end name field -->
						<!-- start description field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="desc" class="form-control" placeholder="Description" required />
							</div>
						</div>
						<!-- end description field -->
						<!-- start price field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Price</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="price" class="form-control" placeholder="Price" required />
							</div>
						</div>
						<!-- end price field -->
						<!-- start Made in field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Made in</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="madein" class="form-control" placeholder="Country" required />
							</div>
						</div>
						<!-- end made in field -->
						<!-- start category field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Category</label>
							<div class="col-sm-10 col-md-4">
								<select name='category' required>
									<option value="0">...</option>
									<?php
									$cats = selectFromWhere("*", "categories", "Where Parent = 0", "", "ID");
									foreach($cats as $cat){
										echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
										foreach(selectFromWhere("*", "categories", "Where Parent = {$cat['ID']}", "", "ID") as $child) {
											echo "<option value='" . $child['ID'] . "'>----" . $child['Name'] . "</option>";
										}
									}
									?>
								</select>
							</div>
						</div>
						<!-- end category field -->
						<!-- start Users field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Publisher</label>
							<div class="col-sm-10 col-md-4">
								<select name='user' required>
									<option value="0">...</option>
									<?php
									$users = getRecords('users');
									foreach($users as $user){
										if($user['GroupID'] == 1) continue;
										echo "<option value='" . $user['UserID'] . "'>" . $user['Username'] . "</option>";
									}
									?>
								</select>
							</div>
						</div>
						<!-- end Users field -->
						<!-- start satatus field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-10 col-md-4">
								<select name='status' required>
									<option value="0">...</option>
									<option value="1">New</option>
									<option value="2">Like new</option>
									<option value="3">Used</option>
									<option value="4">Old</option>
								</select>
							</div>
						</div>
						<!-- end status field -->
						<!-- start tags field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Tags</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="tags" class="form-control" placeholder="Separate between tags by comma" />
							</div>
						</div>
						<!-- end tags field -->
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
				echo "<h1 class='text-center'>Add new item</h1>";
				echo "<div class='container'>";

				$name = $_POST['name'];
				$desc = $_POST['desc'];
				$price = $_POST['price'];
				$country = $_POST['madein'];
				$catid = $_POST['category'];
				$userid = $_POST['user'];
				$status = $_POST['status'];
				$tags = $_POST['tags'];

				// validate form
				$formErrors = array();

				if(empty($name)) {
					$formErrors[] = "Item name can't be <strong>empty</strong>!";
				} elseif(strlen($name) < 4) {
					$formErrors[] = "Item name can't be less than <strong>4 characters</strong>!";
				} elseif(strlen($name) > 20) {
					$formErrors[] = "Item name can't be more than <strong>20 characters</strong>!";
				}

				if(empty($desc)) {
					$formErrors[] = "Description can't be <strong>empty</strong>!";
				}

				if(empty($price)) {
					$formErrors[] = "Price can't be <strong>empty</strong>!";
				}

				if(empty($country)) {
					$formErrors[] = "Country of manufacture can't be <strong>empty</strong>!";
				}	

				if($catid == 0) {
					$formErrors[] = "You should specify item <strong>category</strong>!";
				}

				if($userid == 0) {
					$formErrors[] = "You should specify item <strong>publisher</strong>!";
				}

				if($status == 0) {
					$formErrors[] = "You should specify item <strong>status</strong>!";
				}

				if(empty($formErrors)){
					// Insert new user in DB
					
					$stmt = $con->prepare("INSERT INTO 
											items(Name, Description, Price, Date, MadeIn, Status, CatID, MemberID, Tags)
										   VALUES(:name, :description, :price, now(), :country, :status, :catid, :memberid, :tags)");
					$stmt->execute(array(
						':name' => $name,
						':description' => $desc,
						':price' => $price,
						':country' => $country,
						':status' => $status,
						':catid' => $catid,
						':memberid' => $userid,
						':tags' => $tags,
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
				 redirectHome('<div class="alert alert-danger"> Sorry you are not allowed to view this page!</div>', null, 5);
				 echo "</div>";
			}
			echo "</div>";

		} elseif($do == 'edit') { // edit page
			$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
			
			// check if the user is in the DB
			$stmt = $con->prepare("SELECT * FROM items WHERE ID = ?");
			$stmt->execute(array($itemid));
			$row = $stmt->fetch();
			$count = $stmt->rowCount();

			if($count > 0){ ?>

				<h1 class="text-center">Edit item</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=update" method="POST">
						<input type="hidden" name="itemid" value="<?php echo $itemid; ?>" />
						<!-- start name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="name" value="<?php echo $row['Name']; ?>" class="form-control" autocomplete="off" required/>
							</div>
						</div>
						<!-- end name field -->
						<!-- start Descrpiton field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="desc" value="<?php echo $row['Description']; ?>" class="form-control" autocomplete="off" required />
							</div>
						</div>
						<!-- end Descrpiton field -->
						<!-- start price field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Price</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="price" value="<?php echo $row['Price']; ?>" class="form-control" required/>
							</div>
						</div>
						<!-- end price field -->
						<!-- start Made in field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Made in</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="country" value="<?php echo $row['MadeIn']; ?>" class="form-control" required/>
							</div>
						</div>
						<!-- end Made in field -->
						<!-- start category field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Category</label>
							<div class="col-sm-10 col-md-4">
								<select name='category' required>
									<option value="0">...</option>
									<?php
									$cats = getRecords('categories');
									foreach($cats as $cat){

										echo "<option value='" . $cat['ID'] . "' ";
										if($cat['ID'] == $row['CatID']) echo 'selected';
										echo ">" . $cat['Name'] . "</option>";
									}
									?>
								</select>
							</div>
						</div>
						<!-- end category field -->
						<!-- start publisher field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Publisher</label>
							<div class="col-sm-10 col-md-4">
								<select name='publisher' required>
									<option value="0">...</option>
									<?php
									$users = getRecords('users');
									foreach($users as $user){

										echo "<option value='" . $user['UserID'] . "' ";
										if($user['UserID'] == $row['MemberID']) echo 'selected';
										echo ">" . $user['Username'] . "</option>";
									}
									?>
								</select>
							</div>
						</div>
						<!-- end publisher field -->
						<!-- start tags field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Tags</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="tags" value="<?php echo $row['Tags']; ?>" class="form-control" placeholder="Separate between tags by comma" />
							</div>
						</div>
						<!-- end tags field -->
						<!-- start submit field -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save" class="btn btn-primary btn-lg" />
							</div>
						</div>
						<!-- end submit field -->
					</form>
					<?php
					// get all users from DB
					$stmt = $con->prepare("SELECT 
												c.*, u.Username AS Username
											FROM
												comments AS c, users AS u
											WHERE 
												c.ItemID = :itemid AND c.UserID = u.UserID");
					$stmt->execute(array(':itemid' => $itemid));
					$table = $stmt->fetchAll();

					if(!empty($table)) {
						?>
						<h1 class="text-center">Manage [ <?php echo $row['Name']; ?> ] comments</h1>
						<div class="table-responsive">
							<table class="main-table text-center table table-bordered">
								<tr>
									<td>Comment</td>
									<td>Username</td>
									<td>Date</td>
									<td>Control</td>
								</tr>
								<?php 
									foreach($table as $row) {
										echo "
											<tr>
												<td>" . $row['Comment'] . "</td>
												<td>" . $row['Username'] . "</td>
												<td>" . $row['Date'] . "</td>
												<td>
													<a href='comments.php?do=edit&commentid=" . $row['ID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
													<a href='comments.php?do=delete&commentid=" . $row['ID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a> ";
												if($row['Status'] == 0) {
													echo "<a href='comments.php?do=approve&commentid=" . $row['ID'] . "' class='btn btn-info'><i class='fa fa-check'></i> Approve</a>";
												}
										echo "	</td>
											</tr>
										";
									}
								?>
							</table>
						</div>
					<?php } ?>
				</div>
			<?php 
			} else {
				echo "<div class='container'>";
				redirectHome("<div class='alert alert-danger'>there's no such id</div>", null, 5);
				echo "</div>";
			}
		} elseif($do == "update") { // update page
			echo "<h1 class='text-center'>Update item</h1>";
			echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$id = $_POST['itemid'];
				$name = $_POST['name'];
				$desc = $_POST['desc'];
				$price = $_POST['price'];
				$country = $_POST['country'];
				$catid = $_POST['category'];
				$memberid = $_POST['publisher'];
				$tags = $_POST['tags'];

				// validate form
				$formErrors = array();

				if(empty($name)) {
					$formErrors[] = "Item name can't be <strong>empty</strong>!";
				} elseif(strlen($name) < 4) {
					$formErrors[] = "Item name can't be less than <strong>4 characters</strong>!";
				} elseif(strlen($name) > 20) {
					$formErrors[] = "Item name can't be more than <strong>20 characters</strong>!";
				}

				if(empty($desc)) {
					$formErrors[] = "Description can't be <strong>empty</strong>!";
				}	

				if(empty($price)) {
					$formErrors[] = "Price can't be <strong>empty</strong>!";
				}

				if(empty($country)) {
					$formErrors[] = "Item country can't be <strong>empty</strong>!";
				}

				if(empty($catid)) {
					$formErrors[] = "You should specify item's <strong>category</strong>!";
				}

				if(empty($memberid)) {
					$formErrors[] = "You should specify item's <strong>publisher</strong>!";
				}

				if(empty($formErrors)){
					// update DB with this info
					$stmt = $con->prepare("UPDATE items SET Name = ?, Description = ?, Price = ?, MadeIn = ?, CatID = ?, MemberID = ?, Tags = ? WHERE ID = ?");
					$stmt->execute(array($name, $desc, $price, $country, $catid, $memberid, $tags, $id));

					// echo success msg
					
					redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>", 'back', 5);
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
			echo "<h1 class='text-center'>Delete item</h1>";
			echo "<div class='container'>";

			$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
			
			// check if the item is in the DB
			$count = checkExistance('ID', 'items', $itemid);

			if($count > 0){

				$stmt = $con->prepare("DELETE FROM items WHERE ID = :itemid");
				$stmt->bindParam(":itemid", $itemid);
				$stmt->execute();
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record deleted </div>", 'back', 5);
			} else {
				
				redirectHome("<div class='alert alert-danger'>this id is not exist.</div>");
			}
			echo "</div>";
		} elseif($do == 'approve') { // Approve item
			echo "<h1 class='text-center'>Approve item</h1>";
			echo "<div class='container'>";

			$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
			
			// check if the item is in the DB
			$count = checkExistance('ID', 'items', $itemid);

			if($count > 0){

				$stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE ID = :itemid");
				$stmt->bindParam(":itemid", $itemid);
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