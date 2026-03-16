<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Employee  <small>Employee List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/create/'); ?>','<?php echo '/Admin/Employee/create/';?>')"  class="btn btn-default">Register</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Ledger_employee_ajax') ?>','/Admin/Ledger_employee')" class="btn btn-default">Salary</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Employee List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Employee_ajax/create/'); ?>','<?php echo '/Admin/Employee/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Salary</th>
                                <th>Age</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start =1; foreach ($employee_data as $employee) { ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $employee->name ?></td>
                                    <td><?php echo showWithCurrencySymbol($employee->salary) ?></td>
                                    <td><?php echo $employee->age ?></td>
                                    <td width="180px">

                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/update/'.$employee->employee_id); ?>','<?php echo '/Admin/Employee/update/'.$employee->employee_id; ?>')"  class="btn btn-xs btn-info">Update</a>


                                    </td>
                                </tr>
                                <?php } ?>

                            </tbody>
                        </table>
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
