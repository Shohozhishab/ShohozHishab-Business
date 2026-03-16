<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Previous Data View  <small>Previous Data View</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Previous Data View</li>
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Previous Data View</h3>
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
                        <div class="col-xs-12" style="margin-top: 20px;">
                            <form method="post" action="<?php echo site_url('Admin/Previous_data_show/data'); ?>" >
                            <div class="col-md-8">
                                <select name="year" id="yearpicker" class="form-control"></select>
                            </div>
                            <div class="col-md-4">

                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>
                        </div>

                        <div class="col-xs-12" style="margin-top: 20px;">
                            <div class="panel with-nav-tabs panel-default nav-tabs-custom" >
                                <ul class="nav nav-tabs" id="ul_border ">
                                    <li class="tab-pane  active in"><a href="#ledger" data-toggle="tab">Ledger</a></li>
                                    <li class="tab-pane "><a href="#report" data-toggle="tab">Report</a></li>
                                </ul>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active in" id="ledger">
                                            <div class="box-header">
                                                <!-- <h3 class="box-title">Ledger</h3> -->
                                                <div class="col-xs-12" >
                                                    <a href="javascript:void(0)" onclick="url_ledger('<?php echo site_url('Admin/previous_data_show/customer_ledger'); ?>')" class="btn btn-default" style="margin-top: 5px;">Ledger Customer</a>

                                                    <a href="javascript:void(0)" onclick="url_ledger('<?php echo site_url('Admin/previous_data_show/bank_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Bank</a>

                                                    <a href="javascript:void(0)" onclick="url_ledger('<?php echo site_url('Admin/previous_data_show/loan_ledger'); ?>')" class="btn btn-default" style="margin-top: 5px;">Ledger Account Head</a>



                                                    <a href="javascript:void(0)"onclick="url_ledger('<?php echo site_url('Admin/previous_data_show/suppliers_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Suppliers</a>

                                                    <a href="javascript:void(0)" onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/cash'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Cash</a>


                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/purchase_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Purchase</a>

                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/sale_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Sales</a>

                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/stock_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Stock</a>
                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/expense_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Expense</a>
                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/profit_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Profit</a>
                                                    <a href="javascript:void(0)"onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/capital_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Capital</a>





                                                    <a href="javascript:void(0)" onclick="searchLedgercash('<?php echo site_url('Admin/previous_data_show/vat_ledger'); ?>')"  class="btn btn-default" style="margin-top: 5px;">Ledger Vat</a>
                                                </div>
                                            </div>
                                            <div class="box-body" >
                                                <div class="row" >

                                                    <div class="col-md-12" >
                                                        <div id="hedden">
                                                            <div class="col-md-6" id="all_user" >
                                                                <label>Customer name</label>
                                                                <select class="form-control select2 select2-hidden-accessible" onchange="searchLedger(this.value,'<?php echo site_url('Admin/previous_data_show/customer')?>');" id="customerId" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                                                                    <option selected="selected"  value="">Please Select</option>
                                                                    <?php foreach ($customer as $view) { ?>
                                                                        <option value="<?php echo $view->customer_id ?>" ><?php echo $view->customer_name ?></option>
                                                                    <?php } ?>

                                                                </select>

                                                            </div>
                                                            <div class="col-md-6"></div>
                                                        </div>
                                                        <div id="nameLabel" class="col-md-12" style="margin-top: 20px;" ></div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade " id="report">
                                            <div class="box-header">
                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/balance'); ?>')" class="btn btn-default">Balance Report</a>

                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/invoice'); ?>')" class="btn btn-default">Invoice</a>

                                                <a href="javascript:void(0)" onclick="reportData('<?php echo site_url('Admin/previous_data_show/stockReport'); ?>')" class="btn btn-default">Stock Report</a>

                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/sale'); ?>')" class="btn btn-default" >Sales Report</a>

                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/purchase'); ?>')" class="btn btn-default" >Purchase Report</a>

                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/acquisitionDue'); ?>')" class="btn btn-default" >Accounts Receivable</a>

                                                <a href="javascript:void(0)" onclick="reportView('<?php echo site_url('Admin/previous_data_show/oweAmount'); ?>')" class="btn btn-default">Accounts Payable</a>

                                            </div>
                                            <div class="box-body" >
                                                <div class="row" >
                                                    <div class="col-xs-12" id="reportView">
                                                        <div class="box">
                                                            <div class="box-header">
                                                                <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Balance Report Generation</h3>
                                                            </div>
                                                            <div class="box-body">
                                                                <table class="table table-bordered table-striped" id="TFtable">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td>Cash </td>
                                                                        <td>Bank </td>
                                                                        <td>Acquisition Due</td>
                                                                        <td>Other</td>
                                                                        <td>Vat Earning</td>
                                                                        <td>Total Product Price</td>
                                                                        <td>Total Sale Profit</td>
                                                                        <td>Total Amount</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo showWithCurrencySymbol($cash) ;?></td>
                                                                        <td><?php echo showWithCurrencySymbol($bankCash);?></td>
                                                                        <td><?php echo showWithCurrencySymbol($totalGetCash);?></td>
                                                                        <td><?php echo showWithCurrencySymbol($otherSaleCash);?></td>
                                                                        <td><?php echo showWithCurrencySymbol($vatEarning)?></td>
                                                                        <td><?php echo showWithCurrencySymbol($totalProdPrice)?></td>
                                                                        <td><?php echo showWithCurrencySymbol($invoiceCash);?></td>
                                                                        <td><?php echo showWithCurrencySymbol($totalCash);?></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>Owe Amount</td>
                                                                        <td>Expense </td>
                                                                        <td>Salary</td>
                                                                        <td>Return Sale Profit</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>Total Amount</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?php echo showWithCurrencySymbol($totalDueCash) ?></td>
                                                                        <td><?php echo showWithCurrencySymbol($expenseCash) ?></td>
                                                                        <td><?php echo showWithCurrencySymbol($employeeBalan)?></td>
                                                                        <td><?php echo showWithCurrencySymbol($returnProfit);?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td><?php echo showWithCurrencySymbol($totalDue);?></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>Total Balance</td>
                                                                        <td><?php echo showWithCurrencySymbol($totalBalance);?></td>
                                                                    </tr>


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
