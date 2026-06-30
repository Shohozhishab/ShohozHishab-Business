<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small> Transaction Flow</small></h1>
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
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax'); ?>','<?php echo '/Admin/Transaction'?>')" class="btn btn-danger btn-sm" style="">Back list</a>
            </div>
            <?php
                $userName = '';
                if ($transaction->customer_id != NULL){
                    $customerName = get_data_by_id('customer_name', 'customers', 'customer_id', $transaction->customer_id);
                    $userName ='<h4><b>Customer:</b> '.$customerName.'</h4>';
                }elseif ($transaction->supplier_id != NULL){
                    $name = get_data_by_id('name', 'suppliers', 'supplier_id', $transaction->supplier_id);
                    $userName ='<h4><b>Suppliers:</b> '.$name.'</h4>';
                }elseif ($transaction->loan_pro_id != NULL){
                    $name = get_data_by_id('name', 'loan_provider', 'loan_pro_id', $transaction->loan_pro_id);
                    $userName ='<h4><b>Account Head:</b> '.$name.'</h4>';
                }elseif ($transaction->bank_to_id != NULL){
                    $name = get_data_by_id('name', 'bank', 'bank_id', $transaction->bank_id);
                    $acc = get_data_by_id('account_no', 'bank', 'bank_id', $transaction->bank_id);
                    $name2 = get_data_by_id('name', 'bank', 'bank_id', $transaction->bank_to_id);
                    $acc2 = get_data_by_id('account_no', 'bank', 'bank_id', $transaction->bank_to_id);
                    $userName ='<h4><b>Bank:</b> '.$name.'-'.$acc.'<b> To </b>'.$name2.'-'.$acc2.'</h4>';
                }elseif ($transaction->employee_id != NULL){
                    $name = get_data_by_id('name', 'employee', 'employee_id', $transaction->employee_id);
                    $userName ='<h4><b>Employee:</b> '.$name.'</h4>';
                }elseif ($transaction->account_id != NULL){
                    $name = get_data_by_id('name', 'accounts', 'account_id', $transaction->account_id);
                    $userName ='<h4><b>Assets:</b> '.$name.'</h4>';
                }

                $bankName = '';
                if (!empty($transaction->bank_id)){
                    $bank = getTotalRow('bank','bank_id', $transaction->bank_id);
                    $bankName = '<h4><b>Bank:</b> '.$bank->name.'--'. $bank->account_no.'</h4>';
                }


            ?>
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12 text-capitalize">
                                <?= $userName; ?>
                                <?php if ($transaction->bank_to_id == NULL){ ?>
                                <?= $bankName; ?>
                                <?php } ?>
                            </div>
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