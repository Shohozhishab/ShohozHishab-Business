<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Unit Categories <small>Unit Categories Update</small> </h1>
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
                        <h3 class="box-title">Unit Categories Update</h3>
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
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= $categories->name;?>" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Description</label>
                                        <textarea class="form-control" name="description" id="description"><?= $categories->description;?></textarea>
                                        <div class="error"></div>
                                    </div>

                                    <input type="hidden" name="unit_categories_id" value="<?= $categories->unit_categories_id;?>" >
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Unit_categories_ajax/'); ?>','<?php echo '/Admin/Unit_categories/'; ?>')" class="btn btn-default">Cancel</a>
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