<?php
	session_start();

	$dbName = $_POST['dbName'];
	$dbUserName = $_POST['dbUserName'];
	$dbPassword = $_POST['dbPassword'];
	$dbHost = $_POST['dbHost'];

	$data = array(
		'dbHost' => $dbHost,
		'dbName' => $dbName, 
		'dbUserName' => $dbUserName, 
		'dbPassword' => $dbPassword, 
	);
	$_SESSION['DB'] = $data;

?>


<!DOCTYPE html>
<html lang="en">
<head>
  	<title>Shohozhishab Installation</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
  
</head>
<body>
	<div class="container-fluid">
	  	<div class="row">
	  		<div class="col-md-12 main-head" >
	  			<h2>Shohozhishab Installation</h2>
	  			<p>Please input your Shop details to continue</p>
	  		</div>
	  		<div class="col-md-3"></div>
	  		<form action="action.php" method="post">
	  		<div class="col-md-6">
	  			<div class="form-group">
				    <label for="email"> Name</label>
				    <input type="text" name="userName" class="form-control" id="userName" value="admin" required>
				</div>
				<div class="form-group">
				    <label for="email">Email</label>
				    <input type="text" name="email" class="form-control" id="email" value="admin@gmail.com" required>
				</div>
				<div class="form-group">
				    <label for="pwd">Password:</label>
				    <input type="password" class="form-control" name="password" id="password" required>
				</div>
				<button type="submit" class="btn btn-primary">Continue</button>
	  		</div>
	  		</form>
	  		<div class="col-md-3"></div>
	  	
	  	</div>
	</div>



	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>

</body>
</html>