
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Yearly Closing <small>Yearly Closing</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Yearly Closing</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body text-center">
                        <p>Please be sure, you want to close the business for this year. Your all running data of your business will be removed and necessary data will be transferred to the next year business. You can not get those data back again.</p>
                        <label>Are you sure?</label><br>
                      <?php if (serverType == 'shared'){ ?>
                           <a href="#" onclick="closingData('<?php echo site_url('Admin/Yearly_closing/server') ?>')" class="btn btn-danger btn-closing" id="btn-closing" ><i class="fa fa-clock-o"></i> <span>Yearly closing Start</span></a>
                        <?php }else{?>
                           <a href="#" onclick="closingData('<?php echo site_url('Admin/Yearly_closing/yearly_closing_local') ?>')" class="btn btn-danger btn-closing" id="btn-closing" ><i class="fa fa-clock-o"></i> <span>Yearly closing Start</span></a>
                       <?php  }?>
<!--                        <a href="--><?php //echo site_url('Admin/Yearly_closing/yearly_closing_local') ?><!--" class="btn btn-danger btn-closing" id="btn-closing" ><i class="fa fa-clock-o"></i> <span>Yearly closing Start</span></a>-->
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
