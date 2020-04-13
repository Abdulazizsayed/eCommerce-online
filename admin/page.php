<?php 

	$do = '';

	isset($_GET['do']) ? $do = $_GET['do'] : $do = 'Manage';

	// if page is main page
	if($do == 'Manage') {
		echo "welcome you're in $do page";
		echo "<a href='?do=Add'>Add new category +</a>";
	} elseif($do == 'Add') {
		echo "welcome you're in $do page";
	} elseif($do == 'Insert') {
		echo "welcome you're in $do page";
	} else {
		echo "Error no such page or directory.";
	}