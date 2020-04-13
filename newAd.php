<?php 
	session_start();
	$pageTitle = 'Create new ad';
	include "init.php";
	if($session != '') {
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$formErrors = array();

			$title 		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
			$desc 		= filter_var($_POST['desc'], FILTER_SANITIZE_STRING);
			$price 		= filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
			$country 	= filter_var($_POST['madein'], FILTER_SANITIZE_STRING);
			$status 	= filter_var($_POST['status'], FILTER_SANITIZE_NUMBER_INT);
			$cat 		= filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
			$tags 		= filter_var($_POST['tags'], FILTER_SANITIZE_STRING);

			if(strlen($title) < 4) {
				$formErrors[] = "Item title should be more than 4 chars!";
			}

			if(strlen($desc) < 10) {
				$formErrors[] = "Item description should be more than 10 chars!";
			}

			if(strlen($country) < 2) {
				$formErrors[] = "Item country should be more than 2 chars!";
			}

			if(empty($price)) {
				$formErrors[] = "Item price must be not empty!";
			}

			if(empty($status)) {
				$formErrors[] = "Item status must be not empty!";
			}

			if(empty($cat)) {
				$formErrors[] = "Item category must be not empty!";
			}

			if(empty($formErrors)){
					// Insert new item in DB
					
					$stmt = $con->prepare("INSERT INTO 
											items(Name, Description, Price, Date, MadeIn, Status, CatID, MemberID, Tags)
										   VALUES(:name, :description, :price, now(), :country, :status, :catid, :memberid, :tags)");
					$stmt->execute(array(
						':name' => $title,
						':description' => $desc,
						':price' => $price,
						':country' => $country,
						':status' => $status,
						':catid' => $cat,
						':memberid' => $_SESSION['NormalUserID'],
						':tags' => $tags
					));

					// echo success msg

					if($stmt) {
						$successMsg = "Item added successfully!";
					}

				}		
		}
?>
	<h1 class='text-center'>Create new ad</h1>

	<div class='create-ad block'>
		<div class='container'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>
					Create ad
				</div>
				<div class='panel-body'>
					<div class='row'>
						<div class='col-md-8'>
							<form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<!-- start name field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Name</label>
									<div class="col-sm-9 col-md-9">
										<input pattern=".{4,}" title="At least 4 chars" type="text" name="name" class="form-control" placeholder="Name" required/>
									</div>
								</div>
								<!-- end name field -->
								<!-- start description field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Description</label>
									<div class="col-sm-9 col-md-9">
										<input pattern=".{10,}" title="At least 10 chars"  type="text" name="desc" class="form-control" placeholder="Description" required />
									</div>
								</div>
								<!-- end description field -->
								<!-- start price field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Price</label>
									<div class="col-sm-9 col-md-9">
										<input type="text" name="price" class="form-control" placeholder="Price" required />
									</div>
								</div>
								<!-- end price field -->
								<!-- start Made in field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Made in</label>
									<div class="col-sm-9 col-md-9">
										<input type="text" name="madein" class="form-control" placeholder="Country" required />
									</div>
								</div>
								<!-- end made in field -->
								<!-- start category field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Category</label>
									<div class="col-sm-9 col-md-9">
										<select name='category' required>
											<option value="0">...</option>
											<?php
											$cats = getRecords('*', 'categories', '', '', 'ID');
											foreach($cats as $cat){
												echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
											}
											?>
										</select>
									</div>
								</div>
								<!-- end category field -->
								<!-- start satatus field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-3 control-label">Status</label>
									<div class="col-sm-9 col-md-9">
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
									<label class="col-sm-3 control-label">Tags</label>
									<div class="col-sm-9 col-md-9">
										<input type="text" name="tags" class="form-control" placeholder="Separate between tags by comma" />
									</div>
								</div>
								<!-- end tags field -->
								<!-- start submit field -->
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9">
										<input type="submit" value="Add" class="btn btn-primary btn-lg" />
									</div>
								</div>
								<!-- end submit field -->
							</form>
						</div>
							<div class='col-md-4'>
								<div class='thumbnail item-box live-preview'>
								<span class='price-tag'> $0 </span>
								<img class='image-responsive' src='layout/images/avatar.jpg' alt='Proudct image' />
								<div class='caption'>
									<h3>Title</h3>
									<p>Description</p>
								</div>
							</div>
						</div>
					</div>
					<!-- start looping through errors -->

					<?php 
						if(!empty($formErrors)) {
							foreach($formErrors as $error) {
								echo "<div class='alert alert-danger'>
										" . $error . "
									  </div>";
							}
						}

						if(isset($successMsg)) {
							echo "<div class='alert alert-success'>" . $successMsg . "</div>";
						}
					?>

					<!-- end looping through errors -->
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