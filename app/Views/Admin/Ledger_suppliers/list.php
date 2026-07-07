<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Supplier Ledger <small>Supplier Ledger</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Supplier Ledger</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Ledger_suppliers')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Ledger_suppliers">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <div class="col-xs-12">
                <?php if (isset($filter) && $filter == 1){ ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Ledger_suppliers')?>" method="get">
                            <div class="col-md-2" >
                                <label for="int">Supplier name</label>
                                <select class="form-control select2 select2-hidden-accessible" name="supplier_id" onchange="formSubmit(this)" id="supplierId" style=" width: 100%;" tabindex="-1" aria-hidden="true" required>
                                    <option selected="selected"  value="">Please Select</option>
                                    <?php echo getAllListInOption($supplier_id,'supplier_id','name','suppliers'); ?>
                                </select>

                            </div>
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                       id="st_date" >
                            </div>
                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                       id="en_date" >
                            </div>

                            <div class="col-md-2" style="margin-top: 25px;">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                    Filter
                                </button>
                            </div>
                            <div class="col-md-2" style="margin-top: 25px;">
                                <a href="<?= base_url('Admin/Ledger_suppliers') ?>" class="btn btn-default btn-block"><i
                                            class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </form>

                    </div>
                    <!-- /.box-body -->
                </div>
                <?php } ?>
                <?php
                $name = get_data_by_id('name', 'suppliers', 'supplier_id', $supplier_id);
                $balance = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplier_id);
                ?>
                <div class="box">
                    <?php if (!empty($supplier_id)){ ?>
                        <div class="box-header">
                            <h3 class="box-title">Supplier: <?= $name ?></h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Total Received:</td><td><?= showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Cr.', 'supplier_id', $supplier_id)) ?></td></tr><tr><td>Total Paid:</td><td><?= showWithCurrencySymbol(get_total('ledger_suppliers', 'amount', 'Dr.', 'supplier_id', $supplier_id)) ?></td></tr><tr><td>Due Balance:</td><td><?= showWithCurrencySymbol($balance) ?></td></tr></table></span>
                        </div>
                    <?php } ?>

                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Memo</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $totalRows = count($result) - 1;
                            for ($i = $totalRows; $i >= 0; $i--) {
                                $particulars = ($result[$i]->particulars == NULL) ? "Pay due" : $result[$i]->particulars;
                                $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
                                $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);

                                if (($result[$i]->purchase_id == NULL) && ($result[$i]->trans_id == NULL)) {
                                    $purchaseId = '---';
                                } else {
                                    $purchaseId = ($result[$i]->purchase_id == NULL) ? '<a href="' . site_url('Admin/Transaction/read/' . $result[$i]->trans_id) . '">TRNS_' . $result[$i]->trans_id . '</a>' : '<a href="' . site_url('Admin/Purchase/view/' . $result[$i]->purchase_id) . '">PURS_' . $result[$i]->purchase_id . '</a>';
                                }
                                ?>
                                <tr>
                                    <td><?= $result[$i]->ledg_sup_id ?></td>
                                    <td><?= $result[$i]->createdDtm ?></td>
                                    <td><?= $particulars ?></td>
                                    <td><?= $purchaseId ?></td>
                                    <td><?= $amountDr ?></td>
                                    <td><?= $amountCr ?></td>
                                    <td><?= showWithCurrencySymbol($result[$i]->rest_balance) ?></td>
                                </tr>

                            <?php }?>

                            </tbody>

                        </table>
                    </div>
                </div>

                <div style="display: none;" >
                    <div id="ledgPrint">
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
                                    <th>Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $totalRows = count($result) - 1;
                                for ($i = $totalRows; $i >= 0; $i--) {
                                    $particulars = ($result[$i]->particulars == NULL) ? "Pay due" : $result[$i]->particulars;
                                    $amountCr = ($result[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
                                    $amountDr = ($result[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($result[$i]->amount);
                                    ?>
                                    <tr>
                                        <td><?= $result[$i]->createdDtm ?></td>
                                        <td><?= $particulars ?></td>
                                        <td><?= $amountDr ?></td>
                                        <td><?= $amountCr ?></td>
                                        <td><?= showWithCurrencySymbol($result[$i]->rest_balance) ?></td>
                                    </tr>
                                <?php }?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="row no-print" >
                    <div class="col-xs-12">
                        <?php if (isset($print) && $print == 1){ ?>
                        <button onclick="printDiv('ledgPrint')"  class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                        <?php } ?>
                        <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','suppliers')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                        <?php } ?>
                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','suppliers')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                        <?php } ?>
                    </div>
                </div>


            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
