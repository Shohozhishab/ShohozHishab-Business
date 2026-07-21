
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Unit Categories <small>Unit Categories List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Unit Categories</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Unit_categories')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Unit_categories">
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
                                <h3 class="box-title">Unit Categories List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_categories_ajax/create/'); ?>','<?php echo '/Admin/Unit_categories/create/'; ?>')" class="btn btn-block btn-primary">Add</a>
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
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($categories as $val) { $isDeletable = is_deletable('units','unit_categories_id',$val->unit_categories_id); ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $val->name; ?></td>
                                    <td><?= $val->description; ?></td>
                                    <td>
                                        <?php if (isset($update) && $update == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_categories_ajax/update/'.$val->unit_categories_id); ?>','<?php echo '/Admin/Unit_categories/update/'.$val->unit_categories_id ?>')" class="btn btn-warning btn-xs">Update</a>
                                        <?php } ?>
                                        <?php if (isset($delete) && $delete == 1){ ?>
                                            <?php if($isDeletable == true){ ?>
                                                <a href="<?php echo site_url('/Admin/Unit_categories/delete/' . $val->unit_categories_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
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
