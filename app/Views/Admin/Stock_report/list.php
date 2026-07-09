<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Stock Report <small>Stock Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stock Report</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Stock_report')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Stock_report">
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

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Stock Report</h3>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-4" style="padding: 20px;">
                            <form method="get" action="<?= base_url('Admin/Stock_report')?>" >
                            <label for="int">Store name</label>
                            <select class="form-control select2 select2-hidden-accessible" onchange="formSubmit(this)" name="store_id" id="store_id" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected"  value="">Please Select</option>
                                <?php echo getAllListInOption($store_id,'store_id','name','stores'); ?>
                            </select>
                            </form>

                        </div>
                        <div class="col-md-8"></div>

                    </div>
                    <!-- /.box-body -->
                </div>


            </div>

            <div class="col-md-12" >

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title" >Store Name: <b ><?= $name ?></b></h3>
                        <span class="pull-right" style="margin-right:10px;" ><b>Storage Inventory Prices:</b> <?= showWithCurrencySymbol($purchasePrice) ?></span>
                        <span class="pull-right" style="margin-right:40px;"> <b>Storage Inventory Quantity:</b> <?= $quantity ?></span>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Product Category</th>
                                    <th>Quantity</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i = ''; foreach ($result as $row) { ?>
                                <tr>
                                    <td><?= ++$i ?></td>
                                    <td><?= $row->name ?></td>
                                    <td><?= get_data_by_id('product_category', 'product_category', 'prod_cat_id', $row->prod_cat_id) ?></td>
                                    <td><?= $row->quantity ?></td>
                                    <td><?= showWithCurrencySymbol($row->purchase_price) ?></td>
                                    <td><?= showWithCurrencySymbol($row->selling_price) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row no-print" >
                <div class="col-xs-12">
                    <?php if (isset($print) && $print == 1){ ?>
                    <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                    <?php } ?>
                    <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','stockReport')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                    <?php } ?>
                    <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','stockReport')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                    <?php } ?>
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
                    <table class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Product Category</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = ''; foreach ($result as $row) { ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->name ?></td>
                                <td><?= get_data_by_id('product_category', 'product_category', 'prod_cat_id', $row->prod_cat_id) ?></td>
                                <td><?= $row->quantity ?></td>
                                <td><?= showWithCurrencySymbol($row->purchase_price) ?></td>
                                <td><?= showWithCurrencySymbol($row->selling_price) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
