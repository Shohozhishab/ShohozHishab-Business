
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
                                $prodName = get_data_by_id('name','products','prod_id',$item->prod_id);
                                // Units that have already left this line (sold, damaged, ...).
                                // Once non-zero the purchase price is history and the quantity
                                // may only be raised, never dropped below the sold units.
                                // Hints stay in tooltips/inline warnings so the table layout
                                // is not widened by extra badges or icons.
                                $lineSoldQty = (float) ($soldPayload[$item->prod_id] ?? 0);
                                $lineSoldLocked = $lineSoldQty > 0.000001;
                                $lineSoldText = trim_decimal_zero($lineSoldQty);
                                $lineSoldTitle = $lineSoldLocked ? ' title="' . $lineSoldText . ' unit(s) already sold - purchase price is locked and the quantity can only be increased"' : '';
                                ?>
                                <tr>
                                    <td width="80px"><?= $i++ ?></td>
                                    <td>
                                        <?= $prodName; ?>
                                        <input type="hidden" name="prod_id[]" value="<?= $item->prod_id ?>">
                                        <?php
                                        $serialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $item->prod_id);
                                        $warrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $item->prod_id);
                                        $lineSerials    = $serialPayload[$item->prod_id] ?? '[]';
                                        $lineWarranty   = $warrantyPayload[$item->prod_id] ?? '';
                                        $lineLocked     = (int) ($lockedPayload[$item->prod_id] ?? 0);
                                        // Sold/returned/damaged serials of this line - rendered
                                        // read-only inside the modal, never submitted.
                                        $lineLockedSerials = $lockedSerialPayload[$item->prod_id] ?? '[]';
                                        ?>
                                        <!-- Serial / Warranty payload holders -->
                                        <input type="hidden" name="serialData[<?= $item->prod_id ?>]" id="serialData_<?= $item->prod_id ?>" value="<?= htmlspecialchars($lineSerials, ENT_QUOTES) ?>">
                                        <input type="hidden" name="warrantyData[<?= $item->prod_id ?>]" id="warrantyData_<?= $item->prod_id ?>" value="<?= htmlspecialchars($lineWarranty, ENT_QUOTES) ?>">
                                        <input type="hidden" id="lockedSerialData_<?= $item->prod_id ?>" value="<?= htmlspecialchars($lineLockedSerials, ENT_QUOTES) ?>">
                                        <?php if ($serialStatus == 1 || $warrantyStatus == 1): ?>
                                            <!-- Button sits below the product name, like the purchase create screens -->
                                            <div style="margin-top: 6px;">
                                                <span class="badge pe-serial-badge" id="serialBadge_<?= $item->prod_id ?>" style="display:none;background:#3c8dbc;"></span>
                                                <button type="button" class="btn btn-info btn-xs pe-add-serial-btn"
                                                        data-prod-id="<?= $item->prod_id ?>"
                                                        data-prod-name="<?= htmlspecialchars($prodName, ENT_QUOTES) ?>"
                                                        data-qty="<?= max((int) $item->quantity - $lineLocked, 0) ?>"
                                                        data-locked-count="<?= $lineLocked ?>"
                                                        data-serial-status="<?= $serialStatus ?>"
                                                        data-warranty-status="<?= $warrantyStatus ?>">
                                                    <i class="fa fa-barcode"></i> Serial / Warranty
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><input type="hidden" name="qty[]" id="qtyUp_<?= $item->prod_id ?>" value="<?= $item->quantity; ?>" data-sold="<?= $lineSoldQty ?>" data-prod-name="<?= htmlspecialchars($prodName, ENT_QUOTES) ?>">
                                        <div style="display: flex;">
                                            <?php $unitsArray = convertRevertToArray($item->quantity,json_decode($jsonArray));
                                                foreach ($unitsArray as $input){
                                            ?>
                                                <div style="margin-right: 10px;">
                                                     <label for="int" class="text-capitalize"><?= $input['name'];?></label>
                                                    <input type="text" oninput="qtyMakeBaseUnit('<?= $item->prod_id ?>')" data-factor="<?= $input['conversion_factor']?>"  style="width: 50px;" value="<?= $input['qty']; ?>"<?= $lineSoldLocked ? ' title="Minimum ' . trim_decimal_zero(round($lineSoldQty / max((float) $input['conversion_factor'], 0.00000001), 4)) . ' ' . htmlspecialchars($input['name'], ENT_QUOTES) . ' (already sold)"' : '' ?>>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <span class="text-danger" id="qtySoldNote_<?= $item->prod_id ?>" style="display:none;"></span>
                                    </td>
                                    <td<?= $lineSoldLocked ? ' style="background-color:#fcf8e3;"' : '' ?>>
                                        <input type="hidden" name="price[]" id="priceUp_<?= $item->prod_id; ?>" value="<?= $item->purchase_price?>">
                                        <label for="int"><?= showUnitName($unit);?></label>
                                        <input type="text" name="priceUnit[]" style="width: 100px;<?= $lineSoldLocked ? ' background:#eeeeee; cursor:not-allowed;' : '' ?>"<?= $lineSoldLocked ? ' readonly' : '' ?><?= $lineSoldTitle ?> oninput="priceMakeBaseUnit(this.value,'<?= $conversion_factor;?>','<?= $item->prod_id;?>' )" value="<?= trim_decimal_zero(unitOrBasePriceByUnitPrice($unit,$item->purchase_price))?>">
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
<style>
    /* The edit screen is itself shown inside #modal-default (z-index 1050), so this
       picker modal is a nested modal and must stack above the parent modal. */
    #peSerialModal { z-index: 1060; }
    #peSerialModal .modal-dialog { margin: 60px auto; }
    #peSerialModal .modal-content { border: none; border-radius: 5px; box-shadow: 0 8px 30px rgba(0, 0, 0, .35); }
</style>
<div class="modal fade pe-serial-modal" id="peSerialModal" tabindex="-1" role="dialog" aria-labelledby="peSerialModalLabel">
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

    // Units that already left this line (sold/returned/damaged). They are displayed
    // inside the modal as read-only fields so the count matches the product quantity,
    // but they are never part of the submitted payload.
    window.peParseLockedSerials = function (pid) {
        var raw = jQuery('#lockedSerialData_' + pid).val();
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

    // Badge on the Serial / Warranty button: "entered / required" for the editable
    // units, so an increased quantity immediately shows how many serials are still
    // missing instead of only failing on submit.
    window.peUpdateSerialBadge = function (pid) {
        var $btn = jQuery('.pe-add-serial-btn[data-prod-id="' + pid + '"]');
        var $badge = jQuery('#serialBadge_' + pid);
        if (!$btn.length || !$badge.length) { return; }
        var required = parseInt($btn.attr('data-qty'), 10) || 0;
        var entered = window.peParseSerials(pid).length;
        var warranty = jQuery('#warrantyData_' + pid).val() || '';
        if (parseInt($btn.attr('data-serial-status'), 10) === 1) {
            if (required === 0) {
                // Whole line already sold: nothing to enter
                $badge.text('0 / 0').css('background', '#9e9e9e')
                    .attr('title', 'All units of this line are already sold - no serial needed').show();
            } else {
                var complete = entered >= required;
                $badge.text(entered + ' / ' + required)
                    .css('background', complete ? '#3c8dbc' : '#dd4b39')
                    .attr('title', complete ? 'All editable units have a serial number'
                        : (required - entered) + ' serial number(s) still needed')
                    .show();
            }
        } else if (warranty) {
            $badge.text('W').css('background', '#3c8dbc')
                .attr('title', 'Company warranty end date: ' + warranty).show();
        } else {
            $badge.hide();
        }
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
            }
        }
        // Reflect the new requirement (or the trimmed payload) on the badge
        window.peUpdateSerialBadge(pid);
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

            // Already-sold units can never be taken back out of this line
            peQtySoldWarning(printId, total);

            // Keep the serial target aligned with the new base quantity
            if (typeof window.peSyncSerialQty === 'function') {
                window.peSyncSerialQty(printId, total.toFixed(8));
            }

            calculateGrandTotal();
        };

        // Inline hint shown while the edited quantity is below the sold quantity
        window.peQtySoldWarning = function (printId, total) {
            var $hidden = $("#qtyUp_" + printId);
            var sold = parseFloat($hidden.attr('data-sold')) || 0;
            if (sold <= 0) { return; }
            var $note = $("#qtySoldNote_" + printId);
            if (!$note.length) { return; }
            if (total < sold - 0.000001) {
                $note.text('Minimum ' + window.peTrimQtyStr(sold) + ' (already sold)').show();
            } else {
                $note.text('').hide();
            }
        };

        window.peTrimQtyStr = function (num) {
            return window.peTrimZeros((parseFloat(num) || 0).toFixed(8));
        };


        // Initial calculation
        calculateGrandTotal();

        // Show every line's serial requirement up front (entered / required)
        $('.pe-add-serial-btn').each(function () {
            window.peUpdateSerialBadge($(this).data('prod-id'));
        });

        // ---- Serial / Warranty modal logic (purchase edit) ----
        (function () {
            // The edit screen is injected into #modal-default, so #peSerialModal is a
            // nested modal. Bind the lifecycle handlers once on document (delegated by
            // class, safe across AJAX re-injection) to keep backdrop stacking and the
            // parent's scroll lock correct while the picker is open/closed.
            if (!window.peSerialModalWired) {
                window.peSerialModalWired = true;
                jQuery(document)
                    .on('shown.bs.modal', '.pe-serial-modal', function () {
                        // Dim the parent edit modal too, by lifting this modal's backdrop above it.
                        jQuery('.modal-backdrop').last().css('z-index', 1055);
                    })
                    .on('hidden.bs.modal', '.pe-serial-modal', function () {
                        jQuery('.modal-backdrop').last().css('z-index', '');
                        // Bootstrap clears the scroll lock on close; the outer edit modal still needs it.
                        if (jQuery('#modal-default').hasClass('in')) {
                            jQuery('body').addClass('modal-open');
                        }
                    });
            }

            function peSerialModal() {
                // Prefer the visible instance; a re-injected edit screen can leave a
                // duplicate copy of the modal in the DOM.
                var $visible = jQuery('.pe-serial-modal.in');
                if ($visible.length) { return $visible.last(); }
                return jQuery('.pe-serial-modal').last();
            }

            var $modal = peSerialModal();
            var $rows = jQuery('#peSerialRowsContainer');
            var current = { prodId: null, qty: 0, serialStatus: 0, warrantyStatus: 0 };

            function escapeHtml(str) {
                return jQuery('<i>').text(str == null ? '' : String(str)).html();
            }

            function rowHtml(row, dateVal, locked) {
                if (locked) {
                    // Sold / returned / damaged unit: shown for completeness but frozen.
                    var lSerial = row && row.serial ? String(row.serial) : '';
                    var lDate = row && row.warrant ? String(row.warrant) : (dateVal || '');
                    var lLabel = row && row.label ? String(row.label) : 'Sold';
                    return '<tr class="serial-row locked-serial-row" data-locked="1">' +
                        '<td class="row-index text-center"></td>' +
                        '<td class="col-warranty"><input type="date" class="form-control input-sm serial-warranty locked-field" value="' + escapeHtml(lDate) + '" readonly style="background:#eeeeee;"></td>' +
                        '<td class="col-serial"><input type="text" class="form-control input-sm serial-input locked-field" value="' + escapeHtml(lSerial) + '" readonly style="background:#eeeeee;">' +
                        ' <small class="text-muted"><i class="fa fa-lock"></i> ' + escapeHtml(lLabel) + '</small></td>' +
                        '<td class="text-center"><i class="fa fa-lock text-muted" title="' + escapeHtml(lLabel) + ' - cannot be changed or removed"></i></td>' +
                        '</tr>';
                }
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

            function editableRows() {
                return $rows.find('.serial-row').not('.locked-serial-row');
            }

            function renumberRows() {
                $rows.find('.serial-row').each(function (idx) {
                    jQuery(this).find('.row-index').text(idx + 1);
                });
                // Sold units are listed but never counted as entered serials
                jQuery('#peSerialEnteredQty').text(editableRows().find('.serial-input').length);
            }

            function addRow(row, dateVal, locked) {
                $rows.append(rowHtml(row, dateVal, locked));
                renumberRows();
            }

            // Open modal for a specific purchase item row
            jQuery(document).on('click', '.pe-add-serial-btn', function () {
                // Nested-modal fix: reuse a single instance (a re-injected edit screen
                // can leave duplicate copies) and move it out of the parent dialog
                // (whose CSS transform would otherwise trap and clip it) before showing.
                $modal = peSerialModal();
                $modal.not('body > .modal').detach().appendTo('body');
                $rows = $modal.find('#peSerialRowsContainer');

                var $btn = jQuery(this);
                current.prodId = $btn.data('prod-id');
                // data-qty already excludes the sold/locked units (kept in sync by
                // peSyncSerialQty whenever the row quantity changes), so an increased
                // quantity immediately produces the extra serial fields below.
                current.qty = parseInt($btn.attr('data-qty'), 10) || 0;
                current.serialStatus = parseInt($btn.attr('data-serial-status'), 10) || 0;
                current.warrantyStatus = parseInt($btn.attr('data-warranty-status'), 10) || 0;

                jQuery('#peSerialModalProduct').text('- ' + $btn.attr('data-prod-name'));
                var locked = window.peLockedCount(current.prodId);
                var editableQty = current.qty;
                if (editableQty < 0) { editableQty = 0; }
                jQuery('#peSerialRequiredQty').text(editableQty);
                if (locked > 0) {
                    jQuery('#peSerialQtyAlert .pe-locked-note').text(' (' + locked + ' sold/locked unit(s) shown read-only)').show();
                } else {
                    jQuery('#peSerialQtyAlert .pe-locked-note').text('').hide();
                }
                
                var defaultDate = jQuery('#warrantyData_' + current.prodId).val() || '';
                var existing = current.serialStatus === 1 ? window.peParseSerials(current.prodId) : [];
                var soldList = window.peParseLockedSerials(current.prodId);
                
                // Sold units first (frozen), then one editable row per remaining unit -
                // new empty fields appear automatically whenever quantity was increased
                $rows.empty();
                for (var s = 0; s < soldList.length; s++) {
                    addRow(soldList[s], defaultDate, true);
                }
                var needRows = (current.serialStatus === 1 || current.warrantyStatus === 1) ? editableQty : 0;
                for (var r = 0; r < needRows; r++) {
                    addRow(existing[r] !== undefined ? existing[r] : {}, defaultDate);
                }

                var showSerial = current.serialStatus === 1;
                var showWarranty = current.warrantyStatus === 1;
                jQuery('#peSerialQtyAlert').toggle(showSerial || soldList.length > 0);
                jQuery('#peSerialColHeader').toggle(showSerial || soldList.length > 0);
                jQuery('#peWarrantyColHeader').toggle(showWarranty);
                $rows.find('.col-serial').toggle(showSerial || soldList.length > 0);
                $rows.find('.col-warranty').toggle(showWarranty);

                renumberRows();
                $modal.modal('show');
            });

            jQuery(document).on('click', '#peAddSerialRowBtn', function () {
                var d = editableRows().find('.serial-warranty').first().val() || '';
                addRow({}, d);
            });

            jQuery(document).on('click', '#peRemoveLastRowBtn', function () {
                // Never remove a protected (already in-stock) or sold serial row
                var $removable = editableRows().filter(function () {
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
                jQuery('#peSerialEnteredQty').text(editableRows().find('.serial-input').length);
            });

            // Keep the Company Warranty End Date identical across all editable serial
            // rows; sold rows keep their own stored date and are never overwritten
            jQuery(document).on('input change', '.serial-warranty', function () {
                if (jQuery(this).hasClass('locked-field')) { return; }
                var v = jQuery(this).val();
                editableRows().find('.serial-warranty').not(this).val(v);
            });

            // Save serials back to the hidden payload fields of the row
            jQuery(document).on('click', '#peSaveSerialBtn', function () {
                var rows = [];
                var valid = true;

                // Sold rows are display only - they never enter the submitted payload
                editableRows().each(function () {
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
                    if (rows.length !== current.qty) {
                        alert('Serial count (' + rows.length + ') must equal the editable quantity (' + current.qty + ').');
                        return;
                    }
                    var serials = rows.map(function (r) { return r.serial; });
                    var unique = serials.filter(function (v, i) { return serials.indexOf(v) === i; });
                    if (unique.length !== serials.length) {
                        alert('Duplicate serial numbers are not allowed.');
                        return;
                    }
                    // A sold serial number still exists globally, so reusing it would be
                    // rejected on save - report it here instead
                    var soldSerials = window.peParseLockedSerials(current.prodId).map(function (r) {
                        return String(r.serial == null ? '' : r.serial).trim();
                    });
                    var clash = serials.filter(function (v) { return soldSerials.indexOf(v) !== -1; });
                    if (clash.length) {
                        alert("Serial '" + clash[0] + "' is already sold and cannot be reused.");
                        return;
                    }
                }

                var warranty = editableRows().find('.serial-warranty').first().val()
                    || jQuery('#warrantyData_' + current.prodId).val() || '';
                if (current.warrantyStatus === 1 && !warranty) {
                    alert('Company Warranty End Date is required.');
                    return;
                }

                jQuery('#serialData_' + current.prodId).val(rows.length ? JSON.stringify(rows) : '');
                jQuery('#warrantyData_' + current.prodId).val(warranty);
                window.peUpdateSerialBadge(current.prodId);

                $modal.modal('hide');
            });

            // Final submit guard: validate serial_status and warranty_status independently
            document.addEventListener('submit', function (e) {
                if (!e.target || e.target.id !== 'editPurchaseForm') { return; }
                if (!window.jQuery) { return; }
                var $ = jQuery;
                var block = null;

                // Sold units are permanent: quantity may only be increased, never
                // dropped below what has already left the stock.
                $('input[name="qty[]"]').each(function () {
                    var $hidden = $(this);
                    var sold = parseFloat($hidden.attr('data-sold')) || 0;
                    if (sold <= 0) { return; }
                    var qty = parseFloat($hidden.val()) || 0;
                    if (qty < sold - 0.000001) {
                        block = 'Quantity of "' + $hidden.attr('data-prod-name') + '" cannot be less than the sold quantity (' + window.peTrimQtyStr(sold) + ').';
                        return false;
                    }
                });
                if (block) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert(block);
                    return;
                }

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
                        // A reduced quantity can leave no editable unit at all (everything
                        // sold): then no serial is required, matching the server rule
                        // "serials required only when quantity - sold > 0".
                        if (qty > 0 && list.length === 0) {
                            block = 'Please add serial numbers for "' + $btn.attr('data-prod-name') + '".';
                            return false;
                        }
                        if (list.length !== qty) {
                            block = qty === 0
                                ? 'No serial number is needed for "' + $btn.attr('data-prod-name') + '" (all units are already sold) - clear them in the Serial / Warranty window.'
                                : 'Serial count for "' + $btn.attr('data-prod-name') + '" (' + list.length + ') must equal quantity (' + qty + ').';
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