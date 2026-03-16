<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small>Existing Products Add</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Existing Products Add</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu; ?>
            </div>
            <div class="col-xs-12">
                <form id="geniusform" action="<?php echo base_url('Admin/Products/add_action') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-4">
                                    <h3 class="box-title">Existing Products Add</h3>
                                </div>
                                <div class="col-lg-8">
                                </div>
                                <div class="col-lg-12" style="margin-top: 20px;" id="message">
                                    <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                                </div>
                            </div>


                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-group col-xs-6">
                                <label for="int">Category </label>
                                <select class="form-control" onchange="showSubCategory(this.value)" name="category" id="category">
                                    <option value="">Please Select</option>
                                    <?php echo getCatListInOption('prod_cat_id', 'prod_cat_id', 'product_category', 'product_category'); ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="int">Sub Category </label>
                                <select class="form-control" name="sub_category" id="subCat">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="varchar">Product Name </label> <span id="nameValid" style="color: red;"></span>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name"/>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="int">Unit </label>
                                <select class="form-control" name="unit">
                                    <?php echo selectOptions($selected = 1, unitArray()); ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="int">Purchase Price</label>
                                <input type="number" class="form-control purchase_price" oninput="minusValueCheck(this.value,this)" min="0" name="price" id="price" placeholder="Purchase Price"/>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="int">Selling Price</label>
                                <input type="number" class="form-control selling_price" oninput="minusValueCheck(this.value,this)" min="0" name="selling_price" id="selling_price" placeholder="Selling Price"/>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="int">Quantity </label>
                                <input type="number" class="form-control quantity" name="qty" min="0" placeholder="Quantity" value="1"/>
                            </div>
                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-primary ">Add</button>
                                <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Products_ajax/'); ?>','<?php echo '/Admin/Products/'; ?>')" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </form>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
