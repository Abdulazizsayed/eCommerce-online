<?php

	// Error reporting

	ini_set('display_errors', 'on');
	error_reporting(E_ALL);

	include "admin/connect.php";

	$session = '';
	if(isset($_SESSION['NormalUser'])) {
		$session = $_SESSION['NormalUser'];
	}

	// Routes

	$tpl = 'includes/templates/'; // templates dir
	$lang = 'includes/languages/';
	$func = 'includes/functions/';
	$css = 'layout/css/';
	$js = 'layout/js/';

	// include important folders

	include $func . 'functions.php';
	include $lang . 'english.php';
	include $tpl . 'header.php';