
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Brand <small>Brand List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Brand</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Brand List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Brand_ajax/create/'); ?>','<?php echo '/Admin/Brand/create/'; ?>')" class="btn btn-block btn-primary">Add</a>
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
                                <th>Brand</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($brand as $brand) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo $brand->name ?></td>
                                    <td>
                                        <?php if ($brand->image ) { ?>
                                            <img src="<?php print base_url(); ?>/uploads/brand_image/<?php echo $brand->image ; ?>" width="60" >
                                        <?php }else{ ?>
                                            <img src="<?php print base_url(); ?>/uploads/customer_image/no_image.jpg" width="60" >
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Brand_ajax/update/'.$brand->brand_id); ?>','<?php echo '/Admin/Brand/update/'.$brand->brand_id; ?>')" class="btn btn-warning btn-xs">Update</a>
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
