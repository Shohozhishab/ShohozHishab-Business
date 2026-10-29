<style>
    .sw-wrap{position:relative;display:inline-block;cursor:pointer;margin-left:8px;vertical-align:middle;user-select:none;}
    .sw-wrap input.sw-box{position:absolute;opacity:0;width:40px;height:20px;margin:0;cursor:pointer;z-index:2;}
    .sw-slider{display:inline-block;width:40px;height:20px;background:#ccc;border-radius:20px;position:relative;transition:.2s;vertical-align:middle;}
    .sw-slider:before{content:"";position:absolute;height:16px;width:16px;left:2px;top:2px;background:#fff;border-radius:50%;transition:.2s;}
    .sw-box:checked + .sw-slider{background:#3c8dbc;}
    .sw-box:checked + .sw-slider:before{transform:translateX(20px);}
    .sw-label{font-weight:normal;margin-left:4px;}
</style>
<?php
    // Serial & warranty data is always computed so the Serial tab can be
    // revealed live when a toggle switch is turned on. The tab starts visible
    // only if a flag is already saved ON; otherwise it is hidden until toggled.
    $serialTab           = $serialTab ?? product_serial_tab_data($product);
    $serialOn            = ((int)($product->serial_status ?? 0) === 1);
    $warrantyOn          = ((int)($product->warranty_status ?? 0) === 1);
    $serialsOnTab        = $serialTab['enabled'];
    $serialPaneAvailable = !empty($serialTab['relation']);
    // Bulk-stocked product: the one-row-per-unit grid cannot be rendered safely
    $serialTooLarge      = !empty($serialTab['tooLarge']);
    $serialRowLimit      = (int)($serialTab['rowLimit'] ?? SERIAL_ENTRY_ROW_LIMIT);
?>
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Products Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products</li>
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
                        <h3 class="box-title">Products Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <!-- Nav tabs -->
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active "><a href="#home" aria-controls="home"
                                                                               role="tab" data-toggle="tab">General</a></li>
                                    <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab"
                                                                data-toggle="tab">Personal</a></li>
                                    <li role="presentation" ><a href="#unit" aria-controls="unit" role="tab"
                                                                data-toggle="tab">Unit Tab</a></li>
                                    <?php if ($serialPaneAvailable || $serialsOnTab): ?>
                                    <li role="presentation" id="serialTabItem" style="<?= $serialsOnTab ? '' : 'display:none;'; ?>"><a href="#serials" aria-controls="serials" role="tab"
                                                                data-toggle="tab">Serial</a></li>
                                    <?php endif; ?>
                                    <li role="presentation" ><a href="#messages" aria-controls="messages" role="tab"
                                                                data-toggle="tab">Photo</a></li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content " style="margin-top: 60px;">
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    <div class="col-lg-6 ">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Products/general_update'); ?>" method="post">


                                            <div class="form-group">
                                                <label for="varchar">Name </label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $product->name; ?>" >
                                            </div>

                                            <div class="form-group">
                                                <label for="int">Supplier</label>

                                                <select class="form-control" name="supplier_id" id="supplier_id">
                                                    <option>Please Select</option>
                                                    <?php echo getAllListInOption($product->supplier_id,'supplier_id','name','suppliers'); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="serial_number" style="margin-bottom:0;">Serial Number</label>
                                                <span class="sw-wrap">
                                                    <input type="hidden" name="serial_status" value="0">
                                                    <input type="checkbox" class="sw-box serial-warranty-toggle" id="serial_status_toggle" name="serial_status" value="1"
                                                           <?= (($product->serial_status ?? '0') == 1) ? 'checked' : '' ?>>
                                                    <span class="sw-slider"></span>
                                                    <span class="sw-label"><?php echo (($product->serial_status ?? '0') == 1) ? 'On' : 'Off'; ?></span>
                                                </span>
                                            </div>

                                            <div class="form-group">
                                                <label for="warranty" style="margin-bottom:0;">Warranty</label>
                                                <span class="sw-wrap">
                                                    <input type="hidden" name="warranty_status" value="0">
                                                    <input type="checkbox" class="sw-box serial-warranty-toggle" id="warranty_status_toggle" name="warranty_status" value="1"
                                                           <?= (($product->warranty_status ?? '0') == 1) ? 'checked' : '' ?>>
                                                    <span class="sw-slider"></span>
                                                    <span class="sw-label"><?php echo (($product->warranty_status ?? '0') == 1) ? 'On' : 'Off'; ?></span>
                                                </span>
                                            </div>

                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" >
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn"> Update </button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="profile">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Products/personal_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="int">Category </label>
                                                <select class="form-control" onchange="showSubCategory(this.value)" name="prod_cat_id" id="prod_cat_id">
                                                    <option>Please Select</option>
                                                    <?php
                                                    echo categoryListInOption($product->prod_cat_id);
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Sub Category </label>
                                                <select class="form-control" name="sub_cat_id" id="subCat">
                                                    <option value="">Please Select</option>
                                                    <?php echo subCatListInOption($product->prod_cat_id); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Brand</label>
                                                <select class="form-control" name="brand_id" >
                                                    <option value="">Please Select</option>
                                                    <?php echo getListInOption($product->brand_id,'brand_id', 'name','brand')?>
                                                </select>

                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Size</label>
                                                <input type="text" class="form-control" name="size" id="size" placeholder="Size" value="<?php echo $product->size; ?>" />
                                            </div>
                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane " id="unit">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Products/unit_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Sell Unit</label>
                                                <div id="sell_units_container">
                                                <?php
                                                    $sale_units = json_decode($product->sale_units);
                                                    foreach ($units as $item){
                                                ?>
                                                    <label style="margin-left: 10px;">
                                                        <input type="checkbox" name="sell_units[]" <?php foreach ($sale_units as $value){ echo ($item->units_id == $value)?'checked':''; }?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                                    </label>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Unit</label>
                                                <div id="sell_price_units_container">
                                                    <?php foreach ($units as $item){?>
                                                        <label style="margin-left: 10px;">
                                                            <input type="radio" name="unit" <?= ($item->units_id == $showUnit)?'checked':'';?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                                        </label>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <?php if ($serialPaneAvailable || $serialsOnTab): ?>
                                <div role="tabpanel" class="tab-pane " id="serials">
                                    <div class="col-lg-8">
                                        <?php if (empty($serialTab['relation'])): ?>
                                            <div class="alert alert-warning">No default store stock found for this product. Add stock before entering serial numbers.</div>
                                        <?php else: ?>
                                        <form id="geniusformSerialUpdate" action="<?= base_url('Admin/Products/serial_update'); ?>" method="post">
                                            <input type="hidden" name="prod_id" value="<?= $product->prod_id; ?>">
                                            <input type="hidden" name="serial_status" id="pu_serial_status" value="<?= $serialOn ? '1' : '0'; ?>">
                                            <input type="hidden" name="warranty_status" id="pu_warranty_status" value="<?= $warrantyOn ? '1' : '0'; ?>">

                                            <div id="pu-serial-info" style="<?= $serialOn ? '' : 'display:none;'; ?> margin-bottom:10px;">
                                                Stock Qty: <b><?= (int)$serialTab['quantity']; ?></b>
                                                <span class="text-muted">— editable serials: <b id="pu-expected"><?= (int)$serialTab['expectedCount']; ?></b></span>
                                                <?php if ($serialTab['lockedCount'] > 0): ?>
                                                    <span class="text-muted">(<?= (int)$serialTab['lockedCount']; ?> sold/locked serial(s) excluded)</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group" id="pu-warranty-group" style="<?= $warrantyOn ? '' : 'display:none;'; ?>">
                                                <label for="pu-warranty">Company Warranty End Date <small class="text-muted">(sets all serial rows — each row stays individually editable)</small></label>
                                                <input type="date" class="form-control" id="pu-warranty" name="warrantyData" value="<?= esc($serialTab['warrantyDate']); ?>" style="max-width:220px;">
                                                <input type="hidden" id="pu-warranty-initial" value="<?= esc($serialTab['warrantyDate']); ?>">
                                            </div>

                                            <?php if ($serialTooLarge): ?>
                                            <!-- Stock too large for the per-unit grid: render a notice only,
                                                 otherwise the row loop below would build millions of rows -->
                                            <div id="pu-serial-overlimit" class="alert alert-warning" style="margin-bottom:0;<?= $serialOn ? '' : 'display:none;'; ?>">This product's editable quantity (<b><?= (int)$serialTab['expectedCount']; ?></b> unit<?= $serialTab['expectedCount'] == 1 ? '' : 's'; ?>) exceeds the maximum of <b><?= $serialRowLimit; ?></b> serial rows, so serial numbers cannot be entered here. The company warranty date can still be saved.</div>
                                            <?php else: ?>
                                            <div id="pu-serial-block" style="max-height:320px;overflow-y:auto;border:1px solid #ddd;border-radius:4px;<?= $serialOn ? '' : 'display:none;'; ?>">
                                                <table class="table table-bordered table-condensed" style="margin-bottom:0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:50px;">#</th>
                                                        <th class="pu-w-col" style="width:200px;<?= $warrantyOn ? '' : 'display:none;'; ?>">Company Warranty End Date</th>
                                                        <th>Serial Number</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="serialTableBody">
                                                    <?php
                                                        $rowCount = max((int)$serialTab['expectedCount'], count($serialTab['serials']));
                                                        for ($i = 0; $i < $rowCount; $i++):
                                                            $row = $serialTab['serials'][$i] ?? null;
                                                    ?>
                                                    <tr class="pu-serial-row" data-id="<?= (int)($row['product_serial_id'] ?? 0); ?>">
                                                        <td><?= $i + 1; ?></td>
                                                        <td class="pu-w-col" style="<?= $warrantyOn ? '' : 'display:none;'; ?>"><input type="date" class="form-control pu-warranty-cell input-sm" value="<?= esc(!empty($row['company_warranty_end_date']) ? $row['company_warranty_end_date'] : $serialTab['warrantyDate']); ?>"></td>
                                                        <td><input type="text" class="form-control pu-serial-cell input-sm" value="<?= esc($row['serial_number'] ?? ''); ?>" placeholder="Serial number"></td>
                                                    </tr>
                                                    <?php endfor; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php if ($rowCount == 0): ?>
                                                <div id="pu-serial-empty" class="alert alert-info" style="margin-bottom:0;<?= $serialOn ? '' : 'display:none;'; ?>">There is no in-stock quantity to attach serial numbers to yet.</div>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <div style="margin-top:15px;">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
                                            </div>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-lg-4"></div>
                                </div>
                                <?php endif; ?>

                                <div role="tabpanel" class="tab-pane " id="messages">
                                    <div class="col-lg-6">
                                        <form id="geniusform" action="<?php echo base_url('Admin/Products/photo_update'); ?>" method="post" enctype="multipart/form-data" >
                                            <div class="form-group" id="reloadimg" >
                                                <?php $proimg =  no_image_view('/uploads/product_image/'.$product->picture,'/uploads/product_image/no_image.jpg',$product->picture); ?>
                                                <img src="<?php echo $proimg; ?>" >
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Photo </label>
                                                <input type="file" class="form-control" name="picture" id="picture" required >
                                                <span class="help-block" style="color: #ff0000;"><b>Max. file size 1024KB and (width=300px) x (height=300px)</b></span>
                                            </div>



                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
                            </div>
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
<?php if ($serialPaneAvailable): ?>
<script>
// On a full page render this view is echoed before Admin/footer.php loads jQuery,
// so wait until jQuery exists. On AJAX re-injection it's already there and runs immediately.
(function puWaitJQuery() {
    if (typeof window.jQuery === 'undefined') {
        setTimeout(puWaitJQuery, 50);   // footer not parsed yet — wait
        return;
    }
    (function ($) {
    'use strict';

    // The page can be re-injected via AJAX many times — bind document handlers only once
    var alreadyBound = !!window.puSerialTabBound;
    window.puSerialTabBound = true;

    // Live state comes from the General tab toggle switches, not the saved flags
    function isSerialOn()   { return $('#serial_status_toggle').is(':checked'); }
    function isWarrantyOn() { return $('#warranty_status_toggle').is(':checked'); }

    // Reveal / hide the Serial tab and its inner blocks as the switches change
    function syncSerialTab() {
        var serialOn = isSerialOn();
        var warrantyOn = isWarrantyOn();

        // Show the tab whenever at least one switch is on
        if (serialOn || warrantyOn) {
            $('#serialTabItem').show();
        } else {
            $('#serialTabItem').hide();
        }

        // Mirror the switch state into the serial form's hidden flags (used by serial_update)
        $('#pu_serial_status').val(serialOn ? '1' : '0');
        $('#pu_warranty_status').val(warrantyOn ? '1' : '0');

        // Warranty end date group + per-row warranty column follow the warranty switch
        $('#pu-warranty-group').toggle(warrantyOn);
        $('#serialTableBody .pu-w-col, #serials .pu-w-col').toggle(warrantyOn);

        // Serial info + serial table follow the serial switch
        $('#pu-serial-info').toggle(serialOn);
        $('#pu-serial-block').toggle(serialOn);
        $('#pu-serial-empty').toggle(serialOn);
        $('#pu-serial-overlimit').toggle(serialOn);
    }

    // Keep the On/Off caption next to each switch in sync
    function syncSwitchLabels() {
        $('#serial_status_toggle, #warranty_status_toggle').each(function () {
            $(this).closest('.sw-wrap').find('.sw-label').text(this.checked ? 'On' : 'Off');
        });
    }

    if (!alreadyBound) {
        $(document).on('change', '.serial-warranty-toggle', function () {
            syncSwitchLabels();
            syncSerialTab();
        });

        // Editing the shared date field pushes it into every serial row; the rows
        // stay individually editable afterwards (delegated: AJAX re-injection safe)
        $(document).on('change', '#pu-warranty', function () {
            if ($(this).val()) {
                $('#serialTableBody .pu-warranty-cell').val($(this).val());
            }
        });
    }

    // Initialize on load (page may be injected via AJAX, so run immediately)
    syncSwitchLabels();
    syncSerialTab();

    // Document-delegated so the handler keeps working after the page is re-injected via AJAX
    if (!alreadyBound) {
    $(document).on('submit', '#geniusformSerialUpdate', function (e) {
        e.preventDefault();
        var form = this;
        var serialOn = isSerialOn();
        var warrantyOn = isWarrantyOn();
        // Bulk-stocked product: the grid was intentionally not rendered, so serial
        // numbers cannot be saved (warranty-only saving stays available)
        if (serialOn && $('#pu-serial-overlimit').length) {
            $('#message').html('<div class="alert alert-danger alert-dismissible" role="alert">Serial numbers cannot be entered for this stock quantity. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return;
        }
        var expected = serialOn ? (parseInt($('#pu-expected').text(), 10) || 0) : 0;
        var serials = [];
        var allFilled = true;
        var warrantyDate = $('#pu-warranty').val() || '';

        // Make sure the flags reflect the current switch state before submitting
        $('#pu_serial_status').val(serialOn ? '1' : '0');
        $('#pu_warranty_status').val(warrantyOn ? '1' : '0');

        // The shared date field means "set all rows": if it changed since the page was
        // rendered, force it into every row even if no change event fired (Enter-key submit)
        var initialShared = $('#pu-warranty-initial').val() || '';
        if (warrantyOn && serialOn && warrantyDate !== '' && warrantyDate !== initialShared) {
            $('#serialTableBody .pu-warranty-cell').val(warrantyDate);
        }

        if (!serialOn && !warrantyOn) {
            $('#message').html('<div class="alert alert-danger alert-dismissible" role="alert">Turn on Serial Number or Warranty first. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return;
        }

        var allWarranty = true;

        if (serialOn) {
            $('#serialTableBody .pu-serial-row').each(function () {
                var val = ($(this).find('.pu-serial-cell').val() || '').trim();
                if (val === '') { allFilled = false; } else { serials.push(val); }
                // A row may carry its own date; the shared field acts as fallback
                if (warrantyOn && val !== '' && !(($(this).find('.pu-warranty-cell').val() || '') || warrantyDate)) {
                    allWarranty = false;
                }
            });
        }
        var dup = serials.some(function (s, i) { return serials.indexOf(s) !== i; });

        var error = null;
        if (serialOn && serials.length !== expected) {
            error = 'Please provide exactly ' + expected + ' serial number(s) (row count follows the editable quantity).';
        } else if (serialOn && !allFilled) {
            error = 'All serial numbers must be filled.';
        } else if (dup) {
            error = 'Duplicate serial numbers are not allowed.';
        } else if (warrantyOn && serialOn && !allWarranty) {
            error = 'Company warranty end date is required for every serial row (set it per row or via the field above).';
        } else if (warrantyOn && !serialOn && warrantyDate === '') {
            error = 'Company warranty end date is required.';
        }
        if (error) {
            $('#message').html('<div class="alert alert-danger alert-dismissible" role="alert">' + error + ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return;
        }

        var payload = [];
        if (serialOn) {
            $('#serialTableBody .pu-serial-row').each(function () {
                var $row = $(this);
                var val = ($row.find('.pu-serial-cell').val() || '').trim();
                if (val !== '') {
                    payload.push({ id: parseInt($row.attr('data-id'), 10) || 0, serial: val, warranty: warrantyOn ? ($row.find('.pu-warranty-cell').val() || '') : '' });
                }
            });
        }
        var fd = new FormData(form);
        fd.set('serialData', JSON.stringify(payload));

        var $btn = $(form).find('button[type=submit]');
        $btn.prop('disabled', true);
        $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
        $.ajax({
            method: 'POST',
            url: $(form).prop('action'),
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                $btn.prop('disabled', false);
                if (res && res.status === 'success') {
                    $('#message').html('<div class="alert alert-success alert-dismissible" role="alert">' + res.message + ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    puRebuildSerialRows(res.serials || [], res.warrantyDate || '');
                } else {
                    var msg = (res && res.message) ? res.message : 'something went wrong';
                    $('#message').html('<div class="alert alert-danger alert-dismissible" role="alert">' + msg + ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                $('#message').html('<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
        });
    });
    }

    // Re-render the table from freshly saved in-stock serials (row count always equals expected qty)
    window.puRebuildSerialRows = function (rows, warrantyDate) {
        if (!isSerialOn() || $('#pu-serial-overlimit').length) { return; }
        var warrantyOn = isWarrantyOn();
        var expected = parseInt($('#pu-expected').text(), 10) || 0;
        var count = Math.min(<?= $serialRowLimit; ?>, Math.max(expected, rows.length));
        var html = '';
        for (var i = 0; i < count; i++) {
            var row = rows[i] || null;
            var serial = row && row.serial_number ? $('<span>').text(row.serial_number).html() : '';
            var id = row ? row.product_serial_id : 0;
            // Prefer the row's own saved warranty date; shared date is the fallback
            var wv = (row && row.company_warranty_end_date) ? row.company_warranty_end_date : (warrantyDate || '');
            html += '<tr class="pu-serial-row" data-id="' + id + '">';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td class="pu-w-col" style="' + (warrantyOn ? '' : 'display:none;') + '"><input type="date" class="form-control pu-warranty-cell input-sm" value="' + wv + '"></td>';
            html += '<td><input type="text" class="form-control pu-serial-cell input-sm" value="' + serial + '" placeholder="Serial number"></td>';
            html += '</tr>';
        }
        $('#serialTableBody').html(html);
    };
    })(jQuery);
})();
</script>
<?php endif; ?>