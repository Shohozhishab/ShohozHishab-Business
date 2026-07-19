<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Unit  <small>Unit Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Unit  </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Unit Update</h3>
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
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= $units->name;?>" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Symbol</label>
                                        <input type="text" class="form-control" name="symbol" id="symbol" placeholder="Symbol" value="<?= $units->symbol;?>" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Unit Categories</label>
                                        <select class="form-control" name="unit_categories_id" id="unit_categories_id" <?= !empty($isBase)?'disabled':''; ?>   required>
                                            <option value="">Please Select</option>
                                            <?= getListInOption($units->unit_categories_id, 'unit_categories_id', 'name', 'unit_categories')?>
                                        </select>

                                        <?php if (!empty($isBase)){ ?>
                                            <input type="hidden" name="unit_categories_id" value="<?= $units->unit_categories_id;?>" >
                                        <?php } ?>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Conversion Factor</label>
                                        <input type="text" class="form-control" name="conversion_factor" id="conversion_factor" <?= !empty($isBase)?'readonly':''; ?>  placeholder="0.00000000" value="<?= $units->conversion_factor;?>" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Decimal Places</label>
                                        <input type="text" class="form-control" name="decimal_places" id="decimal_places" placeholder="0" value="<?= $units->decimal_places;?>" required>
                                        <div class="error"></div>
                                    </div>


                                    <input type="hidden" name="units_id" value="<?= $units->units_id;?>" >
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_ajax/'); ?>','<?php echo '/Admin/Unit/'; ?>')" class="btn btn-default">Cancel</a>
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