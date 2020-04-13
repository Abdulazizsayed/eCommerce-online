<?php 
	session_start();

	$pageTitle = 'Categories';
	if(isset($_SESSION['Username'])){
		include 'init.php';
		isset($_GET['do']) ? $do = $_GET['do'] : $do = 'manage';

		if($do == 'manage') { // manage page 
			$sort = 'ASC';
			$sortArray = array('ASC', 'DESC');
			if (isset($_GET['sort']) && in_array($_GET['sort'], $sortArray)) {
				$sort = $_GET['sort'];
			}
			$stmt = $con->prepare("SELECT * FROM categories ORDER BY Ordering $sort");
			$stmt->execute();
			$cats = $stmt->fetchAll(); ?>

			<h1 class="text-center">Manage Categories</h1>
			<div class="container categories">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class='fa fa-edit'></i> Manage Categories
						<div class="option pull-right">
							<i class='fa fa-sort'></i> Ordering: [
							<a class="<?php if($_GET['sort'] == 'ASC') echo 'active' ?>" href="?sort=ASC">ASC</a> |
							<a class="<?php if($_GET['sort'] == 'DESC') echo 'active' ?>" href="?sort=DESC">DESC</a>]
							<i class='fa fa-eye'></i> View: [
							<span data-view="full" class="active">Full</span> |
							<span data-view="classic">Classic</span>]
						</div>
					</div>
					<div class="panel-body">
						<?php
							if(!empty($cats)){
								foreach($cats as $cat){
									if($cat['Parent'] != 0) continue;
									echo "<div class='cat'>";
										echo "<div class='hidden-buttons'>";
											echo "<a href='?do=edit&catid=" . $cat['ID'] . "' class='btn btn-sx btn-primary'><i class='fa fa-edit'></i> Edit</a>";
											echo "<a href='?do=delete&catid=" . $cat['ID'] . "' class='confirm btn btn-sx btn-danger'><i class='fa fa-close'></i> Delete</a>";
										echo "</div>";
										echo "<h3>" . $cat['Name'] . "</h3>";
										echo "<div class='full-view'>";
											echo "<p>";
											if(empty($cat['Description'])){
												echo "This category has no description";
											} else {
												echo $cat['Description'];
											}
											echo "</p>";
											if($cat['Visibility'] == 0){
												echo "<span class='visibility'><i class='fa fa-eye'></i> Hidden</span>";
											}
											if($cat['AllowComment'] == 0){
												echo "<span class='commenting'><i class='fa fa-close'></i> Comments disabled</span>";
											}
											if($cat['AllowAds'] == 0){
												echo "<span class='ads'><i class='fa fa-close'></i> Ads disabled</span>";
											}
											echo "</div>";
										echo "</div>";
										$lis = '';
										foreach($cats as $child) {
											if($child['Parent'] != $cat['ID']) continue;
											$lis .= "<li class='child-cat'>
														<a href='?do=edit&catid=" . $child['ID'] . "' >" . $child['Name'] . "</a>
														<a href='?do=delete&catid=" . $child['ID'] . "' class='confirm show-delete'>Delete</a>
													</li>";
										}
										if(strlen($lis) > 0) {
											echo "<h4 class='child-heading'>Child categories</h4>";
											echo "<ul class='list-unstyled child-cats'>";
												echo $lis;
											echo "</ul>";
										}
										echo "<hr>";
								}
							} else {
								echo "<div class='alert alert-info'>There's no records to show!</div>";
							}
						?>
					</div>
				</div>
				<a href='?do=add' class="btn btn-primary"><i class="fa fa-plus"></i> Add new category</a>
			</div>
			<?php 
		} elseif($do == 'add') { // add page ?>
			
			<h1 class="text-center">Add new category</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=insert" method="POST">
						<!-- start name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="name" class="form-control" autocomplete="off" placeholder="Name" required/>
							</div>
						</div>
						<!-- end name field -->
						<!-- start description field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="desc" class="form-control" placeholder="Description" /> 
							</div>
						</div>
						<!-- end description field -->
						<!-- start ordering field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ordering</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="ordering" class="form-control" placeholder="Order" />
							</div>
						</div>
						<!-- end ordering field -->
						<!-- start category type -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Parent?</label>
							<div class="col-sm-10 col-md-4">
								<select name='parent'>
									<option value='0'>None</option>
									<?php 
									$parents = selectFromWhere("ID, Name", "categories", "WHERE Parent = 0", "", "ID");
									foreach($parents as $parent) {
										echo "<option value='" . $parent['ID'] . "'>" . $parent['Name'] . "</option>";
									}
									?>
								</select>							
							</div>
						</div>
						<!-- end category type -->
						<!-- start Visibility field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Visible</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="visible-yes" type="radio" name="visibility" value="1" checked>
									<label for="visible-yes">Yes</label>
								</div>
								<div>
									<input id="visible-no" type="radio" name="visibility" value="0" >
									<label for="visible-no">No</label>
								</div>
							</div>
						</div>
						<!-- end Visibility field -->
						<!-- start commenting field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Commenting</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="commenting-yes" type="radio" name="commenting" value="1" checked>
									<label for="commenting-yes">Yes</label>
								</div>
								<div>
									<input id="commenting-no" type="radio" name="commenting" value="0" >
									<label for="commenting-no">No</label>
								</div>
							</div>
						</div>
						<!-- end commenting field -->
						<!-- start ads field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ads</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="ads-yes" type="radio" name="ads" value="1" checked>
									<label for="ads-yes">Yes</label>
								</div>
								<div>
									<input id="ads-no" type="radio" name="ads" value="0" >
									<label for="ads-no">No</label>
								</div>
							</div>
						</div>
						<!-- end ads field -->
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
				echo "<h1 class='text-center'>Add new category</h1>";
				echo "<div class='container'>";

				$name = $_POST['name'];
				$desc = $_POST['desc'];
				$order = $_POST['ordering'];
				$parent = $_POST['parent'];
				$visible = $_POST['visibility'];
				$comment = $_POST['commenting'];
				$ads = $_POST['ads'];

				if(empty($name)){
					redirectHome("<div class='alert alert-danger'>Category name can't be <strong>empty</strong></div>", 'back', 5);
				} else {
					// check if the user is already in DB
					if(checkExistance('Name', 'categories', $name)) {
						redirectHome("<div class='alert alert-danger'>Category already exist!</div>", 5);
					} else {

						// Insert new user in DB
						
						$stmt = $con->prepare("INSERT INTO 
												categories(Name, Description, Ordering, Parent, visibility, AllowComment, AllowAds)
											   VALUES(:name, :desc, :order, :parent, :visible, :comment, :ads)");
						$stmt->execute(array(
							':name' => $name,
							':desc' => $desc,
							':order' => $order,
							':parent' => $parent,
							':visible' => $visible,
							':comment' => $comment,
							':ads' => $ads,
						));

						// echo success msg

						redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record inserted </div>", 'back', 5);
					}
				}
			} else {
				 echo "<div class='container'>";
				 redirectHome('<div class="alert alert-danger"> sorry you are not allowed to view this page!</div>', 'back', 5);
				 echo "</div>";
			}
			echo "</div>";
		} elseif($do == 'edit') { // edit page
			$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
			
			// check if the category is in DB
			$stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");
			$stmt->execute(array($catid));
			$cat = $stmt->fetch();
			$count = $stmt->rowCount();

			if($count > 0){ ?>

				<h1 class="text-center">Edit category</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=update" method="POST">
						<input type="hidden" name="catid" value="<?php echo $catid; ?>">
						<!-- start name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="name" class="form-control" value="<?php echo $cat['Name'] ?>" placeholder="Name" required/>
							</div>
						</div>
						<!-- end name field -->
						<!-- start description field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="desc" class="form-control" value="<?php echo $cat['Description'] ?>" placeholder="Description" /> 
							</div>
						</div>
						<!-- end description field -->
						<!-- start ordering field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ordering</label>
							<div class="col-sm-10 col-md-4">
								<input type="text" name="ordering" class="form-control" value="<?php echo $cat['Ordering'] ?>" placeholder="Order" />
							</div>
						</div>
						<!-- end ordering field -->
						<!-- start category type -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Parent?</label>
							<div class="col-sm-10 col-md-4">
								<select name='parent'>
									<option value='0'>None</option>
									<?php 
									$parents = selectFromWhere("ID, Name", "categories", "WHERE Parent = 0", "", "ID");
									foreach($parents as $parent) {
										echo "<option value='" . $parent['ID'] . "'";
										if($parent['ID'] == $cat['Parent']) echo " selected>"; 
										else echo " >";
										echo $parent['Name'] . "</option>";
									}
									?>
								</select>							
							</div>
						</div>
						<!-- end category type -->
						<!-- start Visibility field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Visible</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="visible-yes" type="radio" name="visibility" value="1" <?php if($cat['Visibility'] == 1) echo 'checked'; ?>>
									<label for="visible-yes">Yes</label>
								</div>
								<div>
									<input id="visible-no" type="radio" name="visibility" value="0" <?php if($cat['Visibility'] == 0) echo 'checked'; ?>>
									<label for="visible-no">No</label>
								</div>
							</div>
						</div>
						<!-- end Visibility field -->
						<!-- start commenting field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Commenting</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="commenting-yes" type="radio" name="commenting" value="1" <?php if($cat['AllowComment'] == 1) echo 'checked'; ?>>
									<label for="commenting-yes">Yes</label>
								</div>
								<div>
									<input id="commenting-no" type="radio" name="commenting" value="0" <?php if($cat['AllowComment'] == 0) echo 'checked'; ?>>
									<label for="commenting-no">No</label>
								</div>
							</div>
						</div>
						<!-- end commenting field -->
						<!-- start ads field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ads</label>
							<div class="col-sm-10 col-md-4">
								<div>
									<input id="ads-yes" type="radio" name="ads" value="1" <?php if($cat['AllowAds'] == 1) echo 'checked'; ?>>
									<label for="ads-yes">Yes</label>
								</div>
								<div>
									<input id="ads-no" type="radio" name="ads" value="0" <?php if($cat['AllowAds'] == 0) echo 'checked'; ?>>
									<label for="ads-no">No</label>
								</div>
							</div>
						</div>
						<!-- end ads field -->
						<!-- start submit field -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Edit" class="btn btn-primary btn-lg" />
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
			echo "<h1 class='text-center'>Update category</h1>";
			echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$id = $_POST['catid'];
				$name = $_POST['name'];
				$desc = $_POST['desc'];
				$ordering = $_POST['ordering'];
				$parent = $_POST['parent'];
				$visibility = $_POST['visibility'];
				$commenting = $_POST['commenting'];
				$ads = $_POST['ads'];

				if(empty($name)){
					redirectHome("<div class='alert alert-danger'>Category name can't be <strong>empty</strong></div>", 'back', 5);
				} else {
					// update DB with this info
					$stmt = $con->prepare("UPDATE categories SET Name = ?, Description = ?, Parent = ?, Ordering = ?, Visibility = ?, AllowComment = ?, AllowAds = ? WHERE ID = ?");
					$stmt->execute(array($name, $desc, $parent, $ordering, $visibility, $commenting, $ads, $id));

					// echo success msg
					
					redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record updated </div>", 'back', 5);
				}
			} else {
				echo "<div class='container'>";
				redirectHome("<div class'alert alert-danger'>sorry you are not allowed to view this page!</div>", null, 5);
				echo "</div>";
			}
			echo "</div>";
		} elseif($do == 'delete') {// delete page
			echo "<h1 class='text-center'>Delete category</h1>";
			echo "<div class='container'>";

			$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
			
			// check if the user is in the DB
			$count = checkExistance('ID', 'categories', $catid);

			if($count > 0){

				$stmt = $con->prepare("DELETE FROM categories WHERE ID = :catid");
				$stmt->bindParam(":catid", $catid);
				$stmt->execute();
				
				redirectHome("<div class='alert alert-success'>" . $stmt->rowCount() . " record deleted </div>", 'back', 5);
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