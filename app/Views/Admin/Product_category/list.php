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
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Product Category List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/create/'); ?>','<?php echo '/Admin/Product_category/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
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
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/update/'.$product_category->prod_cat_id); ?>','<?php echo '/Admin/Product_category/update/'.$product_category->prod_cat_id; ?>')"  class="btn btn-xs btn-info">Update</a>
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
