
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Invoice <small>Invoice</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Invoice</li>
        </ol>

        <div class="row">
            <div class="col-xs-12" style="margin-top: 15px;">
                <?php echo $menu;?>
            </div>
        </div>
    </section>

    <section class="invoice">
        <!-- title row -->
        <div class="row">

            <div class="col-xs-12">
                <h2 class="page-header">
                    <small class="pull-right">Date: <?php echo invoiceDateFormat($returnSaleData->createdDtm);?></small>
                    <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" width="200" alt="<?php print $shopsName; ?>">

                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                From
                <address>
                    <strong><?php print $shopsName; ?></strong><!-- <br>
            795 Folsom Ave, Suite 600<br>
            San Francisco, CA 94107<br>
            Phone: (804) 123-5432<br>
            Email: info@almasaeedstudio.com -->
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                To
                <address>
                    <?php ?>
                    <strong><?php
                        $customerId = get_data_by_id('customer_id','return_sale','rtn_sale_id',$returnSaleData->rtn_sale_id);
                        echo get_data_by_id('customer_name','customers','customer_id',$customerId);

                        ?></strong>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Return Id :</b> <?php echo $returnSaleData->rtn_sale_id;?>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=0;
                    foreach ($invoiceItame as $row) { ?>
                        <tr>
                            <td><?php echo ++$i;?></td>
                            <td><?php
                                $catId =  get_data_by_id('prod_cat_id','products','prod_id',$row->prod_id);
                                $parent_pro_cat = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$catId);
                                $category = get_data_by_id('product_category','product_category','prod_cat_id',$parent_pro_cat);
                                $subCategory = get_data_by_id('product_category','product_category','prod_cat_id',$catId);
                                $productName =  get_data_by_id('name','products','prod_id',$row->prod_id);
                                $unit =  get_data_by_id('unit','products','prod_id',$row->prod_id);

                                echo $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>';
                                ?></td>
                            <td><?php echo showWithCurrencySymbol($row->price);?></td>
                            <td><?php echo $row->quantity;?>/<?php echo showUnitName($unit) ?></td>
                            <td><?php echo showWithCurrencySymbol($row->total_price);?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">

            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <p class="lead">Amount Due </p>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th style="width:50%">Total:</th>
                            <td><?php echo showWithCurrencySymbol($returnSaleData->amount);?></td>
                        </tr>
                        <tr>
                            <th>Nagad:</th>
                            <td><?php echo showWithCurrencySymbol($returnSaleData->nagad_paid);?></td>
                        </tr>
                        <tr>
                            <th>Bank:</th>
                            <td><?php echo showWithCurrencySymbol($returnSaleData->bank_paid);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>

            </div>
        </div>
    </section>
</div>
