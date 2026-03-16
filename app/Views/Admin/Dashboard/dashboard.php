
    <div class="content-wrapper" id="viewpage">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <div class="col-lg-3 col-xs-6" >
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Purchase/create'); ?>" class="btn">
                        <div class="small-box bg-aqua" >
                            <div class="inner">

                                <h2></h2>
                                <p id="dashp">Purchase</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-fw fa-briefcase"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Purchase/create'); ?>" class="small-box-footer">Purchase Create <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('/Admin/Sales/create'); ?>" class="btn ">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Sale</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-fw fa-cart-plus"></i>
                            </div>
                            <a href="<?php echo site_url('/Admin/Sales/create'); ?>" class="small-box-footer">Sale Create <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->

                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Transaction/create'); ?>" class="btn">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">All Transaction</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-fw fa-exchange"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Transaction/create'); ?>" class="small-box-footer">Transaction Create <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Ledger_nagodan'); ?>" class="btn">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Cash Ledger</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Ledger_nagodan'); ?>" class="small-box-footer">View Ledger <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
            </div>

            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Ledger'); ?>" class="btn">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Customer Ledger</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Ledger'); ?>" class="small-box-footer">View Ledger <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Ledger_suppliers'); ?>" class="btn">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Supplier Ledger</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Ledger_suppliers'); ?>" class="small-box-footer">View Ledger <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Ledger_bank'); ?>" class="btn">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Bank Ledger</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Ledger_bank'); ?>" class="small-box-footer">View Ledger <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <a href="<?php echo site_url('Admin/Ledger_loan'); ?>" class="btn">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h2></h2>
                                <p id="dashp">Account Head Ledger</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <a href="<?php echo site_url('Admin/Ledger_loan'); ?>" class="small-box-footer">View Ledger <i class="fa fa-arrow-circle-right"></i></a>
                        </div></a>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-12 connectedSortable">
                    <!-- Custom tabs (Charts with tabs)-->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">All Bank Balance</span>
                                    <span class="info-box-number"><?php echo showWithCurrencySymbol($totalBankBal); ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Cash</span>
                                    <span class="info-box-number"><?php echo admin_cash(); ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->

                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua"><i class="fa fa-sort-amount-asc"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Due</span>
                                    <span class="info-box-number"><?php echo showWithCurrencySymbol($totalDue); ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua"><i class="fa fa-sort-amount-desc"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">oweing / Arrears</span>
                                    <span class="info-box-number"><?php echo showWithCurrencySymbol($totalGet); ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->

                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-list"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Product</span>
                                    <span class="info-box-number"><?php echo $totalProduct; ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="ion ion-person-add"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Total Customer</span>
                                    <span class="info-box-number"><?php echo $totalCustomer; ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.nav-tabs-custom -->


                </section>
                <!-- /.Left col -->

            </div>
            <!-- /.row (main row) -->

        </section>
        <!-- /.content -->
    </div>
