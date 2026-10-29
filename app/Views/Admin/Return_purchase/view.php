
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase <small>Purchase</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase</li>
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
                    <small class="pull-right">Date: <?php echo invoiceDateFormat($returnPurchaseData->createdDtm);?></small>
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
                    <strong><?php print $shopsName; ?></strong>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                To
                <address>
                    <strong><?php
                        $supplierId = get_data_by_id('supplier_id','return_purchase','rtn_purchase_id',$returnPurchaseData->rtn_purchase_id);
                        echo get_data_by_id('name','suppliers','supplier_id',$supplierId);

                        ?></strong>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Return Id :</b> <?php echo $returnPurchaseData->rtn_purchase_id;?>
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
                        <th>Serial Numbers</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=0;
                    foreach ($purchaseItame as $row) { ?>
                        <tr>
                            <td><?php echo ++$i;?></td>
                            <td><?php
                                $catId =  get_data_by_id('prod_cat_id','products','prod_id',$row->prod_id);
                                $parent_pro_cat = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$catId);
                                $category = get_data_by_id('product_category','product_category','prod_cat_id',$parent_pro_cat);
                                $subCategory = get_data_by_id('product_category','product_category','prod_cat_id',$catId);
                                $productName =  get_data_by_id('name','products','prod_id',$row->prod_id);

                                echo $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>';
                                ?></td>
                            <td><?php
                                $returnedSerials = get_return_purchase_item_serials($row->rtn_purchase_id, $row->prod_id);
                                if (empty($returnedSerials)) {
                                    echo '-';
                                } else {
                                    foreach ($returnedSerials as $serial) {
                                        echo esc($serial['serial_number']);
                                        if (!empty($serial['customer_warranty_end_date'])) {
                                            echo ' <small class="text-muted">(' . esc($serial['customer_warranty_end_date']) . ')</small>';
                                        } elseif (!empty($serial['company_warranty_end_date'])) {
                                            echo ' <small class="text-muted">(' . esc($serial['company_warranty_end_date']) . ')</small>';
                                        }
                                        echo '<br>';
                                    }
                                }
                                ?></td>
                            <td><?php echo $row->price;?></td>
                            <td><?php echo $row->quantity;?></td>
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
                            <td><?php echo showWithCurrencySymbol($returnPurchaseData->amount);?></td>
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
