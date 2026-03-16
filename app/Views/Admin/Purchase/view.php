<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase  <small>Purchase View</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax'); ?>','<?php echo '/Admin/Purchase';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
            </div>
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3 class="box-title">Purchase Detail</h3>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="box-title pull-right">Supplier : <?php echo get_data_by_id('name','suppliers','supplier_id',$purchase->supplier_id); ?></h3><br>
                                <small class="pull-right">Balance : <?php echo showWithCurrencySymbol(get_data_by_id('balance','suppliers','supplier_id',$purchase->supplier_id)); ?></small><br>
                                <p class=" pull-right">Created By: <?php echo get_data_by_id('name','users','user_id',$purchase->createdBy);?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="box">
                    <table class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Quantity </th>
                        <th>Purchase Price</th>
                    </tr>
                    </thead>
                    <tbody><?php $start= 0;
                    foreach ($purchaseItame as $purchase){ ?>
                        <tr>
                            <td width="80px"><?php echo ++$start ?></td>
                            <td><?php echo get_data_by_id('name','products','prod_id',$purchase->prod_id); ?></td>
                            <td><?php echo $purchase->quantity; ?></td>
                            <td><?php echo showWithCurrencySymbol($purchase->purchase_price); ?></td>
                        </tr>
                    <?php  } ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Quantity </th>
                        <th>Purchase Price</th>
                    </tr>
                    </tfoot>

                </table>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Payment Detail</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-12" style="padding: 10px; border:1px dashed #D0D3D8;">
                            <div class="col-xs-6">
                                <label>Total</label>
                            </div>
                            <div class="col-xs-6" style="border-left: 1px dashed #D0D3D8;">
                                <p><?php echo showWithCurrencySymbol(get_data_by_id('amount','purchase','purchase_id', $purchaseId)); ?></p>
                            </div>
                        </div>

                        <?php $cash = get_data_by_id('nagad_paid','purchase','purchase_id', $purchaseId); if (!empty($cash)) { ?>
                            <div class="col-xs-12" style="padding: 10px; border:1px dashed #D0D3D8;">
                                <div class="col-xs-6">
                                    <label>Cash Pay</label>
                                </div>
                                <div class="col-xs-6" style="border-left: 1px dashed #D0D3D8;">
                                    <p><?php echo showWithCurrencySymbol($cash); ?></p>
                                </div>
                            </div>

                        <?php } ?>

                        <?php
                        $bankAmount = get_data_by_id('bank_paid','purchase','purchase_id', $purchaseId);
                        if (!empty($bankAmount)) { ?>
                            <div class="col-xs-12" style="padding: 10px; border:1px dashed #D0D3D8;">
                                <div class="col-xs-6">
                                    <label>Bank Pay</label>
                                </div>
                                <div class="col-xs-6" style="border-left: 1px dashed #D0D3D8;">
                                    <p><?php echo showWithCurrencySymbol($bankAmount); ?></p>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        $due = get_data_by_id('due','purchase','purchase_id', $purchaseId);
                        if (!empty($due)) { ?>
                            <div class="col-xs-12" style="padding: 10px; border:1px dashed #D0D3D8;">
                                <div class="col-xs-6">
                                    <label>Total Due</label>
                                </div>
                                <div class="col-xs-6" style="border-left: 1px dashed #D0D3D8;">
                                    <p><?php echo showWithCurrencySymbol($due); ?></p>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
