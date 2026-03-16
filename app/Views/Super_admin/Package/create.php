<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Package <small>Package Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Package</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Package Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                            <div class="col-lg-6">
                                <form action="<?php echo $action; ?>" method="post">

                                    <div class="form-group">
                                        <label for="varchar">Package Name</label>
                                        <input type="text" class="form-control" name="package_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Permission</label>
                                        <ol>
                                            <?php foreach ($permission as $key => $value) { ?>
                                                <li><?php echo $key; ?>
                                                    <?php foreach ($value as $k=>$v) {
                                                        $isChecked = ($v == 1) ? 'checked="checked"' : ''; ?>
                                                        <div class="checkbox">
                                                            <label> <input type="checkbox" <?php echo $isChecked; ?> name="package_admin_permission[<?php print $key; ?>][<?php print $k; ?>]" value="1" ><?php echo $k ?></label>
                                                        </div>

                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ol>
                                    </div>




                                    <button type="submit" class="btn btn-primary">Create</button>
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