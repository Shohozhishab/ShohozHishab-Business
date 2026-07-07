<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction Events <small>Transaction Events</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"Transaction Events</li>
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
                                        <select class="form-control" onchange="rolePermission(this.value,'TransactionEvents')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="TransactionEvents">
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
                        <form action="<?= base_url('Admin/Transaction_events') ?>" method="get">
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
                                    <a href="<?= base_url('Admin/Transaction_events') ?>" class="btn btn-default btn-block"><i
                                            class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="col-xs-12">

                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Date</th>
                                <th>Event Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                foreach ($result as $val) {
                                    $url= ''; $urlFlow = ''; $eventType = '';
                                    if (!empty($val->trans_id)){
                                        $url = base_url('Admin/Transaction/read/'.$val->trans_id);
                                        $urlFlow = base_url('Admin/Transaction/transaction_flow/'.$val->trans_id);
                                        $eventType = 'Transaction';
                                    }elseif (!empty($val->sales_id)){
                                        $invId = get_data_by_id('invoice_id','sales','sales_id',$val->sales_id);
                                        $url = base_url('Admin/Invoice/view/'.$invId);
                                        $urlFlow = base_url('Admin/Sales/transaction_flow/'.$val->sales_id);
                                        $eventType = 'Sales';
                                    }elseif (!empty($val->purchase_id)){
                                        $url = base_url('Admin/Purchase/view/'.$val->purchase_id);
                                        $urlFlow = base_url('Admin/Purchase/transaction_flow/'.$val->purchase_id);
                                        $eventType = 'Purchase';
                                    }elseif (!empty($val->dep_id)){
                                        $url = '';
                                        $urlFlow = base_url('Admin/Bank_deposit/transaction_flow/'.$val->dep_id);
                                        $eventType = 'Bank Deposit';
                                    }elseif (!empty($val->wthd_id)){
                                        $url = '';
                                        $urlFlow = base_url('Admin/Bank_withdraw/transaction_flow/'.$val->wthd_id);
                                        $eventType = 'Bank Withdraw';
                                    }
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $val->createdDtm ?></td>
                                    <td><?= $eventType;?></td>
                                    <td>
                                        <?php if (!empty($urlFlow)){ ?>
                                            <?php if (isset($transaction_flow) && $transaction_flow == 1){ ?>
                                            <a href="<?= $urlFlow;?>" class="btn btn-success btn-xs">Transaction Flow </a>
                                            <?php } ?>
                                        <?php } if (!empty($url)){ ?>
                                            <?php if (isset($read) && $read == 1){ ?>
                                            <a href="<?= $url;?>" class="btn btn-xs btn-primary">Detail</a>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
