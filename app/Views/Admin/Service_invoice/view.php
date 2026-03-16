
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
                    <small class="pull-right">Date: <?php echo invoiceDateFormat($invoice->createdDtm);?></small>
                    <!-- <i class="fa fa-globe"></i> <?php //print $shopsName; ?>. -->
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
                        echo ($invoice->customer_id == 0 ) ? $invoice->customer_name : get_data_by_id('customer_name','customers','customer_id',$invoice->customer_id);

                        ?></strong><!-- <br>
            795 Folsom Ave, Suite 600<br>
            San Francisco, CA 94107<br>
            Phone: (555) 539-1037<br>
            Email: john.doe@example.com -->
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Invoice Id :</b> Inv_<?php echo $invoiceId?>
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
                        <th>Title</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=0;
                    foreach ($invoiceItame as $row) { ?>
                        <tr>
                            <td><?php echo ++$i;?></td>
                            <td>
                                <?php echo $row->title; ?>
                            </td>
                            <td><?php echo showWithCurrencySymbol($row->price);?></td>
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
                <p class="lead">Payment Methods:</p>
                <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg" alt="cash">
                <img src="<?php print base_url(); ?>/dist/img/credit/bank.png" alt="bank">
                <img src="<?php print base_url(); ?>/dist/img/credit/cheque.jpg" alt="cheque">

                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg
                    dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                </p>
                <?php $userId = $invoice->createdBy; ?>
                <p>Created By: <?php echo get_data_by_id('name','users','user_id',$userId);?></p>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <p class="lead">Amount Due <?php echo invoiceDateFormat($invoice->createdDtm);?></p>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th style="width:50%">Total:</th>
                            <td><?php echo showWithCurrencySymbol($invoice->amount);?></td>
                        </tr>

                        <tr>
                            <th>Entire Sale discount (%)</th>
                            <td><?php echo $invoice->entire_sale_discount;?></td>
                        </tr>
                        <tr>
                            <th>Subtotal:</th>
                            <td><?php echo showWithCurrencySymbol($invoice->final_amount);?></td>
                        </tr>

                        <?php

                        $nagadPay = $invoice->nagad_paid;
                        if ($nagadPay != 0) {
                            echo '<tr>
		                <th>Cash Pay:</th>
		                <td>'.showWithCurrencySymbol($nagadPay).'</td>
		              </tr>';
                        }

                        $bankPay = $invoice->bank_paid;
                        if ($bankPay != 0) {
                            echo '<tr>
		                <th>Bank Pay:</th>
		                <td>'.showWithCurrencySymbol($bankPay).'</td>
		              </tr>';
                        }

                        $chaquePay = $invoice->chaque_paid;
                        if ($chaquePay != 0) {
                            echo '<tr>
		                <th>Cheque Pay:</th>
		                <td>'.showWithCurrencySymbol($chaquePay).'</td>
		              </tr>';
                        }

                        ?>

                        <tr>
                            <th>Today Due:</th>
                            <td><?php echo showWithCurrencySymbol($invoice->due);?></td>
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
