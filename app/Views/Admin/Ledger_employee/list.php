<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Employee Ledger <small>Employee Ledger</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Employee Ledger</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <a href="#" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/'); ?>','<?php echo '/Admin/Employee/';?>')"  class="btn btn-default">Employee</a>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Ledger_employee')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Ledger_employee">
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

            <div class="col-xs-12">
                <?php if (isset($filter) && $filter == 1){ ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Ledger_employee')?>" method="get">
                            <div class="col-lg-2" >
                                <label>Employee name</label>
                                <select class="form-control select2 select2-hidden-accessible" name="employee_id" onchange="formSubmit(this)" style=" width: 100%;" required >
                                    <option selected="selected" value="">Please Select</option>
                                    <?php echo getAllListInOption($employee_id, 'employee_id', 'name', 'employee'); ?>
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
                                <a href="<?= base_url('Admin/Ledger_employee') ?>" class="btn btn-default btn-block"><i
                                            class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <!-- /.box-body -->
                </div>
                <?php } ?>
                <!-- /.box -->
                <div class="box" >
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach ($result as $row) {
                                $particulars = ($row->particulars == NULL) ? "Transaction" : $row->particulars;
                                $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
                                $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
                                ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= $row->createdDtm ?></td>
                                    <td><?= $particulars ?></td>
                                    <td><?= $amountDr ?></td>
                                    <td><?= $amountCr ?></td>
                                    <td><?= $row->r_balance ?></td>
                                </tr>

                            <?php }?>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
