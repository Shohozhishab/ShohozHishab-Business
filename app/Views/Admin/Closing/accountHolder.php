<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Account Head balance update  <small>Account Head balance update</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Account Head balance update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-12">
                <?php echo $menu;?>
            </div>

            <div class="col-lg-12" style="margin-top: 15px;">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Account Head balance update</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" >
                        <div class="col-md-6" style="border-right: 1px solid #dddddd;padding-left: 100px; padding-right: 100px;">
                            <form id="geniusform2" method="POST" action="<?php echo site_url('Admin/Closing/account_action');?>">
                                <div class="form-group">
                                    <label for="int">Account Head </label>
                                    <select class="form-control select2 select2-hidden-accessible" style=" width: 100%;" tabindex="-1" aria-hidden="true" name="loan_pro_id" >
                                        <option selected="selected"  value="">Please Select</option>
                                        <?php echo getAllListInOption('','loan_pro_id','name','loan_provider'); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="particulars">Particulars </label>
                                    <textarea class="form-control" rows="3" name="particulars" id="particulars" placeholder="Particulars" required ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="enum">Trangaction Type </label>
                                    <select class="form-control" name="trangaction_type" id="trangaction_type">
                                        <option value="">Please Select</option>
                                        <option value="1">খরচ (Cr.)</option>
                                        <option value="2">জমা (Dr.)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Amount </label>
                                    <input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" value="" required />
                                </div>

                                <button type="submit" class="btn btn-primary geniusSubmit-btn2">Update</button>

                            </form>
                        </div>
                        <div class="col-md-6" style="padding-left: 100px; padding-right: 100px;">
                            <form id="geniusform" action="<?php echo site_url('Admin/Closing/acc_bulk_action'); ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="int">Bulk upload</label>
                                    <input type="file" accept=".csv" class="form-control" name="file" required>
                                    <small>support type: .csv</small><br>
                                    <small><a href="<?php echo base_url()?>/xlStracture/account.csv">Download sheets</a></small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Upload</button>
                                </div>
                            </form>
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
