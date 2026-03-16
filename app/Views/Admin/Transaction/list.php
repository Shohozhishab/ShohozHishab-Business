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
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/create/'); ?>','<?php echo '/Admin/Transaction/create/';?>')"  class="btn btn-default">Add</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Transaction List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('//Admin/Transaction_ajax/create/'); ?>','<?php echo '//Admin/Transaction/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;" id="messageAcc">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="panel with-nav-tabs panel-default " id="nav_panel">
                            <ul class="nav nav-tabs" id="ul_border">
                                <li class="active"><a href="#customer" data-toggle="tab">Customer</a></li>
                                <li class="tab-pane fade in"><a href="#supplier" data-toggle="tab">Supplier</a></li>
                                <li class="tab-pane fade in"><a href="#loanProvider" data-toggle="tab">Account Head</a>
                                </li>
                                <li class="tab-pane fade in"><a href="#bank" data-toggle="tab">Fund Transfer</a></li>
                                <li class="tab-pane fade in"><a href="#expense" data-toggle="tab">Expense</a></li>
                                <li class="tab-pane fade in"><a href="#othersales" data-toggle="tab">Other Sales</a>
                                </li>
                                <li class="tab-pane fade in"><a href="#employeeSalary" data-toggle="tab">Employee
                                        Salary</a></li>
                                <li class="tab-pane fade in"><a href="#vatpay" data-toggle="tab">Vat Pay</a></li>
                            </ul>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active in" id="customer">
                                        <div class="box-header">
                                            <h3 class="box-title">Customer Transaction List</h3>
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
                                                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/moneyReceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/moneyReceipt/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-info">Money Receipt</a>
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="cusTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default" >Edit</a>
                                                                <?php } ?>

                                                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>

                                                            </td>
                                                        </tr>
                                                    <?php } } ?>
                                                </tbody>

                                            </table>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="supplier">
                                        <div class="box-header">
                                            <h3 class="box-title">Supplier Transaction</h3>
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
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="supplierTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default" >Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>

                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="loanProvider">
                                        <div class="box-header">
                                            <h3 class="box-title">Account Head Transaction</h3>
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
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="accountTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>

                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="bank">
                                        <div class="box-header">
                                            <h3 class="box-title">Fund Transfer</h3>
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
                                                    if ($row->bank_to_id != NULL){ ?>
                                                        <tr>
                                                            <td><?php echo ++$i; ?></td>
                                                            <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $row->bank_id); ?></td>
                                                            <td><?php echo $row->trangaction_type; ?></td>
                                                            <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            <td>
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="fundTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            </td>
                                                        </tr>
                                                <?php } } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="expense">
                                        <div class="box-header">
                                            <h3 class="box-title">Expense Transaction</h3>
                                        </div>
                                        <div class="box-body">
                                            <table class="table table-bordered table-striped expense" id="expense2">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Expense</th>
                                                    <th>Transaction Type</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i = 0;
                                                foreach ($transaction_data as $row) {
                                                    if ($row->loan_pro_id == NULL && $row->customer_id == NULL && $row->supplier_id == NULL && $row->bank_id == NULL && $row->lc_id == NULL && $row->employee_id == NULL && $row->trangaction_type == 'Cr.') { ?>
                                                        <tr>
                                                            <td><?php echo ++$i; ?></td>
                                                            <td>Expense</td>
                                                            <td><?php echo $row->trangaction_type; ?></td>
                                                            <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            <td>
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="expenseTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="othersales">
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
                                                    if ($row->loan_pro_id == NULL && $row->customer_id == NULL && $row->supplier_id == NULL && $row->bank_id == NULL && $row->lc_id == NULL && $row->trangaction_type == 'Dr.') { ?>
                                                        <tr>
                                                            <td><?php echo ++$i; ?></td>
                                                            <td>Other Sales</td>
                                                            <td><?php echo $row->trangaction_type; ?></td>
                                                            <td><?php echo showWithCurrencySymbol($row->amount); ?></td>
                                                            <td>
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onclick="otherSalesTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>

                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="employeeSalary">
                                        <div class="box-header">
                                            <h3 class="box-title">Employee Salary Transaction</h3>
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
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/salaryreceipt/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/salaryreceipt/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-info">Salary Receipt</a>
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning " onclick="employeeTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>

                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="vatpay">
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
                                                                <?php if(edit_expire_check($row->createdDtm) == true){ ?>
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-warning"  onclick="vatTranEdit('<?= $row->trans_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
                                                                <?php } ?>
                                                                <a href="javascript:void(0)"
                                                                   onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $row->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $row->trans_id; ?>')"
                                                                   class="btn btn-xs btn-success">View</a>
                                                            </td>
                                                        </tr>
                                                    <?php } } ?>
                                                </tbody>

                                            </table>
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
    function cusTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/cusDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }

    function submitForm(formId){
        $('#'+formId).submit();
    }

    function supplierTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/supplierDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }

    function accountTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/accountDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }

    function fundTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/fundDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }

    function expenseTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/expenseDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }
    function employeeTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/employeeDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }
    function otherSalesTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/otherSalesDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }
    function vatTranEdit(tranId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Transaction/vatDataEdit') ?>",
            data: {id: tranId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }




</script>