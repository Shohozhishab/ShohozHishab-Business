<?php
/**
 * Warranty Short List – module view.
 *
 * Renders products whose company_warranty_end_date falls within the dynamic
 * "next 2 months" window, in both a flat List (tree) view and a Grouped
 * (by product) view. Includes a smart-search box and colour decoration based
 * on days remaining.
 *
 * @var array $rows       Flat list of serial rows
 * @var array $grouped    Rows keyed by product
 * @var array $windowInfo ['months','from','to']
 * @var string $viewMode  'list' | 'group'
 */

/** Colour helper: red when <=30 days, orange when <=60 days. */
if (!function_exists('wsl_days_class')) {
    function wsl_days_class($days)
    {
        if ($days <= 30) { return 'wsl-danger'; }
        if ($days <= 60) { return 'wsl-warning'; }
        return 'wsl-ok';
    }
}
?>
<style>
    .wsl-legend span { display: inline-block; width: 12px; height: 12px; border-radius: 2px; margin-right: 4px; vertical-align: middle; }
    .wsl-legend .wsl-danger  { background: #dd4b39; }
    .wsl-legend .wsl-warning { background: #f39c12; }
    .wsl-legend .wsl-ok      { background: #00a65a; }
    tr.wsl-danger  > td:first-child { box-shadow: inset 3px 0 0 #dd4b39; }
    tr.wsl-warning > td:first-child { box-shadow: inset 3px 0 0 #f39c12; }
    tr.wsl-ok      > td:first-child { box-shadow: inset 3px 0 0 #00a65a; }
    .wsl-days-badge { font-weight: 700; }
    #warrantyGroupView .box-body { padding-top: 10px; }
</style>

<div class="content-wrapper" id="viewpage">
    <section class="content-header">
        <h1> Warranty Short List <small>Expiring company warranties</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Warranty Short List</li>
        </ol>
    </section>

    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu ?? ''; ?>
        </div>
        <?php if (isDefaultRole() == true): ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12">
                    <button class="btn btn-sm btn-info" style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction') ?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Warranty_short_list')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php foreach (userRole() as $val): ?>
                                                <option value="<?= $val->role_id ?>"><?= $val->role ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Warranty_short_list">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Warranty expiring in next <?= (int) $windowInfo['months'] ?> months
                        </h3>
                        <span class="label label-info pull-right" style="margin-left:10px;">
                            Window: <?= esc($windowInfo['from']) ?> &rarr; <?= esc($windowInfo['to']) ?>
                        </span>
                        <span class="label label-default pull-right">
                            Total units: <?= count($rows) ?>
                        </span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    <input type="text" id="wslSmartSearch" class="form-control"
                                           placeholder="Warranty expiring in next <?= (int) $windowInfo['months'] ?> months &mdash; search product / serial"
                                           value="<?= esc($search ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="btn-group" role="group" aria-label="View mode" style="margin-top:2px;">
                                    <button type="button" id="btnListView" class="btn btn-default <?= $viewMode === 'list' ? 'btn-primary active' : '' ?>"
                                            onclick="wslSetView('list')"><i class="fa fa-list"></i> List / Tree view</button>
                                    <button type="button" id="btnGroupView" class="btn btn-default <?= $viewMode === 'group' ? 'btn-primary active' : '' ?>"
                                            onclick="wslSetView('group')"><i class="fa fa-object-group"></i> Grouped by product</button>
                                </div>
                            </div>
                            <div class="col-md-3 wsl-legend text-right" style="margin-top:8px;">
                                <span class="wsl-danger"></span> &le; 30 days
                                <span class="wsl-warning" style="margin-left:10px;"></span> &le; 60 days
                                <span class="wsl-ok" style="margin-left:10px;"></span> &gt; 60 days
                            </div>
                        </div>

                        <?php if (session()->getFlashdata('message') !== NULL): echo session()->getFlashdata('message'); endif; ?>

                        <?php if (empty($rows)): ?>
                            <div class="alert alert-info">No products have a company warranty expiring within the next <?= (int) $windowInfo['months'] ?> months.</div>
                        <?php else: ?>

                            <!-- ============ LIST / TREE VIEW ============ -->
                            <div id="warrantyListView" style="<?= $viewMode === 'group' ? 'display:none;' : '' ?>">
                                <table class="table table-bordered table-striped table-hover" id="warrantyTable">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Serial Number</th>
                                        <th>Company Warranty End Date</th>
                                        <th>Days Remaining</th>
                                        <th>Status</th>
                                        <th>Customer / Partner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($rows as $row): $cls = wsl_days_class($row->days_remaining); ?>
                                        <tr class="<?= $cls ?>">
                                            <td><?= esc($row->product_name) ?></td>
                                            <td><?= esc($row->serial_number) ?></td>
                                            <td><?= esc($row->company_warranty_end_date) ?></td>
                                            <td><span class="wsl-days-badge"><?= (int) $row->days_remaining ?></span> day<?= $row->days_remaining == 1 ? '' : 's' ?></td>
                                            <td><span class="label label-<?= $row->status === 'sold' ? 'primary' : 'default' ?>"><?= esc(ucfirst($row->status)) ?></span></td>
                                            <td><?= $row->customer_name ? esc($row->customer_name) : '<span class="text-muted">&mdash;</span>' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- ============ GROUPED BY PRODUCT VIEW ============ -->
                            <div id="warrantyGroupView" style="<?= $viewMode === 'group' ? '' : 'display:none;' ?>">
                                <table class="table table-bordered table-striped" id="warrantyGroupTable">
                                    <thead>
                                    <tr>
                                        <th style="width:32px;"></th>
                                        <th>Product</th>
                                        <th>Units / Serials</th>
                                        <th>Earliest Warranty End</th>
                                        <th>Days Remaining</th>
                                        <th>Customer / Partner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($grouped as $gkey => $group):
                                        $minDays = null; $earliest = null; $customerNames = [];
                                        foreach ($group['items'] as $it) {
                                            if ($minDays === null || $it->days_remaining < $minDays) { $minDays = $it->days_remaining; }
                                            if ($earliest === null || $it->company_warranty_end_date < $earliest) { $earliest = $it->company_warranty_end_date; }
                                            if (!empty($it->customer_name)) { $customerNames[$it->customer_name] = $it->customer_name; }
                                        }
                                        $cls = wsl_days_class($minDays); ?>
                                        <tr class="<?= $cls ?> wsl-group-head" data-target="wslgrp-<?= (int) $gkey ?>" style="cursor:pointer;">
                                            <td class="text-center"><i class="fa fa-plus-circle wsl-group-toggle"></i></td>
                                            <td><strong><?= esc($group['product_name']) ?></strong></td>
                                            <td><span class="badge"><?= (int) $group['count'] ?></span></td>
                                            <td><?= esc($earliest) ?></td>
                                            <td><span class="wsl-days-badge"><?= (int) $minDays ?></span> day<?= $minDays == 1 ? '' : 's' ?></td>
                                            <td><?= count($customerNames) ? esc(implode(', ', $customerNames)) : '<span class="text-muted">&mdash;</span>' ?></td>
                                        </tr>
                                        <tr class="wsl-group-detail" id="wslgrp-<?= (int) $gkey ?>" style="display:none;">
                                            <td colspan="6" style="background:#f9f9f9;">
                                                <table class="table table-condensed" style="margin-bottom:0;">
                                                    <thead>
                                                    <tr>
                                                        <th>Serial Number</th>
                                                        <th>Company Warranty End Date</th>
                                                        <th>Days Remaining</th>
                                                        <th>Status</th>
                                                        <th>Customer / Partner</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($group['items'] as $row): $rowCls = wsl_days_class($row->days_remaining); ?>
                                                        <tr class="<?= $rowCls ?>">
                                                            <td><?= esc($row->serial_number) ?></td>
                                                            <td><?= esc($row->company_warranty_end_date) ?></td>
                                                            <td><span class="wsl-days-badge"><?= (int) $row->days_remaining ?></span> day<?= $row->days_remaining == 1 ? '' : 's' ?></td>
                                                            <td><span class="label label-<?= $row->status === 'sold' ? 'primary' : 'default' ?>"><?= esc(ucfirst($row->status)) ?></span></td>
                                                            <td><?= $row->customer_name ? esc($row->customer_name) : '<span class="text-muted">&mdash;</span>' ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>
                    </div>
                    <!-- /.box-body -->

                    <?php if (!empty($rows)): ?>
                        <div class="box-footer no-print">
                            <?php if (isset($print) && $print == 1): ?>
                                <button onclick="printDiv('wslPrint')" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Print Now</button>
                            <?php endif; ?>
                            <?php if (isset($download_PDF) && $download_PDF == 1): ?>
                                <button type="button" class="btn btn-info pull-right" style="margin-right:10px;" onclick="downloadPDF('wslPrint','warrantyShortList')"><i class="fa fa-file-pdf-o"></i> Download PDF</button>
                            <?php endif; ?>
                            <?php if (isset($download_CSV) && $download_CSV == 1): ?>
                                <button type="button" class="btn btn-success pull-right" style="margin-right:10px;" onclick="downloadCSV('wslPrint','warrantyShortList')"><i class="fa fa-file-excel-o"></i> Download CSV</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Hidden print/PDF/CSV copy -->
        <div class="col-md-12" id="wslPrint" style="display:none; text-transform: capitalize;">
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <div class="col-xs-6">
                    <?php if (logo_image() == NULL): ?>
                        <img src="<?= base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                    <?php else: ?>
                        <img src="<?= base_url() ?>/uploads/schools/<?= logo_image() ?>" class="" alt="User Image">
                    <?php endif; ?>
                </div>
                <div class="col-xs-6"><?php print address(); ?></div>
            </div>
            <div class="col-md-12">
                <h4>Warranty Short List &mdash; expiring in next <?= (int) $windowInfo['months'] ?> months (<?= esc($windowInfo['from']) ?> to <?= esc($windowInfo['to']) ?>)</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Serial Number</th>
                        <th>Company Warranty End Date</th>
                        <th>Days Remaining</th>
                        <th>Status</th>
                        <th>Customer / Partner</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0; foreach ($rows as $row): ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td><?= esc($row->product_name) ?></td>
                            <td><?= esc($row->serial_number) ?></td>
                            <td><?= esc($row->company_warranty_end_date) ?></td>
                            <td><?= (int) $row->days_remaining ?></td>
                            <td><?= esc(ucfirst($row->status)) ?></td>
                            <td><?= $row->customer_name ? esc($row->customer_name) : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    // This view is emitted BEFORE the footer that loads jQuery/DataTables, so on a
    // direct page reload `jQuery` is undefined at parse time. Define the global view
    // toggle without touching jQuery, then boot the rest once the libraries exist.
    window.wslSetView = function (mode) {
        if (!window.jQuery) { return; }
        var list = mode === 'list';
        jQuery('#warrantyListView').toggle(list);
        jQuery('#warrantyGroupView').toggle(!list);
        jQuery('#btnListView').toggleClass('active btn-primary', list).toggleClass('btn-default', !list);
        jQuery('#btnGroupView').toggleClass('active btn-primary', !list).toggleClass('btn-default', list);
    };

    (function () {
        function init() {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) {
                // Footer scripts not loaded yet (full page reload) – retry shortly.
                setTimeout(init, 60);
                return;
            }

            var $t = jQuery('#warrantyTable');
            // Guard against double-initialisation (AJAX shell may already wire the table).
            if ($t.length && !jQuery.fn.DataTable.isDataTable($t)) {
                $t.DataTable({
                    pageLength: 25,
                    order: [[3, 'asc']],
                    dom: '<"top"if>rt<"bottom"lp>'
                });
            }

            // Smart search also drives the grouped-by-product view (filter product rows).
            jQuery(document).off('keyup.wsl', '#wslSmartSearch').on('keyup.wsl', '#wslSmartSearch', function () {
                var term = jQuery.trim(this.value).toLowerCase();
                var $tbl = jQuery('#warrantyTable');
                if ($tbl.length) {
                    if (!jQuery.fn.DataTable.isDataTable($tbl)) {
                        $tbl.DataTable({ pageLength: 25, order: [[3, 'asc']] });
                    }
                    $tbl.DataTable().search(term).draw();
                }

                jQuery('#warrantyGroupTable tbody tr.wsl-group-head').each(function () {
                    var $head = jQuery(this);
                    var match = $head.text().toLowerCase().indexOf(term) !== -1;
                    $head.toggle(match);
                    // Hide the detail row too when its product row is filtered out.
                    if (!match) { jQuery('#' + $head.data('target')).hide(); }
                });
            });

            // Grouped view: expand / collapse the serial detail for a product.
            jQuery(document).off('click.wsl', '#warrantyGroupTable tr.wsl-group-head')
                .on('click.wsl', '#warrantyGroupTable tr.wsl-group-head', function () {
                    var $detail = jQuery('#' + jQuery(this).data('target'));
                    var open = $detail.is(':visible');
                    $detail.toggle(!open);
                    jQuery(this).find('.wsl-group-toggle')
                        .toggleClass('fa-plus-circle', open)
                        .toggleClass('fa-minus-circle', !open);
                });
        }

        init();
    })();
</script>
