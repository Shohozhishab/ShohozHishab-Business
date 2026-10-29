<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Existing Products Add</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Existing Products Add</li>
        </ol>
    </section>

    <style>
    .sw-wrap{position:relative;display:inline-block;cursor:pointer;margin-left:8px;vertical-align:middle;user-select:none;}
    .sw-wrap input.sw-box{position:absolute;opacity:0;width:40px;height:20px;margin:0;cursor:pointer;z-index:2;}
    .sw-slider{display:inline-block;width:40px;height:20px;background:#ccc;border-radius:20px;position:relative;transition:.2s;vertical-align:middle;}
    .sw-slider:before{content:"";position:absolute;height:16px;width:16px;left:2px;top:2px;background:#fff;border-radius:50%;transition:.2s;}
    .sw-box:checked + .sw-slider{background:#3c8dbc;}
    .sw-box:checked + .sw-slider:before{transform:translateX(20px);}
    .sw-label{font-weight:normal;margin-left:4px;}
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu; ?>
            </div>
            <div class="col-xs-12">
                <form id="geniusform" action="<?php echo base_url('Admin/Products/add_action') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-4">
                                    <h3 class="box-title">Existing Products Add</h3>
                                </div>
                                <div class="col-lg-8">
                                </div>
                                <div class="col-lg-12" style="margin-top: 20px;" id="message">
                                    <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                                </div>
                            </div>


                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-group col-xs-3">
                                <label for="int">Category </label>
                                <select class="form-control" onchange="showSubCategory(this.value)" name="category" id="category">
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
                                <label for="varchar">Product Name </label> <span id="nameValid" style="color: red;"></span>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name"/>
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
                            <div class="form-group col-xs-3" id="serialBtnWrap" style="display:none;margin-top:25px;">
                                <button type="button" class="btn btn-info btn-sm" id="openSerialModal">
                                    <i class="fa fa-barcode"></i> Add Serial / Warranty
                                    <span class="badge" id="serialBadge" style="display:none;background:#3c8dbc;"></span>
                                </button>
                            </div>
                            <!-- Hidden payload -->
                            <input type="hidden" name="serialData" id="serialDataHidden" value="">
                            <input type="hidden" name="warrantyData" id="warrantyDataHidden" value="">

                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-primary ">Add</button>
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </form>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<!-- Serial / Warranty Modal -->
<div class="modal fade" id="serialModal" tabindex="-1" role="dialog" aria-labelledby="serialModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="serialModalLabel">Serial Numbers / Warranty</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="serialQtyAlert" style="padding:8px 12px;margin-bottom:12px;">
                    Entered: <b id="serialEnteredQty">0</b>
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
                <button type="button" class="btn btn-primary" id="saveSerialBtn"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<!-- /Serial / Warranty Modal -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    var $modal = jQuery('#serialModal');
    var $rows = jQuery('#serialRowsContainer');

    // Toggle label text on switch change.
    jQuery(document).on('change', '.sw-box', function () {
        jQuery(this).closest('.sw-wrap').find('.sw-label').text(this.checked ? 'On' : 'Off');
        updateSerialBtnVisibility();
    });

    function isSerialOn() { return jQuery('#serial_status').is(':checked'); }
    function isWarrantyOn() { return jQuery('#warranty_status').is(':checked'); }

    function updateSerialBtnVisibility() {
        if (isSerialOn() || isWarrantyOn()) {
            jQuery('#serialBtnWrap').show();
        } else {
            jQuery('#serialBtnWrap').hide();
            // Clear data when both off.
            jQuery('#serialDataHidden').val('');
            jQuery('#warrantyDataHidden').val('');
            jQuery('#serialBadge').hide();
        }
    }

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

    // Open modal.
    jQuery('#openSerialModal').on('click', function () {
        var showSerial = isSerialOn();
        var showWarranty = isWarrantyOn();

        var existing = [];
        var raw = jQuery('#serialDataHidden').val();
        if (raw) { try { existing = JSON.parse(raw); } catch(e) { existing = []; } }
        if (!Array.isArray(existing)) existing = [];

        var defaultDate = jQuery('#warrantyDataHidden').val() || '';

        $rows.empty();
        if (existing.length > 0) {
            for (var r = 0; r < existing.length; r++) {
                addRow(existing[r], defaultDate);
            }
        } else {
            addRow('', defaultDate); // Start with one row.
        }

        // Toggle column visibility.
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

    // Sync warranty date across all rows.
    jQuery(document).on('input change', '.serial-warranty', function () {
        var v = jQuery(this).val();
        $rows.find('.serial-warranty').not(this).val(v);
    });

    // Save modal data to hidden fields.
    jQuery('#saveSerialBtn').on('click', function () {
        var serials = [];
        if (isSerialOn()) {
            var valid = true;
            $rows.find('.serial-input').each(function () {
                var v = jQuery(this).val().trim();
                if (v === '') { valid = false; }
                serials.push(v);
            });
            if (!valid) { alert('All serial numbers must be filled.'); return; }
            var unique = serials.filter(function (v, i) { return serials.indexOf(v) === i; });
            if (unique.length !== serials.length) { alert('Duplicate serial numbers are not allowed.'); return; }
        }

        var warranty = $rows.find('.serial-warranty').first().val() || '';
        if (isWarrantyOn() && !warranty) { alert('Company Warranty End Date is required.'); return; }

        jQuery('#serialDataHidden').val(serials.length ? JSON.stringify(serials) : '');
        jQuery('#warrantyDataHidden').val(warranty);

        var $badge = jQuery('#serialBadge');
        if (serials.length > 0) {
            $badge.text(serials.length + (warranty ? ' / W' : '')).show();
        } else if (warranty) {
            $badge.text('W').show();
        } else {
            $badge.hide();
        }

        $modal.modal('hide');
    });

    // Submit guard for the geniusform (runs before ajaxScript handler).
    jQuery('#geniusform').on('submit', function (e) {
        if (isSerialOn()) {
            var raw = jQuery('#serialDataHidden').val();
            var list = [];
            if (raw) { try { list = JSON.parse(raw); } catch(ex) { list = []; } }
            if (!Array.isArray(list)) list = [];
            if (list.length === 0) {
                alert('Please add serial numbers (Serial Number is ON).');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }
        if (isWarrantyOn() && !jQuery('#warrantyDataHidden').val()) {
            alert('Company warranty end date is required (Warranty is ON).');
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    });

    // Reset serial/warranty UI after form is reset (ajaxScript.php calls form.reset() on success).
    jQuery('#geniusform').on('reset', function () {
        setTimeout(function () {
            jQuery('.sw-box').each(function () {
                jQuery(this).closest('.sw-wrap').find('.sw-label').text('Off');
            });
            jQuery('#serialBtnWrap').hide();
            jQuery('#serialDataHidden').val('');
            jQuery('#warrantyDataHidden').val('');
            jQuery('#serialBadge').hide();
            jQuery('#serialRowsContainer').empty();
        }, 50);
    });
});
</script>
