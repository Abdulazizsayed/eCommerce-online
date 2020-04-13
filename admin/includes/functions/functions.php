<?php 
	// get page title
	function getTitle() {
		global $pageTitle;
		if(isset($pageTitle)) echo $pageTitle;
		else echo "Default";
	}

	// redirect function
	function redirectHome($msg, $url = null, $seconds = 3){
		if($url === null) {
			$url = 'index.php';
		} else {
			$url = isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '' ? $_SERVER['HTTP_REFERER'] : 'index.php';
		}

		$link = $url == 'index.php' ? 'home page' : 'previous page';
		echo $msg;
		echo "<div class='alert alert-info'>You will be redirected to $link after $seconds</div>";
		header("refresh:$seconds;url=$url");
		exit();
	}

	// check if item is in DB
	function checkExistance($select, $from, $value){
		global $con;
		$statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
		$statement->execute(array($value));
		return $statement->rowCount();
	}

	// get number of items
	function countItems($item, $table){
		global $con;
		$stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
		$stmt2->execute();

		return $stmt2->fetchColumn();
	}

	// get latest items
	function getLatest($select, $table, $order, $limit = 5){
		global $con;
		$getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
		$getStmt->execute();
		return $getStmt->fetchAll();
	}

	// get All records in DB
	function getRecords($table){
		global $con;
		$getStmt = $con->prepare("SELECT * FROM $table");
		$getStmt->execute();
		return $getStmt->fetchAll();
	}

	// get All records in DB
	function selectFromWhere($field, $table, $where = null, $and = null, $orderField, $ordering = 'DESC'){
		global $con;

		$getStmt = $con->prepare("SELECT $field FROM $table $where $and ORDER BY $orderField $ordering");
		$getStmt->execute();
		return $getStmt->fetchAll();
	}