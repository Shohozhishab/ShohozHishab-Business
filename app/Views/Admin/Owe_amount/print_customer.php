<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Accounts Payable  <small>Accounts Payable List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Accounts Payable</li>
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
                        <div class="col-xs-12" style="margin-bottom: 20px;   ">
                            <div class="col-xs-6">
                                <?php if(logo_image() == NULL){ ?>
                                    <img src="<?php echo base_url() ?>/uploads/schools/no_image_logo.jpg" alt="User Image" >
                                <?php }else{ ?>
                                    <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                <?php } ?>
                            </div>
                            <div class="col-xs-6">
                                <?php print address(); ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="">

                                <div class="box-body">
                                    <span style="float:right; " >Total: <?php echo showWithCurrencySymbol($customer); ?></span>
                                    <h4>Customer</h4>
                                    <table class="table table-bordered table-striped" id="TFtable">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; foreach ($customerData as $row) {
                                            if ($row->balance < 0) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $i++ ?></td>
                                                    <td><?php echo $row->customer_name ?></td>
                                                    <td><?php echo showWithCurrencySymbol($row->balance) ?></td>
                                                </tr>
                                            <?php } }?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <div class="print_line btn btn-primary pull-right no-print" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>


                                </div>
                            </div>
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
