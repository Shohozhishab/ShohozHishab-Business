<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Profit balance update  <small>Profit balance update</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Profit balance update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-12">
                <?php echo $menu;?>
            </div>

            <div class="col-lg-12" style="margin-top: 15px;">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Profit balance update</h3>
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
                    <div class="box-body" >
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <form id="geniusform2" method="POST" action="<?php echo site_url('Admin/Closing/profit_action');?>">

                                <div class="form-group">
                                    <label for="particulars">Particulars</label>
                                    <textarea class="form-control" rows="3" name="particulars" id="particulars" placeholder="Particulars" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Amount </label>
                                    <input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" value="" required />
                                </div>

                                <button type="submit" class="btn btn-primary geniusSubmit-btn2">Update</button>

                            </form>
                        </div>
                        <div class="col-md-4"></div>
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
