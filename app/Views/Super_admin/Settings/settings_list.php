<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Profile <small>Profile List</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Profile</li>
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Profile List</h3>
                            </div>
                            <div class="col-lg-3">
<!--                                <a href="--><?php //echo base_url('Super_admin/Settings/create')?><!--" class="btn btn-block btn-primary">Add</a>-->
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
                                <th>No</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Country</th>
                                <th>Phone</th>
                                <th>Photo</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($settings as $val){ ?>
                                <tr>
                                    <td><?php echo $i++?></td>
                                    <td><?php echo $val->name;?></td>
                                    <td><?php echo $val->ComName;?></td>
                                    <td><?php echo $val->country;?></td>
                                    <td><?php echo $val->mobile;?></td>
                                    <td>
                                        <?php $progenimg =  no_image_view('/uploads/admin_image/'.$val->pic,'/uploads/admin_image/no_image.jpg',$val->pic); ?>
                                        <img src="<?php echo $progenimg ?>" width="50" class="img-circle">

                                    </td>
                                    <td>
                                        <!--                                        <a href="--><?php //echo base_url('Super_admin/License/License/'.$val->lic_id)?><!--" class="btn btn-primary btn-xs">Reset DB</a>-->
                                        <a href="<?php echo base_url('Super_admin/Settings/update/'.$val->user_id)?>" class="btn btn-warning btn-xs">Update</a>
                                    </td>
                                </tr>
                            <?php }?>

                            </tfoot>
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