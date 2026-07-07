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
                                        <select class="form-control" onchange="rolePermission(this.value,'Purchase')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Purchase">
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
            <?php if (isset($filter) && $filter == 1){ ?>
            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Purchase') ?>" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" required>
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Purchase') ?>" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if (isset($create) && $create == 1){ ?>
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="javascript:void(0)"
                   onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/create/'); ?>','<?php echo '/Admin/Purchase/create/'; ?>')"
                   class="btn btn-success"><i class="fa fa-plus"></i> Add Purchase</a>
            </div>
            <?php } ?>
            <div class="col-xs-12">

                <div class="box">

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>

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
                                        <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/transaction_flow/' . $purchase->purchase_id); ?>','<?php echo '/Admin/Purchase/transaction_flow/' . $purchase->purchase_id; ?>')"
                                           class="btn btn-success btn-xs">Transaction Flow </a>
                                        <?php } ?>
                                        <?php if (isset($read) && $read == 1){ ?>
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/view/' . $purchase->purchase_id); ?>','<?php echo '/Admin/Purchase/view/' . $purchase->purchase_id; ?>')"
                                           class="btn btn-primary btn-xs">View</a>
                                        <?php } ?>
                                        <?php if (isset($update) && $update == 1){ ?>
                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning"  onclick="purchaseEdit('<?= $purchase->purchase_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
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
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','purchase')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <?php } ?>
                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','purchase')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
                                        <th>Purchase Date</th>
                                        <th>Supplier </th>
                                        <th>Total Amount</th>
                                        <th>Due</th>
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