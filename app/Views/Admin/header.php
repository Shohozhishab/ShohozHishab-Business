<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Shohozhishab | Super Admin</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url()?>/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url()?>/dist/css/custom.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url()?>/dist/css/skins/_all-skins.min.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/morris.js/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/jvectormap/jquery-jvectormap.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url()?>/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="<?php echo base_url()?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <link rel="stylesheet" href="<?php print base_url(); ?>/bower_components/select2/dist/css/select2.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper" >

    <header class="main-header" id="reload">
        <!-- Logo -->
        <a href="<?php echo base_url()?>" class="logo">
            <span class="logo-lg"><b>Shohozhishab</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
<!--        <nav class="navbar navbar-static-top">-->
        <nav class="navbar navbar-fixed-top  mainnav" id="stickyNav" style="height: 50px;">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <span  class="head-cash">Shop Cash : <?php echo admin_cash(); ?></span>

                <ul class="nav navbar-nav" style="margin-right: 135px;" >
                    <li style="padding: 5px;"><a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/create/'); ?>','<?php echo '/Admin/Purchase/create/';?>'),activeTab(this)"  class="btn btn-success " id="btnSize" ><i class="fa fa-fw fa-briefcase"></i>Purchase</a></li>

                    <li style="padding: 5px;"><a href="#" onclick="showData('<?php echo site_url('Admin/Sales_ajax/create/'); ?>','<?php echo '/Admin/Sales/create/';?>'),activeTab(this)" class="btn btn-success " id="btnSize" ><i class="fa fa-fw fa-cart-plus"></i> Sale</a></li>
                    <li style="padding: 5px;"><a href="#" onclick="showData('<?php echo site_url('Admin/Transaction_ajax/create/'); ?>','<?php echo '/Admin/Transaction/create/';?>'),activeTab(this)" class="btn btn-success " id="btnSize" ><i class="fa fa-fw fa-exchange"></i> Transaction</a></li>
                </ul>

                <ul class="nav navbar-nav">

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
<!--                            <img src="--><?php //echo base_url()?><!--/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                            <?php $profiimg =  no_image_view('/uploads/schools/'.profile_image(),'/uploads/schools/no_image.jpg',profile_image()); ?>
                            <img src="<?php print $profiimg; ?>" class="img-circle" alt="User Image" width="25" height="25">
                            <span class="hidden-xs"><?php echo profile_name();?> <i class="fa fa-sign-out" aria-hidden="true"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php print $profiimg; ?>" class="img-circle" alt="User Image" >

                                <p>
                                    <?php echo profile_name();?>
                                    <!--                                    <small>Member since Nov. 2012</small>-->
                                </p>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
<!--                                    <a href="#" class="btn btn-default btn-flat">Profile</a>-->
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo base_url('Admin/Login/logout')?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <!--                    <li>-->
                    <!--                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>-->
                    <!--                    </li>-->
                </ul>
            </div>
        </nav>
    </header>

    <div id="loading-image" style="background-color: #000;width: 100%;height: 100%;display: block;z-index: 3000;position: fixed;opacity: 0.6; display: none; " >
        <img style="position: absolute;z-index: 5000;top: 200px;left: 606px;"  src="<?php echo base_url() ?>/uploads/loading.gif" />
        <p style="    position: absolute;
    z-index: 5000;
    top: 150px;
    left: 562px;
    font-size: 25px;
    color: #fff;"><?php echo get_sup_settings_by_lavel('loading_message')?></p>
    </div>

