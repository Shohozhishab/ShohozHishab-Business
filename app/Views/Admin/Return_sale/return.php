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
                <form action="<?php echo $action; ?>" method="post" onsubmit="return validateReturnForm()">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Return Product List</h3>
                                </div>
                                <div class="col-lg-6"></div>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                                <div id="errorMsg"></div>
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
                                <?php foreach ($invoice_item as $row) {   $availableReturnQty = get_available_quantity_to_return($invoiceId,$row->prod_id,$row->quantity); ?>
                                    <tr role="row" class="odd">
                                        <td>
                                            <input type="checkbox" oninput="calculateTotalPrice()" name="returnchecked[]" class="datatablesReturn" id="checkedProd" value="<?php echo $row->prod_id; ?>" <?= empty($availableReturnQty)?'disabled':'';?> >
                                            <input type="hidden" name="product_stock_relation_id[]" value="<?= $row->product_stock_relation_id;?>">
                                        </td>
                                        <td><?php echo get_data_by_id('name', 'products', 'prod_id', $row->prod_id) ?></td>
                                        <td><input type="number" class="quantityReturn form-control" oninput="calculateTotalPrice()" id="quantity"
                                                   name="quantity[]" placeholder="Quantity" min="<?= empty($availableReturnQty)?0:1;?>" max="<?= $availableReturnQty;?>"
                                                   value="<?= $availableReturnQty ?>"> </td>
                                        <td><input type="text" class="purchase_priceReturn form-control" id="searchColumn"
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
                                        <?php
                                        $vatper = get_data_by_id('vat', 'invoice', 'invoice_id', $invoiceId);
                                        if (!empty($vatper)){
                                            ?>
                                            <div class="col-xs-6">

                                                <input type="hidden" name="vat" id="vat" value="<?php echo $vatper; ?>">
                                                <label for="int">Vat (<?php echo $vatper; ?>%)</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" id="vatAmount" name="vatAmount" value="" readonly>
                                            </div>

                                            <?php
                                        }
                                        $discount = get_data_by_id('entire_sale_discount', 'invoice', 'invoice_id', $invoiceId);
                                        if (!empty($discount)){
                                            ?>
                                            <div class="col-xs-6">

                                                <input type="hidden" name="discount" id="discount" value="<?php echo $discount; ?>">
                                                <label for="int">Discount (<?php echo $discount; ?>%)</label>
                                            </div>
                                            <div class="form-group col-xs-6">
                                                <input type="text" class="form-control" id="discountAmount" name="discountAmount" value="" readonly>
                                            </div>
                                        <?php } ?>


                                        <div class="col-xs-6">
                                            <label for="int">Total Amount</label>
                                        </div>
                                        <div class="form-group col-xs-6">
                                            <input type="text" class="form-control" name="totalPrice" id="totalAmount" readonly>
                                            <!-- <input type="hidden" class="form-control" name="totalPrice"  id="totalPrice" readonly > -->
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


                                    <button type="submit" class="btn btn-primary">Return</button>
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

<script>
    function validateReturnForm() {

        let checkedProduct = document.querySelectorAll('.datatablesReturn:checked').length;

        let message = '';

        // Product validation
        if (checkedProduct == 0) {

            message += 'Please select at least one product .<br>';

        }

        // Quantity validation
        let checkedRows = document.querySelectorAll('.datatablesReturn:checked');

        checkedRows.forEach(function (item) {

            let row = item.closest('tr');

            let qty = row.querySelector('.quantityReturn').value;

            let maxQty = row.querySelector('.quantityReturn').max;

            if (qty == '' || qty <= 0) {

                message += 'Quantity must be greater than 0 .<br>';

            }

            if (parseInt(qty) > parseInt(maxQty)) {

                message += 'Quantity cannot exceed stock .<br>';

            }

        });

        // Final validation
        if (message != '') {

            $('#errorMsg').html(
                '<div class="alert alert-danger">' + message + '</div>'
            );

            return false;

        }

        return true;

    }

    function calculateTotalPrice() {

        var totalPrice = 0;

        var checks = $("input.datatablesReturn");
        var qnt = $("input[name='quantity[]']");
        var pur_price = $("input[name='purchase_price[]']");

        // Calculate selected product total
        for (var i = 0; i < checks.length; i++) {

            if (checks[i].checked) {
                var quantity = parseFloat(qnt[i].value) || 0;
                var purchasePrice = parseFloat(pur_price[i].value) || 0;

                totalPrice += quantity * purchasePrice;
            }
        }

        // VAT %
        var vatPercent = parseFloat($("#vat").val()) || 0;

        // Discount %
        var discountPercent = parseFloat($("#discount").val()) || 0;

        // Discount amount
        var discountAmount = totalPrice * discountPercent / 100;

        // Amount after discount
        var afterDiscount = totalPrice - discountAmount;

        // VAT amount
        var vatAmount = afterDiscount * vatPercent / 100;

        // Final total
        var finalAmount = afterDiscount + vatAmount;

        // Set values
        $("#vatAmount").val(vatAmount.toFixed(8));
        $("#discountAmount").val(discountAmount.toFixed(8));

        $("#totalAmount").val(finalAmount.toFixed(8));
        $("#totalDueAmount").val(finalAmount.toFixed(8));
    }

</script>