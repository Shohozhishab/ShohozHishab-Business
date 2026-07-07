
    <div class="content-wrapper" id="viewpage">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> Customer Type <small>Customer Type List</small></h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Customer Type</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax/create/') ?>','/Admin/Customer_type/create/')" class="btn btn-default">Add</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax'); ?>','<?php echo '/Admin/Customers';?>')"  class="btn btn-default">Customer</a>
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
                                            <select class="form-control" onchange="rolePermission(this.value,'Customer_type')" name="role_id">
                                                <option value="">Please Select</option>
                                                <?php  foreach (userRole() as $val ){ ?>
                                                    <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="moduleName" value="Customer_type">
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
                                    <h3 class="box-title">Customer Type List</h3>
                                </div>
                                <?php if (isset($create) && $create == 1){ ?>
                                <div class="col-lg-3">
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax/create/'); ?>','<?php echo '/Admin/Customer_type/create/'; ?>')" class="btn btn-block btn-primary">Add</a>
                                </div>
                                <?php } ?>
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
                                    <th>Type Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;
                                foreach ($cusType as $val) { ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo $val->type_name; ?></td>
                                        <td>
                                            <?php if (isset($update) && $update == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax/update/'.$val->cus_type_id); ?>','<?php echo '/Admin/Customer_type/update/'.$val->cus_type_id; ?>')" class="btn btn-warning btn-xs">Update</a>
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
