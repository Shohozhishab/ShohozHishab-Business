<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Suppliers  <small>Suppliers List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Suppliers</li>
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
                                <h3 class="box-title">Suppliers List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/create/'); ?>','<?php echo '/Admin/Suppliers/create/'; ?>')"
                                   class="btn btn-block btn-primary">Add</a>
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
                                <th>Supplier Id</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start = 1;
                            foreach ($supplier as $suppliers) {
                                $isDeletable = is_deletable('ledger_suppliers','supplier_id',$suppliers->supplier_id);
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $suppliers->supplier_id ?></td>
                                    <td><?php echo $suppliers->name ?></td>
                                    <td><?php echo $suppliers->address ?></td>
                                    <td><?php echo showWithPhoneNummberCountryCode($suppliers->phone) ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/transaction/' . $suppliers->supplier_id); ?>','<?php echo '/Admin/Suppliers/transaction/' . $suppliers->supplier_id; ?>')"
                                           class="btn btn-primary btn-xs">Transaction</a>

                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/update/' . $suppliers->supplier_id); ?>','<?php echo '/Admin/Suppliers/update/' . $suppliers->supplier_id; ?>')"
                                           class="btn btn-warning btn-xs">Update</a>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Suppliers/delete/' . $suppliers->supplier_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
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
