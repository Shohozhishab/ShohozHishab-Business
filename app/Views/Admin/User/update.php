<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> User <small>User Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Update </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Role_ajax') ?>','/Admin/Role')" class="btn btn-default">User Role</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">User Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>

                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active "><a href="#home" aria-controls="home"
                                                                               role="tab" data-toggle="tab">Registration Info</a></li>
                                    <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab"
                                                                data-toggle="tab">Personal</a></li>
                                    <li role="presentation" ><a href="#messages" aria-controls="messages" role="tab"
                                                                data-toggle="tab">Photo</a></li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content " style="margin-top: 60px;">
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    <div class="col-lg-6 ">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/User/register_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Name</label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $user->name; ?>" >
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Email</label>
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $user->email; ?>" >
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Password</label>
                                                <input type="password" class="form-control" name="password" id="password" placeholder="Password"  required>
                                            </div>
                                            <div class="form-group">
                                                <label for="longtext">Confirm Password </label>
                                                <input type="password" class="form-control" name="con_password" id="con_password" placeholder="Confirm Password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="enum">Status </label>
                                                <select class="form-control" name="status" id="status">
                                                    <?php print globalStatus($user->status); ?>
                                                </select>
                                            </div>


                                            <input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>" >
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn"> Update </button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/'; ?>')" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="profile">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/User/personal_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Phone </label>
                                                <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone" value="<?php echo $user->mobile; ?>" />
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Address</label>
                                                <input type="text" class="form-control" name="address" id="address" placeholder="Address" value="<?php echo $user->address; ?>" />
                                            </div>

                                            <div class="form-group">
                                                <label for="enum">Role Id </label>
                                                <select class="form-control" name="role_id" id="role_id">
                                                    <option value="">Please Select</option>
                                                    <?php echo getRoleIdListInOption($user->role_id,'role_id','role','roles'); ?>
                                                </select>
                                            </div>

                                            <input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>" />

                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="messages">
                                    <div class="col-lg-6">
                                        <form id="geniusform" action="<?php echo base_url('Admin/User/photo_update'); ?>" method="post" enctype="multipart/form-data" >
                                            <div class="form-group text-center" id="reloadimg">
                                                <?php if ($user->pic) { ?>
                                                    <img src="<?php print base_url(); ?>/uploads/users_image/<?php echo $user->pic; ?>" width="150">
                                                <?php }else{ ?>
                                                    <img src="<?php print base_url(); ?>/uploads/users_image/no_image.jpg" width="150" >
                                                <?php } ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Photo </label>
                                                <input type="file" class="form-control" name="pic" id="pic" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=300px) x (height=300px)</b></span>
                                            </div>

                                            <input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>" />

                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/'; ?>')" class="btn btn-default">Cancel</a>
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