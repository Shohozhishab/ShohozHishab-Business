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
                            </div>
                            <div class="col-lg-4">
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
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-xs-12 col-md-12" id="printdata"  >

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
                                <div class="col-xs-8 col-md-8">
                                    <h3>Cash Statement</h3>
                                    <table class="table table-bordered table-striped" style="font-size: 12px;">
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
                                                <td><?php echo bdDateFormat($cashLedger[$i]->createdDtm) ?></td>
                                                <td><?php echo $particulars ?></td>
                                                <td><?php echo $amountDr ?></td>
                                                <td><?php echo $amountCr ?></td>
                                                <td><?php echo showWithCurrencySymbol($cashLedger[$i]->rest_balance) ?></td>
                                            </tr>

                                        <?php } ?>

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td>Date</td>
                                            <td>Particulars</td>
                                            <!-- <th>Trangaction Id</th> -->
                                            <td>Debit</td>
                                            <td>Credit</td>
                                            <td>Balance</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <h3>Balance</h3>
                                    <table class="table table-bordered table-striped" style="font-size: 12px;">
                                        <tr>
                                            <td>Previous Balance :</td>
                                            <td><?php echo showWithCurrencySymbol($prevAll_balance) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Last Balance :</td>
                                            <td><?php echo showWithCurrencySymbol($cashrest_balance) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <?php foreach ($allBank as $rowbak) { ?>
                                <div class="col-xs-12 col-md-12" style="text-transform: capitalize;" >
                                    <div class="col-xs-8 col-md-8">
                                        <h3><?php echo $rowbak->name; ?></h3>
                                        <table class="table table-bordered table-striped" style="font-size: 12px;">
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
                                            <?php $bankData =  bank_ledger($rowbak->bank_id,date('Y-m-d'));
                                            foreach ($bankData as $row) {

                                                $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
                                                $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                                $amountDr =($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount);
                                                $transId = ($row->trans_id == NULL)?"---":$row->trans_id;
                                                $purchaseId = ($row->purchase_id == NULL)?"---":$row->purchase_id;
                                                $invoiceId = ($row->invoice_id == 0)?"---":$row->invoice_id;
                                                ?>
                                                <tr>
                                                    <td><?php print bdDateFormat($row->createdDtm) ?></td>
                                                    <td><?php print $particulars ?></td>
                                                    <td><?php print $amountDr ?></td>
                                                    <td><?php print $amountCr ?></td>
                                                    <td><?php print showWithCurrencySymbol($row->rest_balance) ?></td>
                                                </tr>


                                            <?php }?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td>Date</td>
                                                <td>Particulars</td>
                                                <td>Debit</td>
                                                <td>Credit</td>
                                                <td>Balance</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-xs-4 col-md-4">
                                        <h3>Balance</h3>
                                        <table class="table table-bordered table-striped" style="font-size: 12px;">
                                            <tr>
                                                <td>Previous Balance :</td>
                                                <td><?php echo showWithCurrencySymbol(bank_ledger_prev_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Last Balance :</td>
                                                <td><?php echo showWithCurrencySymbol(bank_ledger_restBalance($rowbak->bank_id,date('Y-m-d'))) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>


                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>


            </div>



        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
