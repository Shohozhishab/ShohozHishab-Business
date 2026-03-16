<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Product Category <small>Product Category Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Product Category </li>
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
                        <h3 class="box-title">Product Category Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Category </label>
                                        <input type="text" class="form-control" name="product_category" id="product_category" placeholder="Product Category" value="<?php echo $product_category->product_category?>" required>
                                    </div>
                                    <div class="form-group" id="reloadimg">
                                        <label for="varchar">Parent Category </label>
                                        <select class="form-control" name="parent_pro_cat" >
                                            <option value="">Please Select</option>
                                            <?php echo subCategoryListOption($product_category->parent_pro_cat,'product_category','product_category'); ?>
                                        </select>
                                    </div>
                                    <div class="form-group" >
                                        <label for="enum">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <?php print globalStatus($product_category->status); ?>
                                        </select>
                                    </div>

                                    <input type="hidden" name="prod_cat_id" id="prod_cat_id" value="<?php echo $product_category->prod_cat_id?>" required>
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/'); ?>','<?php echo '/Admin/Product_category/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">

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