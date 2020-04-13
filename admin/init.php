<?php

	include "connect.php";

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

	// include navbar in all pages except noNavbar var

	if(!isset($noNavbar)){ include $tpl . 'navbar.php'; }