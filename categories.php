<?php 
	session_start();
	include "init.php"; 
?>

<div class="container">
	<h1 class="text-center">Show category</h1>
	<div class='row'>
		<?php 
			$pageid = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
			if($pageid != 0){
				foreach(getRecords('*', 'items', "WHERE CatID = {$_GET['id']}", 'AND Approve = 1', 'ID') as $item) {
					echo "<div class='col-sm-6 col-md-3'>
							<div class='thumbnail item-box'>
								<span class='price-tag'>" . $item['Price'] . " </span>
								<img class='image-responsive' src='layout/images/avatar.jpg' alt='Proudct image' />
								<div class='caption'>
									<h3><a href='items.php?itemid=" . $item['ID'] . "'>" . $item['Name'] . "</a></h3>
									<p> " . $item['Description'] . " </p>
								</div>
								<div class='date'> " . $item['Date'] . " </div>
							</div>
						  </div>";
				}
			} else {
				echo "invalid id!";
			}
		?>
	</div>
</div>

<?php include $tpl . "footer.php"; ?>