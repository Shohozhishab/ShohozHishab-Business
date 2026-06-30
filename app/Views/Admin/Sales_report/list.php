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
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <?php echo $menu;?>
        </div>
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'Sales_report')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Sales_report">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <div class="col-lg-8">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Sale Report List</h3>
                                <span class="pull-right"><b>Total Profit: <?php echo showWithCurrencySymbol($saleprofit)?></b></span>
                            </div>
                            <?php if (isset($filter) && $filter == 1){ ?>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <form  action="<?php echo site_url('Admin/Sales_report/search'); ?>" method="post">
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
                            <?php } ?>
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
                            <?php if (isset($print) && $print == 1){ ?>
                            <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                            <?php } ?>
                            <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                            <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','salesReport')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                            <?php } ?>
                            <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                            <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','salesReport')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-lg-4">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Customer Sale Report List</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Customer All Paid Amount</th>
                            </tr>
                            </thead>
                            <tbody><?php $start='';
                            foreach ($customers as $customers)
                            {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo ++$start ?></td>
                                    <td><?php echo $customers->customer_name ?></td>
                                    <td><?php echo CustomerTotalSaleAmount($customers->customer_id) ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>

                        </table>
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
