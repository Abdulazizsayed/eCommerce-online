<?php 
	session_start();

	$pageTitle = '';
	if(isset($_SESSION['Username'])){
		include 'init.php';
		isset($_GET['do']) ? $do = $_GET['do'] : $do = 'manage';
		if($do == 'manage') { // manage page 
		
		} elseif($do == 'add') { // add page 
				 
		} elseif($do == 'insert') { // insert page

		} elseif($do == 'edit') { // edit page

		} elseif($do == "update") { // update page
			
		} elseif($do == 'delete') {// delete page
			
		} elseif($do == 'activate') { // activate member
			
		}
		include $tpl . 'footer.php';
	} else {
		header('Location: index.php');
		exit();
	}