<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Stock Report <small>Stock Report</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stock Report</li>
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
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Stock Report</h3>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-4" style="padding: 20px;">
                            <label for="int">Store name</label>

                            <select class="form-control select2 select2-hidden-accessible" onchange="storePro(this.value)" id="store_id" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected"  value="">Please Select</option>
                                <?php echo getAllListInOption('','store_id','name','stores'); ?>
                            </select>

                        </div>
                        <div class="col-md-8"></div>
                    </div>
                    <!-- /.box-body -->
                </div>


            </div>

            <div class="col-md-12" id="product"></div>


        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
