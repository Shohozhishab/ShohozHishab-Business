<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Sale Report <small>Sale Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sale Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-lg-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Sale Report List</h3>
                                <span class="pull-right"><b>Total Profit: <?php echo showWithCurrencySymbol($saleprofit)?></b></span>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <form action="<?php echo site_url('Admin/Sales_report/search'); ?>" method="post">
                                    <div class="input-group col-xs-12" style="padding: 20px;">
                                        <div class="col-xs-5">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="st_date" id="date"  required>
                                        </div>
                                        <div class="col-xs-5">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="en_date" id="date"  required>
                                        </div>
                                        <div class="col-xs-2" style="margin-top: 23px">
                                            <button  class="btn btn-primary geniusSubmit-btn" type="submit">Filter</button>
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
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Discount</th>
                                <th>Final Price</th>
                                <th>Profit</th>
                            </tr>
                            </thead>
                            <tbody><?php $i='';
                            foreach ($sale as $sale)
                            {
                                ?>
                                <tr>
                                    <td width="15px"><?php echo ++$i ?></td>
                                    <td><?php echo get_data_by_id('name','products','prod_id',$sale->prod_id)?></td>
                                    <td><?php echo showWithCurrencySymbol($sale->price) ?></td>
                                    <td><?php echo $sale->quantity ?></td>
                                    <td><?php echo showWithCurrencySymbol($sale->total_price) ?></td>
                                    <td><?php echo $sale->discount ?></td>
                                    <td><?php echo showWithCurrencySymbol($sale->final_price) ?></td>
                                    <td><?php echo showWithCurrencySymbol($sale->profit) ?></td>
                                </tr>
                                <?php
                            }
                            ?>
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
                    <table class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Discount</th>
                            <th>Final Price</th>
                            <th>Profit</th>
                        </tr>
                        </thead>
                        <tbody><?php
                        foreach ($sale2 as $row)
                        {
                            ?>
                            <tr>
                                <td><?php echo get_data_by_id('name','products','prod_id',$row->prod_id)?></td>
                                <td><?php echo showWithCurrencySymbol($row->price) ?></td>
                                <td><?php echo $row->quantity ?></td>
                                <td><?php echo showWithCurrencySymbol($row->total_price) ?></td>
                                <td><?php echo $row->discount ?></td>
                                <td><?php echo showWithCurrencySymbol($row->final_price) ?></td>
                                <td><?php echo showWithCurrencySymbol($row->profit) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>

                    </table>
                </div>
            </div>


        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
