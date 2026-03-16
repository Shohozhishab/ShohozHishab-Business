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
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-4">
                                <h3 class="box-title">Daily Book</h3>
                            </div>
                            <div class="col-lg-4">
                                <a href="<?php echo site_url('Admin/Daily_book/print_preview'); ?>"
                                   class="print_line btn btn-primary pull-right" style="margin-right: 20px;"><i
                                            class="fa fa-print"></i> Print Statement Now</a>
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
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <!-- <th>Trangaction Id</th> -->
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                </tfoot>
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

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
