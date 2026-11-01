
<?php
// Preload sold/returned serial numbers and the customer warranty end date for
// this invoice, grouped by stock line (product_stock_relation_id) so each item
// row can show its serial number(s) and warranty date.
$invoiceSerials = array();
if (!empty($invoiceId)) {
    $serialRows = DB()->table('product_serial')
        ->select('product_stock_relation_id, serial_number, customer_warranty_end_date')
        ->where('invoice_id', $invoiceId)
        ->whereIn('status', array('sold', 'returned'))
        ->orderBy('product_serial_id', 'ASC')
        ->get()
        ->getResultArray();
    foreach ($serialRows as $sr) {
        $key = (string) $sr['product_stock_relation_id'];
        if (!isset($invoiceSerials[$key])) {
            $invoiceSerials[$key] = array('serials' => array(), 'warranty' => '');
        }
        $invoiceSerials[$key]['serials'][] = $sr['serial_number'];
        if ($invoiceSerials[$key]['warranty'] === '' && !empty($sr['customer_warranty_end_date'])) {
            $invoiceSerials[$key]['warranty'] = $sr['customer_warranty_end_date'];
        }
    }
}
?>
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
    <style>
        @media print {
            .content-header,
            .no-print,
            .main-header,
            .main-sidebar,
            .main-footer {
                display: none !important;
            }
            body,
            .wrapper,
            .content-wrapper,
            .content {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                float: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
            .box {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .table-bordered,
            .table-bordered > tbody > tr > td,
            .table-bordered > thead > tr > th {
                border-color: #000 !important;
            }
            .invoice {
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px !important;
            }
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }
    </style>
    <?php
    // Invoice logo (falls back to the shop logo, then to no_image_logo.jpg)
    $invLogoRow = DB()->table('gen_settings')->where('label', 'invoice_logo')->where('sch_id', $_SESSION['shopId'])->get()->getRow();
    $invLogo = $invLogoRow ? (string) $invLogoRow->value : '';
    ?>
    <section class="invoice" id="invoicePrint">
        <!-- title row -->
        <div class="row">

            <div class="col-xs-12">
                <h2 class="page-header">
                    <small class="pull-right">Date: <?php echo invoiceDateFormat(get_data_by_id('createdDtm','invoice','invoice_id',$invoiceId));?></small>
                    <!-- <i class="fa fa-globe"></i> <?php //print $shopsName; ?>. -->
                    <img src="<?php echo no_image_view('/uploads/schools/' . $invLogo, '/uploads/schools/' . logo_image(), $invLogo); ?>" class="" width="200" alt="<?php print $shopsName; ?>">

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
                        $customerId = get_data_by_id('customer_id','invoice','invoice_id',$invoiceId);
                        echo ($customerId == 0 ) ? get_data_by_id('customer_name','invoice','invoice_id',$invoiceId) : get_data_by_id('customer_name','customers','customer_id',$customerId);

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
                        <th>Product</th>
                        <th>Warranty Date</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <?php if ($discount == 1) { ?>
                            <th>Discount</th>
                            <th>Subtotal</th>
                        <?php } ?>
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
                                $unit =  productIdByDefaultStoreUnit($row->prod_id);

                                echo $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>';

                                // Serial number(s) recorded on this invoice line.
                                $psrKey = (string) ($row->product_stock_relation_id ?? '');
                                $lineSerial = $invoiceSerials[$psrKey] ?? null;
                                if ($lineSerial && !empty($lineSerial['serials'])) {
                                    echo '<br><small><b>Serial:</b> ' . esc(implode(', ', $lineSerial['serials'])) . '</small>';
                                }
                                ?></td>
                            <td><?php echo ($lineSerial && $lineSerial['warranty'] !== '') ? esc($lineSerial['warranty']) : '---';?></td>
                            <td><?php echo showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit,$row->price));?></td>
                            <td><?php echo unitOrQtyByUnitQty($unit,$row->quantity);?>/<?php echo showUnitName($unit) ?></td>
                            <td><?php echo showWithCurrencySymbol($row->total_price);?></td>
                            <?php if ($discount == 1) { ?>
                                <td><?php echo $row->discount;?></td>
                                <td><?php echo showWithCurrencySymbol($row->final_price);?></td>
                            <?php } ?>
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
                <?php $userId = get_data_by_id('createdBy','invoice','invoice_id',$invoiceId); ?>
                <p>Created By: <?php echo get_data_by_id('name','users','user_id',$userId);?></p>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <p class="lead">Amount Due <?php echo invoiceDateFormat(get_data_by_id('createdDtm','invoice','invoice_id',$invoiceId));?></p>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th style="width:50%">Total:</th>
                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','invoice','invoice_id',$invoiceId));?></td>
                        </tr>
                        <?php if ($discount == 1) { ?>
                            <tr>
                                <th>Entire Sale discount (%)</th>
                                <td><?php echo get_data_by_id('entire_sale_discount','invoice','invoice_id',$invoiceId);?></td>
                            </tr>
                            <tr>
                                <th>Vat (<?php echo get_data_by_id('vat','invoice','invoice_id',$invoiceId);?> %)</th>
                                <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','ledger_vat','invoice_id',$invoiceId));?> </td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td><?php echo showWithCurrencySymbol(get_data_by_id('final_amount','invoice','invoice_id',$invoiceId));?></td>
                            </tr>
                        <?php }?>
                        <?php

                        $nagadPay = get_data_by_id('nagad_paid','invoice','invoice_id',$invoiceId);
                        if ($nagadPay != 0) {
                            echo '<tr>
		                <th>Cash Pay:</th>
		                <td>'.showWithCurrencySymbol($nagadPay).'</td>
		              </tr>';
                        }

                        $bankPay = get_data_by_id('bank_paid','invoice','invoice_id',$invoiceId);
                        if ($bankPay != 0) {
                            $bankDetail = getTotalRow('bank', 'bank_id', $invoiceData->bank_id);
                            echo '<tr>
		                <th>Bank Pay:
		                        <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                        <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <span class="tooltip-text">'. $bankDetail->name.'--'. $bankDetail->account_no .'</span>
                                </div>
		                </th>
		                <td>'.showWithCurrencySymbol($bankPay).'</td>
		              </tr>';
                        }

                        $chaquePay = get_data_by_id('chaque_paid','invoice','invoice_id',$invoiceId);
                        if ($chaquePay != 0) {
                            echo '<tr>
		                <th>Cheque Pay:</th>
		                <td>'.showWithCurrencySymbol($chaquePay).'</td>
		              </tr>';
                        }

                        ?>

                        <tr>
                            <th>Today Due:</th>
                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due','invoice','invoice_id',$invoiceId));?></td>
                        </tr>
                        <!-- <tr>
                <th>Previous Due:</th>
                <td><?php //echo showWithCurrencySymbol($oldDue);?></td>
              </tr>
              <tr>
                <th>Total Due:</th>
                <td><?php //echo showWithCurrencySymbol($totalDue);?></td>
              </tr> -->
                        </tbody></table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <?php if (isset($print) && $print == 1){ ?>
            <!-- this row will not appear when printing -->
            <div class="row no-print" data-html2canvas-ignore="true">
                <div class="col-xs-12">
                    <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>
                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('invoicePrint','Invoice','Inv_<?= $invoiceId ?>.pdf')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="sharePDFChoose('invoicePrint','Invoice','Inv_<?= $invoiceId ?>.pdf')"><i class="fa fa-share-square-o"></i> Share </button>

                </div>
            </div>
        <?php } ?>
    </section>
</div>
