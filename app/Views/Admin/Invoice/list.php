
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Invoice <small>Invoice List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Invoice</li>
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
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Invoice List</h3>
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
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($invoice_data as $invoice) { ?>
                                <tr>
                                    <td><?php echo $invoice->invoice_id ?></td>
                                    <td><?php echo ($invoice->customer_id == NULL) ? $invoice->customer_name : get_data_by_id('customer_name','customers','customer_id',$invoice->customer_id) ;?></td>
                                    <td><?php echo showWithCurrencySymbol($invoice->final_amount) ?></td>
                                    <td><?php echo ($invoice->due == 0) ? "<button class='btn btn-xs btn-success'>Paid</button>" : showWithCurrencySymbol($invoice->due); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/'.$invoice->invoice_id); ?>','<?php echo '/Admin/Invoice/view/'.$invoice->invoice_id; ?>')" class="btn btn-warning btn-xs">View</a>
                                    </td>
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
