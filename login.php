<?php 
	session_start();
	$pageTitle = 'Login';

	if(isset($_SESSION['NormalUser'])){
		header('Location: index.php');
	}

	include 'init.php';

	// check if user coming from http post request

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['login'])){
			$username = $_POST['username'];
			$password = $_POST['password'];
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
								   LIMIT 1");
			$stmt->execute(array($username, $hashedPass));
			$get = $stmt->fetch();
			$count = $stmt->rowCount();
			
			if($count > 0){
				$_SESSION['NormalUser'] = $username; 
				$_SESSION['NormalUserID'] = $get['UserID'];
				header('Location: index.php');
				exit();
			}
		} else {
			$formErrors = array();
			if(isset($_POST['username'])) {
				$filteredUser = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
				
				if(strlen($filteredUser) < 4) {
					$formErrors[] = "Username can't be smaller than 4 chars!";
				}
			}

			if(isset($_POST['password']) and isset($_POST['password2'])) {

				if(empty($_POST['password'])) {
					$formErrors[] = "Password can't be empty!";
				}

				$pass1 = sha1($_POST['password']);
				$pass2 = sha1($_POST['password2']);
				
				if($pass1 !== $pass2) {
					$formErrors[] = "The two passwords should be equal!";
				}
			}

			if(isset($_POST['email'])) {
				$filteredEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
				
				if(!filter_var($filteredEmail, FILTER_VALIDATE_EMAIL)) {
					$formErrors[] = "This email is not valid!";
				}
			}

			if(empty($formErrors)){
				// check if the user is already in DB
				if(checkExistance('Username', 'users', $filteredUser)) {
					$formErrors[] = "Username used before!";
				}

				// Insert new user in DB
				
				$stmt = $con->prepare("INSERT INTO 
										users(Username, Password, Email, RegStatus, Date)
									   VALUES(:user, :pass, :email, 0, now())");
				$stmt->execute(array(
					':user' => $filteredUser,
					':pass' => $pass1,
					':email' => $filteredEmail
				));

				// echo success msg

				$successMsg = "Congrats you have an account now!";
			} else {
				foreach($formErrors as $error){
					echo "<div class='alert alert-danger'>" . $error . "</div>";
				}
			}
		}
	}
?>

<div class='container login-page'>
	<h1 class='text-center'>
		<span class='login-title text-primary' data-class='login'>Login</span> | <span class='signup-title' data-class='signup'>Signup</span>
	</h1>
	<!-- start login form -->
	<form class='login' action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input class='form-control' type='text' name='username' autocomplete='off' placeholder='Type your username' />
		<input class='form-control' type='password' name='password' autocomplete='new-password' placeholder="Type your password" />
		<input class='btn btn-primary btn-block' name='login' type='submit' value='Login' />
	</form>
	<!-- end login form -->

	<!-- start signup form -->
	<form class='signup' action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input pattern='.{3,}' title='Username must be greater than 4 chars!' class='form-control' type='text' name='username' autocomplete='off' placeholder='Type your username' />
		<input minlength='4' class='form-control' type='password' name='password' autocomplete='new-password' placeholder="Type a complex password" />
		<input minlength='4' class='form-control' type='password' name='password2' autocomplete='new-password' placeholder="Type a password again" />
		<input class='form-control' type='email' name='email' autocomplete='off' placeholder="Type a valid email" />
		<input class='btn btn-success btn-block' name='signup' type='submit' value='Signup' />
	</form>
	<!-- end signup form -->
	<div class='the-errors text-center'>
		<?php 
			if(!empty($formErrors)) {
				foreach($formErrors as $error) {
					echo "<div class='error msg'>" . $error . "</div>";
				}
			}

			if(isset($successMsg)) {
				echo "<div class='success msg'>" . $successMsg . "</div>";
			}
		?>
	</div>
</div>

<?php include $tpl . 'footer.php'; ?>