
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php $userIdSuper = newSession()->userIdSuper;
                $prosidenimg = no_image_view('/uploads/admin_image/' . profile_image_super($userIdSuper), 'uploads/admin_image/no_image.jpg', profile_image_super($userIdSuper)); ?>
                <img src="<?php print $prosidenimg; ?>" class="img-circle" alt="User Image" width="25" height="25">
            </div>
            <div class="pull-left info">
                <p><?php echo newSession()->sup_name;?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
<!--            <li class="active">-->
<!--                <a href="--><?php //echo base_url('Super_admin/Dashboard')?><!--">-->
<!--                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>-->
<!--                </a>-->
<!--            </li>-->
             <?php
                echo add_main_menu('Shops', '/Super_admin/Shops', 'shops', 'fa fa-list-alt');
                echo add_main_menu('License', '/Super_admin/License', 'shops', 'fa fa-list-alt');
                echo add_main_menu('Profile', '/Super_admin/Settings', 'shops', 'fa fa-gears');
             echo add_main_menu('Package', '/Super_admin/Package', 'package', 'fa fa-list-alt');
                echo add_main_menu('General Settings', '/Super_admin/General_settings', 'shops', 'fa fa-gears');

            ?>




        </ul>
    </section>
    <!-- /.sidebar -->
</aside>