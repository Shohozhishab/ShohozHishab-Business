<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small>Transaction view</small></h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax'); ?>','<?php echo '/Admin/Transaction';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Transaction view</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <table class="table" id="TFtable">
                            <?php
                            echo ($trans->customer_id == NULL) ? " ":"<tr><td>Customer</td><td>".get_data_by_id('customer_name','customers','customer_id',$trans->customer_id)."</td></tr>";
                            echo ($trans->loan_pro_id == NULL) ? " ":"<tr><td>Loan Provider</td><td>".get_data_by_id('name','loan_provider','loan_pro_id',$trans->loan_pro_id)."</td></tr>" ;
                            echo ($trans->supplier_id == NULL) ? " ":"<tr><td>Supplier</td><td>".get_data_by_id('name','suppliers','supplier_id',$trans->supplier_id)."</td></tr>" ;
                            ?>


                            <tr><td>Title</td><td><?php echo $trans->title; ?></td></tr>
                            <tr><td>Description</td><td><?php echo $trans->description; ?></td></tr>
                            <tr><td>Trangaction Type</td><td><?php echo $trans->trangaction_type; ?></td></tr>
                            <tr><td>Amount</td><td><?php echo showWithCurrencySymbol($trans->amount); ?></td></tr>
                            <tr><td>Created</td><td><?php echo invoiceDateFormat($trans->createdDtm); ?></td></tr>
                            <tr><td>Created By</td><td><?php echo get_data_by_id('name','users','user_id',$trans->createdBy); ?></td></tr>
                            <tr><td></td><td><a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax') ?>','/Admin/Transaction')" class="btn btn-default">Cancel</a></td></tr>
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
