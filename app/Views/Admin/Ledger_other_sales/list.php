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
            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Ledger_other_sales') ?>" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" required>
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Ledger_other_sales') ?>" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">

                <div class="box">
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

                   

                    <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
                    <div class="col-xs-12" style="margin-bottom: 20px;   ">
                        <div class="col-xs-6">
                            <?php if(logo_image() == NULL){ ?>
                                <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                            <?php }else{ ?>
                                <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                            <?php } ?>
                        </div>
                        <div class="col-xs-6">
                            <?php print address(); ?>
                        </div>
                    </div>
                    <div class="col-md-12" >
                        <table class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($ledger_data as $row) {
                                $particulars = ($row->particulars == NULL) ? "Payment" : $row->particulars;
                                $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                $amountDr =($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount);
                                ?>
                                <tr>
                                    <td><?php echo bdDateFormat($row->createdDtm) ?></td>
                                    <td><?php echo $particulars ?></td>
                                    <td><?php echo $amountDr ?></td>
                                    <td><?php echo $amountCr ?></td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>
                    </div>
                </div>

                    <!-- /.box-body -->
                </div>

 <div class="row no-print" >
                        <div class="col-xs-12">
                            <button onclick="printDiv('ledgPrint')"    class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','profit')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','profit')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                        </div>
                    </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
