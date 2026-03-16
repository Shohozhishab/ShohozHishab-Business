<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Ledger other sales  <small>Ledger other sales</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Ledger other sales</li>
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
                                <h3 class="box-title">Ledger other sales</h3>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Memo </th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <!--		<th>Rest Balance</th>-->
                            </tr></thead><tbody><?php
                            $totalRows = count($ledger_data)-1;
                            for($i = $totalRows; $i >= 0; $i--) {

                                $amountCr = ($ledger_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_data[$i]->amount);
                                $amountDr =($ledger_data[$i]->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($ledger_data[$i]->amount);
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $ledger_data[$i]->ledg_oth_sales_id;  ?></td>
                                    <td><?php echo $ledger_data[$i]->createdDtm;  ?></td>
                                    <td><?php echo $ledger_data[$i]->particulars ?></td>
                                    <td><?php echo $ledger_data[$i]->ledg_oth_sales_id  ?></td>
                                    <td><?php echo $amountDr ?></td>
                                    <td><?php echo $amountCr ?></td>
                                    <!--			<td>--><?php //echo showWithCurrencySymbol($ledger_data[$i]->rest_balance) ?><!--</td>-->

                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>

                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Memo </th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <!--        <th>Rest Balance</th>-->
                            </tr></tfoot>

                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>


            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
