
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Return Sales <small>Return Sales List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Return Sales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>

            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="box-title">Return Sales List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="post" action="<?php echo site_url('Admin/Return_sale/invoice_search') ?>"  >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;" class="btn btn-primary " type="submit">Search</button>
                                    </div>
                                    <div class="col-lg-8 pull-right">
                                        <label>Input Invoice ID</label>
                                        <input type="text" class="form-control" name="invoiceId" id="invoiceId"  required>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>



                        <div class="col-lg-12">
                            <form action="<?= base_url('Admin/Return_sale')?>" method="get">
                                <div class="col-lg-3 ">
                                    <label>Customer</label><br>
                                    <select class="form-control select2" name="customer" >
                                        <option value="">Please Select</option>
                                        <?= getAllListInOptionWithStatus( $customerId, 'customer_id', 'customer_name', 'customers', 'customer_name' );?>
                                    </select>
                                </div>
                                <div class="col-xs-3" >
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>" id="st_date" >
                                </div>
                                <div class="col-xs-3" >
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>" id="en_date" >
                                </div>
                                <div class="col-lg-1 ">
                                    <button style="margin-top: 25px;" class="btn btn-primary" type="submit"> <i class="fa fa-search"></i> Filter </button>
                                </div>
                                <div class="col-lg-2 ">
                                    <a href="<?= base_url('Admin/Return_sale') ?>" style="margin-top: 25px;" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-12" style="margin-top: 30px"></div>



                        <table id="example1" class="table table-bordered table-striped text-capitalize" style="margin-top: 20px;">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($return_sale_data as $return) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($return->createdDtm) ?></td>
                                    <td><?php
                                        if ($return->customer_id) {
                                            echo get_data_by_id('customer_name', 'customers', 'customer_id', $return->customer_id) ;
                                        }else{
                                            echo $return->customer_name;
                                        }

                                        ?></td>
                                    <td><?php echo showWithCurrencySymbol($return->amount) ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Return_sale_ajax/view/'.$return->rtn_sale_id); ?>','<?php echo '/Admin/Return_sale/view/'.$return->rtn_sale_id; ?>')" class="btn btn-warning btn-xs">View</a>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','returnSale')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','returnSale')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            </div>
                        </div>

                        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; " >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if(logo_image() == NULL){ ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                                    <?php }else{ ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12" >
                                <table class="table table-bordered table-striped text-capitalize" >
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; foreach ($return_sale_data as $return) { ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo invoiceDateFormat($return->createdDtm) ?></td>
                                            <td><?php
                                                if ($return->customer_id) {
                                                    echo get_data_by_id('customer_name', 'customers', 'customer_id', $return->customer_id) ;
                                                }else{
                                                    echo $return->customer_name;
                                                }

                                                ?></td>
                                            <td><?php echo showWithCurrencySymbol($return->amount) ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
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
