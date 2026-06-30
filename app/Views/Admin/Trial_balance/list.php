<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Trial Balance <small>Trial Balance</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Trial Balance</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Trial_balance')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Trial_balance">
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Trial Balance</h3>
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
                        <div class="row">
                            <div class="col-lg-6 trail">
                                <h4>All Debit</h4>
                                <h5><b>Assets </b></h5>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_nagodan')?>" class="text-black ta-link" >Cash</a></td>
                                        <td><?php echo showWithCurrencySymbol($cash); ?></td>
                                    </tr>

                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_stock')?>" class="text-black" >Stock Amount</a></td>
                                        <td><?php echo showWithCurrencySymbol($stockAmount); ?></td>
                                    </tr>
                                <?php foreach ($accountsAssets as $val){ ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_accounts?account_id='.$val->account_id)?>" class="text-black" ><?= $val->name ?></a></td>
                                        <td><?= showWithCurrencySymbol($val->balance) ?></td>
                                    </tr>
                                <?php } ?>
                                </table>

                                <h5><b>Bank</b></h5>

                                <table class="table table-bordered table-striped">
                                    <?php foreach ($bankData as $rowbank) { ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_bank?bank_id='.$rowbank->bank_id)?>" class="text-black" ><?php echo $rowbank->name ?></a></td>
                                            <td><?php echo showWithCurrencySymbol($rowbank->balance) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>

                                <h5><b>Customers</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($customerData as $rowcus) {
                                        if ($rowcus->balance > 0) { ?>
                                            <tr>
                                                <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger?customer_id='.$rowcus->customer_id)?>" class="text-black" ><?php echo $rowcus->customer_name ?></a></td>
                                                <td><?php echo showWithCurrencySymbol($rowcus->balance) ?></td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </table>

                                <h5><b>Account Head</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($loanProData as $rowlon) {
                                        if ($rowlon->balance > 0) { ?>
                                            <tr>
                                                <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_loan?loan_pro_id='.$rowlon->loan_pro_id)?>" class="text-black" ><?php echo $rowlon->name ?></a></td>
                                                <td><?php echo showWithCurrencySymbol($rowlon->balance) ?></td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </table>

                                <h5><b>Suppliers</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($supplierData as $rowsup) {
                                        if ($rowsup->balance > 0) { ?>
                                            <tr>
                                                <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_suppliers?supplier_id='.$rowsup->supplier_id)?>" class="text-black" ><?php echo $rowsup->name ?></a></td>
                                                <td><?php echo showWithCurrencySymbol($rowsup->balance) ?></td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </table>

                                <h5><b>Employe </b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($employee as $rowem) { ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_employee?employee_id='.$rowem->employee_id)?>" class="text-black" ><?php echo $rowem->name ?></a></td>
                                            <td><?php echo showWithCurrencySymbol($rowem->balance) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                <h5><b>Expense </b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <!--                                    <tr>-->
                                    <!--                                        <td style="width: 50%;">Expense</td>-->
                                    <!--                                        <td>--><?php //echo showWithCurrencySymbol($expensedata); ?><!--</td>-->
                                    <!--                                    </tr>-->
                                    <?php foreach ($accountsExpenses as $val){ ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_accounts?account_id='.$val->account_id)?>" class="text-black" ><?= $val->name ?></a></td>
                                            <td><?= showWithCurrencySymbol($val->balance) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                <table class="table table-bordered table-striped" id="">
                                    <tr style="background-color: #decf77;">
                                        <td style="width: 50%;">Total</td>
                                        <td><?php echo showWithCurrencySymbol($totalDebit); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-6 trail">
                                <h4>All Credit</h4>
                                <table class="table table-bordered table-striped" id="">
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_capital')?>" class="text-black" >Capital</a></td>
                                        <td><?php echo showWithCurrencySymbol(-$capitalcr); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_profit')?>" class="text-black" >Profit</a></td>
                                        <td><?php echo showWithCurrencySymbol(-$profit); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_vat')?>" class="text-black" >Vat</a></td>
                                        <td><?php echo showWithCurrencySymbol(-$vatEarn); ?></td>
                                    </tr>

                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_service_charge')?>" class="text-black" >Service Charge</a></td>
                                        <td><?php echo showWithCurrencySymbol(-$service_charge); ?></td>
                                    </tr>

                                </table>

                                <h5><b>Account Head</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($loanProData as $rowlonc) { if ($rowlonc->balance < 0) {  ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_loan?loan_pro_id='.$rowlonc->loan_pro_id)?>" class="text-black" ><?php echo $rowlonc->name ?></a></td>
                                            <td><?php echo showWithCurrencySymbol(-$rowlonc->balance) ?></td>
                                        </tr>
                                    <?php }} ?>
                                </table>

                                <h5><b>Suppliers</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($supplierData as $rowsupc) { if ($rowsupc->balance < 0) {  ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_suppliers?supplier_id='.$rowsupc->supplier_id)?>" class="text-black" ><?php echo $rowsupc->name ?></a></td>
                                            <td><?php echo showWithCurrencySymbol(-$rowsupc->balance) ?></td>
                                        </tr>
                                    <?php }} ?>
                                </table>

                                <h5><b>Customers</b></h5>
                                <table class="table table-bordered table-striped" id="">
                                    <?php foreach ($customerData as $rowcusc) { if ($rowcusc->balance < 0) {  ?>
                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger?customer_id='.$rowcusc->customer_id)?>" class="text-black" ><?php echo $rowcusc->customer_name ?></a></td>
                                            <td><?php echo showWithCurrencySymbol(-$rowcusc->balance) ?></td>
                                        </tr>
                                    <?php } } ?>
                                </table>

                                <table class="table table-bordered table-striped" >
                                    <tr style="background-color: #decf77;">
                                        <td style="width: 50%;">Total</td>
                                        <td><?php echo showWithCurrencySymbol(-$totalCredit); ?></td>
                                    </tr>
                                </table>
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
