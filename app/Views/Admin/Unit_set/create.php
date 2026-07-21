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
                        <h3 class="box-title">Unit Set Create</h3>
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
                                        <label for="varchar">Unit Categories</label>
                                        <select class="form-control" name="unit_categories_id" id="unit_categories_id" onchange="unitShowInOption(this.value)" required>
                                            <option value="">Please Select</option>
                                            <?= getListInOption('', 'unit_categories_id', 'name', 'unit_categories')?>
                                        </select>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Purchase Units</label>
                                        <div id="purchase_units_container"></div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Purchase Units Price</label>
                                        <div id="purchase_price_units_container"></div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Sell Units</label>
                                        <div id="sell_units_container"></div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Sell Units Price</label>
                                        <div id="sell_price_units_container"></div>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Default Set</label>
                                        <select class="form-control" name="default_set" id="default_set" required>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <div class="error"></div>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="unitSetValidat()"  >Create</button>
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