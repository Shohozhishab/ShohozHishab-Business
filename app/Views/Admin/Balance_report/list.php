<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Balance Report  <small>Balance Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Balance Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Balance Report Generation</h3>
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
                        <table class="table table-bordered table-striped" >
                            <tbody>
                            <tr>
                                <td>Cash </td>
                                <td>Bank </td>
                                <td>Accounts Receivable </td>

                                <td>Total Product Price</td>
                                <td>Total Amount</td>
                            </tr>
                            <tr>
                                <td><?php echo showWithCurrencySymbol($cash) ;?></td>
                                <td><?php echo showWithCurrencySymbol($bankCash);?></td>
                                <td><?php echo showWithCurrencySymbol($totalGetCash);?></td>

                                <td><?php echo showWithCurrencySymbol($totalProdPrice)?></td>
                                <td><?php echo showWithCurrencySymbol($totalCash);?></td>
                            </tr>

                            <tr>
                                <td>Accounts Payable</td>
                                <td>Capital</td>
                                <td></td>
                                <td></td>
                                <td>Total Amount</td>
                            </tr>
                            <tr>
                                <td><?php echo showWithCurrencySymbol($totalDueCash) ?></td>
                                <td><?php echo showWithCurrencySymbol($capital)?></td>
                                <td></td>
                                <td></td>
                                <td><?php echo showWithCurrencySymbol($totalDue);?></td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Business profit/loss</td>
                                <td><?php echo showWithCurrencySymbol($totalBalance);?></td>
                            </tr>


                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->



            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-pie-chart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Other</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($otherSaleCash);?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-signal"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Vat Received</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($vatEarning)?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Sale Profit</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($profit);?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-sort-amount-desc"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Expense</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($expenseCash) ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-sitemap"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Salary</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($employeeBalan)?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon "><i class="fa  fa-recycle"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Return Sale Profit  </span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($returnProfit);?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Net Profit</span>
                        <span class="info-box-number"><?php echo showWithCurrencySymbol($profit - $returnProfit);?></span>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
