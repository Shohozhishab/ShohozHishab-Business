
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Return Purchase <small>Return Purchase List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Return Purchase</li>
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
                            <div class="col-lg-6">
                                <h3 class="box-title">Return Purchase Invoice List</h3>
                            </div>
                            <div class="col-lg-6">
                                <form method="POST" action="<?php echo site_url('Admin/Return_purchase/purchase_search') ?>" >
                                    <div class="col-lg-4 pull-right">
                                        <button style="margin-top: 25px;"  class="btn btn-primary " type="submit">search</button>
                                    </div>
                                    <div class="col-lg-8 pull-right">
                                        <label>Input PurchaseId</label>
                                        <input type="text" class="form-control" name="purchaseId" id="purchaseId"  required>
                                    </div>
                                </form>
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
                                <th>Purchase ID</th>
                                <th>Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($purchase_data as $purchase) { ?>
                                <tr>
                                    <td width="80px"><?php echo $purchase->purchase_id ?></td>
                                    <td><?php echo showWithCurrencySymbol($purchase->amount) ?></td>
                                    <td><?php echo ($purchase->due == 0) ? "<button class='btn btn-xs btn-success'>Paid</button>" : showWithCurrencySymbol($purchase->due); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/view/'.$purchase->purchase_id); ?>','<?php echo '/Admin/Purchase/view/'.$purchase->purchase_id; ?>')" class="btn btn-info btn-xs">View</a>
                                        <?php if ($return_status == 0){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Return_purchase_ajax/return/'.$purchase->purchase_id); ?>','<?php echo '/Admin/Return_purchase/return/'.$purchase->purchase_id; ?>')" class="btn btn-warning btn-xs"><i class="fa fa-pencil-square-o"></i> Return</a>
                                        <?php }else{ ?>
                                        <button class="btn btn-success btn-xs" >Already returned</button>
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
