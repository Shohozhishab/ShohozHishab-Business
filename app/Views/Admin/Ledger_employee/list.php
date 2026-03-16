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
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Employee_ajax/'); ?>','<?php echo '/Admin/Employee/';?>')"  class="btn btn-default">Employee</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Employee Ledger</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12 ">

                                    <div class="col-lg-3" style="padding: 20px;">
                                        <label>Employee name</label>
                                        <select class="form-control select2 select2-hidden-accessible"
                                                onchange="employeeLedg(this.value)" id="employeeId" style=" width: 100%;"
                                                tabindex="-1" aria-hidden="true">
                                            <option selected="selected" value="">Please Select</option>
                                            <?php echo getAllListInOption('', 'employee_id', 'name', 'employee'); ?>
                                        </select>

                                    </div>
                                    <div class="col-lg-3" style="padding: 17px;">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" name="st_date" id="st_date" required>
                                    </div>
                                    <div class="col-lg-3" style="padding: 17px;">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" name="en_date" id="en_date" required>
                                    </div>
                                    <div class="col-lg-3" style="padding: 13px;">
                                        <button style="margin-top: 28px;" onclick="emplLedSerc()" class="btn btn-primary "
                                                type="submit">Filter
                                        </button>
                                    </div>


                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="margin-top: 20px;" >
                        <div id="ledgPrint">
                            <div class="col-xs-12" id="printHead" style="margin-bottom: 20px; display: none;">
                                <div class="col-xs-6">
                                    <?php if (logo_image() == NULL) { ?>
                                        <img src="<?php echo base_url() ?>uploads/schools/no_image_logo.jpg" alt="User Image">
                                    <?php } else { ?>
                                        <img src="<?php echo base_url(); ?>uploads/schools/<?php echo logo_image(); ?>" class=""
                                             alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6 text-capitalize ">
                                    <div style="float: right;  text-align: right; border-right: 3px solid #decf77;padding: 5px;">
                                        Name: <span id="prName"></span><br>
                                        Start: <span id="prStD">0000-00-00</span><br>
                                        End: <span id="prEtD">0000-00-00</span><br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div id="ledger_employee"></div>
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
