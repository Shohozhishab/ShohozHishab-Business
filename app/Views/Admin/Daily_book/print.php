<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Daily Book<small>Daily Book Print</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Daily Book</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-4">
                                <h3 class="box-title">Daily Book Print</h3>
                            </div>
                            <div class="col-lg-4">
                                <button class="print_line btn btn-primary pull-right" style="margin-right: 20px;" onclick="printDiv('printdata')" ><i class="fa fa-print"></i> Print </button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('dowData','dailyBook')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>

                            </div>
                            <div class="col-lg-4">
                                <form action="<?php echo site_url('Admin/Daily_book/print_preview'); ?>" method="get">
                                    <div class="input-group pull-right no-print">
                                <span class="input-group-addon " style="background-color:#367FA9; ">
                                    <i class="fa fa-fw fa-filter" style="color: white;"></i>
                                </span>
                                        <input type="date" class="form-control " name="date" id="date" value="<?= $dateSelected;?>" required>
                                        <span class="input-group-btn">
                                  <button class="btn btn-primary " type="submit">Filter</button>
                                </span>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12 col-md-12"  id="printdata"  >

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

                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;">
                                <div class="<?=($searchDate == date('Y-m-d'))?'col-xs-8 col-md-8': 'col-xs-12 col-md-12'; ?>">
                                    <h3>Cash Statement</h3>
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                        <tr>
                                            <td>Date</td>
                                            <td>Particulars</td>
                                            <td>Debit</td>
                                            <td>Credit</td>
                                            <td>Balance</td>
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
                                                <td><?php echo showWithCurrencySymbol($cashLedger[$i]->rest_balance) ?></td>
                                            </tr>

                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <?php if ($searchDate == date('Y-m-d')){ ?>
                                    <h3>Balance</h3>
                                    <table class="table table-bordered table-striped" >
                                        <tr>
                                            <td>Previous Balance :</td>
                                            <td><?php echo showWithCurrencySymbol($prevAll_balance) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Last Balance :</td>
                                            <td><?php echo showWithCurrencySymbol($cashrest_balance) ?></td>
                                        </tr>
                                    </table>
                                    <?php } ?>
                                </div>
                            </div>

                            <?php foreach ($allBank as $rowbak) { ?>
                                <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                    <div class="<?=($searchDate == date('Y-m-d'))?'col-xs-8 col-md-8': 'col-xs-12 col-md-12'; ?> ">
                                        <h3><?php echo $rowbak->name; ?></h3>
                                        <table class="table table-bordered table-striped" >
                                            <thead>
                                            <tr>
                                                <td>Date</td>
                                                <td>Particulars</td>
                                                <td>Debit</td>
                                                <td>Credit</td>
                                                <td>Balance</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $bankData =  bank_ledger($rowbak->bank_id,$searchDate);
                                            foreach ($bankData as $row) {

                                                $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
                                                $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                                $amountDr =($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount);
                                                $transId = ($row->trans_id == NULL)?"---":$row->trans_id;
                                                $purchaseId = ($row->purchase_id == NULL)?"---":$row->purchase_id;
                                                $invoiceId = ($row->invoice_id == 0)?"---":$row->invoice_id;
                                                ?>
                                                <tr>
                                                    <td><?php print invoiceDateFormat($row->createdDtm) ?></td>
                                                    <td><?php print $particulars ?></td>
                                                    <td><?php print $amountDr ?></td>
                                                    <td><?php print $amountCr ?></td>
                                                    <td><?php print showWithCurrencySymbol($row->rest_balance) ?></td>
                                                </tr>


                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-4 col-md-4">
                                        <?php if ($searchDate == date('Y-m-d')){ ?>
                                        <h3>Balance</h3>
                                        <table class="table table-bordered table-striped" >
                                            <tr>
                                                <td>Previous Balance :</td>
                                                <td><?php echo showWithCurrencySymbol(bank_ledger_prev_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Last Balance :</td>
                                                <td><?php echo showWithCurrencySymbol(bank_ledger_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                            </tr>
                                        </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Sale List</h3>
                                <?php $profitAcc = permission_check('Sales',newSession()->role,'profit');?>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Total Due</th>
                                        <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                        <th>Profit</th>
                                        <?php } ?>
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
                                            <td><?php echo invoiceDateFormat($val->date) ?></td>
                                            <td><?php echo $cusName ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                            <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                            <td><?php echo showWithCurrencySymbol($profit) ?></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                    <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Total Profit</th>
                                            <th><?= showWithCurrencySymbol($totalProfit);?></th>
                                        </tr>
                                    </tfoot>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Purchase List</h3>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>Purchase Date</th>
                                        <th>Supplier </th>
                                        <th>Total Amount</th>
                                        <th>Due</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;
                                    foreach ($purchase_data as $purchase) { ?>
                                        <tr>
                                            <td><?php echo invoiceDateFormat($purchase->date) ?></td>
                                            <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id); ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Capital List</h3>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                    <tr>
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
                                            <td><?= invoiceDateFormat($val->createdDtm) ?></td>
                                            <td><?= $val->description ?></td>
                                            <td><?= showWithCurrencySymbol($val->amount) ?></td>
                                        </tr>
                                    <?php }?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Transaction List</h3>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
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
                                            <td><?= invoiceDateFormat($row->date) ?></td>
                                            <td><?= $name;?></td>
                                            <td><?= $row->trangaction_type; ?></td>
                                            <td><?= showWithCurrencySymbol($row->amount); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>



                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-body">
                        <div class="col-xs-12 col-md-12" style="display: none;" id="dowData"  >

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

                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;">
                                <div class="<?=($searchDate == date('Y-m-d'))?'col-xs-8 col-md-8': 'col-xs-12 col-md-12'; ?>">
                                    <h3>Cash Statement</h3>
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                        <tr>
                                            <td>Date</td>
                                            <td>Particulars</td>
                                            <td>Debit</td>
                                            <td>Credit</td>
                                            <td>Balance</td>
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
                                                <td><?php echo showWithCurrencySymbol($cashLedger[$i]->rest_balance) ?></td>
                                            </tr>

                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <?php if ($searchDate == date('Y-m-d')){ ?>
                                        <h3>Balance</h3>
                                        <table class="table table-bordered table-striped" >
                                            <tr>
                                                <td>Previous Balance :</td>
                                                <td><?php echo showWithCurrencySymbol($prevAll_balance) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Last Balance :</td>
                                                <td><?php echo showWithCurrencySymbol($cashrest_balance) ?></td>
                                            </tr>
                                        </table>
                                    <?php } ?>
                                </div>
                            </div>

                            <?php foreach ($allBank as $rowbak) { ?>
                                <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                    <div class="<?=($searchDate == date('Y-m-d'))?'col-xs-8 col-md-8': 'col-xs-12 col-md-12'; ?> ">
                                        <h3><?php echo $rowbak->name; ?></h3>
                                        <table class="table table-bordered table-striped" >
                                            <thead>
                                            <tr>
                                                <td>Date</td>
                                                <td>Particulars</td>
                                                <td>Debit</td>
                                                <td>Credit</td>
                                                <td>Balance</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $bankData =  bank_ledger($rowbak->bank_id,$searchDate);
                                            foreach ($bankData as $row) {

                                                $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
                                                $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                                $amountDr =($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount);
                                                $transId = ($row->trans_id == NULL)?"---":$row->trans_id;
                                                $purchaseId = ($row->purchase_id == NULL)?"---":$row->purchase_id;
                                                $invoiceId = ($row->invoice_id == 0)?"---":$row->invoice_id;
                                                ?>
                                                <tr>
                                                    <td><?php print invoiceDateFormat($row->createdDtm) ?></td>
                                                    <td><?php print $particulars ?></td>
                                                    <td><?php print $amountDr ?></td>
                                                    <td><?php print $amountCr ?></td>
                                                    <td><?php print showWithCurrencySymbol($row->rest_balance) ?></td>
                                                </tr>


                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-4 col-md-4">
                                        <?php if ($searchDate == date('Y-m-d')){ ?>
                                            <h3>Balance</h3>
                                            <table class="table table-bordered table-striped" >
                                                <tr>
                                                    <td>Previous Balance :</td>
                                                    <td><?php echo showWithCurrencySymbol(bank_ledger_prev_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Last Balance :</td>
                                                    <td><?php echo showWithCurrencySymbol(bank_ledger_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                                </tr>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Sale List</h3>
                                <?php $profitAcc = permission_check('Sales',newSession()->role,'profit');?>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Total Due</th>
                                        <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                            <th>Profit</th>
                                        <?php } ?>
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
                                            <td><?php echo invoiceDateFormat($val->date) ?></td>
                                            <td><?php echo $cusName ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                            <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                                <td><?php echo showWithCurrencySymbol($profit) ?></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                    <?php if (isset($profitAcc) && $profitAcc == 1){ ?>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Total Profit</th>
                                            <th><?= showWithCurrencySymbol($totalProfit);?></th>
                                        </tr>
                                        </tfoot>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Purchase List</h3>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Purchase Date</th>
                                        <th>Supplier </th>
                                        <th>Total Amount</th>
                                        <th>Due</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;
                                    foreach ($purchase_data as $purchase) { ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo invoiceDateFormat($purchase->date) ?></td>
                                            <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id); ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Capital List</h3>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                    <tr>
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
                                            <td><?= invoiceDateFormat($val->createdDtm) ?></td>
                                            <td><?= $val->description ?></td>
                                            <td><?= showWithCurrencySymbol($val->amount) ?></td>
                                        </tr>
                                    <?php }?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                <h3>Transaction List</h3>
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
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
                                            <td><?= invoiceDateFormat($row->date) ?></td>
                                            <td><?= $name;?></td>
                                            <td><?= $row->trangaction_type; ?></td>
                                            <td><?= showWithCurrencySymbol($row->amount); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>



                        </div>
                    </div>
                </div>


            </div>



        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
