<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Customer  <small>Customer List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Customer</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12" style="margin-bottom: 15px;">
            <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax/create/'); ?>','<?php echo '/Admin/Customers/create/';?>')"  class="btn btn-default">Register</a>

            <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax') ?>','/Admin/Customer_type')" class="btn btn-default">Customer type</a>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Customers')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Customers">
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
                                <h3 class="box-title">Customer List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Customers_ajax/create/'); ?>','<?php echo '/Admin/Customers/create/'; ?>')"
                                   class="btn btn-block btn-primary"><i class="fa fa-plus"></i> Create Customer</a>
                                <?php } ?>
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
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Balance</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1;
                            foreach ($customer as $val) {
                                $isDeletable = is_deletable('ledger','customer_id',$val->customer_id);
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo $val->customer_id; ?></td>
                                    <td><?php echo $val->customer_name; ?></td>
                                    <td><?php echo $val->mobile; ?></td>
                                    <td><?php echo showWithCurrencySymbol($val->balance); ?></td>
                                    <td><?php echo $val->address; ?></td>
                                    <td>
                                        <?php if (isset($return_list) && $return_list == 1){ ?>
                                        <a href="<?= site_url('/Admin/Return_sale?customer=' . $val->customer_id)?>" class="btn btn-warning btn-xs">Return List</a>
                                        <?php } ?>
                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Customers_ajax/transaction/' . $val->customer_id); ?>','<?php echo '/Admin/Customers/transaction/' . $val->customer_id; ?>')"
                                           class="btn btn-primary btn-xs">Transaction</a>
                                        <?php if (isset($update) && $update == 1){ ?>
                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Customers_ajax/update/' . $val->customer_id); ?>','<?php echo '/Admin/Customers/update/' . $val->customer_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php } ?>
                                        <?php if (isset($delete) && $delete == 1){ ?>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Customers/delete/' . $val->customer_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
                                        <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print" >
                            <div class="col-xs-12">
                                <?php if (isset($print) && $print == 1){ ?>
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right" ><i class="fa fa-print "></i> Print Now</button>
                                <?php } ?>
                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','customer')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <?php } ?>
                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','customer')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Balance</th>
                                        <th>Address</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;
                                    foreach ($customer as $val) {
                                        $isDeletable = is_deletable('ledger','customer_id',$val->customer_id);
                                        ?>
                                        <tr>
                                            <td><?php echo $i++ ?></td>
                                            <td><?php echo $val->customer_name; ?></td>
                                            <td><?php echo $val->mobile; ?></td>
                                            <td><?php echo showWithCurrencySymbol($val->balance); ?></td>
                                            <td><?php echo $val->address; ?></td>
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
