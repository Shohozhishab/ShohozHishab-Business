<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Starting business balance update  <small>Starting business balance update</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Starting business balance update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" id="reload">
        <!-- Small boxes (Stat box) -->
        <div class="row" >
            <?php if (shop_opening_status() == 0) {?>
            <div class="col-lg-12">
                <?php echo $menu;?>
            </div>
            <?php } ?>
            <div class="col-lg-12" style="margin-top: 15px;">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body text-center" >
                        <?php if (shop_opening_status() == 0) {?>
                            <div class="col-xs-12">
                                <p>After setting all the business starting data to the software, you need to press the "RUN" button. Please make sure all the data are inputted properly.</p>

                            </div>
                            <div class="col-xs-12">
                                <label>Are you sure, you want to run it? You will not be able to put any starting data after running the business.</label><br>
                                <a href="#" onclick="opening_status('<?php echo site_url('Admin/Closing/change_status') ?>')" class="btn btn-info btn-closing" ><i class="fa fa-clock-o"></i> <span>Yes</span></a>
                            </div>
                        <?php }else{ ?>
                            <div class="col-xs-12">
                                <p>Thanks! You have run your business. You can now start any transaction of your business. If you made any mistake, please contact with the support team.</p>

                            </div>
                        <?php }?>
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
