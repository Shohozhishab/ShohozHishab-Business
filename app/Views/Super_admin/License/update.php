<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> License <small>License Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">License</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">License Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6">
                                <form action="<?php echo $action; ?>" method="post">

                                    <div class="form-group">
                                        <label for="varchar">Shops</label>
                                        <select class="form-control" name="sch_id" required >
                                            <option value="">Please Select</option>
                                            <?php echo getListInOptionCheckLicens2($license->sch_id,'sch_id','name','shops' );?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo $license->start_date;?>" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">End Date</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo $license->end_date;?>" required />
                                    </div>

                                    <input type="hidden" name="lic_id" id="lic_id" value="<?php echo $license->lic_id;?>" required />
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="<?php echo site_url('Super_admin/License') ?>" class="btn btn-default">Cancel</a>
                                </form>
                            </div>
                            <div class="col-lg-6"></div>
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