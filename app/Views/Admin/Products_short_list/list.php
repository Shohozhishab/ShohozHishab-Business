
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products Short List <small>Products Short List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products Short List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Products Short List</h3>
                            </div>
                            <div class="col-lg-3"></div>
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
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Supplier</th>
                                <th>Prod Category </th>
                                <th>Purchase Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($products_data as $products) { ?>
                                <tr>
                                    <td width="40px"><?php echo $i++ ?></td>
                                    <td><?php echo $products->name ?></td>
                                    <td><?php echo $products->total_quantity ?>/ <?php echo showUnitName($products->unit) ?></td>
                                    <td><?php echo showWithCurrencySymbol($products->purchase_price) ?></td>
                                    <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $products->supplier_id) ?></td>
                                    <td><?php echo get_data_by_id('product_category', 'product_category', 'prod_cat_id', $products->prod_cat_id) ?></td>
                                    <td><?php echo $products->purchase_date ?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','sales')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','sales')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
                                <table id="example1" class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Purchase Price</th>
                                        <th>Supplier</th>
                                        <th>Prod Category </th>
                                        <th>Purchase Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; foreach ($products_data as $products) { ?>
                                        <tr>
                                            <td width="40px"><?php echo $i++ ?></td>
                                            <td><?php echo $products->name ?></td>
                                            <td><?php echo $products->total_quantity ?>/ <?php echo showUnitName($products->unit) ?></td>
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
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
