<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Role <small>Role Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Role</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Role_ajax/') ?>','/Admin/Role/')" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
                <a href="#" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/';?>')"  class="btn btn-default">Users</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Role Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-12">
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">

                                    <div class="form-group col-md-6">
                                        <label for="varchar">Role Name</label>
                                        <input type="text" class="form-control" name="role" id="role" placeholder="Role" >
                                    </div>
                                    <div class="form-group col-md-12 text-capitalize">
                                        <label for="varchar">Permission</label> <button type="button" class="btn btn-xs btn-primary select-all-btn2" onclick="selectAll(this)" >Select All</button>
                                        <ol>
                                            <?php foreach ($permission as $key => $value) { ?>
                                                <li><b><?php echo str_replace('_', ' ', $key); ?></b><br><button type="button" class="btn btn-xs btn-success select-all-btn" onclick="checkedFunction('<?php print $key; ?>', this)">Checked</button>
                                                    <div style="display: flex; column-gap: 15px;flex-wrap: wrap;" >
                                                    <?php foreach ($value as $k=>$v) {
                                                        $isChecked = ($v == 1) ? '' : '';
                                                        $mName = str_replace('_', ' ', $k);
                                                        $displayName = str_replace(['update', 'read', 'mod'], ['edit', 'view', 'Module'], $mName);
                                                    ?>
                                                        <div class="checkbox text-capitalize" style="margin: 5px;">
                                                            <label>
                                                                <input type="checkbox" <?php echo $isChecked; ?> name="permission[<?php print $key; ?>][<?php print $k; ?>]" value="1" ><?= $displayName; ?></label>
                                                        </div>

                                                    <?php } ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ol>
                                    </div>

                                    <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Role_ajax/'); ?>','<?php echo '/Admin/Role/'; ?>')" class="btn btn-default">Cancel</a>
                                    </div>
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