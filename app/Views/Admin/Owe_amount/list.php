<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Accounts Payable  <small>Accounts Payable List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Accounts Payable</li>
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
                                <h3 class="box-title">Accounts Payable List</h3>
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
                        <div class="col-xs-4">
                            <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($customer); ?></span>
                            <h4>Customer</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($customerData as $row) {
                                    if ($row->balance < 0) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo $row->customer_name ?></td>
                                            <td><?php echo showWithCurrencySymbol($row->balance) ?></td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </tfoot>
                            </table>
                            <a href="<?php echo base_url() ?>/Admin/Owe_amount/print_customer" class="btn btn-primary" style="float: right;">Print</a >
                        </div>

                        <div class="col-xs-4">
                            <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($supplier); ?></span>
                            <h4>Suppliers</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $j = 1; foreach ($supplierData as $view) {
                                    if ($view->balance < 0) {
                                        ?>
                                        <tr>
                                            <td><?php echo $j++ ?></td>
                                            <td><?php echo $view->name ?></td>
                                            <td><?php echo showWithCurrencySymbol($view->balance) ?></td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </tfoot>
                            </table>
                            <a href="<?php echo base_url() ?>/Admin/Owe_amount/print_supplier" class="btn btn-primary" style="float: right;">Print</a >
                        </div>

                        <div class="col-xs-4">
                            <span style="float:right; " >Total:<?php echo showWithCurrencySymbol($loanProvider); ?></span>
                            <h4>Account Head</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $l = 1; foreach ($loanProData as $value) {
                                    if ($value->balance < 0) {
                                        ?>
                                        <tr>
                                            <td><?php echo $l++ ?></td>
                                            <td><?php echo $value->name ?></td>
                                            <td><?php echo showWithCurrencySymbol($value->balance) ?></td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </tfoot>
                            </table>
                            <a href="<?php echo base_url() ?>/Admin/Owe_amount/print_account_holder" class="btn btn-primary" style="float: right;">Print</a >
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
