<?php 

	$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'];
	$url .= $_SERVER['REQUEST_URI'];

	$redirectUrl = dirname(dirname($url));

    $database = __DIR__.'../../app/Config/Database.php';
    $app = __DIR__.'../../app/Config/App.php';
    $installation = __DIR__;
//    echo substr(sprintf('%o', fileperms(__DIR__.'../../app/Config/Database.php')), -4);
//    print __DIR__.'../../app/Config/Database.php';

//    $dir_writable = substr(sprintf('%o', fileperms(__DIR__.'../../app/Config/Database.php')), -4) == "0774" ? "true" : "false";
//    print is_writable($database);
//    print is_writable($app);
//    print is_writable($installation);

//    if (is_writable($app)) {
//        echo $app.' is writable.<br>';
//    } else {
//        echo $app.' is not writable. Permissions may have to be adjusted.<br>';
//    }

//    if (is_writable($app) && is_writable($database) && is_writable($installation)){
//        echo 'is writable.<br>';
//    } else {
//        echo 'is not writable. Permissions may have to be adjusted.<br>';
//    }

//    print is_writable($app);
//die();
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
	  		<div id="loading-image" style="background-color: #000;width: 100%;height: 100%;z-index: 3000;position: fixed;opacity: 0.6; display: none; " >
		      	<img style="position: absolute;z-index: 5000;top: 200px;left: 606px;"  src="assets/loading.gif" />
		      	<p style="    position: absolute;z-index: 5000;top: 150px;left: 562px;font-size: 25px;color: #fff;">Please wait until it is processing...</p>
		 	 </div>
	  		<div class="col-md-12 main-head" >
	  			<h2>Shohozhishab Installation</h2>
	  			<p>Please input your Database details to continue</p>
	  		</div>
	  		<div class="col-md-12 main-head" id="message" >
	  		</div>
	  		<div class="col-md-3" ></div>
	  		<form id="geniusform" action="action.php" method="post">
	  		<div class="col-md-6">
	  			<div class="form-group">
				    <label for="email">Host Name</label>
				    <input type="text" name="dbHost" class="form-control" id="dbHost" value="localhost" required>
				</div>

	  			<div class="form-group">
				    <label for="email">Database Name</label>
				    <input type="text" name="dbName" class="form-control" id="dbName" value="shohozhishabDB" required>
				</div>
				<div class="form-group">
				    <label for="email">User Name</label>
				    <input type="text" name="dbUserName" class="form-control" id="userName" value="root" required>
				</div>
				<div class="form-group">
				    <label for="pwd">Password:</label>
				    <input type="password" class="form-control" name="dbPassword" id="dbPassword" >
				</div>
                <div class="form-group ">
                    <?php if (is_writable($database)){ ?>
                        <p style="float: right; color: green;">Yes</p>
                    <?php }else{ ?>
                        <p style="float: right; color: red;">No</p>
                    <?php } ?>
                    <p> 1) Database file read/write permissions</p>
                </div>
                <div class="form-group ">
                    <?php if (is_writable($app)){ ?>
                        <p style="float: right; color: green;">Yes</p>
                    <?php }else{ ?>
                        <p style="float: right; color: red;">No</p>
                    <?php } ?>

                    <p> 2) Config file read/write permissions</p>
                </div>

                <div class="form-group ">
                    <?php if (is_writable($installation)){ ?>
                        <p style="float: right; color: green;">Yes</p>
                    <?php }else{ ?>
                        <p style="float: right; color: red;">No</p>
                    <?php } ?>
                    <p> 3) Installation folder file read/write permissions</p>
                </div>
                <?php
                    if (is_writable($app) && is_writable($database) && is_writable($installation)){
                        echo '<button type="submit" class="btn btn-primary geniusSubmit-btn">Continue</button>';
                    } else {
                        echo 'Is not writable. Permissions may have to be adjusted.<br>';
                    }
                ?>

	  		</div>
	  		</form>
	  		<div class="col-md-3"></div>
	  	
	  	</div>
	</div>


	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>


	<script type="text/javascript">
		$(document).on('submit','#geniusform',function(e){
		    e.preventDefault();
		    
		    var fd = new FormData(this);

		    var geniusform = $(this);
		    $('button.geniusSubmit-btn').prop('disabled',true);
		    $.ajax({
		      method:"POST",
		      url:$(this).prop('action'),
		      data:fd,
		      contentType: false,
		      cache: false,
		      processData: false,
		      beforeSend: function() {
		        $("#loading-image").show();
		      },
		      success:function(data){
		        $("#loading-image").hide();		          
	          	$('#message').html(data);
	          	$('#geniusform')[0].reset();		          

		        $('button.geniusSubmit-btn').prop('disabled',false);
		       // $(window).scrollTop(0);
		        window.location.replace('<?php print $redirectUrl; ?>');
		      }

		    });

		});
	</script>

</body>
</html>