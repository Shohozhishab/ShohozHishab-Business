<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Product Category  <small>Product Category List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Product Category</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Product_category')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Product_category">
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
                                <h3 class="box-title">Product Category List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/create/'); ?>','<?php echo '/Admin/Product_category/create/'; ?>')"
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
                                <th>Category Id</th>
                                <th>Product Category</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($product_category_data as $product_category) {
                                if (!empty($product_category->parent_pro_cat)){
                                    $pCat = get_data_by_id('product_category','product_category','prod_cat_id',$product_category->parent_pro_cat).' >';
                                }else{
                                    $pCat = '';
                                }
                            ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $product_category->prod_cat_id ?></td>
                                    <td><?php echo $pCat;?> <?php echo $product_category->product_category ?> </td>
                                    <td><?php echo ($product_category->status == 1) ? '<button class="btn btn-xs btn-info">Active</button>' : '<button class="btn btn-xs btn-danger">Inactive</button>'; ?></td>
                                    <td width="180px">
                                        <?php if (isset($update) && $update == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/update/'.$product_category->prod_cat_id); ?>','<?php echo '/Admin/Product_category/update/'.$product_category->prod_cat_id; ?>')"  class="btn btn-xs btn-info">Update</a>
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
