<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase Report <small>Purchase Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-lg-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Purchase Report</h3>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <form action="<?php echo site_url('Admin/Purchase_report/search'); ?>" method="post">
                                    <div class="input-group col-xs-12" style="padding: 20px;">
                                        <div class="col-xs-5">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="st_date" id="date"  required>
                                        </div>
                                        <div class="col-xs-5">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="en_date" id="date"  required>
                                        </div>
                                        <div class="col-xs-2" style="margin-top: 30px">
                                            <button  class="btn btn-primary " type="submit">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                            </tr>
                            </thead>
                            <tbody><?php $i='';
                            foreach ($purchase as $value)
                            { foreach ($value as  $row) {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo ++$i ?></td>
                                    <td><?php echo get_data_by_id('name','products','prod_id',$row->prod_id) ?></td>
                                    <td><?php echo $row->quantity ?></td>
                                    <td><?php echo showWithCurrencySymbol($row->purchase_price) ?></td>
                                </tr>
                            <?php } } ?>
                            </tbody>

                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="row no-print" >
                        <div class="col-xs-12" style="margin-bottom: 20px;">
                            <button onclick="printDiv('ledgPrint')"    class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                        </div>
                    </div>
                </div>


            </div>


            <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
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

                <div class="col-xs-12" >
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                        </tr>
                        </thead>
                        <tbody><?php $i='';
                        foreach ($purchase as $value)
                        { foreach ($value as  $row) {
                            ?>
                            <tr>
                                <td width="80px"><?php echo ++$i ?></td>
                                <td><?php echo get_data_by_id('name','products','prod_id',$row->prod_id) ?></td>
                                <td><?php echo $row->quantity ?></td>
                                <td><?php echo showWithCurrencySymbol($row->purchase_price) ?></td>
                            </tr>
                        <?php } } ?>
                        </tbody>

                </div>
            </div>


        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
