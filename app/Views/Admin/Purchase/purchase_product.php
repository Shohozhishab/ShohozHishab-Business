<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase New Product <small>Purchase New Product</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase New Product</li>
        </ol>
    </section>

    

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="message"></div>
                        <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                    </div>
                    <div class="col-lg-12">

                            <div class="col-lg-12">
                                <div class="box" style="padding: 20px;">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-pencil-square-o fa-lg" style="color: white;"></i>
                                    </span>
                                            <input type="text" class="form-control input-lg" onkeypress="findResultPurchase()"
                                                   name="keyWord" id="keyWord" value="">

                                            <span class="input-group-btn">
                                      <button class="btn btn-primary btn-lg" type="submit">Search</button>
                                    </span>
                                        </div>

                                    </div>
                                    <div class="input-group col-md-12">
                                        <ul style="list-style-type:none;" id="result"></ul>
                                    </div>
                                </div>
                            </div>

                        <form action="<?php echo $action; ?>" method="post" id="purchaseProductForm">

                            <div class="col-lg-12">
                                <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Purchase Product List</h3>
                                </div>
                                <div class="box-body ">
                                    <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="purchaseProductCards">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th>Purchase Price</th>
                                            <th>Sale Price</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0;
                                        foreach (Cart()->contents() as $row) {
                                            $unitId = productIdByDefaultStoreUnit($row['id']);
                                            $serialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $row['id']);
                                            $warrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $row['id']);
                                            $uPrice = unitOrBasePriceByUnitPrice($unitId, $row['price']);
                                            $conversion_factor = get_data_by_id('conversion_factor', 'units', 'units_id', $unitId);
                                            $uSalePrice = unitOrBasePriceByUnitPrice($unitId, $row['sale_price']);
                                        ?>
                                            <tr class="product-card" data-prod-id="<?= $row['id']; ?>" data-conv="<?= $conversion_factor; ?>">
                                                <td><?= ++$i; ?></td>
                                                <td><?= $row['name']; ?>
                                                    <span class="badge serial-badge" id="serialBadge_<?= $row['id']; ?>" style="display:none;background:#3c8dbc;"></span>
                                                    <!-- Preserved purchase inputs (order kept identical to original submit payload) -->
                                                    <input type="hidden" name="productId[]" value="<?= $row['id']; ?>">
                                                    <input type="hidden" name="qty[]" value="<?= $row['qty']; ?>">
                                                    <input type="hidden" name="unit[]" value="<?= $unitId; ?>">
                                                    <input type="hidden" class="upprice" id="qtyUp_<?= $row['id']; ?>" name="price[]" value="<?= $row['price']; ?>">
                                                    <input type="hidden" name="conversion_factore[]" value="<?= $conversion_factor; ?>">
                                                    <input type="hidden" id="salePrice_<?= $row['id']; ?>" name="salePrice[]" value="<?= $row['sale_price']; ?>">
                                                    <input type="hidden" class="subtotal" name="subtotal[]" value="<?= $row['subtotal'] ?>">
                                                    <input type="hidden" name="suballtotal[]" value="<?= $row['subtotal']; ?>">
                                                    <!-- Serial / Warranty payload holders -->
                                                    <input type="hidden" name="serialData[<?= $row['id']; ?>]" id="serialData_<?= $row['id']; ?>" value="">
                                                    <input type="hidden" name="warrantyData[<?= $row['id']; ?>]" id="warrantyData_<?= $row['id']; ?>" value="">
                                                </td>
                                                <td><?= showUnitName($unitId); ?></td>
                                                <td width="160px">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control qty-unit-input" min="0" step="any" oninput="purchaseQtyChanged(this)" value="<?= unitOrQtyByUnitQty($unitId, $row['qty']) ?>" data-prod-id="<?= $row['id']; ?>" data-conv="<?= $conversion_factor; ?>">
                                                        <span class="input-group-addon"><?= showUnitName($unitId) ?></span>
                                                    </div>
                                                </td>
                                                <td width="140px"><input type="text" class="form-control input-sm" name="unitPrice[]" oninput="priceMakeBasePurchase(this.value,'<?= $conversion_factor;?>','<?= $row['id'];?>' )" value="<?= $uPrice ?>"></td>
                                                <td width="140px"><input type="text" class="form-control input-sm" name="salePrice[]" oninput="priceMakeBaseSalePrice(this.value,'<?= $conversion_factor;?>','<?= $row['id'];?>' )" value="<?= $uSalePrice ?>"></td>
                                                <td><span id="subtext_<?= $row['id']; ?>"><?= number_format($row['subtotal'], 2); ?></span></td>
                                                <td width="180px">
                                                    <?php if ($serialStatus == 1 || $warrantyStatus == 1): ?>
                                                        <button type="button" class="btn btn-info btn-xs add-serial-btn"
                                                                data-prod-id="<?= $row['id']; ?>"
                                                                data-prod-name="<?= htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                                                data-qty="<?= (int) $row['qty']; ?>"
                                                                data-serial-status="<?= $serialStatus; ?>"
                                                                data-warranty-status="<?= $warrantyStatus; ?>">
                                                            <i class="fa fa-barcode"></i> Add Serial
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="<?php echo site_url('/Admin/Purchase/removeCart/' . $row['rowid']); ?>"
                                                       onclick="javascript: return confirm('Are You Sure ?')"
                                                       class="btn btn-danger btn-xs">Cancel</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="box" id="box_bac">
                                    <div class="box-header">
                                        <h3 class="box-title">Payment</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-xs-12">
                                            <div class="col-xs-12" id="box_form">
                                                <div class="col-xs-6">
                                                    <p class="lead">Payment Type:</p>
                                                    <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg"
                                                         alt="Cash">
                                                    <img src="<?php print base_url(); ?>/dist/img/credit/bank.png"
                                                         alt="Bank">

                                                    <p class="text-muted well well-sm no-shadow"
                                                       style="margin-top: 10px;">
                                                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                        weebly
                                                        ning heekya handango imeem plugg
                                                        dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                                    </p>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="col-xs-6">
                                                        <label for="int">Purchase Date</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <label for="int">Supplier</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <select class="form-control" name="supplier_id" id="supData" required >
                                                            <option value="">Please select</option>
                                                            <?php echo getAllListInOptionWithStatus('','supplier_id','name','suppliers','name'); ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Total Amount</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" class="form-control" name="totalPrice"
                                                               id="totalPrice" value="<?php echo round(Cart()->total(),2) ?>"
                                                               readonly>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Cash</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" onchange="checkShopsBalance(this.value)" class="cash form-control" oninput="minusValueCheck(this.value,this)" name="cash" id="cash">
                                                        <b id="Balance_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Bank</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <select class="form-control" name="bank_id" id="bank_id">
                                                            <option value="">Select Bank</option>
                                                            <?php echo getTwoValueInOption('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                        </select><br>
                                                        <input type="number" onchange="checkBankBalance(this.value)"
                                                               class="bank form-control"
                                                               oninput="minusValueCheck(this.value,this)" name="bank"
                                                               id="bank">
                                                        <b id="Bank_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Due</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" class="form-control" name="due"
                                                               id="totaldue"
                                                               readonly value="<?php echo round(Cart()->total(),2) ?>">
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col-xs-12">
                                                <div class="form-group form-check">
                                                    <input type="checkbox" class="form-check-input" name="sms" id="sms">
                                                    <label class="form-check-label" for="sms">Send SMS</label>
                                                </div>

                                                <button type="submit" class="btn btn-primary" >Purchase </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>


                        </form>
                    </div>

                    <!-- Serial / Warranty Modal -->
                    <div class="modal fade" id="serialModal" tabindex="-1" role="dialog" aria-labelledby="serialModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="serialModalLabel">Serial Numbers
                                        <small id="serialModalProduct"></small>
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info" id="serialQtyAlert" style="padding:8px 12px;margin-bottom:12px;">
                                        Required serials: <b id="serialRequiredQty">0</b>
                                        &nbsp;|&nbsp; Entered: <b id="serialEnteredQty">0</b>
                                    </div>
                                    <div class="table-responsive" style="max-height:320px;overflow-y:auto;">
                                        <table class="table table-condensed" style="margin-bottom:0;">
                                            <thead>
                                                <tr>
                                                    <th style="width:40px;">#</th>
                                                    <th id="serialColHeader">Serial Number</th>
                                                    <th id="warrantyColHeader">Company Warranty End Date</th>
                                                    <th style="width:50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="serialRowsContainer"></tbody>
                                        </table>
                                    </div>
                                    <div class="text-left" style="margin:10px 0;">
                                        <button type="button" class="btn btn-success btn-sm" id="addSerialRowBtn"><i class="fa fa-plus"></i> Add Row</button>
                                        <button type="button" class="btn btn-warning btn-sm" id="removeLastRowBtn"><i class="fa fa-minus"></i> Remove Last</button>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveSerialBtn"><i class="fa fa-save"></i> Save Serials</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Serial / Warranty Modal -->


                </div>

            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<script>
    // ---- Purchase card recalculation (global, callable from inline handlers) ----
    // Defined at top level so it works even if DOMContentLoaded bindings did
    // not run (e.g. page injected via AJAX). jQuery is loaded in the footer and
    // is guaranteed present by the time a user types.
    function recalcPurchaseCards() {
        if (!window.jQuery) { return; }
        var $ = jQuery;
        var grandTotal = 0;
        $('#purchaseProductCards .product-card').each(function () {
            var $card = $(this);
            var pid = $card.data('prod-id');
            var qty = parseFloat($card.find('input[name="qty[]"]').val()) || 0;
            var price = parseFloat($card.find('input[name="unitPrice[]"]').val()) || 0;
            var conv = parseFloat($card.find('input[name="conversion_factore[]"]').val()) || 1;
            var actualQty = conv ? (qty / conv) : qty;
            var subtotal = actualQty * price;
            $card.find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
            $card.find('input[name="suballtotal[]"]').val(subtotal.toFixed(2));
            $('#subtext_' + pid).text(subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            grandTotal += subtotal;
        });
        $('#totalPrice').val(grandTotal.toFixed(8));
        if (typeof totalDue === 'function') { totalDue(); }
        if (typeof checkDue === 'function') { checkDue(); }
    }

    // Editing the unit quantity updates the hidden base qty, the serial target
    // and all totals for that product card.
    function purchaseQtyChanged(input) {
        if (!window.jQuery) { return; }
        var $ = jQuery;
        var $input = $(input);
        var $card = $input.closest('.product-card');
        if (!$card.length) { return; }
        var pid = $card.data('prod-id');
        var conv = parseFloat($input.data('conv')) || parseFloat($card.find('input[name="conversion_factore[]"]').val()) || 1;
        var unitQty = parseFloat($input.val()) || 0;
        var baseQty = unitQty * conv;
        $card.find('input[name="qty[]"]').val(baseQty);

        var target = parseInt(baseQty, 10) || 0;
        var $btn = $card.find('.add-serial-btn');
        if ($btn.length) {
            $btn.data('qty', target);
            if (parseInt($btn.data('serial-status'), 10) === 1) {
                var $hd = $('#serialData_' + pid);
                var raw = $hd.val();
                var list = [];
                if (raw) { try { list = JSON.parse(raw); } catch (e) { list = []; } }
                if (!Array.isArray(list)) { list = []; }
                if (list.length > target) {
                    list = list.slice(0, target);
                    $hd.val(list.length ? JSON.stringify(list) : '');
                }
                var $badge = $('#serialBadge_' + pid);
                if (list.length > 0) { $badge.text(list.length).show(); } else { $badge.hide(); }
            }
        }
        recalcPurchaseCards();
    }

    // Serial / Warranty modal logic for the Purchase Product page.
    // jQuery + Bootstrap are loaded in the footer AFTER this view, so all
    // bindings are deferred until DOMContentLoaded (when $ is guaranteed ready).
    document.addEventListener('DOMContentLoaded', function () {
        var $modal = jQuery('#serialModal');
        var $rows = jQuery('#serialRowsContainer');
        var current = { prodId: null, qty: 0, serialStatus: 0, warrantyStatus: 0 };

        function escapeHtml(str) {
            return jQuery('<i>').text(str == null ? '' : String(str)).html();
        }

        function rowHtml(value, dateVal) {
            return '<tr class="serial-row">' +
                '<td class="row-index text-center"></td>' +
                '<td class="col-serial"><input type="text" class="form-control input-sm serial-input" placeholder="Enter serial number" value="' + escapeHtml(value) + '"></td>' +
                '<td class="col-warranty"><input type="date" class="form-control input-sm serial-warranty" value="' + escapeHtml(dateVal || '') + '"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-serial-row"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
        }

        function renumberRows() {
            $rows.find('.serial-row').each(function (idx) {
                jQuery(this).find('.row-index').text(idx + 1);
            });
            updateEnteredQty();
        }

        function updateEnteredQty() {
            var count = $rows.find('.serial-input').length;
            jQuery('#serialEnteredQty').text(count);
        }

        function addRow(value, dateVal) {
            $rows.append(rowHtml(value || '', dateVal));
            renumberRows();
        }

        // Open modal for a specific product card.
        jQuery(document).on('click', '.add-serial-btn', function () {
            var $btn = jQuery(this);
            current.prodId = $btn.data('prod-id');
            current.qty = parseInt($btn.data('qty'), 10) || 0;
            current.serialStatus = parseInt($btn.data('serial-status'), 10) || 0;
            current.warrantyStatus = parseInt($btn.data('warranty-status'), 10) || 0;

            jQuery('#serialModalProduct').text('- ' + $btn.data('prod-name'));
            jQuery('#serialRequiredQty').text(current.qty);

            // Company Warranty End Date default is shared across all serials.
            var defaultDate = jQuery('#warrantyData_' + current.prodId).val() || '';

            // Render one row per unit whenever serial OR warranty tracking is
            // active. Serial inputs stay disabled when the product has no serial
            // tracking, so the shared warranty date can still be captured.
            $rows.empty();
            var needRows = (current.serialStatus === 1 || current.warrantyStatus === 1) ? current.qty : 0;
            var existing = [];
            if (current.serialStatus === 1) {
                var raw = jQuery('#serialData_' + current.prodId).val();
                if (raw) {
                    try { existing = JSON.parse(raw); } catch (e) { existing = []; }
                }
                if (!Array.isArray(existing)) existing = [];
            }
            // Always render exactly qty rows: preserve entered serials, pad
            // missing ones with blanks, or truncate when qty was reduced.
            for (var r = 0; r < needRows; r++) {
                addRow(existing[r] !== undefined ? existing[r] : '', defaultDate);
            }
            // Show a column only when its status is on: Serial Number appears
            // when serial_status is active; Company Warranty End Date appears
            // when warranty_status is active. Hidden fields are not required.
            var showSerial = current.serialStatus === 1;
            var showWarranty = current.warrantyStatus === 1;
            jQuery('#serialQtyAlert').toggle(showSerial);
            jQuery('#serialColHeader').toggle(showSerial);
            jQuery('#warrantyColHeader').toggle(showWarranty);
            $rows.find('.col-serial').toggle(showSerial);
            $rows.find('.col-warranty').toggle(showWarranty);

            renumberRows();
            $modal.modal('show');
        });

        jQuery('#addSerialRowBtn').on('click', function () {
            var d = $rows.find('.serial-warranty').first().val() || '';
            addRow('', d);
        });

        jQuery('#removeLastRowBtn').on('click', function () {
            $rows.find('.serial-row').last().remove();
            renumberRows();
        });

        jQuery(document).on('click', '.remove-serial-row', function () {
            jQuery(this).closest('.serial-row').remove();
            renumberRows();
        });

        jQuery(document).on('input', '.serial-input', updateEnteredQty);

        // Keep the Company Warranty End Date identical across all serial rows.
        jQuery(document).on('input change', '.serial-warranty', function () {
            var v = jQuery(this).val();
            $rows.find('.serial-warranty').not(this).val(v);
        });

        // Save serials back to the product card hidden fields.
        jQuery('#saveSerialBtn').on('click', function () {
            var serials = [];
            var valid = true;

            if (current.serialStatus === 1) {
                $rows.find('.serial-input').each(function () {
                    var v = jQuery(this).val().trim();
                    if (v === '') { valid = false; }
                    serials.push(v);
                });

                if (!valid) {
                    alert('All serial numbers must be filled. Empty serial rows are not allowed.');
                    return;
                }
                if (serials.length !== current.qty) {
                    alert('Serial count (' + serials.length + ') must equal the product quantity (' + current.qty + ').');
                    return;
                }
                var unique = serials.filter(function (v, i) { return serials.indexOf(v) === i; });
                if (unique.length !== serials.length) {
                    alert('Duplicate serial numbers are not allowed.');
                    return;
                }
            }

            var warranty = $rows.find('.serial-warranty').first().val() || '';
            if (current.warrantyStatus === 1 && !warranty) {
                alert('Company Warranty End Date is required.');
                return;
            }

            jQuery('#serialData_' + current.prodId).val(serials.length ? JSON.stringify(serials) : '');
            jQuery('#warrantyData_' + current.prodId).val(warranty);

            var $badge = jQuery('#serialBadge_' + current.prodId);
            if (serials.length > 0) {
                $badge.text(serials.length + (warranty ? ' / W' : '')).show();
            } else {
                $badge.hide();
            }

            $modal.modal('hide');
        });

        // Keep the existing unitPrice / price handlers working on cards: point
        // the shared calculateGrandTotal (which assumed #TFtable) at the
        // card-aware recalc defined at the top of this script. Quantity edits
        // are handled by the inline purchaseQtyChanged(this) handler.
        window.calculateGrandTotal = recalcPurchaseCards;

        // Final purchase submit guard: validate serial_status and warranty_status
        // independently so a warranty-only product is still enforced.
        jQuery('#purchaseProductForm').on('submit', function () {
            var block = null;
            jQuery('.add-serial-btn').each(function () {
                var $btn = jQuery(this);
                var pid = $btn.data('prod-id');
                var qty = parseInt($btn.data('qty'), 10) || 0;
                var serialOn = parseInt($btn.data('serial-status'), 10) === 1;
                var warrantyOn = parseInt($btn.data('warranty-status'), 10) === 1;

                if (serialOn) {
                    var raw = jQuery('#serialData_' + pid).val();
                    var list = [];
                    if (raw) { try { list = JSON.parse(raw); } catch (e) { list = []; } }
                    if (!Array.isArray(list)) list = [];
                    if (list.length === 0) {
                        block = 'Please add serial numbers for "' + $btn.data('prod-name') + '".';
                        return false;
                    }
                    if (list.length !== qty) {
                        block = 'Serial count for "' + $btn.data('prod-name') + '" (' + list.length + ') must equal quantity (' + qty + ').';
                        return false;
                    }
                    var hasEmpty = list.some(function (v) { return String(v).trim() === ''; });
                    if (hasEmpty) {
                        block = 'All serial numbers must be filled for "' + $btn.data('prod-name') + '".';
                        return false;
                    }
                }

                if (warrantyOn && !jQuery('#warrantyData_' + pid).val()) {
                    block = 'Company warranty end date is required for "' + $btn.data('prod-name') + '".';
                    return false;
                }
            });
            if (block) {
                alert(block);
                return false;
            }
        });
    });
</script>