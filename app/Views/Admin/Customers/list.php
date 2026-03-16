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
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax/create/'); ?>','<?php echo '/Admin/Customers/create/';?>')"  class="btn btn-default">Register</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax') ?>','/Admin/Customer_type')" class="btn btn-default">Customer type</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Customer List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Customers_ajax/create/'); ?>','<?php echo '/Admin/Customers/create/'; ?>')"
                                   class="btn btn-block btn-primary">Register</a>
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
                                    <th>FatherName</th>
                                    <th>MotherName</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>PresentAddress</th>
                                    <th>Age</th>
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
                                    <td><?php echo $val->customer_name; ?></td>
                                    <td><?php echo $val->father_name; ?></td>
                                    <td><?php echo $val->mother_name; ?></td>
                                    <td><?php echo $val->mobile; ?></td>
                                    <td><?php echo $val->address; ?></td>
                                    <td><?php echo $val->present_address; ?></td>
                                    <td><?php echo $val->age; ?></td>
                                    <td>
                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Customers_ajax/transaction/' . $val->customer_id); ?>','<?php echo '/Admin/Customers/transaction/' . $val->customer_id; ?>')"
                                           class="btn btn-primary btn-xs">Transaction</a>

                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Customers_ajax/update/' . $val->customer_id); ?>','<?php echo '/Admin/Customers/update/' . $val->customer_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Customers/delete/' . $val->customer_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
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
