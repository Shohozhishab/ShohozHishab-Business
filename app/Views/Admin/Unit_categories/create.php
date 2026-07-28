<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Unit Categories <small>Unit Categories Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Unit Categories </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Unit Categories Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post" >
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Description</label>
                                        <textarea class="form-control" name="description" id="description"></textarea>
                                        <div class="error"></div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="unitCategoryValidat()"  >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_categories_ajax/'); ?>','<?php echo '/Admin/Unit_categories/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <form  action="<?php echo $actionCategory; ?>" method="post" >
                                    <div class="form-group">
                                        <label for="enum">Unit Category </label> <button type="button" class="btn btn-xs btn-primary select-all-btn2" onclick="toggleAllCategories(this)" >Select All</button><br>
                                        <?php
                                        foreach (getUnitCategoriesWithUnits() as $key => $val){
                                            $checked = '';
                                            foreach ($categories as $cat) {
                                                if ($cat->name == $key) {
                                                    $checked = 'checked disabled';
                                                    break;
                                                }
                                            }
                                            ?>
                                            <label style="margin-left: 10px;">
                                                <input type="checkbox" name="unit_category[]" <?= $checked ?>  value="<?= $key ?>"> <?= $key ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary"  >Get Unit Category</button>
                                    </div>
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