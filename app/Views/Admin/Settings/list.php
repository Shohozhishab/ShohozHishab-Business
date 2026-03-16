<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Settings  <small>Settings List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="box-title">Settings List</h3>
                            </div>
                            <div class="col-lg-6">
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Capital_ajax/'); ?>','<?php echo '/Admin/Capital/' ?>'),activeTab(this)"  class="btn btn-xs btn-success"><i class="fa fa-clock-o"></i> Capital</a>

                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Yearly_closing_ajax/'); ?>','<?php echo '/Admin/Yearly_closing/' ?>'),activeTab(this)"  class="btn btn-xs btn-info"><i class="fa fa-clock-o"></i> Yearly closing</a>
                                <?php $cheOpen = get_data_by_id('opening_status','shops','sch_id',newSession()->shopId); if ( $cheOpen == 0){ ?>
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Closing_ajax/'); ?>','<?php echo '/Admin/Closing/'; ?>'),activeTab(this)"  class="btn btn-xs btn-danger"><i class="fa fa-clock-o"></i> Starting business</a>
                                <?php } ?>
                                <a href="<?php echo site_url('/Admin/Settings/databaseBackup'); ?>"   class="btn btn-xs btn-success"><i class="fa fa-database"></i> Database Backup</a>


                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Logo</th>
                                <th>Profile Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td><?php echo $stores->name ?></td>
                                    <td><?php echo showWithPhoneNummberCountryCode($stores->mobile) ?></td>
                                    <td>
                                        <?php $logoimg =  no_image_view('/uploads/schools/'.$stores->logo,'/uploads/schools/no_image_logo.jpg',$stores->logo); ?>

                                        <img src="<?php echo $logoimg; ?>" width="40%">
                                    </td>

                                    <td>
                                        <?php $img =  no_image_view('/uploads/schools/'.$stores->image,'/uploads/schools/no_image.jpg',$stores->image); ?>
                                        <img src="<?php echo $img; ?>" width="40%">
                                    </td>
                                    <td><?php echo statusView($stores->status); ?></td>
                                    <td width="180px">
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/update/'.$stores->sch_id); ?>','<?php echo '/Admin/Settings/update/'.$stores->sch_id; ?>')"  class="btn btn-xs btn-info">Update</a>
                                    </td>
                                </tr>
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
