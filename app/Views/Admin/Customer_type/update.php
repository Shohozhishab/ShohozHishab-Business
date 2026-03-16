<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Customer Type <small>Customer Type update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Customer update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax/') ?>','/Admin/Customer_type/')" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax'); ?>','<?php echo '/Admin/Customers';?>')"  class="btn btn-default">Customer</a>


            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Customer Type update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6">
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Type Name</label>
                                        <input type="text" class="form-control" name="type_name" id="type_name" placeholder="Type Name" value="<?php echo $cusType->type_name;?>" required />
                                        <input type="hidden" class="form-control" name="cus_type_id" value="<?php echo $cusType->cus_type_id;?>" required />
                                    </div>
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax/'); ?>','<?php echo '/Admin/Customer_type/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>
                            <div class="col-lg-6"></div>
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