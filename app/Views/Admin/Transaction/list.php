<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small>Transaction List</small></h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Transaction')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Transaction">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px">
            <?php if (isset($filter) && $filter == 1){ ?>
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Transactions</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Transaction') ?>" method="get" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" required>
                                </div>

                                <input type="hidden" name="category" id="hidden_category" value="<?php echo $active_category ?? ''; ?>">
                                <input type="hidden" name="customer_id" id="hidden_customer_id" value="<?php echo $customer_id_filter ?? ''; ?>">
                                <input type="hidden" name="supplier_id" id="hidden_supplier_id" value="<?php echo $supplier_id_filter ?? ''; ?>">
                                <input type="hidden" name="loan_pro_id" id="hidden_loan_pro_id" value="<?php echo $loan_pro_id_filter ?? ''; ?>">
                                <input type="hidden" name="bank_id" id="hidden_bank_id" value="<?php echo $bank_id_filter ?? ''; ?>">
                                <input type="hidden" name="employee_id" id="hidden_employee_id" value="<?php echo $employee_id_filter ?? ''; ?>">


                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Transaction') ?>" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if (isset($create) && $create == 1){ ?>
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="javascript:void(0)"
                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/create/'); ?>','<?php echo '/Admin/Transaction/create/'; ?>')"
                   class="btn btn-success"><i class="fa fa-plus"></i> Add Transaction</a>
            </div>
            <?php } ?>

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 20px;" id="messageAcc">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message');
                                endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="panel with-nav-tabs panel-default " id="nav_panel">
                            <ul class="nav nav-tabs" id="ul_border">
                                <li class=" in <?php echo (($active_category ?? '') == 'customer' || empty($active_category ?? '')) ? 'active' : ''; ?>"><a href="#customer" data-toggle="tab">Customer</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'supplier') ? 'active' : ''; ?>"><a href="#supplier" data-toggle="tab">Supplier</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'loan_provider') ? 'active' : ''; ?>"><a href="#loanProvider" data-toggle="tab">Account Head</a>
                                </li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'fund_transfer') ? 'active' : ''; ?>"><a href="#bank" data-toggle="tab">Fund Transfer</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'expense') ? 'active' : ''; ?>"><a href="#expense" data-toggle="tab">Expense</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'othersales') ? 'active' : ''; ?>"><a href="#othersales" data-toggle="tab">Other Sales</a>
                                </li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'employee') ? 'active' : ''; ?>"><a href="#employeeSalary" data-toggle="tab">Employee
                                        Salary</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'vat') ? 'active' : ''; ?>"><a href="#vatpay" data-toggle="tab">Vat Pay</a></li>
                                <li class="tab-pane fade in <?php echo (($active_category ?? '') == 'assets') ? 'active' : ''; ?>"><a href="#assets" data-toggle="tab">Assets</a></li>
                            </ul>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade <?php echo (($active_category ?? '') == 'customer' || empty($active_category ?? '')) ? 'active in' : ''; ?>"" id="customer">
                                    <div class="box-header">
                                        <div class="col-md-3">
                                            <h3 class="box-title">Customer Transaction List</h3>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control select2" onchange="filterByCategory('customer', 'hidden_customer_id', $(this).val());">
                                                <option value="">-- All Customers --</option>
                                                <?php foreach ($customers as $cus) { ?>
                                                    <option value="<?php echo $cus->customer_id; ?>" <?php echo (($customer_id_filter ?? '') == $cus->customer_id) ? 'selected' : ''; ?>><?php echo $cus->customer_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped transaction" id="customer2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Customer</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->customer_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/moneyReceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/moneyReceipt/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-info">Money Receipt</a>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="cusTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>

                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>
                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('cusPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('cusPrint','customer')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('cusPrint','customer')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="cusPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped ">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Customer</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->customer_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'supplier') ? 'active in' : 'in'; ?>"  id="supplier">
                                    <div class="box-header">
                                        <div class="col-md-3">
                                            <h3 class="box-title">Supplier Transaction</h3>
                                        </div>

                                        <div class="col-md-3">
                                            <select class="form-control select2" onchange="filterByCategory('supplier', 'hidden_supplier_id', $(this).val());">
                                                <option value="">-- All Suppliers --</option>
                                                <?php foreach ($suppliers as $sup) { ?>
                                                    <option value="<?php echo $sup->supplier_id; ?>" <?php echo (($supplier_id_filter ?? '') == $sup->supplier_id) ? 'selected' : ''; ?>><?php echo $sup->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped supplier" id="supplier2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Supplier</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->supplier_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="supplierTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('supplierPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('supplierPrint','supplier')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('supplierPrint','supplier')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="supplierPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped ">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Supplier</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->supplier_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'loan_provider') ? 'active in' : 'in'; ?>" id="loanProvider">
                                    <div class="box-header">
                                        <div class="col-md-3"><h3 class="box-title">Account Head Transaction</h3></div>
                                        <div class="col-md-3">
                                            <select class="form-control select2" onchange="filterByCategory('loan_provider', 'hidden_loan_pro_id', $(this).val());">
                                                <option value="">-- All Account Heads --</option>
                                                <?php foreach ($loan_providers as $lp) { ?>
                                                    <option value="<?php echo $lp->loan_pro_id; ?>" <?php echo (($loan_pro_id_filter ?? '') == $lp->loan_pro_id) ? 'selected' : ''; ?>><?php echo $lp->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped loanProvider" id="account2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Account Holder</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->loan_pro_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                        <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="accountTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>

                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>
                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('accountPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('accountPrint','account')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('accountPrint','account')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="accountPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Account Holder</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->loan_pro_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'loan_provider', 'loan_pro_id', $row->loan_pro_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'fund_transfer') ? 'active in' : 'in'; ?> " id="bank">
                                    <div class="box-header">
                                        <div class="col-md-3"><h3 class="box-title">Fund Transfer</h3></div>
                                        <div class="col-md-3">
                                            <select class="form-control select2" onchange="filterByCategory('fund_transfer', 'hidden_bank_id', $(this).val());">
                                                <option value="">-- All Banks --</option>
                                                <?php foreach ($banks as $bank) { ?>
                                                    <option value="<?php echo $bank->bank_id; ?>" <?php echo (($bank_id_filter ?? '') == $bank->bank_id) ? 'selected' : ''; ?>><?php echo $bank->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped banktrans" id="fund2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Bank</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->bank_to_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $row->bank_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="fundTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>
                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('fundPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('fundPrint','fund')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('fundPrint','fund')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="fundPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Bank</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->bank_to_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $row->bank_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'expense') ? 'active in' : 'in'; ?>" id="expense">
                                    <div class="box-header">
                                        <h3 class="box-title">Expense Transaction</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped expense" id="expense2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->account_id != NULL) { $accountType = accountIdByType($row->account_id); if (!empty($accountType)){ if ($accountType->type_key == 'expenses'){  ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                            <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="assetsTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                            <?php } ?>
                                                            <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } } } } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('expensePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('expensePrint','expense')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('expensePrint','expense')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="expensePrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->account_id != NULL) { $accountType = accountIdByType($row->account_id); if (!empty($accountType)){ if ($accountType->type_key == 'expenses'){ ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php } } } } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'othersales') ? 'active in' : 'in'; ?>" id="othersales">
                                    <div class="box-header">
                                        <h3 class="box-title">Other Sales Transaction</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped othersales" id="other2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Other Sales</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->loan_pro_id == NULL && $row->customer_id == NULL && $row->supplier_id == NULL && $row->bank_id == NULL && $row->lc_id == NULL && $row->account_id == NULL && $row->trangaction_type == 'Dr.') { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td>Other Sales</td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                            <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="otherSalesTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                            <?php } ?>
                                                            <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                            <?php } ?>

                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('otherSalesPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('otherSalesPrint','otherSales')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('otherSalesPrint','otherSales')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="otherSalesPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Other Sales</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->loan_pro_id == NULL && $row->customer_id == NULL && $row->supplier_id == NULL && $row->bank_id == NULL && $row->lc_id == NULL && $row->trangaction_type == 'Dr.') { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td>Other Sales</td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'employee') ? 'active in' : 'in'; ?>" id="employeeSalary">
                                    <div class="box-header">
                                        <div class="col-md-3"><h3 class="box-title">Employee Salary Transaction</h3></div>
                                        <div class="col-md-3">
                                            <select class="form-control select2" onchange="filterByCategory('employee', 'hidden_employee_id', $(this).val());">
                                                <option value="">-- All Employees --</option>
                                                <?php foreach ($employees as $emp) { ?>
                                                    <option value="<?php echo $emp->employee_id; ?>" <?php echo (($employee_id_filter ?? '') == $emp->employee_id) ? 'selected' : ''; ?>><?php echo $emp->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped employeeSalary" id="employee2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Transaction Type</th>
                                                <th>Salary</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->employee_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'employee', 'employee_id', $row->employee_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/salaryreceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/salaryreceipt/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-info">Salary Receipt</a>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning " onclick="employeeTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>

                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('employeePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('employeePrint','employee')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('employeePrint','employee')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="employeePrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                        <th>Transaction Type</th>
                                                        <th>Salary</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->employee_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'employee', 'employee_id', $row->employee_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'vat') ? 'active in' : 'in'; ?>" id="vatpay">
                                    <div class="box-header">
                                        <h3 class="box-title">Vat Pay</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped vatpay" id="vat2">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Vat Register Name</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->vat_id != NULL) { ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'vat_register', 'vat_id', $row->vat_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="vatTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('vatPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('vatPrint','vat')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('vatPrint','vat')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="vatPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Vat Register Name</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                        if ($row->vat_id != NULL) { ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'vat_register', 'vat_id', $row->vat_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade <?php echo (($active_category ?? '') == 'assets') ? 'active in' : 'in'; ?>" id="assets">
                                    <div class="box-header">
                                        <h3 class="box-title">Assets</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped asstest" id="assets1">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Transaction Type</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($transaction_data as $row) {
                                                if ($row->account_id != NULL) { $accountType = accountIdByType($row->account_id); if (!empty($accountType)){ if ($accountType->type_key == 'assets'){  ?>
                                                    <tr>
                                                        <td><?php echo ++$i; ?></td>
                                                        <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                        <td><?php echo $row->trangaction_type; ?></td>
                                                        <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                        <td>
                                                    <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/transaction_flow/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/transaction_flow/' . $row->trans_id; ?>')"
                                                               class="btn btn-success btn-xs">Transaction Flow </a>
                                                    <?php } ?>
                                                    <?php if (isset($update) && $update == 1){ ?>
                                                            <?php if (edit_expire_check($row->createdDtm) == true) { ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="assetsTranEdit('<?= $row->trans_id; ?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                            <?php } ?>
                                                    <?php } ?>
                                                    <?php if (isset($read) && $read == 1){ ?>
                                                            <a href="javascript:void(0)"
                                                               onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                               class="btn btn-xs btn-success">View</a>
                                                    <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } } } } ?>
                                            </tbody>

                                        </table>

                                        <div class="row no-print">
                                            <div class="col-xs-12">
                                                <?php if (isset($print) && $print == 1){ ?>
                                                <button onclick="printDiv('assetsPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now</button>
                                                <?php } ?>
                                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('assetsPrint','assets')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                                <?php } ?>
                                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('assetsPrint','assets')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="assetsPrint" style="display: none; text-transform: capitalize; ">
                                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                                <div class="col-xs-6">
                                                    <?php if (logo_image() == NULL) { ?>
                                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image">
                                                    <?php } else { ?>
                                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                                    <?php } ?>
                                                </div>
                                                <div class="col-xs-6">
                                                    <?php print address(); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                        <th>Transaction Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i = 0;
                                                    foreach ($transaction_data as $row) {
                                                    if ($row->account_id != NULL) { $accountType = accountIdByType($row->account_id); if (!empty($accountType)){ if ($accountType->type_key == 'assets'){ ?>
                                                            <tr>
                                                                <td><?php echo ++$i; ?></td>
                                                                <td><?php echo get_data_by_id('name', 'accounts', 'account_id', $row->account_id); ?></td>
                                                                <td><?php echo $row->trangaction_type; ?></td>
                                                                <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            </tr>
                                                    <?php } } } } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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


<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Data</h4>
            </div>
            <div class="modal-body" id="formData">


            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    function cusTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/cusDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function submitForm(formId) {
        $('#' + formId).submit();
    }

    function supplierTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/supplierDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function accountTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/accountDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function fundTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/fundDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function expenseTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/expenseDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function employeeTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/employeeDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function otherSalesTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/otherSalesDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function vatTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/vatDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }

    function assetsTranEdit(tranId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/assetsDataEdit') ?>",
            data: {
                id: tranId
            },
            success: function(data) {
                $('#formData').html(data);
            }
        });
    }
    function filterByCategory(category, entityId, value) {
        $('#hidden_customer_id').val('');
        $('#hidden_supplier_id').val('');
        $('#hidden_loan_pro_id').val('');
        $('#hidden_bank_id').val('');
        $('#hidden_employee_id').val('');

        $('#hidden_category').val(category);
        if (entityId && value) {
            $('#' + entityId).val(value);
        }
        $('#filterForm').submit();
    }
</script>