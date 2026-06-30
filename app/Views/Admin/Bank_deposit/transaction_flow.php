<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Bank Deposit <small> Bank Deposit Transaction Flow</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_deposit_ajax'); ?>','<?php echo '/Admin/Bank_deposit'?>')" class="btn btn-danger btn-sm" style="">Back list</a>
            </div>

            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Table</th>
                                    <th>transaction Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($flow as $val){ ?>
                                <tr>
                                    <td><?= $i++;?></td>
                                    <td><?= tableNameArrayKeyByValue($val->table_name);?></td>
                                    <td><?= $val->trangaction_type;?></td>
                                    <td>
                                        <?= showWithCurrencySymbol(tableNameOrIdByAmount($val->table_name,$val->ledger_id));?>
                                    </td>
                                </tr>
                            <?php }  ?>
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
    function saleEdit(salesId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Sales/salesEdit') ?>",
            data: {id: salesId},
            success: function (data) {
                $('#formData').html(data);
            }
        });
    }
</script>