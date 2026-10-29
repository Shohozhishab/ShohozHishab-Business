
<?php
// Purchases created from Admin/Products (no supplier) are funded from shop capital:
// cash / bank / due are not applicable and stay locked on this screen.
$isCapitalPurchase = empty($purchase->supplier_id);
?>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="box-title">Purchase Item</h3>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form action="<?= base_url('Admin/Purchase/purchaseEdiAction')?>" method="post" id="editPurchaseForm">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Quantity </th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($purchaseItem as $item){
                                $unit = productIdByDefaultStoreUnit($item->prod_id);
                                $conversion_factor = get_data_by_id('conversion_factor', 'units', 'units_id', $unit);
                                $jsonArray = get_data_by_id('purchase_units', 'products', 'prod_id', $item->prod_id);
                                $salePrice = get_data_by_id('selling_price', 'product_stock_relation', 'product_stock_relation_id', $item->product_stock_relation_id);
                                ?>
                                <tr>
                                    <td width="80px"><?= $i++ ?></td>
                                    <td>
                                        <?= get_data_by_id('name','products','prod_id',$item->prod_id); ?>
                                        <input type="hidden" name="prod_id[]" value="<?= $item->prod_id ?>">
                                        <?php
                                        $serialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $item->prod_id);
                                        $warrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $item->prod_id);
                                        $lineSerials    = $serialPayload[$item->prod_id] ?? '[]';
                                        $lineWarranty   = $warrantyPayload[$item->prod_id] ?? '';
                                        $lineLocked     = (int) ($lockedPayload[$item->prod_id] ?? 0);
                                        ?>
                                        <!-- Serial / Warranty payload holders -->
                                        <input type="hidden" name="serialData[<?= $item->prod_id ?>]" id="serialData_<?= $item->prod_id ?>" value="<?= htmlspecialchars($lineSerials, ENT_QUOTES) ?>">
                                        <input type="hidden" name="warrantyData[<?= $item->prod_id ?>]" id="warrantyData_<?= $item->prod_id ?>" value="<?= htmlspecialchars($lineWarranty, ENT_QUOTES) ?>">
                                        <?php if ($serialStatus == 1 || $warrantyStatus == 1): ?>
                                            <span class="badge pe-serial-badge" id="serialBadge_<?= $item->prod_id ?>" style="display:none;background:#3c8dbc;"></span>
                                            <button type="button" class="btn btn-info btn-xs pe-add-serial-btn" style="margin-left:6px;"
                                                    data-prod-id="<?= $item->prod_id ?>"
                                                    data-prod-name="<?= htmlspecialchars(get_data_by_id('name','products','prod_id',$item->prod_id), ENT_QUOTES) ?>"
                                                    data-qty="<?= max((int) $item->quantity - $lineLocked, 0) ?>"
                                                    data-locked-count="<?= $lineLocked ?>"
                                                    data-serial-status="<?= $serialStatus ?>"
                                                    data-warranty-status="<?= $warrantyStatus ?>">
                                                <i class="fa fa-barcode"></i> Serial / Warranty
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td><input type="hidden" name="qty[]" id="qtyUp_<?= $item->prod_id ?>" value="<?= $item->quantity; ?>">
                                        <div style="display: flex;">
                                            <?php $unitsArray = convertRevertToArray($item->quantity,json_decode($jsonArray));
                                                foreach ($unitsArray as $input){
                                            ?>
                                                <div style="margin-right: 10px;">
                                                     <label for="int" class="text-capitalize"><?= $input['name'];?></label>
                                                    <input type="text" oninput="qtyMakeBaseUnit('<?= $item->prod_id ?>')" data-factor="<?= $input['conversion_factor']?>"  style="width: 50px;" value="<?= $input['qty']; ?>">
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="price[]" id="priceUp_<?= $item->prod_id; ?>" value="<?= $item->purchase_price?>">
                                        <label for="int"><?= showUnitName($unit);?></label>
                                        <input type="text" name="priceUnit[]" style="width: 100px;" oninput="priceMakeBaseUnit(this.value,'<?= $conversion_factor;?>','<?= $item->prod_id;?>' )" value="<?= trim_decimal_zero(unitOrBasePriceByUnitPrice($unit,$item->purchase_price))?>">
                                    </td>

                                    <td>
                                        <input type="hidden" name="salePrice[]" id="salePriceUp_<?= $item->prod_id; ?>" value="<?= $salePrice;?>">
                                        <label for="int"><?= showUnitName($unit);?></label>
                                        <input type="text" name="salePriceUnit[]" oninput="salePriceMakeBaseUnit(this.value,'<?= $conversion_factor;?>','<?= $item->prod_id;?>' )" value="<?= trim_decimal_zero(unitOrBasePriceByUnitPrice($unit,$salePrice))?>" >
                                    </td>
                                    <td><input type="text" name="total_price[]" value="<?= trim_decimal_zero($item->total_price)?>" readonly></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div class="col-xs-6"></div>
                        <div class="col-xs-6">
                            <div class="col-xs-6">
                                <label for="int">Total Amount</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="number" class="form-control" name="totalPrice"
                                       id="totalPrice" value="<?= trim_decimal_zero($purchase->amount) ?>"
                                       readonly>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Cash</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="number" onchange="checkShopsBalance(this.value),calculateDueTotal()"
                                       class="cash form-control"
                                       oninput="minusValueCheck(this.value,this)" name="cash"
                                       id="cash" value="<?= $isCapitalPurchase ? '0' : trim_decimal_zero($purchase->nagad_paid) ?>"<?= $isCapitalPurchase ? ' readonly' : '' ?>><b id="Balance_valid"></b>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Bank</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <select class="form-control" name="bank_id" id="bank_id"<?= $isCapitalPurchase ? ' disabled' : '' ?>>
                                    <option value="">Select Bank</option>
                                    <?php echo getTwoValueInOption($purchase->bank_id, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                </select><br>
                                <input type="number" onchange="checkBankBalance(this.value),calculateDueTotal()"
                                       class="bank form-control"
                                       oninput="minusValueCheck(this.value,this)" name="bank"
                                       id="bank" value="<?= $isCapitalPurchase ? '0' : trim_decimal_zero($purchase->bank_paid) ?>"<?= $isCapitalPurchase ? ' readonly' : '' ?>>
                                <b id="Bank_valid"></b>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Due</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="hidden" class="form-control"  id="totalDue2" readonly value="<?= $purchase->due ?>">
                                <input type="number" class="form-control" name="due" id="totaldue" readonly value="<?= trim_decimal_zero($purchase->due) ?>">
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <input type="hidden" name="purchase_id" value="<?= $purchase->purchase_id ?>">
                            <button type="submit" class="btn btn-success " style="float: right;" id="dueBtn">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->

<!-- Serial / Warranty Modal (purchase edit) -->
<div class="modal fade" id="peSerialModal" tabindex="-1" role="dialog" aria-labelledby="peSerialModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="peSerialModalLabel">Serial Numbers
                    <small id="peSerialModalProduct"></small>
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="peSerialQtyAlert" style="padding:8px 12px;margin-bottom:12px;">
                    Required serials: <b id="peSerialRequiredQty">0</b><span class="pe-locked-note text-muted" style="display:none;"></span>
                    &nbsp;|&nbsp; Entered: <b id="peSerialEnteredQty">0</b>
                </div>
                <div class="table-responsive" style="max-height:320px;overflow-y:auto;">
                    <table class="table table-condensed" style="margin-bottom:0;">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th id="peWarrantyColHeader">Company Warranty End Date</th>
                                <th id="peSerialColHeader">Serial Number</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="peSerialRowsContainer"></tbody>
                    </table>
                </div>
                <div class="text-left" style="margin:10px 0;">
                    <button type="button" class="btn btn-success btn-sm" id="peAddSerialRowBtn"><i class="fa fa-plus"></i> Add Row</button>
                    <button type="button" class="btn btn-warning btn-sm" id="peRemoveLastRowBtn"><i class="fa fa-minus"></i> Remove Last</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="peSaveSerialBtn"><i class="fa fa-save"></i> Save Serials</button>
            </div>
        </div>
    </div>
</div>
<!-- /Serial / Warranty Modal (purchase edit) -->


<script id="purchaseEditScript">
    // Remove previously injected copies of this script so helpers are never
    // redefined twice (this view can be opened via AJAX multiple times).
    (function () {
        var scripts = document.querySelectorAll('script#purchaseEditScript');
        for (var s = 0; s < scripts.length - 1; s++) {
            scripts[s].parentNode.removeChild(scripts[s]);
        }
    })();

    // Serial payload helpers (global, callable from inline handlers injected via AJAX)
    window.peParseSerials = function (pid) {
        var raw = jQuery('#serialData_' + pid).val();
        var list = [];
        if (raw) { try { list = JSON.parse(raw); } catch (e) { list = []; } }
        return Array.isArray(list) ? list : [];
    };

    // Serials already sold/returned are never loaded into the payload; the button
    // carries their count so qty targets and validation exclude the locked units.
    window.peLockedCount = function (pid) {
        var $btn = jQuery('.pe-add-serial-btn[data-prod-id="' + pid + '"]');
        return $btn.length ? (parseInt($btn.attr('data-locked-count'), 10) || 0) : 0;
    };

    // Keep serial button target / hidden payload in sync when the base qty changes.
    window.peSyncSerialQty = function (pid, baseQty) {
        if (!window.jQuery) { return; }
        var locked = window.peLockedCount(pid);
        var target = (parseInt(baseQty, 10) || 0) - locked;
        if (target < 0) { target = 0; }
        var $btn = jQuery('.pe-add-serial-btn[data-prod-id="' + pid + '"]');
        if (!$btn.length) { return; }
        $btn.attr('data-qty', target);
        if (parseInt($btn.attr('data-serial-status'), 10) === 1) {
            var list = window.peParseSerials(pid);
            if (list.length > target) {
                // Trim to target, always preserving protected (existing) serials first
                var protectedRows = list.filter(function (r) { return parseInt(r['protected'], 10) === 1; });
                var plainRows = list.filter(function (r) { return parseInt(r['protected'], 10) !== 1; });
                if (protectedRows.length > target) { protectedRows = protectedRows.slice(0, target); }
                var room = target - protectedRows.length;
                var kept = protectedRows.concat(plainRows.slice(0, room));
                jQuery('#serialData_' + pid).val(kept.length ? JSON.stringify(kept) : '');
                list = kept;
            }
            var $badge = jQuery('#serialBadge_' + pid);
            if (list.length > 0) { $badge.text(list.length).show(); } else { $badge.hide(); }
        }
    };

    // Strip redundant trailing zeros from an 8-decimal string for display,
    // e.g. "500.00000000" -> "500", "437.50000000" -> "437.5".
    window.peTrimZeros = function (str) {
        str = String(str);
        if (str.indexOf('.') === -1) { return str; }
        str = str.replace(/0+$/, '');
        return str.replace(/\.$/, '');
    };

    window.calculateGrandTotal = function () {
        if (!window.jQuery) { return; }
        var $ = jQuery;
        var grandTotal = 0;
        $('tbody tr').each(function () {
            var qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            var price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            var total = qty * price;
            $(this).find('input[name="total_price[]"]').val(window.peTrimZeros(total.toFixed(8)));
            grandTotal += total;
        });
        $('#totalPrice').val(window.peTrimZeros(grandTotal.toFixed(8)));
        if (typeof window.calculateDueTotal === 'function') { window.calculateDueTotal(); }
    };
</script>

<script id="purchaseEditCalcScript">
        // Remove previously injected copies of this block so delegated handlers
        // and the capture-phase submit guard are never bound twice.
        (function () {
            var scripts = document.querySelectorAll('script#purchaseEditCalcScript');
            for (var s = 0; s < scripts.length - 1; s++) {
                scripts[s].parentNode.removeChild(scripts[s]);
            }
        })();

        $(document).ready(function () {

            // Capital-funded purchase (no supplier): payments are not applicable, due stays 0
            window.peNoSupplier = <?= $isCapitalPurchase ? 'true' : 'false' ?>;

            function calculateRow(row) {
                let qty = parseFloat($(row).find('input[name="qty[]"]').val()) || 0;
                let price = parseFloat($(row).find('input[name="price[]"]').val()) || 0;

                let total = qty * price;

                $(row).find('input[name="total_price[]"]').val(window.peTrimZeros(total.toFixed(8)));

                return total;
            }

            function calculateGrandTotal() {
                let grandTotal = 0;

                $('tbody tr').each(function () {
                    grandTotal += calculateRow(this);
                });

                $('#totalPrice').val(window.peTrimZeros(grandTotal.toFixed(8)));
                calculateDueTotal();
            }

        window.calculateDueTotal = function () {
            let total = parseFloat($('#totalPrice').val()) || 0;
            let cash  = parseFloat($('#cash').val()) || 0;
            let bank  = parseFloat($('#bank').val()) || 0;

            // Capital-funded purchases never carry a payable due
            let due = window.peNoSupplier ? 0 : total - (cash + bank);

            if (due < 0) {
                $('#dueBtn').prop('disabled', true);
            } else {
                $('#dueBtn').prop('disabled', false);
            }

            $('#totaldue').val(window.peTrimZeros(due.toFixed(8)));
            $('#totalDue2').val(due.toFixed(8));
        }

        // Recalculate when quantity or price changes
        $(document).on('input', 'input[name="qty[]"], input[name="price[]"]', function () {
            calculateGrandTotal();
        });

        // Recalculate due when cash or bank changes
        $(document).on('input', '#cash, #bank', function () {
            calculateDueTotal();
        });

        window.priceMakeBaseUnit = function(val, key, printId) {
            val = parseFloat(val) || 0;
            key = parseFloat(key) || 1;

            var total = val / key;

            $("#priceUp_" + printId).val(total.toFixed(8));

            calculateGrandTotal();
        };

        window.salePriceMakeBaseUnit = function(val, key, printId) {
            val = parseFloat(val) || 0;
            key = parseFloat(key) || 1;

            var total = val / key;

            $("#salePriceUp_" + printId).val(total.toFixed(8));
        };

        window.qtyMakeBaseUnit = function (printId) {

            let row = $("#qtyUp_" + printId).closest("td");
            let total = 0;
            row.find("input[data-factor]").each(function () {
                let factor = parseFloat($(this).data("factor")) || 0;
                let qty = parseFloat($(this).val()) || 0;

                total += qty * factor;
            });
            $("#qtyUp_" + printId).val(total.toFixed(8));

            // Keep the serial target aligned with the new base quantity
            if (typeof window.peSyncSerialQty === 'function') {
                window.peSyncSerialQty(printId, total.toFixed(8));
            }

            calculateGrandTotal();
        };


        // Initial calculation
        calculateGrandTotal();

        // ---- Serial / Warranty modal logic (purchase edit) ----
        (function () {
            var $modal = jQuery('#peSerialModal');
            var $rows = jQuery('#peSerialRowsContainer');
            var current = { prodId: null, qty: 0, serialStatus: 0, warrantyStatus: 0 };

            function escapeHtml(str) {
                return jQuery('<i>').text(str == null ? '' : String(str)).html();
            }

            function rowHtml(row, dateVal) {
                var id = row && row.id ? parseInt(row.id, 10) : '';
                var serial = row && row.serial ? String(row.serial) : '';
                var isProtected = id !== '';
                // Existing (in-stock) serials stay editable so the number itself can be
                // updated; only removal is blocked for them.
                return '<tr class="serial-row" data-serial-id="' + id + '">' +
                    '<td class="row-index text-center"></td>' +
                    '<td class="col-warranty"><input type="date" class="form-control input-sm serial-warranty" value="' + escapeHtml(dateVal || '') + '"></td>' +
                    '<td class="col-serial"><input type="text" class="form-control input-sm serial-input" placeholder="Enter serial number" value="' + escapeHtml(serial) + '"></td>' +
                    '<td class="text-center">' + (isProtected ? '<i class="fa fa-check text-success" title="Already in stock - number editable, cannot remove"></i>' : '<button type="button" class="btn btn-danger btn-xs remove-serial-row"><i class="fa fa-times"></i></button>') + '</td>' +
                    '</tr>';
            }

            function renumberRows() {
                $rows.find('.serial-row').each(function (idx) {
                    jQuery(this).find('.row-index').text(idx + 1);
                });
                jQuery('#peSerialEnteredQty').text($rows.find('.serial-input').length);
            }

            function addRow(row, dateVal) {
                $rows.append(rowHtml(row, dateVal));
                renumberRows();
            }

            // Open modal for a specific purchase item row
            jQuery(document).on('click', '.pe-add-serial-btn', function () {
                var $btn = jQuery(this);
                current.prodId = $btn.data('prod-id');
                current.qty = parseInt($btn.attr('data-qty'), 10) || 0;
                current.serialStatus = parseInt($btn.attr('data-serial-status'), 10) || 0;
                current.warrantyStatus = parseInt($btn.attr('data-warranty-status'), 10) || 0;

                jQuery('#peSerialModalProduct').text('- ' + $btn.attr('data-prod-name'));
                var locked = window.peLockedCount(current.prodId);
                var editableQty = current.qty - locked;
                if (editableQty < 0) { editableQty = 0; }
                jQuery('#peSerialRequiredQty').text(editableQty);
                if (locked > 0) {
                    jQuery('#peSerialQtyAlert .pe-locked-note').text(' (' + locked + ' sold/locked serial(s) excluded)').show();
                } else {
                    jQuery('#peSerialQtyAlert .pe-locked-note').text('').hide();
                }
                
                var defaultDate = jQuery('#warrantyData_' + current.prodId).val() || '';
                var existing = current.serialStatus === 1 ? window.peParseSerials(current.prodId) : [];
                
                // Render one row per editable unit whenever serial OR warranty tracking is active
                $rows.empty();
                var needRows = (current.serialStatus === 1 || current.warrantyStatus === 1) ? editableQty : 0;
                for (var r = 0; r < needRows; r++) {
                    addRow(existing[r] !== undefined ? existing[r] : {}, defaultDate);
                }

                var showSerial = current.serialStatus === 1;
                var showWarranty = current.warrantyStatus === 1;
                jQuery('#peSerialQtyAlert').toggle(showSerial);
                jQuery('#peSerialColHeader').toggle(showSerial);
                jQuery('#peWarrantyColHeader').toggle(showWarranty);
                $rows.find('.col-serial').toggle(showSerial);
                $rows.find('.col-warranty').toggle(showWarranty);

                renumberRows();
                $modal.modal('show');
            });

            jQuery(document).on('click', '#peAddSerialRowBtn', function () {
                var d = $rows.find('.serial-warranty').first().val() || '';
                addRow({}, d);
            });

            jQuery(document).on('click', '#peRemoveLastRowBtn', function () {
                // Never remove a protected (already in-stock) serial row
                var $removable = $rows.find('.serial-row').filter(function () {
                    return !jQuery(this).attr('data-serial-id');
                });
                $removable.last().remove();
                renumberRows();
            });

            jQuery(document).on('click', '.remove-serial-row', function () {
                jQuery(this).closest('.serial-row').remove();
                renumberRows();
            });

            jQuery(document).on('input', '.serial-input', function () {
                jQuery('#peSerialEnteredQty').text($rows.find('.serial-input').length);
            });

            // Keep the Company Warranty End Date identical across all serial rows
            jQuery(document).on('input change', '.serial-warranty', function () {
                var v = jQuery(this).val();
                $rows.find('.serial-warranty').not(this).val(v);
            });

            // Save serials back to the hidden payload fields of the row
            jQuery(document).on('click', '#peSaveSerialBtn', function () {
                var rows = [];
                var valid = true;

                $rows.find('.serial-row').each(function () {
                    var $row = jQuery(this);
                    var id = $row.attr('data-serial-id') ? parseInt($row.attr('data-serial-id'), 10) : null;
                    var v = id !== null
                        ? ($row.find('.serial-input').val() || '').trim()
                        : $row.find('.serial-input').val().trim();
                    if (v === '') { valid = false; }
                    rows.push({ id: id, serial: v, 'protected': id !== null ? 1 : 0 });
                });

                if (current.serialStatus === 1) {
                    if (!valid) {
                        alert('All serial numbers must be filled. Empty serial rows are not allowed.');
                        return;
                    }
                    if (rows.length !== current.qty - window.peLockedCount(current.prodId)) {
                        alert('Serial count (' + rows.length + ') must equal the editable quantity (' + (current.qty - window.peLockedCount(current.prodId)) + ').');
                        return;
                    }
                    var serials = rows.map(function (r) { return r.serial; });
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

                jQuery('#serialData_' + current.prodId).val(rows.length ? JSON.stringify(rows) : '');
                jQuery('#warrantyData_' + current.prodId).val(warranty);

                var $badge = jQuery('#serialBadge_' + current.prodId);
                if (rows.length > 0) {
                    $badge.text(rows.length + (warranty ? ' / W' : '')).show();
                } else {
                    $badge.hide();
                }

                $modal.modal('hide');
            });

            // Final submit guard: validate serial_status and warranty_status independently
            document.addEventListener('submit', function (e) {
                if (!e.target || e.target.id !== 'editPurchaseForm') { return; }
                if (!window.jQuery) { return; }
                var $ = jQuery;
                var block = null;
                $('.pe-add-serial-btn').each(function () {
                    var $btn = $(this);
                    var pid = $btn.data('prod-id');
                    var qty = parseInt($('#qtyUp_' + pid).val(), 10) || 0;
                    qty = qty - window.peLockedCount(pid);
                    if (qty < 0) { qty = 0; }
                    var serialOn = parseInt($btn.attr('data-serial-status'), 10) === 1;
                    var warrantyOn = parseInt($btn.attr('data-warranty-status'), 10) === 1;

                    if (serialOn) {
                        var list = window.peParseSerials(pid);
                        if (list.length === 0) {
                            block = 'Please add serial numbers for "' + $btn.attr('data-prod-name') + '".';
                            return false;
                        }
                        if (list.length !== qty) {
                            block = 'Serial count for "' + $btn.attr('data-prod-name') + '" (' + list.length + ') must equal quantity (' + qty + ').';
                            return false;
                        }
                        if (list.some(function (r) { return String(r.serial == null ? '' : r.serial).trim() === ''; })) {
                            block = 'All serial numbers must be filled for "' + $btn.attr('data-prod-name') + '".';
                            return false;
                        }
                    }

                    if (warrantyOn && !$('#warrantyData_' + pid).val()) {
                        block = 'Company warranty end date is required for "' + $btn.attr('data-prod-name') + '".';
                        return false;
                    }
                });
                if (block) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert(block);
                }
            }, true);
        })();
    });

</script>