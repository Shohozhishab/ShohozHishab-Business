<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> License <small>License List</small> </h1>
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
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">License List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="<?php echo base_url('Super_admin/License/create')?>" class="btn btn-block btn-primary">Add</a>
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
                                <th>Shop Name</th>
                                <th>License Key</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($license as $val){ ?>
                                <tr>
                                    <td><?php echo $i++?></td>
                                    <td><?php echo get_data_by_id('name','shops','sch_id',$val->sch_id) ?></td>
                                    <td><?php echo $val->lic_key;?></td>
                                    <td><?php echo $val->start_date;?></td>
                                    <td><?php echo $val->end_date;?></td>
                                    <td>
<!--                                        <a href="--><?php //echo base_url('Super_admin/License/License/'.$val->lic_id)?><!--" class="btn btn-primary btn-xs">Reset DB</a>-->
                                        <a href="<?php echo base_url('Super_admin/License/update/'.$val->lic_id)?>" class="btn btn-warning btn-xs">Update</a>
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