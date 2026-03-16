<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Chaque  <small>Chaque List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Chaque</li>
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
                                <h3 class="box-title">Chaque List</h3>
                            </div>
                            <div class="col-lg-3">
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
                                <th>Date</th>
                                <th>Chaque Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($chaque as $chaque) { ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo invoiceDateFormat($chaque->createdDtm) ?></td>
                                    <td><?php echo $chaque->chaque_number ?></td>
                                    <td><?php echo $chaque->amount ?></td>
                                    <td><?php echo $chaque->status ?></td>
                                    <td width="180px">
                                        <?php if ($chaque->status != 'Approved') { ?>
                                        <a href="<?php echo site_url('/Admin/Chaque/paid/'.$chaque->chaque_id); ?>" onclick="javasciprt: return confirm('Are You Sure ?')"  class="btn btn-xs btn-warning">Approved</a>
                                        <?php }else{ ?>
                                            <button class='btn btn-xs btn-success'>Paid</button>
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
