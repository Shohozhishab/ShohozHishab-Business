<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Unit Set  <small>Unit Set Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Unit Set </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Unit Set Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post" >
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= $units_set->name;?>" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Unit Categories</label>
                                        <select class="form-control" name="unit_categories_id" id="unit_categories_id"  disabled required>
                                            <option value="">Please Select</option>
                                            <?= getListInOption($units_set->unit_categories_id, 'unit_categories_id', 'name', 'unit_categories')?>
                                        </select>
                                        <input type="hidden"  name="unit_categories_id" value="<?= $units_set->unit_categories_id;?>" required>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Purchase Units</label>
                                        <div id="purchase_units_container">
                                            <?php foreach ($unit as $item){
                                                $purchase_units = json_decode($units_set->purchase_units);
                                                $sale_units = json_decode($units_set->sell_units);
                                            ?>
                                            <label style="margin-left: 10px;">
                                                <input type="checkbox" name="purchase_units[]" <?php foreach ($purchase_units as $value){ echo ($item->units_id == $value)?'checked':''; }?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                            </label>
                                            <?php } ?>
                                        </div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Purchase Units Price</label>
                                        <div id="purchase_price_units_container">
                                            <?php foreach ($unit as $item){?>
                                                <label style="margin-left: 10px;">
                                                    <input type="radio" name="purchase_price" <?= ($item->units_id == $units_set->purchase_units_price)?'checked':'';?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Sell Units</label>
                                        <div id="sell_units_container">
                                            <?php foreach ($unit as $item){?>
                                                <label style="margin-left: 10px;">
                                                    <input type="checkbox" name="sell_units[]" <?php foreach ($sale_units as $value){ echo ($item->units_id == $value)?'checked':''; }?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Sell Units Price</label>
                                        <div id="sell_price_units_container">
                                            <?php foreach ($unit as $item){?>
                                                <label style="margin-left: 10px;">
                                                    <input type="radio" name="sell_price" <?= ($item->units_id == $units_set->sell_unit_price)?'checked':'';?> value="<?= $item->units_id;?>"> <?= $item->name;?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="varchar">Default Set</label>
                                        <select class="form-control" name="default_set" id="default_set" required>
                                            <option value="0" <?= ($units_set->default_set == '0')?'Selected':'';?> >No</option>
                                            <option value="1" <?= ($units_set->default_set == '1')?'Selected':'';?> >Yes</option>
                                        </select>
                                        <div class="error"></div>
                                    </div>
                                    <input type="hidden"  name="unit_set_id" value="<?= $units_set->unit_set_id;?>" required>
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn" >Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_set_ajax/'); ?>','<?php echo '/Admin/Unit_set/'; ?>')" class="btn btn-default">Cancel</a>
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