<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Shops <small>Shops Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Shops</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customers_ajax'); ?>','<?php echo '/Admin/Customers';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Customer_type_ajax') ?>','/Admin/Customer_type')" class="btn btn-default">Customer type</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Shop Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <!-- Nav tabs -->
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active "><a href="#home" aria-controls="home"
                                                                                                                                                                                            role="tab" data-toggle="tab">General</a></li>
                                    <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab"
                                                                                                                                                   data-toggle="tab">Personal</a></li>
                                    <li role="presentation" ><a href="#messages" aria-controls="messages" role="tab"
                                                                                                                                                data-toggle="tab">Photo</a></li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content " style="margin-top: 60px;">
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    <div class="col-lg-6 ">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Customers/general_update'); ?>" method="post">
                                            <div class="form-group">
                                                <label for="varchar">Customer Name </label>
                                                <input type="text" class="form-control" name="customer_name" id="CustomerName" placeholder="CustomerName" value="<?php echo $customer->customer_name; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Mobile</label>
                                                <input type="number" class="form-control" name="mobile" id="Mobile" placeholder="Mobile" value="<?php echo $customer->mobile; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Customer Type </label>
                                                <select class="form-control" name="cus_type_id" >
                                                    <?php echo getListInOption($customer->cus_type_id,'cus_type_id','type_name', 'customer_type')?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Status </label>
                                                <select class="form-control" name="status" >
                                                    <?php echo globalStatus($customer->status)?>
                                                </select>
                                            </div>

                                            <input type="hidden" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn"> Update </button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customers_ajax/'); ?>','<?php echo '/Admin/Customers/'; ?>')" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="profile">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Customers/personal_update'); ?>" method="post">
                                            <div class="form-group">
                                                <label for="varchar">FatherName </label>
                                                <input type="text" class="form-control" name="father_name" id="FatherName" placeholder="FatherName" value="<?php echo $customer->father_name; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">MotherName </label>
                                                <input type="text" class="form-control" name="mother_name" id="MotherName" placeholder="MotherName" value="<?php echo $customer->mother_name; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Address </label>
                                                <input type="text" class="form-control" name="address" id="Address" placeholder="Address" value="<?php echo $customer->address; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">PresentAddress </label>
                                                <input type="text" class="form-control" name="present_address" id="PresentAddress" placeholder="PresentAddress" value="<?php echo $customer->present_address; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Age </label>
                                                <input type="text" class="form-control" name="age" id="Age" placeholder="Age" value="<?php echo $customer->age; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="int">NID </label>
                                                <input type="text" class="form-control" name="nid" id="NID" placeholder="NID" value="<?php echo $customer->nid; ?>" />
                                            </div>

                                            <input type="hidden" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customers_ajax/'); ?>','<?php echo '/Admin/Customers/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="messages">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Customers/photo_update'); ?>" method="post" enctype="multipart/form-data" >

                                            <div class="form-group" >
                                                <?php $proimg =  no_image_view('/uploads/customer_image/'.$customer->pic,'/uploads/customer_image/no_image.jpg',$customer->pic); ?>
                                                <img src="<?php print $proimg ?>"  width="300">

                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Photo </label>
                                                <input type="file" class="form-control" name="pic" id="pic" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=300px) x (height=300px)</b></span>
                                            </div>

                                            <input type="hidden" name="customer_id" value="<?php echo $customer->customer_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Customers_ajax/'); ?>','<?php echo '/Admin/Customers/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
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