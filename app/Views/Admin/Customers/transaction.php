<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Customer Transaction  <small>Customer Transaction</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Customer Transaction</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax'); ?>','<?php echo '/Admin/Customers';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax') ?>','/Admin/Customer_type')" class="btn btn-default">Customer type</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Customer Transaction</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12"  >
                                <div style="float: right;">
                                    <h4>Name: <?php echo  get_data_by_id('customer_name','customers','customer_id', $id); ?><br></br>Phone: <?php echo  showWithPhoneNummberCountryCode(get_data_by_id('mobile','customers','customer_id', $id)); ?></h4>
                                    <h4> Balance: <?php echo showWithCurrencySymbol(get_data_by_id('balance','customers','customer_id', $id)); ?></h4>
                                    <h4></h4>
                                </div>

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
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>invoice Id/Memo</th>
                                <th>Debit <small>( খরচ )</small></th>
                                <th>Credit <small>( জমা )</small></th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=''; foreach ($transaction as $row) {
                                if(($row->invoice_id == NULL) && ($row->trans_id == NULL)){
                                    $invoice_id = '---';
                                }else{
                                    $invoice_id = ($row->invoice_id == NULL) ? '<a href="' . site_url('Admin/Transaction/moneyReceipt/' . $row->trans_id) . '">TRNS_' . $row->trans_id . '</a>' : '<a href="' . site_url('Admin/Invoice/view/' . $row->invoice_id) . '">INV_' . $row->invoice_id . '</a>';
                                }

                                ?>
                                <tr>
                                    <td><?php echo $row->ledg_id?></td>
                                    <td><?php echo $row->createdDtm?></td>
                                    <td><?php echo ($row->particulars == NULL) ? "Sale" : $row->particulars;?></td>
                                    <td><?php echo $invoice_id?></td>
                                    <td><?php echo ($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount)?></td>
                                    <td><?php echo ($row->trangaction_type != "Cr.")?"---":showWithCurrencySymbol($row->amount) ?></td>
                                    <td><?php echo showWithCurrencySymbol($row->rest_balance)?></td>
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
