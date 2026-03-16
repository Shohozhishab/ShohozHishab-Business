<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Suppliers Transaction <small>Suppliers Transaction List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Suppliers Transaction</li>
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
                                <h3 class="box-title">Suppliers Transaction List</h3>
                            </div>
                            <div class="col-lg-3">
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
                                <th>Id</th>
                                <th>Date</th>
                                <th>Memo</th>
                                <th>Purchase Id</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=''; foreach ($transaction as $row) {
                                if(($row->purchase_id == NULL) && ($row->trans_id == NULL)){
                                    $purchaseId ='---';
                                }else {
                                    $purchaseId = ($row->purchase_id == NULL) ? '<a href="' . site_url('Admin/Transaction/read/' . $row->trans_id) . '">TRNS_' . $row->trans_id . '</a>' : '<a href="' . site_url('Admin/Purchase/view/' . $row->purchase_id) . '" >PURS_' . $row->purchase_id . '</a>';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $row->ledg_sup_id ;?></td>
                                    <td><?php echo $row->createdDtm ;?></td>
                                    <td><?php echo ($row->particulars == NULL)? "Purchase": $row->particulars ;?></td>
                                    <td><?php echo $purchaseId;?></td>
                                    <td><?php echo ($row->trangaction_type != 'Dr.')?"---":showWithCurrencySymbol($row->amount) ;?> </td>
                                    <td><?php echo ($row->trangaction_type != 'Cr.')?"---":showWithCurrencySymbol($row->amount) ;?> </td>
                                    <td><?php echo showWithCurrencySymbol($row->rest_balance) ;?></td>

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
