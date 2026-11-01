<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products <small> Products Add</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"> Products Add</li>
        </ol>
    </section>

    <style>
    .sw-wrap{position:relative;display:inline-block;cursor:pointer;margin-left:8px;vertical-align:middle;user-select:none;}
    .sw-wrap input.sw-box{position:absolute;opacity:0;width:40px;height:20px;margin:0;cursor:pointer;z-index:2;}
    .sw-slider{display:inline-block;width:40px;height:20px;background:#ccc;border-radius:20px;position:relative;transition:.2s;vertical-align:middle;}
    .sw-slider:before{content:"";position:absolute;height:16px;width:16px;left:2px;top:2px;background:#fff;border-radius:50%;transition:.2s;}
    .sw-box:checked + .sw-slider{background:#3c8dbc;}
    .sw-box:checked + .sw-slider:before{transform:translateX(20px);}
    .sw-label{font-weight:normal;margin-left:4px;}
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu; ?>
            </div>
            <div class="col-xs-12">
                <form id="geniusform" action="<?php echo base_url('Admin/Products/add_action_product') ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-4">
                                    <h3 class="box-title"> Products Add</h3>
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
                            <div class="form-group col-xs-3">
                                <label for="int">Category </label>
                                <select class="form-control" onchange="showSubCategory(this.value)" name="category" id="category">
                                    <option value="">Please Select</option>
                                    <?php echo getCatListInOption('prod_cat_id', 'prod_cat_id', 'product_category', 'product_category'); ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-3">
                                <label for="int">Sub Category </label>
                                <select class="form-control" name="sub_category" id="subCat">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-3">
                                <label for="int">Supplier</label>
                                <select class="form-control" name="supplier_id" id="supData"  >
                                    <option value="">Please select</option>
                                    <?php echo getAllListInOptionWithStatus('','supplier_id','name','suppliers','name'); ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-3">
                                <label for="varchar">Product Name </label> <span id="nameValid" style="color: red;"></span>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name"/>
                            </div>
                            <div class="form-group col-xs-3">
                                <label for="int">Unit Set</label>
                                <select class="form-control" name="categories_id" id="categories_id" onchange="unitShow(this.value)" >
                                    <option value="">Please Select</option>
                                    <?php foreach ($unit_set as $cat){ ?>
                                        <option value="<?= $cat->unit_set_id;?>" <?= ($cat->default_set == '1')?'Selected':'';?> ><?= $cat->name;?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden"   id="noPurchase" value="1">
                            </div>
                            <div id="unitData"></div>

                            <div class="form-group col-xs-3">
                                <label for="serial_status" style="margin-bottom:0;">Serial Number</label>
                                <span class="sw-wrap">
                                    <input type="hidden" name="serial_status" value="0">
                                    <input type="checkbox" class="sw-box" name="serial_status" id="serial_status" value="1">
                                    <span class="sw-slider"></span>
                                    <span class="sw-label">Off</span>
                                </span>
                            </div>
                            <div class="form-group col-xs-3">
                                <label for="warranty_status" style="margin-bottom:0;">Company Warranty</label>
                                <span class="sw-wrap">
                                    <input type="hidden" name="warranty_status" value="0">
                                    <input type="checkbox" class="sw-box" name="warranty_status" id="warranty_status" value="1">
                                    <span class="sw-slider"></span>
                                    <span class="sw-label">Off</span>
                                </span>
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

<script>
// Toggle label text on switch change.
jQuery(document).on('change', '#geniusform .sw-box', function () {
    jQuery(this).closest('.sw-wrap').find('.sw-label').text(this.checked ? 'On' : 'Off');
});
</script>
