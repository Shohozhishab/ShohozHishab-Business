<style>
.sw-wrap{position:relative;display:inline-block;cursor:pointer;margin-left:8px;vertical-align:middle;user-select:none;}
.sw-wrap input.sw-box{position:absolute;opacity:0;width:40px;height:20px;margin:0;cursor:pointer;z-index:2;}
.sw-slider{display:inline-block;width:40px;height:20px;background:#ccc;border-radius:20px;position:relative;transition:.2s;vertical-align:middle;}
.sw-slider:before{content:"";position:absolute;height:16px;width:16px;left:2px;top:2px;background:#fff;border-radius:50%;transition:.2s;}
.sw-box:checked + .sw-slider{background:#3c8dbc;}
.sw-box:checked + .sw-slider:before{transform:translateX(20px);}
.sw-label{font-weight:normal;margin-left:4px;}
</style>
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
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Product Add</h3>
                                        <p style="float: right;">Supplier: <?= $supplier->name;?>. Balance: <?= showWithCurrencySymbol($supplier->balance);?></p>
                                    </div>
                                    <div class="box-body ">
                                        <form id="adtoCartform" action="<?= site_url('Admin/Purchase/addCart') ?>" method="post">
                                            <div class="form-group col-xs-3">
                                                <label for="int">Category </label>
                                                <select class="form-control" onchange="showSubCategory(this.value)"
                                                        name="category" id="category">
                                                    <option value="">Please Select</option>
                                                    <?php echo getCatListInOption('prod_cat_id', 'prod_cat_id', 'product_category', 'product_category'); ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-xs-3">
                                                <label for="int">Sub Category </label>
                                                <select class="form-control" name="sub_category" id="subCat">
                                                    <option value="">Please Select</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-xs-3">
                                                <label for="varchar">Product Name </label> <span id="nameValid"
                                                                                                 style="color: red;"></span>
                                                <input type="text" class="form-control" name="name" id="name"
                                                       placeholder="Name"/>
                                            </div>
                                            <div class="form-group col-xs-3">
                                                <label for="int">Unit Set</label>
                                                <select class="form-control" name="categories_id" id="categories_id" onchange="unitShow(this.value)" >
                                                    <option value="">Please Select</option>
                                                    <?php foreach ($unit_set as $cat){ ?>
                                                    <option value="<?= $cat->unit_set_id;?>" <?= ($cat->default_set == '1')?'Selected':'';?> ><?= $cat->name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div id="unitData"></div>

                                            <div class="form-group col-xs-3">
                                                <label for="serial_status" style="margin-bottom:0;">Serial Number</label>
                                                <span class="sw-wrap">
                                                    <input type="hidden" name="serial_status" value="0">
                                                    <input type="checkbox" class="sw-box" name="serial_status" id="serial_status" value="1">
                                                    <span class="sw-slider"></span>
                                                    <span class="sw-label">Off</span>
                                                </span>
                                            </div>
                                            <div class="form-group col-xs-3">
                                                <label for="warranty_status" style="margin-bottom:0;">Company Warranty</label>
                                                <span class="sw-wrap">
                                                    <input type="hidden" name="warranty_status" value="0">
                                                    <input type="checkbox" class="sw-box" name="warranty_status" id="warranty_status" value="1">
                                                    <span class="sw-slider"></span>
                                                    <span class="sw-label">Off</span>
                                                </span>
                                            </div>

                                            <div class="form-group col-xs-3 " style="margin-top: 25px;">
                                                <button type="submit" class="form-control btn btn-info btn-xs geniusSubmit-btn">Add Cart </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                        <form action="<?php echo $action; ?>" method="post">
                            <input type="hidden" class="form-control" name="purchase_id" value="<?php echo $purchaseId; ?>">
                            <input type="hidden" class="form-control" name="supplier_id" value="<?php echo $supplierId; ?>">
                            <div class="col-lg-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Product List</h3>
                                    </div>
                                    <div class="box-body ">
                                        <table class="table table-bordered table-striped" id="TFtable">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Unit</th>
                                                <th>Purchase Price</th>
                                                <th>Selling Price</th>
                                                <th>Quantity</th>
                                                <th>Sub Total</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach (Cart()->contents() as $row) {
                                                $serialStatus = (int)($row['serial_status'] ?? 0);
                                                $warrantyStatus = (int)($row['warranty_status'] ?? 0);
                                            ?>
                                                <tr class="product-row" data-row-idx="<?= $i; ?>" data-serial-status="<?= $serialStatus; ?>" data-warranty-status="<?= $warrantyStatus; ?>" data-qty="<?= (int)$row['qty']; ?>">
                                                    <td width=""><input type="hidden" class="form-control"
                                                                        name="prod_cat_id[]"
                                                                        id="prod_cat_id[]"
                                                                        value="<?php echo $row['cat_id']; ?>"/><?php echo ++$i; ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control" name="name[]"
                                                               id="name"
                                                               value="<?php echo $row['name']; ?>"/><?php echo $row['name']; ?>
                                                        <input type="hidden" name="serialStatus[]" value="<?= $serialStatus; ?>">
                                                        <input type="hidden" name="warrantyStatus[]" value="<?= $warrantyStatus; ?>">
                                                        <input type="hidden" name="serialData[<?= $i - 1; ?>]" id="serialData_<?= $i - 1; ?>" value="">
                                                        <input type="hidden" name="warrantyData[<?= $i - 1; ?>]" id="warrantyData_<?= $i - 1; ?>" value="">
                                                    </td>
                                                    <td><input type="hidden" class="form-control" name="unit[]"
                                                               id="unit"
                                                               value="<?php echo $row['unit']; ?>"/><?php echo showUnitName($row['unit']); ?>
                                                        <input type="hidden" name="unit_set_id[]" value="<?php echo $row['unit_set_id']; ?>">
                                                    </td>
                                                    <td><input type="hidden" class="form-control purchase_price"
                                                               name="purchase_price[]" id="purchase_price"
                                                               value="<?php echo $row['price']; ?>"/><?php echo showWithCurrencySymbol(unitOrBasePriceByUnitPrice($row['unit'],$row['price'])); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control selling_price"
                                                               name="selling_price[]" id="selling_price"
                                                               value="<?php echo $row['salePrice']; ?>"/><?php echo showWithCurrencySymbol(unitOrBasePriceByUnitPrice($row['unit'],$row['salePrice'])); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control quantity"
                                                               name="quantity[]"
                                                               placeholder="Quantity"
                                                               value="<?php echo $row['qty']; ?>"/>
                                                        <?php echo unitOrQtyByUnitQty($row['unit'],$row['qty']); ?>/<?php echo showUnitName($row['unit']); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control"
                                                               name="total_price[]"
                                                               value="<?php echo $row['subtotal']; ?>"/><?php echo showWithCurrencySymbol($row['subtotal']); ?>
                                                    </td>
                                                    <td width="180px">
                                                        <?php if ($serialStatus == 1 || $warrantyStatus == 1): ?>
                                                            <button type="button" class="btn btn-info btn-xs add-serial-btn"
                                                                    data-row-idx="<?= $i - 1; ?>"
                                                                    data-qty="<?= (int)$row['qty']; ?>"
                                                                    data-serial-status="<?= $serialStatus; ?>"
                                                                    data-warranty-status="<?= $warrantyStatus; ?>"
                                                                    data-prod-name="<?= htmlspecialchars($row['name'], ENT_QUOTES); ?>">
                                                                <i class="fa fa-barcode"></i> Add Serial
                                                                <span class="badge serial-badge" id="serialBadge_<?= $i - 1; ?>" style="display:none;background:#3c8dbc;"></span>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button class="btn btn-xs btn-danger" type="button"
                                                                id="<?php echo $row['rowid'] ?>"
                                                                onclick="remove_cart(this)">
                                                            Cancel
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th colspan="8">
                                                    <button style="float: right; margin-right: 40px;"
                                                            onclick="clearCart()"
                                                            class="btn btn-info btn-xs" type="button">Clear All
                                                    </button>
                                                </th>
                                            </tr>
                                            </tfoot>

                                        </table>
                                        <!-- <div id="cart"></div> -->
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
                                                        <input type="number" onchange="checkShopsBalance(this.value)"
                                                               class="cash form-control"
                                                               oninput="minusValueCheck(this.value,this)" name="cash"
                                                               id="cash"><b
                                                                id="Balance_valid"></b>
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

                                                <button type="submit" class="btn btn-primary" >Create
                                                </button>
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
                                                    <th id="warrantyColHeader">Company Warranty End Date</th>
                                                    <th id="serialColHeader">Serial Number</th>
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
    // Serial / Warranty modal logic for new_product page.
    document.addEventListener('DOMContentLoaded', function () {
        // Update switch label text on toggle.
        jQuery(document).on('change', '.sw-box', function () {
            jQuery(this).closest('.sw-wrap').find('.sw-label').text(this.checked ? 'On' : 'Off');
        });

        var $modal = jQuery('#serialModal');
        var $rows = jQuery('#serialRowsContainer');
        var current = { rowIdx: null, qty: 0, serialStatus: 0, warrantyStatus: 0 };

        function escapeHtml(str) {
            return jQuery('<i>').text(str == null ? '' : String(str)).html();
        }

        function rowHtml(value, dateVal) {
            return '<tr class="serial-row">' +
                '<td class="row-index text-center"></td>' +
                '<td class="col-warranty"><input type="date" class="form-control input-sm serial-warranty" value="' + escapeHtml(dateVal || '') + '"></td>' +
                '<td class="col-serial"><input type="text" class="form-control input-sm serial-input" placeholder="Enter serial number" value="' + escapeHtml(value) + '"></td>' +
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

        // Open modal for a specific product row.
        jQuery(document).on('click', '.add-serial-btn', function () {
            var $btn = jQuery(this);
            current.rowIdx = $btn.data('row-idx');
            current.qty = parseInt($btn.data('qty'), 10) || 0;
            current.serialStatus = parseInt($btn.data('serial-status'), 10) || 0;
            current.warrantyStatus = parseInt($btn.data('warranty-status'), 10) || 0;

            jQuery('#serialModalProduct').text('- ' + $btn.data('prod-name'));
            jQuery('#serialRequiredQty').text(current.qty);

            var defaultDate = jQuery('#warrantyData_' + current.rowIdx).val() || '';

            $rows.empty();
            var needRows = (current.serialStatus === 1 || current.warrantyStatus === 1) ? current.qty : 0;
            var existing = [];
            if (current.serialStatus === 1) {
                var raw = jQuery('#serialData_' + current.rowIdx).val();
                if (raw) {
                    try { existing = JSON.parse(raw); } catch (e) { existing = []; }
                }
                if (!Array.isArray(existing)) existing = [];
            }
            for (var r = 0; r < needRows; r++) {
                addRow(existing[r] !== undefined ? existing[r] : '', defaultDate);
            }

            // Show only columns whose status is on.
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

        // Sync warranty date across rows.
        jQuery(document).on('input change', '.serial-warranty', function () {
            var v = jQuery(this).val();
            $rows.find('.serial-warranty').not(this).val(v);
        });

        // Save serials back to hidden fields.
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

            jQuery('#serialData_' + current.rowIdx).val(serials.length ? JSON.stringify(serials) : '');
            jQuery('#warrantyData_' + current.rowIdx).val(warranty);

            var $badge = jQuery('#serialBadge_' + current.rowIdx);
            if (serials.length > 0) {
                $badge.text(serials.length + (warranty ? ' / W' : '')).show();
            } else {
                $badge.hide();
            }

            $modal.modal('hide');
        });

        // Submit guard: validate serial and warranty per row independently.
        jQuery('form[action*="product_create_action"]').on('submit', function () {
            var block = null;
            jQuery('.add-serial-btn').each(function () {
                var $btn = jQuery(this);
                var idx = $btn.data('row-idx');
                var qty = parseInt($btn.data('qty'), 10) || 0;
                var serialOn = parseInt($btn.data('serial-status'), 10) === 1;
                var warrantyOn = parseInt($btn.data('warranty-status'), 10) === 1;

                if (serialOn) {
                    var raw = jQuery('#serialData_' + idx).val();
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

                if (warrantyOn && !jQuery('#warrantyData_' + idx).val()) {
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