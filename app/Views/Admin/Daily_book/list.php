<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Daily Book<small>Daily Book</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Daily Book</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Daily_book')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Daily_book">
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

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-4">
                                <h3 class="box-title">Daily Book</h3>
                            </div>
                            <div class="col-lg-4">
                                <?php if (isset($print) && $print == 1){ ?>
                                    <a href="<?php echo site_url('Admin/Daily_book/print_preview'); ?>"
                                       class="print_line btn btn-primary pull-right" style="margin-right: 20px;"><i
                                                class="fa fa-print"></i> Print Statement Now</a>
                                <?php } ?>
                            </div>
                            <div class="col-lg-4">
                                <?php if (isset($filter) && $filter == 1){ ?>
                                    <form action="<?php echo site_url('Admin/Daily_book/search'); ?>" method="post">
                                        <div class="input-group pull-right no-print">
                                <span class="input-group-addon " style="background-color:#367FA9; ">
                                    <i class="fa fa-fw fa-filter" style="color: white;"></i>
                                </span>
                                            <input type="date" class="form-control " name="date" id="date" required>
                                            <span class="input-group-btn">
                                  <button class="btn btn-primary " type="submit">Filter</button>
                                </span>
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                    </div>
                    <!-- /.box-body -->
                </div>


            </div>

            <div class="col-xs-6">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Cash Ledger</h3>
                        <span class="pull-right">Last Balance <?php echo showWithCurrencySymbol($cashrest_balance); ?></span>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $totalRows = count($cashLedger)-1;
                            for($i = $totalRows; $i >= 0; $i--) {
                                $particulars = ($cashLedger[$i]->particulars == NULL) ? "Payment" : $cashLedger[$i]->particulars;
                                $amountCr = ($cashLedger[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($cashLedger[$i]->amount);
                                $amountDr = ($cashLedger[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($cashLedger[$i]->amount);
                                ?>

                                <tr>
                                    <td><?php echo invoiceDateFormat($cashLedger[$i]->createdDtm) ?></td>
                                    <td><?php echo $particulars ?></td>
                                    <td><?php echo $amountDr ?></td>
                                    <td><?php echo $amountCr ?></td>
                                    <td><?php echo showWithCurrencySymbol($cashLedger[$i]->r_balance) ?></td>
                                </tr>

                            <?php } ?>

                            </tbody>
                        </table>
                    </div>

                </div>


            </div>

            <div class="col-xs-6 no-print">
                <div class="box">
                    <div class="box-header">
                        <div class="col-xs-4">
                            <h3 class="box-title">Bank Ledger</h3>
                        </div>

                        <div class="col-xs-8" style="padding-bottom: 10px;">
                            <select class="form-control select2 select2-hidden-accessible"
                                    onchange="bankLedgdaily(this.value)" style=" width: 100%;" tabindex="-1"
                                    aria-hidden="true">
                                <option selected="selected" value="">Please Select</option>
                                <?php echo getTwoValueInOption('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                            </select>
                        </div>

                        <div class="col-xs-12">
                            <span id="bankdaileyLedg"></span>
                        </div>


                    </div>
                </div>

            </div>

            <div class="col-xs-12" style="text-transform: capitalize;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h3>Sale List</h3>
                        <?php $profitAcc = permission_check('Sales',newSession()->role,'profit');?>
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Total Due</th>
                                <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                    <th>Profit</th>
                                <?php } ?>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1;
                            $totalProfit = 0;
                            foreach ($sales as $val) {
                                $cus_id = get_data_by_id('customer_id', 'invoice', 'invoice_id', $val->invoice_id);
                                $cusName = !empty($cus_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $cus_id) : get_data_by_id('customer_name', 'invoice', 'invoice_id', $val->invoice_id);
                                $profit = get_data_by_id('profit', 'invoice', 'invoice_id', $val->invoice_id);
                                $totalProfit += $profit;
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($val->createdDtm) ?></td>
                                    <td><?php echo $cusName ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                    <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                        <td><?php echo showWithCurrencySymbol($profit) ?></td>
                                    <?php } ?>
                                    <td>
                                        <?php if (isset($read) && $read == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/' . $val->invoice_id); ?>','<?php echo '/Admin/Invoice/view/' . $val->invoice_id; ?>')" class="btn btn-primary btn-xs">View</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                            <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total Profit</th>
                                    <th><b style="color: green;"><?= showWithCurrencySymbol($totalProfit);?></b></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>

            <div class="col-xs-12" style="text-transform: capitalize;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h3>Purchase List</h3>
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Purchase Date</th>
                                <th>Supplier </th>
                                <th>Total Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1;
                            foreach ($purchase_data as $purchase) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($purchase->createdDtm) ?></td>
                                    <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id); ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('due','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                    <td>
                                        <?php if (isset($read) && $read == 1){ ?>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/view/' . $purchase->purchase_id); ?>','<?php echo '/Admin/Purchase/view/' . $purchase->purchase_id; ?>')"
                                               class="btn btn-primary btn-xs">View</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-xs-12" style="text-transform: capitalize;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h3>Capital List</h3>
                        <table class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach ($capital as $val){
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= invoiceDateFormat($val->createdDtm) ?></td>
                                    <td><?= $val->description ?></td>
                                    <td><?= showWithCurrencySymbol($val->amount) ?></td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-xs-12" style="text-transform: capitalize;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h3>Transaction List</h3>
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($transaction as $row){
                                $name = '';
                                $accountType = null;
                                if (!empty($row->account_id)) {
                                    $accountType = accountIdByType($row->account_id);
                                }

                                if (!empty($row->customer_id)) {

                                    $name = get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id)
                                        . '<br><small>(Customer)</small>';

                                } elseif (!empty($row->supplier_id)) {

                                    $name = get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id)
                                        . '<br><small>(Supplier)</small>';

                                } elseif (!empty($row->loan_pro_id)) {

                                    $name = get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id)
                                        . '<br><small>(Account Head)</small>';

                                } elseif (!empty($row->bank_id)) {

                                    $name = get_data_by_id('name', 'bank', 'bank_id', $row->bank_id)
                                        . '<br><small>(Bank)</small>';

                                } elseif (!empty($row->employee_id)) {

                                    $name = get_data_by_id('name', 'employee', 'employee_id', $row->employee_id)
                                        . '<br><small>(Employee)</small>';

                                } elseif (!empty($row->vat_id)) {

                                    $name = get_data_by_id('name', 'vat_register', 'vat_id', $row->vat_id)
                                        . '<br><small>(Vat)</small>';

                                } elseif (!empty($row->account_id) && !empty($accountType)) {

                                    if ($accountType->type_key == 'expenses') {
                                        $name = get_data_by_id('name', 'accounts', 'account_id', $row->account_id)
                                            . '<br><small>(Expense)</small>';
                                    } elseif ($accountType->type_key == 'assets') {
                                        $name = get_data_by_id('name', 'accounts', 'account_id', $row->account_id)
                                            . '<br><small>(Assets)</small>';
                                    }

                                } elseif (
                                    empty($row->loan_pro_id) &&
                                    empty($row->customer_id) &&
                                    empty($row->supplier_id) &&
                                    empty($row->bank_id) &&
                                    empty($row->lc_id) &&
                                    empty($row->account_id) &&
                                    $row->trangaction_type == 'Dr.'
                                ) {

                                    $name = 'Other Sales';
                                }
                                ?>
                                <tr>
                                    <td><?= $i++;?></td>
                                    <td><?= invoiceDateFormat($row->createdDtm) ?></td>
                                    <td><?= $name;?></td>
                                    <td><?= $row->trangaction_type; ?></td>
                                    <td><?= showWithCurrencySymbol($row->amount); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?= site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?= '/Admin/Transaction/read/' . $row->trans_id; ?>')" class="btn btn-xs btn-success">View</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
