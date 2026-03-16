<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Shops <small>Shops List</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Shops</li>
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
                                <h3 class="box-title">Shop List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="<?php echo base_url('Super_admin/Shops/create')?>" class="btn btn-block btn-primary">Add</a>
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
                                <th>Balance</th>
                                <th>Logo</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($shops as $val){
                                $logoimg =  no_image_view('/uploads/schools/'.$val->logo,'/uploads/schools/no_image_logo.jpg',$val->logo);
                                $profiimg =  no_image_view('/uploads/schools/'.$val->image,'/uploads/schools/no_image.jpg',$val->image);
                            ?>
                            <tr>
                                <td><?php echo $i++?></td>
                                <td><?php echo $val->name;?></td>
                                <td><?php echo showWithCurrencySymbolSuper($val->sch_id,$val->cash);?></td>
                                <td><img src="<?php echo $logoimg;?>" width="100"></td>
                                <td><img src="<?php echo $profiimg; ?>" width="100"></td>
                                <td>
                                    <a href="<?php echo base_url('Super_admin/Shops/resetdb/'.$val->sch_id)?>" class="btn btn-primary btn-xs">Reset Shop</a>
                                    <a href="<?php echo base_url('Super_admin/Shops/update/'.$val->sch_id)?>" class="btn btn-warning btn-xs">Update</a>
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