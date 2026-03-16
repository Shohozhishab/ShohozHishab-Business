<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Account Head <small>Account Head Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Account Head </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Account Head Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post">
                                    <h4>New Account Head</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $loan_provider->name ?>" />
                                        <input type="hidden" class="form-control" name="loan_pro_id" id="loan_pro_id" value="<?php echo $loan_provider->loan_pro_id ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Phone</label>
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone"  value="<?php echo $loan_provider->phone ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Adress </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="address"><?php echo $loan_provider->address ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Status </label>
                                        <select class="form-control" name="status" >
                                            <?php echo globalStatus($loan_provider->status)?>
                                        </select>
                                    </div>


                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Loan_provider_ajax/'); ?>','<?php echo '/Admin/Loan_provider/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">

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