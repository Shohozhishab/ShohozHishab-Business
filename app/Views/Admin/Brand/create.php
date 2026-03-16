<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Brand <small>Brand Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Brand </li>
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
                        <h3 class="box-title">Brand Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="varchar">Brand Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Brand" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Image</label>
                                        <input type="file" class="form-control" name="image" id="image"  >
                                        <span class="help-block"><b>Max. file size 1024KB and (width=300px) x (height=300px)</b></span>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="brandValidat()"  >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Brand_ajax/'); ?>','<?php echo '/Admin/Brand/'; ?>')" class="btn btn-default">Cancel</a>
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