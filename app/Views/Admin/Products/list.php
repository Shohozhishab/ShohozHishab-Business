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
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu; ?>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Products')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Products">
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
                                        <td><?php echo productIdByQuantity($products->prod_id) ?>
                                            / <?php echo showUnitName($products->unit) ?></td>
                                        <td><?php echo showWithCurrencySymbol($products->purchase_price) ?></td>
                                        <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                                        <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
                                        <td><?php echo $products->purchase_date ?></td>
                                        <td>
                                            <?php if (isset($update) && $update == 1){ ?>
                                            <a href="javascript:void(0)"
                                               onclick="showData('<?php echo site_url('/Admin/Products_ajax/update/' . $products->prod_id); ?>','<?php echo '/Admin/Products/update/' . $products->prod_id; ?>')"
                                               class="btn btn-warning btn-xs">Update</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>

                            <div class="row no-print" >
                                <div class="col-xs-12">
                                    <?php if (isset($print) && $print == 1){ ?>
                                    <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                    <?php } ?>
                                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','sales')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                    <?php } ?>
                                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','sales')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
                                <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                    <div class="col-xs-6">
                                        <?php if(logo_image() == NULL){ ?>
                                            <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                                        <?php }else{ ?>
                                            <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?php print address(); ?>
                                    </div>
                                </div>
                                <div class="col-md-12" >
                                    <table class="table table-bordered table-striped text-capitalize">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Purchase Price</th>
                                            <th>Supplier</th>
                                            <th>Prod Category</th>
                                            <th>Purchase Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1;
                                        foreach ($products_data as $products) { ?>
                                            <tr>
                                                <td><?php echo $products->name ?></td>
                                                <td><?php echo productIdByQuantity($products->prod_id) ?>
                                                    / <?php echo showUnitName($products->unit) ?></td>
                                                <td><?php echo showWithCurrencySymbol($products->purchase_price) ?></td>
                                                <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                                                <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
                                                <td><?php echo $products->purchase_date ?></td>
                                            </tr>
                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
