<?php 
	session_start();
	include "init.php"; 
?>

<div class="container">
	<div class='row'>
		<?php 
			if(isset($_GET['name'])){
				$tag = $_GET['name'];
				echo "<h1 class='text-center'>Show " . $tag . " items</h1>";
				foreach(getRecords('*', 'items', "WHERE Tags LIKE '%$tag%'", 'AND Approve = 1', 'ID') as $item) {
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
				echo "Type tag name!";
			}
		?>
	</div>
</div>

<?php include $tpl . "footer.php"; ?>