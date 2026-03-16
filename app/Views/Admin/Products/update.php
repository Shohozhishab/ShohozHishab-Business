<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Products Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products</li>
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
                        <h3 class="box-title">Products Update</h3>
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
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Products/general_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Store</label>
                                                <select class="form-control" name="store_id" id="store_id">
                                                    <option value="">Please Select</option>
                                                    <?php echo getAllListInOption($product->store_id,'store_id','name','stores'); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Name </label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $product->name; ?>" >
                                            </div>

                                            <div class="form-group">
                                                <label for="int">Supplier</label>

                                                <select class="form-control" name="supplier_id" id="supplier_id">
                                                    <option>Please Select</option>
                                                    <?php echo getAllListInOption($product->supplier_id,'supplier_id','name','suppliers'); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Serial Number</label>
                                                <input type="text" class="form-control" name="serial_number" value="<?php echo $product->serial_number; ?>">
                                            </div>

                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" >
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn"> Update </button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="profile">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Products/personal_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="int">Category </label>
                                                <select class="form-control" onchange="showSubCategory(this.value)" name="prod_cat_id" id="prod_cat_id">
                                                    <option>Please Select</option>
                                                    <?php
                                                    echo categoryListInOption($product->prod_cat_id);
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Sub Category </label>
                                                <select class="form-control" name="sub_cat_id" id="subCat">
                                                    <option value="">Please Select</option>
                                                    <?php echo subCatListInOption($product->prod_cat_id); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Brand</label>
                                                <select class="form-control" name="brand_id" >
                                                    <option value="">Please Select</option>
                                                    <?php echo getListInOption($product->brand_id,'brand_id', 'name','brand')?>
                                                </select>

                                            </div>
                                            <div class="form-group">
                                                <label for="int">Selling Price</label>
                                                <input type="number" class="form-control" name="selling_price" id="selling_price" placeholder="Selling Price" value="<?php echo $product->selling_price; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Size</label>
                                                <input type="text" class="form-control" name="size" id="size" placeholder="Size" value="<?php echo $product->size; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="int">Warranty </label>
                                                <input type="text" class="form-control" name="warranty" id="warranty" placeholder="Warranty " value="<?php echo $product->warranty; ?>" />
                                            </div>

                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="messages">
                                    <div class="col-lg-6">
                                        <form id="geniusform" action="<?php echo base_url('Admin/Products/photo_update'); ?>" method="post" enctype="multipart/form-data" >
                                            <div class="form-group" id="reloadimg" >
                                                <?php $proimg =  no_image_view('/uploads/product_image/'.$product->picture,'/uploads/product_image/no_image.jpg',$product->picture); ?>
                                                <img src="<?php echo $proimg; ?>" >
                                            </div>

                                            <div class="form-group">
                                                <label for="varchar">Photo </label>
                                                <input type="file" class="form-control" name="picture" id="picture" required >
                                                <span class="help-block" style="color: #ff0000;"><b>Max. file size 1024KB and (width=300px) x (height=300px)</b></span>
                                            </div>



                                            <input type="hidden" name="prod_id" value="<?php echo $product->prod_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-default">Cancel</a>
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