<?php 
	session_start();
	$noNavbar = '';
	$pageTitle = 'Login';
	if(isset($_SESSION['Username'])){
		header('Location: dashboard.php');
	}
	include "init.php";

	// check if user coming from http post request

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$username = $_POST['user'];
		$password = $_POST['pass'];
		$hashedPass = sha1($password);

		// check if the user is in the DB
		$stmt = $con->prepare("SELECT 
									UserID, Username, Password
							   FROM 
							   		users 
							   WHERE 
							   		Username = ? 
							   AND 
							   		Password = ? 
							   AND 
							   		GroupID = 1
							   LIMIT 1");
		$stmt->execute(array($username, $hashedPass));
		$row = $stmt->fetch();
		$count = $stmt->rowCount();
		
		if($count > 0){
			$_SESSION['Username'] = $username;
			$_SESSION['ID'] = $row['UserID'];
			header('Location: dashboard.php');
			exit();
		}
	}
?>

	<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<h4 class="title text-center"><?php echo lang('ADMIN_LOGIN'); ?></h4>
		<input class="form-control" type="text" name="user" placeholder="<?php echo lang('USERNAME'); ?>" autocomplete="off">
		<input class="form-control" type="password" name="pass" placeholder="<?php echo lang('PASSWORD'); ?>" autocomplete="new-password">
		<input class="btn btn-primary btn-block" type="submit" value="<?php echo lang('LOGIN'); ?>">
	</form>

<?php include $tpl . "footer.php"; ?>