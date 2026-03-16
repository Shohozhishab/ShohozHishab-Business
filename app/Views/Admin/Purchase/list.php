<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase  <small>Purchase List</small></h1>
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
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/create/'); ?>','<?php echo '/Admin/Purchase/create/';?>')"  class="btn btn-default">purchase</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Purchase List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/create/'); ?>','<?php echo '/Admin/Purchase/create/'; ?>')"
                                   class="btn btn-block btn-primary">Purchase</a>
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
                                <th>Purchase Date</th>
                                <th>Supplier </th>
                                <th>Total Amount</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1;
                            foreach ($purchase_data as $purchase) { ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($purchase->createdDtm) ?></td>
                                    <td><?php echo get_data_by_id('name', 'suppliers', 'supplier_id', $purchase->supplier_id); ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('amount','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('due','purchase','purchase_id',$purchase->purchase_id)); ?></td>
                                    <td>

                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/view/' . $purchase->purchase_id); ?>','<?php echo '/Admin/Purchase/view/' . $purchase->purchase_id; ?>')"
                                           class="btn btn-primary btn-xs">View</a>
                                        <?php if(edit_expire_check($purchase->createdDtm) == true){ ?>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-warning"  onclick="purchaseEdit('<?= $purchase->purchase_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
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
<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Data</h4>
            </div>
            <div class="modal-body" id="formData">


            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    function purchaseEdit(purchaseId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Purchase/purchaseEdit') ?>",
            data: {id: purchaseId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }
</script>