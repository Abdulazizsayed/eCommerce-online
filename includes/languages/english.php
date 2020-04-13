<?php 
	
	function lang($phrase){
		static $lang = array(

			// navbar page
			'HOME_ADMIN' => 'Admin area',
			'CATEGORIES' => 'Categories',
			'ITEMS' => 'Items',
			'MEMBERS' => 'Members',
			'COMMENTS' => 'Comments',
			'STATISTICS' => 'Statistics',
			'LOGS' => 'Logs',
			'EDIT_PROFILE' => 'Edit profile',
			'SETTINGS' => 'Settings',
			'LOGOUT' => 'Logout',

			// login page
			'ADMIN_LOGIN' => 'Admin login',
			'USERNAME' => 'Username',
			'PASSWORD' => 'Password',
			'LOGIN' => 'Login',
		);

		return $lang[$phrase];
	}
