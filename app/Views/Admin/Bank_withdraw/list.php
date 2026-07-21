<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Bank Withdraw <small>Bank Withdraw List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bank Withdraw</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'Bank_withdraw')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="Bank_withdraw">
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
                                <h3 class="box-title">Bank Withdraw List</h3>
                            </div>
                            <div class="col-lg-3">
                                <?php if (isset($create) && $create == 1){ ?>
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Bank_withdraw_ajax/create/'); ?>','<?php echo '/Admin/Bank_withdraw/create/'; ?>')"
                                   class="btn btn-block btn-primary">Withdraw</a>
                                <?php } ?>
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;" id="messageAcc">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table id="example1" class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bank Name</th>
                                        <th>Account No</th>
                                        <th>Amount</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $start = 1;
                                    foreach ($bank_withdraw as $val) { ?>
                                        <tr>
                                            <td width="80px"><?php echo $start++ ?></td>
                                            <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $val->bank_id) ?></td>
                                            <td><?php echo get_data_by_id('account_no', 'bank', 'bank_id', $val->bank_id) ?></td>
                                            <td><?php echo showWithCurrencySymbol($val->amount) ?></td>
                                            <td><?php echo $val->commont ?></td>
                                            <td>
                                                <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                                <a href="javascript:void(0)"
                                                   onclick="showData('<?php echo site_url('/Admin/Bank_withdraw_ajax/transaction_flow/' . $val->wthd_id); ?>','<?php echo '/Admin/Bank_withdraw/transaction_flow/' . $val->wthd_id; ?>')"
                                                   class="btn btn-success btn-xs">Transaction Flow </a>
                                                <?php } ?>
                                                <?php if (isset($update) && $update == 1){ ?>
                                                    <a href="javascript:void(0)" class="btn btn-xs btn-warning"  onclick="withdrawEdit('<?= $val->wthd_id;?>')" data-toggle="modal" data-target="#modal-default">Edit</a>
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
                                        <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('ledgPrint','bankWithdraw')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                        <?php } ?>
                                        <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('ledgPrint','bankWithdraw')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
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
                                                <th>Bank Name</th>
                                                <th>Account No</th>
                                                <th>Amount</th>
                                                <th>Comment</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $start = 1;
                                            foreach ($bank_withdraw as $item) { ?>
                                                <tr>
                                                    <td width="80px"><?php echo $start++ ?></td>
                                                    <td><?php echo get_data_by_id('name', 'bank', 'bank_id', $item->bank_id) ?></td>
                                                    <td><?php echo get_data_by_id('account_no', 'bank', 'bank_id', $item->bank_id) ?></td>
                                                    <td><?php echo showWithCurrencySymbol($item->amount) ?></td>
                                                    <td><?php echo $item->commont ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered table-striped" id="bankData">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bank Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <?php $i=1; foreach ($bank as $val){?>
                                        <tr>
                                            <td><?php echo $i++?></td>
                                            <td><?php echo $val->name?></td>
                                            <td><?php echo showWithCurrencySymbol($val->balance)?></td>
                                        </tr>
                                    <?php } ?>
                                    <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Bank Name</th>
                                        <th>Amount</th>
                                    </tr>
                                    </tfoot>
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
    <div class="modal-dialog">
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
    function withdrawEdit(wthdId){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Bank_withdraw/withdrawDataEdit') ?>",
            data: {id: wthdId},
            success: function(data){
                $('#formData').html(data);
            }
        });
    }
</script>
