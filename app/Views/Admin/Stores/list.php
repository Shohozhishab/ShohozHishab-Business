
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Stores <small>Stores List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stores</li>
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
                                <h3 class="box-title">Stores List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Stores_ajax/create/'); ?>','<?php echo '/Admin/Stores/create/'; ?>')" class="btn btn-block btn-primary">Add</a>
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
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($stores as $stores) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo $stores->name ?></td>
                                    <td><?php echo $stores->description ?></td>
                                    <td>
                                        <?php if (is_default($stores->store_id,'store_id', 'stores') != 1) {?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Stores_ajax/update/'.$stores->store_id); ?>','<?php echo '/Admin/Stores/update/'.$stores->store_id; ?>')" class="btn btn-warning btn-xs">Update</a>
                                        <?php } ?>
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
