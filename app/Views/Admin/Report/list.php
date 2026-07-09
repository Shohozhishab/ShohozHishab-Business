<style>
    table.report-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
        font-family: Arial, sans-serif;
        font-size: 15px;
    }

    table.report-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    table.report-table td {
        border: 1px solid #ddd;
        padding: 12px 15px;
    }

    table.report-table td:first-child {
        font-weight: 600;
        width: 50%;
    }

    table.report-table td:last-child {
        text-align: right;
        font-weight: 600;
    }

    table.report-table tr:last-child td {
        background: #f1f8ff;
        font-size: 16px;
        font-weight: bold;
    }
</style>
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Report <small>Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Report</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Report')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Report">
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
                                <h3 class="box-title">Report</h3>
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
                                <h4>All Earning</h4>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_nagodan')?>" class="text-black ta-link" >Cash</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol($cash); ?></td>
                                    </tr>

                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Stores')?>" class="text-black" >Stock Amount</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol($stockAmount); ?></td>
                                    </tr>
<!--                                    <tr>-->
<!--                                        <td style="width: 50%;"><a href="--><?php //= base_url('Admin/Ledger_profit')?><!--" class="text-black" >Profit</a></td>-->
<!--                                        <td>--><?php //echo showWithCurrencySymbol(-$profit); ?><!--</td>-->
<!--                                    </tr>-->
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Service')?>" class="text-black" >Service Charge</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol(-$service_charge); ?></td>
                                    </tr>
                                <?php $totalAccAssets = 0; foreach ($accountsAssets as $val){ $totalAccAssets +=$val->balance; }  ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Assets')?>" class="text-black" >Assets</a></td>
                                        <td style="float: right;"><?= showWithCurrencySymbol($totalAccAssets) ?></td>
                                    </tr>
                                    <?php $bankBalAll = 0; foreach ($bankData as $rowbank) { $bankBalAll +=$rowbank->balance; } ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Bank')?>" class="text-black" >Bank</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol($bankBalAll) ?></td>
                                    </tr>


                                <?php
                                    $customerBalAll = 0;
                                    foreach ($customerData as $rowcus) {
                                        if ($rowcus->balance > 0) { $customerBalAll += $rowcus->balance;
                                    } }
                                    $accountHeadBalAll = 0;
                                    foreach ($loanProData as $rowlon) {
                                        if ($rowlon->balance > 0) { $accountHeadBalAll += $rowlon->balance;
                                         } }
                                     $supplierBalAll = 0;
                                    foreach ($supplierData as $rowsup) {
                                        if ($rowsup->balance > 0) { $supplierBalAll += $rowsup->balance;
                                        }
                                    }
                                    $receivable = $customerBalAll+$accountHeadBalAll+$supplierBalAll;
                                ?>


                                        <tr>
                                            <td style="width: 50%;"><a href="<?= base_url('Admin/Acquisition_due')?>" class="text-black" >Total Receivable</a></td>
                                            <td style="float: right;"><?php echo showWithCurrencySymbol($receivable) ?></td>
                                        </tr>
                                </table>
                                <?php
                                    $serviceCharge = -$service_charge;
                                    $totalDebitAll = $cash + $stockAmount + $serviceCharge + $totalAccAssets + $bankBalAll+$customerBalAll+$accountHeadBalAll+$supplierBalAll;

                                ?>
                            </div>
                            <div class="col-lg-6 trail">
                                <h4>All Expense</h4>
                                <table class="table table-bordered table-striped" id="">
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Capital/list')?>" class="text-black" >Capital</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol(-$capitalcr); ?></td>
                                    </tr>

                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Ledger_vat')?>" class="text-black" >Vat</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol(-$vatEarn); ?></td>
                                    </tr>

                                    <?php $expAllCr = 0; foreach ($accountsExpenses as $val){ $expAllCr += $val->balance;} ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Expenses')?>" class="text-black" >Expense</a></td>
                                        <td style="float: right;"><?= showWithCurrencySymbol($expAllCr) ?></td>
                                    </tr>

                                    <?php $empAllCr = 0; foreach ($employee as $rowem) { $empAllCr += $rowem->balance;} ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Employee')?>" class="text-black" >Employee</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol($empAllCr) ?></td>
                                    </tr>
                                    <?php
                                        $accBalCr = 0; foreach ($loanProData as $rowlonc) { if ($rowlonc->balance < 0) { $accBalCr += $rowlonc->balance;  }}
                                        $supAllCr = 0; foreach ($supplierData as $rowsupc) { if ($rowsupc->balance < 0) { $supAllCr += $rowsupc->balance;  }}
                                        $cusAllCr = 0; foreach ($customerData as $rowcusc) { if ($rowcusc->balance < 0) { $cusAllCr +=$rowcusc->balance;  }}
                                        $payable = $accBalCr + $supAllCr + $cusAllCr;
                                    ?>
                                    <tr>
                                        <td style="width: 50%;"><a href="<?= base_url('Admin/Owe_amount')?>" class="text-black" >Payable</a></td>
                                        <td style="float: right;"><?php echo showWithCurrencySymbol(-$payable) ?></td>
                                    </tr>
                                </table>
                                <?php $totalCreditAll = $capitalcr + $vatEarn + $accBalCr + $supAllCr + $cusAllCr + $expAllCr + $empAllCr; ?>
                            </div>
                            <div class="col-lg-12" style="padding: unset;">
                                <div class="col-lg-6">
                                    <table class="table table-bordered table-striped" id="">
                                        <tr style="background-color: #decf77;">
                                            <td style="width: 50%;"><b>Total Earning</b></td>
                                            <td style="float: right;">
                                                <?php //echo showWithCurrencySymbol($totalDebit); ?>
                                                <?php echo showWithCurrencySymbol($totalDebitAll); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table table-bordered table-striped" >
                                        <tr style="background-color: #decf77;">
                                            <td style="width: 50%;"><b>Total Expense</b></td>
                                            <td style="float: right;">
                                                <?php //echo showWithCurrencySymbol(-$totalCredit); ?>
                                                <?php echo showWithCurrencySymbol(-$totalCreditAll); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12">
<!--                                <div class="col-lg-4"></div>-->
                                <div class="col-lg-6"></div>
                                <div class="col-lg-6">
                                <table class="report-table">
                                    <tr>
                                        <td>Total Earning</td>
                                        <td><?php echo showWithCurrencySymbol($totalDebitAll); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total Expense</td>
                                        <td><?php echo showWithCurrencySymbol(-$totalCreditAll); ?></td>
                                    </tr>
                                    <tr >
                                        <td style="width: 50%;">Total Gross Profit/Loss</td>
                                        <td>
                                            <?php $totalGross = $totalDebitAll -  (-$totalCreditAll); ?>
                                            <?php if ($totalGross >= 0){ ?>
                                            <span style="color: green;"><?= showWithCurrencySymbol($totalGross)?></span>
                                            <?php }else{ ?>
                                                <span style="color: red;"><?= showWithCurrencySymbol($totalGross)?></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </table>
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
