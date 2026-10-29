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
                                    <th>Serial Numbers</th>
                                    <th>Quantity</th>
                                    <th>Sale Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoice_item as $row) {
                                    $availableReturnQty = get_available_quantity_to_return($invoiceId, $row->prod_id, $row->quantity);
                                    $lineSerials = get_return_sale_line_serials($invoiceId, $row->product_stock_relation_id, $row->prod_id);
                                    $hasSerials = !empty($lineSerials['serials']);
                                    // Serialized lines can only return units whose serial is still sold on this invoice.
                                    $maxQty = $hasSerials ? min((float)$availableReturnQty, (float)$lineSerials['available_count']) : (float)$availableReturnQty;
                                    $rowDisabled = ($maxQty <= 0);
                                    $psrId = (int)$row->product_stock_relation_id;
                                    $productName = get_data_by_id('name', 'products', 'prod_id', $row->prod_id);
                                ?>
                                    <tr role="row" class="odd">
                                        <td>
                                            <input type="checkbox" oninput="calculateTotalPrice()" onchange="returnRowToggle(this)"
                                                   name="returnchecked[<?= $psrId; ?>]" class="datatablesReturn"
                                                   id="checkedProd<?= $psrId; ?>" value="<?= $row->prod_id; ?>" <?= $rowDisabled ? 'disabled' : '' ?>>
                                        </td>
                                        <td><span class="rs-product-name"><?= esc($productName) ?></span></td>
                                        <td>
                                            <?php if ($hasSerials): $preSelected = 0; ?>
                                                <div style="max-height:160px; overflow-y:auto; border:1px solid #ddd; border-radius:4px; padding:6px;">
                                                    <?php foreach ($lineSerials['serials'] as $serial):
                                                        $serialId = (int)$serial['product_serial_id'];
                                                        $isSold = ($serial['status'] === 'sold');
                                                        $isChecked = $isSold && ($preSelected < $maxQty);
                                                        if ($isChecked) { $preSelected++; }
                                                        $warrantyDate = !empty($serial['customer_warranty_end_date']) ? $serial['customer_warranty_end_date'] : $serial['company_warranty_end_date'];
                                                    ?>
                                                        <label style="display:block; font-weight:normal; margin:0 0 4px;">
                                                            <input type="checkbox" class="returnSerial" onchange="returnSerialChanged(this)"
                                                                   name="return_serials[<?= $psrId; ?>][]" value="<?= $serialId; ?>"
                                                                   <?= $isChecked ? 'checked="checked"' : '' ?> <?= $isSold ? '' : 'disabled' ?>>
                                                            <span><?= esc($serial['serial_number']) ?></span>
                                                            <?php if (!empty($warrantyDate)): ?>
                                                                <small class="text-muted">(Warranty: <?= esc($warrantyDate) ?>)</small>
                                                            <?php endif; ?>
                                                            <?php if (!$isSold): ?>
                                                                <span class="label label-default">Already Returned</span>
                                                            <?php endif; ?>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                                <p class="text-muted" style="margin:4px 0 0;">Selected:
                                                    <b class="returnSerialCount"><?= $preSelected; ?></b> of <?= (int)$lineSerials['available_count']; ?>
                                                </p>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><input type="number" class="quantityReturn form-control" oninput="calculateTotalPrice()" id="quantity<?= $psrId; ?>"
                                                   name="quantity[<?= $psrId; ?>]" placeholder="Quantity" min="<?= ($maxQty <= 0) ? 0 : 1; ?>" max="<?= $maxQty; ?>"
                                                   value="<?= $maxQty; ?>" <?= $hasSerials ? 'readonly data-serialized="1"' : '' ?>> </td>
                                        <td><input type="text" class="purchase_priceReturn form-control" id="searchColumn<?= $psrId; ?>"
                                                   name="purchase_price[<?= $psrId; ?>]" value="<?php echo (float)$row->price ?>" readonly></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Serial Numbers</th>
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
                                    <div class="col-xs-7">
                                        <p class="lead">Payment Type:</p>
                                        <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg" alt="Cash">
                                        <img src="<?php print base_url(); ?>/dist/img/credit/bank.png" alt="Bank">

                                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning
                                            heekya handango imeem plugg
                                            dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                        </p>
                                    </div>
                                    <div class="col-xs-5">
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
                                            <label for="int">Final Return Amount
                                                <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                                        <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    <span class="tooltip-text">Product-Discount+Vat</span>
                                                </div>
                                            </label>
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
                                    <a href="<?php echo site_url('Admin/Return_sale') ?>" class="btn btn-default">Cancel</a>
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
    // ---- Serial number reversal helpers (start) ----
    // Serialized lines are returned unit by unit: one checked serial = one unit.
    // The quantity box of such a line is readonly and always mirrors the picker.
    function returnEnabledSerials(row) {
        var boxes = row.querySelectorAll('.returnSerial');
        var enabled = [];
        for (var i = 0; i < boxes.length; i++) {
            if (!boxes[i].disabled) {
                enabled.push(boxes[i]);
            }
        }
        return enabled;
    }

    function returnCheckedSerialCount(row) {
        return row.querySelectorAll('.returnSerial:checked').length;
    }

    function returnSerialSync(row) {
        var checked = returnCheckedSerialCount(row);
        var qty = row.querySelector('.quantityReturn');
        var counter = row.querySelector('.returnSerialCount');
        if (qty && qty.getAttribute('data-serialized') === '1') {
            qty.value = checked;
        }
        if (counter) {
            counter.textContent = checked;
        }
    }

    function returnSerialChanged(el) {
        var row = el.closest('tr');
        var qty = row.querySelector('.quantityReturn');
        var max = parseInt(qty ? qty.getAttribute('max') : 0, 10) || 0;
        var checked = returnCheckedSerialCount(row);

        // Never allow more serials than the units that can still be returned.
        if (max > 0 && checked > max) {
            el.checked = false;
            checked = checked - 1;
        }

        var select = row.querySelector('.datatablesReturn');
        if (select && !select.disabled && checked > 0 && !select.checked) {
            select.checked = true;
        }

        returnSerialSync(row);
        calculateTotalPrice();
    }

    function returnRowToggle(cb) {
        var row = cb.closest('tr');
        var serials = returnEnabledSerials(row);
        if (serials.length === 0) {
            return;
        }

        var qty = row.querySelector('.quantityReturn');
        var max = parseInt(qty ? qty.getAttribute('max') : 0, 10) || 0;

        if (cb.checked) {
            if (returnCheckedSerialCount(row) === 0) {
                var limit = max > 0 ? max : serials.length;
                for (var i = 0; i < serials.length && i < limit; i++) {
                    serials[i].checked = true;
                }
            }
        } else {
            // Deselecting the line must not leave its serials in the form.
            for (var j = 0; j < serials.length; j++) {
                serials[j].checked = false;
            }
        }

        returnSerialSync(row);
        calculateTotalPrice();
    }
    // ---- Serial number reversal helpers (end) ----

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

            let productName = row.querySelector('.rs-product-name') ? row.querySelector('.rs-product-name').textContent.trim() : 'this product';

            if (qty == '' || qty <= 0) {

                message += 'Quantity must be greater than 0 .<br>';

            }

            if (parseInt(qty) > parseInt(maxQty)) {

                message += 'Quantity cannot exceed stock .<br>';

            }

            // Serial validation: every returned unit needs its serial number
            if (row.querySelector('.quantityReturn').getAttribute('data-serialized') === '1') {

                let serialCount = row.querySelectorAll('.returnSerial:checked').length;

                if (serialCount != parseInt(qty, 10) || serialCount == 0) {

                    message += 'Select ' + qty + ' serial number(s) for ' + productName + ' (selected: ' + serialCount + ').<br>';

                }

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
        var qnt = $("input.quantityReturn");
        var pur_price = $("input.purchase_priceReturn");

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
        
        $("#totalAmount").val(finalAmount !== 0 ? finalAmount : "");
        $("#totalDueAmount").val(finalAmount !== 0 ? finalAmount : "");
    }

</script>