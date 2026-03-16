<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Vat Ledger <small>Vat Ledger</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Vat Ledger</li>
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
                                <h3 class="box-title">Vat Ledger</h3>
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
                                <th>Rest Balance</th>
                            </tr></thead><tbody><?php
                            $restBalance = 0;
                            $totalRows = count($ledger_vat_data)-1;
                            for($i = $totalRows; $i >= 0; $i--) {

                                $amountCr = ($ledger_vat_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_vat_data[$i]->amount);
                                $amountDr =($ledger_vat_data[$i]->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($ledger_vat_data[$i]->amount);
                                if ($ledger_vat_data[$i]->trangaction_type == 'Dr.') {
                                    $restBalance = $restBalance + $ledger_vat_data[$i]->amount;
                                }else {
                                    $restBalance = $restBalance - $ledger_vat_data[$i]->amount;
                                }
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $ledger_vat_data[$i]->ledg_vat_id;  ?></td>
                                    <td><?php echo $ledger_vat_data[$i]->createdDtm;  ?></td>
                                    <td><?php echo $ledger_vat_data[$i]->particulars ?></td>
                                    <td><?php echo $ledger_vat_data[$i]->ledg_vat_id  ?></td>
                                    <td><?php echo $amountDr ?></td>
                                    <td><?php echo $amountCr ?></td>
                                    <td><?php echo showWithCurrencySymbol($restBalance) ?></td>

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
                                <th>Rest Balance</th>
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
