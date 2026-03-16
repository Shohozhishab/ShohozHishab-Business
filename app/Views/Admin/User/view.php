<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Users  <small>Users View</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Users</li>
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
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Users View</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped text-capitalize">
                            <tbody>

                                <tr>
                                    <td>Email</td>
                                    <td><?php echo $user->email ?></td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td><?php echo $user->name ?></td>
                                </tr>
                                <tr>
                                    <td>Role</td>
                                    <td><?php echo get_data_by_id('role','roles','role_id',$user->role_id) ?></td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td><?php echo showWithPhoneNummberCountryCode($user->mobile) ?></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td><?php echo $user->address ?></td>
                                </tr>
                                <tr>
                                    <td>Pic</td>
                                    <td><?php if ($user->pic) { ?><img src="<?php print base_url(); ?>/uploads/users_image/<?php echo $user->pic; ?>" width="100" ><?php }else{ ?><img src="<?php print base_url(); ?>/uploads/users_image/no_image.jpg" width="100" ><?php } ?></td>
                                </tr>

                                <tr>
                                    <td>Status</td>
                                    <td><?php echo ($user->status == 1)?'<button class="btn btn-xs btn-info">Active</button>':'<button class="btn btn-xs btn-danger">Inactive</button>'; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/'; ?>')" class="btn btn-default">Cancel</a></td>
                                </tr>

                            </tbody>
                        </table>
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
