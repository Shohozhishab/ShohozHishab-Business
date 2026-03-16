<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Employee <small>Employee Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Employee </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/'); ?>','<?php echo '/Admin/Employee/';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Ledger_employee_ajax') ?>','/Admin/Ledger_employee')" class="btn btn-default">Salary</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Employee Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $employee_data->name;?>" >
                                        <input type="hidden"  name="employee_id" id="employee_id" value="<?php echo $employee_data->employee_id;?>" >
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Salary</label>
                                        <input type="number" class="form-control" name="salary" id="salary" placeholder="Salary " value="<?php echo $employee_data->salary;?>" >
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Age </label>
                                        <input type="text" class="form-control" name="age" id="age" placeholder="Age " value="<?php echo $employee_data->age;?>" >
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Status </label>
                                        <select class="form-control" name="status" >
                                            <?php echo globalStatus($employee_data->status)?>
                                        </select>
                                    </div>


                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/'); ?>','<?php echo '/Admin/Employee/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;"></div>
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