<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Return Product <small>Return Product List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Return Product</li>
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
                <form action="<?php echo $action; ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Return Product List</h3>
                                </div>
                                <div class="col-lg-6"></div>
                            </div>


                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                                   aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Sale Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoice_item as $row) { ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" name="returnchecked[]" class="datatables"
                                                   id="checkedProd" value="<?php echo $row->prod_id; ?>">
                                            <input type="hidden" name="prod_id[]" value="<?php echo $row->prod_id ?>"></td>
                                        <td><?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><input type="number" class="quantity form-control" id="quantity"
                                                   name="quantity[]" placeholder="Quantity"
                                                   value="<?php echo $row->quantity ?>"></td>
                                        <td><input type="text" class="purchase_price form-control" id="searchColumn"
                                                   name="purchase_price[]" value="<?php echo $row->price ?>" readonly></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Sale Price</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Payment</h3>
                        </div>
                        <div class="box-body">

                                <div class="col-xs-12">
                                    <div class="col-xs-12" id="box_form">
                                        <div class="col-xs-8">
                                            <p class="lead">Payment Type:</p>
                                            <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg" alt="Cash">
                                            <img src="<?php print base_url(); ?>/dist/img/credit/bank.png" alt="Bank">

                                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning
                                                heekya handango imeem plugg
                                                dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                            </p>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="col-xs-6">
                                                <label for="int">Customer</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <?php foreach ($invoice as $row) {

                                                    if ($row->customer_id != NULL) { ?>

                                                        <input type="text" class="form-control"
                                                               value="<?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id) ?>"
                                                               readonly>
                                                    <?php } else { ?>

                                                        <input type="text" class="form-control"
                                                               value="<?php echo $row->customer_name ?>" readonly>

                                                    <?php }
                                                } ?>
                                            </div>

                                            <div class="col-xs-6">
                                                <?php $vatper = get_data_by_id('vat', 'invoice', 'invoice_id', $invoiceId); ?>
                                                <input type="hidden" name="vetper" id="vetper"
                                                       value="<?php echo $vatper; ?>">
                                                <label for="int">Vat (<?php echo $vatper; ?>%)</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" id="vatAmount" name="vatAmount"
                                                       value="">
                                            </div>


                                            <div class="col-xs-6">
                                                <label for="int">Total Amount</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" name="totalPrice" id="totalAmount"
                                                       readonly>
                                                <!-- <input type="hidden" class="form-control" name="totalPrice"  id="totalPrice" readonly > -->
                                            </div>
                                            <div class="col-xs-6">
                                                <label for="int">Cash</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" onchange="checkShopsBalance(this.value)"
                                                       class="cash form-control" name="cash" id="cash"><b
                                                        id="Balance_valid"></b>
                                            </div>
                                            <div class="col-xs-6">
                                                <label for="int">Bank</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <select class="form-control" name="bank_id" id="bank_id">
                                                    <option value="">Select Bank</option>
                                                    <?php echo getTwoValueInOption('', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                </select><br>
                                                <input type="text" onchange="checkBankBalance(this.value)"
                                                       class="bank form-control" name="bank" id="bank">
                                                <b id="Bank_valid"></b>
                                            </div>
                                            <div class="col-xs-6">
                                                <label for="int">Due</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" name="due" id="totalDueAmount"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12" id="box_form">
                                        <?php foreach ($invoice as $row) {

                                            if ($row->customer_id != NULL) { ?>

                                                <input type="hidden" name="customer_id"
                                                       value="<?php echo $row->customer_id ?>">
                                            <?php } else { ?>

                                                <input type="hidden" name="customer_name"
                                                       value="<?php echo $row->customer_name ?>">

                                            <?php } ?>
                                            <input type="hidden" name="invoice_id" value="<?php echo $row->invoice_id ?>">
                                        <?php } ?>


                                        <button type="submit" class="btn btn-primary">Create</button>
                                        <a href="<?php echo site_url('purchase') ?>" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>

                        </div>
                    </div>

                </form>


            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
