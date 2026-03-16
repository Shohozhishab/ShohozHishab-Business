<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Shops <small>Shops Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Shops</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Shop Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <!-- Nav tabs -->
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="<?php echo (!empty($_GET) && ($_GET['active'] == 'general'))?'active':'';?> <?php echo(empty($_GET))?'active':'';?> "><a href="#home" aria-controls="home"
                                                                              role="tab" data-toggle="tab">General</a></li>
                                    <li role="presentation" class="<?php echo(!empty($_GET) && ($_GET['active'] == 'personal'))?'active':'';?>"><a href="#profile" aria-controls="profile" role="tab"
                                                               data-toggle="tab">Personal</a></li>
                                    <li role="presentation" class="<?php echo(!empty($_GET) && ($_GET['active'] == 'photo'))?'active':'';?>"><a href="#messages" aria-controls="messages" role="tab"
                                                               data-toggle="tab">Photo</a></li>
                                    <li role="presentation" class="<?php echo(!empty($_GET) && ($_GET['active'] == 'user'))?'active':'';?>"><a href="#settings" aria-controls="settings" role="tab"
                                                               data-toggle="tab">User Update</a></li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content " style="margin-top: 60px;">
                                <div role="tabpanel" class="tab-pane <?php echo (!empty($_GET) && ($_GET['active'] == 'general'))?'active':'';?> <?php echo(empty($_GET))?'active':'';?>" id="home">
                                    <div class="col-lg-6 ">
                                        <form action="<?php echo base_url('Super_admin/Shops/general_update'); ?>" method="post">
                                            <div class="form-group">
                                                <label for="varchar">Name </label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $shops->name; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Shop Email </label>
                                                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $shops->email; ?>" />
                                            </div>

                                            <div class="form-group">
                                                <label for="enum">Status </label>
                                                <select class="form-control" name="status" id="status">
                                                    <?php print globalStatus($shops->status); ?>
                                                </select>
                                            </div>

                                            <input type="hidden" name="sch_id" value="<?php echo $shops->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                            <a href="<?php echo site_url('Super_admin/shops') ?>" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane <?php echo (!empty($_GET) && ($_GET['active'] == 'personal'))?'active':'';?>" id="profile">
                                    <div class="col-lg-6">
                                        <form action="<?php echo base_url('Super_admin/Shops/personal_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Mobile</label>
                                                <input type="number" class="form-control" name="mobile" id="mobile" placeholder="Mobile" value="<?php echo $shops->mobile; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="enum">Package </label>
                                                <select class="form-control" name="package_id" id="package_id">
                                                    <option value="">Please select</option>
                                                    <?php print getListInOptionCheckLicens($shops->package_id,'package_id','package_name','package'); ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Address</label>
                                                <input type="text" class="form-control" name="address" id="address" placeholder="Address" value="<?php echo $shops->address ?>" />
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Comment</label>
                                                <input type="text" class="form-control" name="comment" id="comment" placeholder="Comment" value="<?php echo $shops->comment; ?>" />
                                            </div>


                                            <input type="hidden" name="sch_id" value="<?php echo $shops->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="<?php echo site_url('Super_admin/shops') ?>" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane <?php echo(!empty($_GET) && ($_GET['active'] == 'photo'))?'active':'';?>" id="messages">
                                    <div class="col-lg-6">
                                        <form action="<?php echo base_url('Super_admin/Shops/photo_update'); ?>" method="post" enctype="multipart/form-data" >

                                            <div class="form-group">
                                                <label for="longtext">Logo</label>
                                                <input type="file" class="form-control" name="logo" id="logo" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=350px) x (height=100px)</b></span>
                                            </div>

                                            <div class="form-group">
                                                <label for="longtext">Profile Image</label>
                                                <input type="file" class="form-control" name="profile_image" id="profile_image" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=160px) x (height=160px)</b></span>
                                            </div>

                                            <input type="hidden" name="sch_id" value="<?php echo $shops->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="<?php echo site_url('Super_admin/shops') ?>" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane <?php echo(!empty($_GET) && ($_GET['active'] == 'user'))?'active':'';?>" id="settings">
                                    <div class="col-lg-6">
                                        <form action="<?php echo base_url('Super_admin/Shops/user_update'); ?>" method="post">
                                            <?php if (!empty($users)) {?>
                                                <div class="form-group">
                                                    <label for="varchar">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $users->email; ?>" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="varchar">Password</label>
                                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="longtext">Confirm Password</label>
                                                    <input type="password" class="form-control" name="con_password" id="password" placeholder="Confirm Password" value="" required />
                                                </div>
                                            <?php } ?>


                                            <input type="hidden" name="sch_id" value="<?php echo $users->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="<?php echo site_url('Super_admin/shops') ?>" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>