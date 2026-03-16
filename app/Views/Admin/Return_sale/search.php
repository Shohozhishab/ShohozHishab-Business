
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Return Sales <small>Return Sales List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Return Sales</li>
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
                            <div class="col-lg-6">
                                <h3 class="box-title">Return Sales Invoice List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="POST" action="<?php echo site_url('Admin/Return_sale/invoice_search') ?>" >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;"  class="btn btn-primary " type="submit">search</button>
                                    </div>
                                    <div class="col-lg-8 pull-right">
                                        <label>Input InvoiceId</label>
                                        <input type="text" class="form-control" name="invoiceId" id="invoiceId"  required>
                                    </div>
                                </form>
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
                                <th>Invoice ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($invoice_data as $invoice) { ?>
                                <tr>
                                    <td width="80px"><?php echo $invoice->invoice_id ?></td>
                                    <td><?php echo ($invoice->customer_id == NULL) ? $invoice->customer_name : get_data_by_id('customer_name','customers','customer_id',$invoice->customer_id) ;?></td>
                                    <td><?php echo showWithCurrencySymbol($invoice->final_amount) ?></td>
                                    <td><?php echo ($invoice->due == 0) ? "<button class='btn btn-xs btn-success'>Paid</button>" : showWithCurrencySymbol($invoice->due); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/'.$invoice->invoice_id); ?>','<?php echo '/Admin/Invoice/view/'.$invoice->invoice_id; ?>')" class="btn btn-info btn-xs">View</a>

                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Return_sale_ajax/return/'.$invoice->invoice_id); ?>','<?php echo '/Admin/Return_sale/return/'.$invoice->invoice_id; ?>')" class="btn btn-warning btn-xs"><i class="fa fa-pencil-square-o"></i> Return</a>
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
