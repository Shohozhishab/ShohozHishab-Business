<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Products List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu; ?>
            </div>
            <div class="col-xs-12">
                <form action="<?php echo base_url('Admin/Products/barcode') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-4">
                                    <h3 class="box-title">Products List</h3>
                                </div>
                                <div class="col-lg-8">
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/add_existing_product/'); ?>','<?php echo '/Admin/Products/add_existing_product/'; ?>')" class="btn btn-info pull-right btn-xs" style="margin-left:10px;"><i class="fa fa-fw fa-tasks"></i> Add Existing Product</a>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_short_list_ajax/'); ?>','<?php echo '/Admin/Products_short_list/'; ?>')" class="btn btn-danger pull-right btn-xs" style="margin-left:10px;"><i class="fa fa-fw fa-tasks"></i> Short List</a>

                                    <button type="submit" class="btn btn-primary pull-right btn-xs"><i class="fa fa-barcode"></i> Barcode Generate</button>

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
                                    <th>Barcode Qty</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Purchase Price</th>
                                    <th>Supplier</th>
                                    <th>Prod Category</th>
                                    <th>Purchase Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;
                                foreach ($products_data as $products) { ?>
                                    <tr>
                                        <td>
                                            <input type="number" name="barcodeqty[<?php print $products->prod_id; ?>]"
                                                   style="width: 40px;" value="0">
                                        </td>
                                        <td><?php echo $products->name ?></td>
                                        <td><?php echo $products->quantity ?>
                                            /<?php echo showUnitName($products->unit) ?></td>
                                        <td><?php echo showWithCurrencySymbol($products->purchase_price) ?></td>
                                        <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                                        <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
                                        <td><?php echo $products->purchase_date ?></td>
                                        <td>
                                            <a href="javascript:void(0)"
                                               onclick="showData('<?php echo site_url('/Admin/Products_ajax/update/' . $products->prod_id); ?>','<?php echo '/Admin/Products/update/' . $products->prod_id; ?>')"
                                               class="btn btn-warning btn-xs">Update</a>
                                        </td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </form>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
