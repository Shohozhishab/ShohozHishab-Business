<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Package <small>Package Update</small> </h1>
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
                        <h3 class="box-title">Package Update</h3>
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
                                        <input type="text" class="form-control" name="package_name" value="<?php echo $package->package_name;?>" required>
                                        <input type="hidden" name="package_id" value="<?php echo $package->package_id;?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Permission</label>
                                        <ol>
                                            <?php
                                                $adPermission = json_decode($package->package_admin_permission);
                                            foreach ($permission as $key => $value) { ?>
                                                <li><?php echo $key; ?>
                                                    <?php foreach ($value as $k=>$v) {
                                                        if(isset($adPermission->$key->$k)) {
                                                            $isChecked = ($adPermission->$key->$k == 1) ? 'checked="checked"' : '';
                                                        }else{
                                                            $isChecked = '';
                                                        } ?>
                                                        <div class="checkbox">
                                                            <label> <input type="checkbox" <?php echo $isChecked; ?> name="package_admin_permission[<?php print $key; ?>][<?php print $k; ?>]" value="1" ><?php echo $k ?></label>
                                                        </div>

                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ol>
                                    </div>




                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="<?php echo site_url('Super_admin/Package') ?>" class="btn btn-default">Cancel</a>
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