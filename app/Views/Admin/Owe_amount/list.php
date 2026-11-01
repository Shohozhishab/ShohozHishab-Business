<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Accounts Payable  <small>Accounts Payable List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Accounts Payable</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Owe_amount')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Owe_amount">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <div class="row">

                <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Customer</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12">
                            <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($customer); ?></span>
                            <h4>Customer</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($customerData as $row) {
                                    if ($row->balance < 0) {
                                        $hasNotice = (!empty($row->notification_date) || !empty($row->note));
                                        $dueToday = (!empty($row->notification_date) && $row->notification_date == date('Y-m-d'));
                                        ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo $row->customer_name ?></td>
                                            <td><?php echo showWithCurrencySymbol($row->balance) ?></td>
                                            <td>
                                                <?php if (!empty($row->notification_date)) { ?>
                                                    <?php if ($dueToday) { ?>
                                                        <span class="text-danger"><i class="fa fa-bell"></i> <?php echo $row->notification_date; ?></span>
                                                    <?php } else { ?>
                                                        <?php echo $row->notification_date; ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($hasNotice) { ?>
                                                    <button type="button" class="btn btn-xs btn-info"
                                                            data-toggle="modal" data-target="#notificationModal"
                                                            onclick="showNotificationModal(this)"
                                                            data-name="<?php echo htmlspecialchars((string)$row->customer_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-phone="<?php echo htmlspecialchars((string)$row->mobile, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-date="<?php echo htmlspecialchars((string)$row->notification_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-note="<?php echo htmlspecialchars((string)$row->note, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-id="<?php echo $row->customer_id; ?>" data-type="customer"
                                                            data-due="<?php echo $dueToday ? 1 : 0; ?>">
                                                        View
                                                    </button>
                                                    <?php if ($dueToday) { ?>
                                                        <button type="button" class="btn btn-xs btn-danger" title="Due today"><i class="fa fa-bell"></i></button>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </tfoot>
                            </table>
                            <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('customer')" class="btn btn-sm btn-primary" style="float: right;">Print</button>
                            <?php } ?>
                            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-sm btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('customer','customer')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <?php } ?>
                            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-sm btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('customer','customer')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            <?php } ?>
                        </div>
                        <div class="col-md-12" style="display: none;" id="customer" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image_logo.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($customer); ?></span>
                                <h4>Customer</h4>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; foreach ($customerData as $row) {
                                        if ($row->balance < 0) {
                                            ?>
                                            <tr>
                                                <td><?php echo $i++ ?></td>
                                                <td><?php echo $row->customer_name ?></td>
                                                <td><?php echo showWithCurrencySymbol($row->balance) ?></td>
                                            </tr>
                                        <?php } }?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div><!-- /.col -->
                <!-- /.box -->

                <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Suppliers</h3>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12">
                            <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($supplier); ?></span>
                            <h4>Suppliers</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $j = 1; foreach ($supplierData as $view) {
                                    if ($view->balance < 0) {
                                        $hasNotice = (!empty($view->notification_date) || !empty($view->note));
                                        $dueToday = (!empty($view->notification_date) && $view->notification_date == date('Y-m-d'));
                                        ?>
                                        <tr>
                                            <td><?php echo $j++ ?></td>
                                            <td><?php echo $view->name ?></td>
                                            <td><?php echo showWithCurrencySymbol($view->balance) ?></td>
                                            <td>
                                                <?php if (!empty($view->notification_date)) { ?>
                                                    <?php if ($dueToday) { ?>
                                                        <span class="text-danger"><i class="fa fa-bell"></i> <?php echo $view->notification_date; ?></span>
                                                    <?php } else { ?>
                                                        <?php echo $view->notification_date; ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($hasNotice) { ?>
                                                    <button type="button" class="btn btn-xs btn-info"
                                                            data-toggle="modal" data-target="#notificationModal"
                                                            onclick="showNotificationModal(this)"
                                                            data-name="<?php echo htmlspecialchars((string)$view->name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-phone="<?php echo htmlspecialchars((string)$view->phone, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-date="<?php echo htmlspecialchars((string)$view->notification_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-note="<?php echo htmlspecialchars((string)$view->note, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-id="<?php echo $view->supplier_id; ?>" data-type="supplier"
                                                            data-due="<?php echo $dueToday ? 1 : 0; ?>">
                                                        View
                                                    </button>
                                                    <?php if ($dueToday) { ?>
                                                        <button type="button" class="btn btn-xs btn-danger" title="Due today"><i class="fa fa-bell"></i></button>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </tfoot>
                            </table>
                            <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('suppliers')" class="btn btn-sm btn-primary" style="float: right;">Print</button>
                            <?php } ?>
                            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-sm btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('suppliers','suppliers')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <?php } ?>
                            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-sm btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('suppliers','suppliers')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12" style="display: none;" id="suppliers" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image_logo.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($supplier); ?></span>
                                <h4>Suppliers</h4>
                                <table class="table table-bordered table-striped" id="TFtable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $j = 1; foreach ($supplierData as $view) {
                                        if ($view->balance < 0) {
                                            ?>
                                            <tr>
                                                <td><?php echo $j++ ?></td>
                                                <td><?php echo $view->name ?></td>
                                                <td><?php echo showWithCurrencySymbol($view->balance) ?></td>
                                            </tr>
                                        <?php } }?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div><!-- /.col -->
                <!-- /.box -->

                <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Account Head</h3>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12">
                            <span style="float:right; " >Total:<?php echo showWithCurrencySymbol($loanProvider); ?></span>
                            <h4>Account Head</h4>
                            <table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $l = 1; foreach ($loanProData as $value) {
                                    if ($value->balance < 0) {
                                        $hasNotice = (!empty($value->notification_date) || !empty($value->note));
                                        $dueToday = (!empty($value->notification_date) && $value->notification_date == date('Y-m-d'));
                                        ?>
                                        <tr>
                                            <td><?php echo $l++ ?></td>
                                            <td><?php echo $value->name ?></td>
                                            <td><?php echo showWithCurrencySymbol($value->balance) ?></td>
                                            <td>
                                                <?php if (!empty($value->notification_date)) { ?>
                                                    <?php if ($dueToday) { ?>
                                                        <span class="text-danger"><i class="fa fa-bell"></i> <?php echo $value->notification_date; ?></span>
                                                    <?php } else { ?>
                                                        <?php echo $value->notification_date; ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($hasNotice) { ?>
                                                    <button type="button" class="btn btn-xs btn-info"
                                                            data-toggle="modal" data-target="#notificationModal"
                                                            onclick="showNotificationModal(this)"
                                                            data-name="<?php echo htmlspecialchars((string)$value->name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-phone="<?php echo htmlspecialchars((string)$value->phone, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-date="<?php echo htmlspecialchars((string)$value->notification_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-note="<?php echo htmlspecialchars((string)$value->note, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-id="<?php echo $value->loan_pro_id; ?>" data-type="account"
                                                            data-due="<?php echo $dueToday ? 1 : 0; ?>">
                                                        View
                                                    </button>
                                                    <?php if ($dueToday) { ?>
                                                        <button type="button" class="btn btn-xs btn-danger" title="Due today"><i class="fa fa-bell"></i></button>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Notification Date</th>
                                    <th>Notification</th>
                                </tr>
                                </tfoot>
                            </table>
                            <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('account')" class="btn btn-sm btn-primary" style="float: right;">Print</button>
                            <?php } ?>
                            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-sm btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('account','account')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <?php } ?>
                            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-sm btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('account','account')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12" style="display: none;" id="account" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image_logo.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <span style="float:right; " >Total:<?php echo showWithCurrencySymbol($loanProvider); ?></span>
                                <h4>Account Head</h4>
                                <table class="table table-bordered table-striped" id="TFtable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $l = 1; foreach ($loanProData as $value) {
                                        if ($value->balance < 0) {
                                            ?>
                                            <tr>
                                                <td><?php echo $l++ ?></td>
                                                <td><?php echo $value->name ?></td>
                                                <td><?php echo showWithCurrencySymbol($value->balance) ?></td>
                                            </tr>
                                        <?php } }?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                </div><!-- /.col -->
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->

    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Notification</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
