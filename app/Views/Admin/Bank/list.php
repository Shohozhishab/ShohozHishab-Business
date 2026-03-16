<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Bank  <small>Bank List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bank</li>
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Bank List</h3>
                            </div>
                            <div class="col-lg-3">
                                <a href="javascript:void(0)"
                                   onclick="showData('<?php echo site_url('/Admin/Bank_ajax/create/'); ?>','<?php echo '/Admin/Bank/create/'; ?>'),activeTab(this)"
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
                                <th>Name</th>
                                <th>Account No</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start=1; foreach ($bank as $bank) {
                                $isDeletable = is_deletable('ledger_bank','bank_id',$bank->bank_id);
                                ?>
                                <tr>
                                    <td width="80px"><?php echo $start++ ?></td>
                                    <td><?php echo $bank->name ?></td>
                                    <td><?php echo $bank->account_no ?></td>
                                    <td><?php echo showWithCurrencySymbol($bank->balance) ?></td>
                                    <td width="180px">
                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Bank_ajax/update/'.$bank->bank_id); ?>','<?php echo '/Admin/Bank/update/'.$bank->bank_id; ?>')"  class="btn btn-xs btn-info">Update</a>
                                        <?php if($isDeletable == true){ ?>
                                            <a href="<?php echo site_url('/Admin/Bank/delete/' . $bank->bank_id); ?>" onclick="return confirm('Are you sure you want to delete this item?');"  class="btn btn-danger btn-xs">Delete</a>
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
