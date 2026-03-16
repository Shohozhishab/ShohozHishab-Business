
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
                                <h3 class="box-title">Return Sales List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="post" action="<?php echo site_url('Admin/Return_sale/invoice_search') ?>"  >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">search</button>
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
                                <th>No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($return_sale_data as $return) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($return->createdDtm) ?></td>
                                    <td><?php
                                        if ($return->customer_id) {
                                            echo get_data_by_id('customer_name', 'customers', 'customer_id', $return->customer_id) ;
                                        }else{
                                            echo $return->customer_name;
                                        }

                                        ?></td>
                                    <td><?php echo showWithCurrencySymbol($return->amount) ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Return_sale_ajax/view/'.$return->rtn_sale_id); ?>','<?php echo '/Admin/Return_sale/view/'.$return->rtn_sale_id; ?>')" class="btn btn-warning btn-xs">View</a>
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
