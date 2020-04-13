<?php 
	session_start();
	$pageTitle = 'Home';
	include "init.php";
?>
	<div class="container">
	<div class='row'>
		<?php 
			foreach(getRecords('*', 'items', 'WHERE Approve = 1', '', 'ID') as $item) {
				echo "<div class='col-sm-6 col-md-3'>
						<div class='thumbnail item-box'>
							<span class='price-tag'>$" . $item['Price'] . " </span>
							<img class='image-responsive' src='layout/images/avatar.jpg' alt='Proudct image' />
							<div class='caption'>
								<h3><a href='items.php?itemid=" . $item['ID'] . "'>" . $item['Name'] . "</a></h3>
								<p> " . $item['Description'] . " </p>
							</div>
							<div class='date'> " . $item['Date'] . " </div>
						</div>
					  </div>";
			}
		?>
	</div>
</div>
<?php
	include $tpl . "footer.php"; 

?>