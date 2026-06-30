<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Users  <small>Users List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Users</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <a href="#" onclick="showData('<?php echo site_url('/Admin/User_ajax/create/'); ?>','<?php echo '/Admin/User/create/';?>')"  class="btn btn-default">Add</a>

            <a href="#" onclick="showData('<?php echo site_url('/Admin/Role_ajax') ?>','/Admin/Role')" class="btn btn-default">User Role</a>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'User')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="User">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Users List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/User_ajax/create/'); ?>','<?php echo '/Admin/User/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
                                <?php } ?>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start = 1; foreach ($users_data as $users) { ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $users->name ?></td>
                                    <td><?php echo showWithPhoneNummberCountryCode($users->mobile) ?></td>
                                    <td><?php echo get_data_by_id('role','roles','role_id',$users->role_id) ?></td>
                                    <td><?php echo ($users->status == 1)?'<button class="btn btn-xs btn-info">Active</button>':'<button class="btn btn-xs btn-danger">Inactive</button>'; ?></td>
                                    <td>
                                        <?php if (isset($read) && $read == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/view/' . $users->user_id); ?>','<?php echo '/Admin/User/view/' . $users->user_id; ?>')"
                                           class="btn btn-primary btn-xs">View</a>
                                        <?php } ?>
                                        <?php if (isset($update) && $update == 1){ ?>
                                        <?php if (is_default($users->user_id,'user_id', 'users') != 1) { ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/update/' . $users->user_id); ?>','<?php echo '/Admin/User/update/' . $users->user_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

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
