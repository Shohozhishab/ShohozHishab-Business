<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Bank <small>Bank Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bank </li>
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
                        <h3 class="box-title">Bank Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <button type="button" onclick="modalOpen(this)" data-action="<?= base_url('Admin/Bank/csv_action') ?>" class="btn btn-info" style="float: right;"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> CSV Upload</button>
                                <br><br><a href="<?= base_url('uploads/csv/bank.csv') ?>" style="float: right;">Example</a>
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <h4>New Bank</h4>
                                    <div class="form-group">
                                        <label for="varchar">Bank Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Bank Name"  required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Account Name </label>
                                        <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Account Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Account No </label>
                                        <input type="number" class="form-control" oninput="minusValueCheck(this.value,this)" name="account_no" id="account_no" placeholder="Account No"  required>
                                        <div class="error"></div>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="bankValidat()"  >Add</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Bank_ajax/'); ?>','<?php echo '/Admin/Bank/'; ?>'),activeTab(this)" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <button type="button" onclick="modalOpen(this)" data-action="<?= base_url('Admin/Bank/csv_existing_action') ?>" class="btn btn-info" style="float: right;"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> CSV Upload</button>
                                <br><br><a href="<?= base_url('uploads/csv/bankExisting.csv') ?>" style="float: right;">Example</a>

                                <form id="geniusform3" action="<?php echo $action2; ?>" method="post">
                                    <h4>Existing Bank</h4>
                                    <div class="form-group">
                                        <label for="varchar">Bank Name </label>
                                        <input type="text" class="form-control" name="name" id="name_ex" placeholder="Bank Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Account Name </label>
                                        <input type="text" class="form-control" name="account_name" id="account_name_ex" placeholder="Account Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Account No </label>
                                        <input type="number" class="form-control" name="account_no" id="account_no_ex" placeholder="Account No" required />
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group databank" id="chaque">
                                        <label for="int">Amount </label>
                                        <input type="number" class="form-control input" name="amount" id="amount_ex" placeholder="Amount" oninput="minusValueCheck(this.value,this)" required/>
                                        <div class="error"></div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="bankExValidat()"  >Add</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Bank_ajax/'); ?>','<?php echo '/Admin/bank/'; ?>'),activeTab(this)" class="btn btn-default">Cancel</a>
                                </form>

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
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">File upload</h4>
            </div>
            <div class="modal-body">
                <form id="csvForm" action="" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="csv-file">CSV File</label>
                        <input type="file" class="form-control" id="csv-file" accept=".csv,text/csv" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function modalOpen(item) {
        var actionUrl = $(item).data('action');
        if (actionUrl) {
            $('#csvForm').attr('action', actionUrl);
        }
        $('#modal-default').modal('show');
    }
</script>