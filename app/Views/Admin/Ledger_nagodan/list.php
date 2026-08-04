<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Cash Ledger <small>Cash Ledger</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cash Ledger</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv" style="margin-bottom:20px; ">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Ledger_nagodan')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Ledger_nagodan">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <?php if (isset($filter) && $filter == 1){ ?>
            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date"  id="st_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" id="en_date" required>
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" onclick="nagodLedSerc();nagodLedSercPrint();" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Ledger_nagodan') ?>" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="col-xs-12">

                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body" style="margin-top: 20px;" id="ledger_cash">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped" id="example1">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Trangaction Id</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalRows = count($ledger_nagodan_data) - 1;
                                    for ($i = 0; $i <= $totalRows; $i++) {
                                        $particulars = ($ledger_nagodan_data[$i]->particulars == NULL) ? "Payment" : $ledger_nagodan_data[$i]->particulars;
                                        $amountCr = ($ledger_nagodan_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_nagodan_data[$i]->amount);
                                        $amountDr = ($ledger_nagodan_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_nagodan_data[$i]->amount);

                                        ?>
                                        <tr>
                                            <td><?php echo $ledger_nagodan_data[$i]->ledg_nagodan_id ?></td>
                                            <td><?php echo $ledger_nagodan_data[$i]->createdDtm ?></td>
                                            <td><?php echo $particulars ?></td>
                                            <td><a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/read/' . $ledger_nagodan_data[$i]->trans_id); ?>','<?php echo '/Admin/Transaction/read/' . $ledger_nagodan_data[$i]->trans_id; ?>')" ><?php echo $ledger_nagodan_data[$i]->trans_id ?> </a></td>
                                            <td><?php echo $amountDr ?></td>
                                            <td><?php echo $amountCr ?></td>
                                            <td><?php echo showWithCurrencySymbol($ledger_nagodan_data[$i]->r_balance) ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Trangaction Id</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>
                                            = <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Dr.')) ?></th>
                                        <th>
                                            = <?php echo showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Cr.')) ?></th>
                                        <th>= <?php echo admin_cash(); ?></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-xs-12">
                                <?php if (isset($print) && $print == 1){ ?>
                                    <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now </button>
                                <?php } ?>
                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','cash')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <?php } ?>
                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','cash')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                <?php } ?>
                            </div>


                        </div>

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
                            <table class="table table-bordered table-striped">
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
                                <?php
                                    foreach ($ledger_nagodan_data as $row) {
                                        $particulars = ($row->particulars == NULL) ? "Payment" : $row->particulars;
                                        $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                        $amountDr =($row->trangaction_type != "Dr.")?"---":showWithCurrencySymbol($row->amount);
                                ?>
                                    <tr>
                                        <td><?php echo bdDateFormat($row->createdDtm) ?></td>
                                        <td><?php echo $particulars ?></td>
                                        <td><?php echo $amountDr ?></td>
                                        <td><?php echo $amountCr ?></td>
                                        <td><?php echo showWithCurrencySymbol($row->r_balance) ?></td>
                                    </tr>
                                <?php }?>

                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-md-12" style="display: none; text-transform: capitalize; " >
                        <div id="ledger_cashPrint"></div>
                    </div>

                    <div class="row no-print" >
                        <div class="col-xs-12" id="printbutt" style="display: none;">
                            <button onclick="printDiv('ledger_cashPrint')"    class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledger_cashPrint','cash')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledger_cashPrint','cash')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
