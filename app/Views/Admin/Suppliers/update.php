<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Suppliers <small>Suppliers Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Suppliers </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Suppliers Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusformUpdate" action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Name </label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $supplier->name?>"  />
                                        <input type="hidden" class="form-control" name="supplier_id" id="supplier_id" value="<?php echo $supplier->supplier_id?>"  />
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Phone </label>
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone" value="<?php echo $supplier->phone?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"><?php echo $supplier->address?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Status </label>
                                        <select class="form-control" name="status" >
                                            <?php echo globalStatus($supplier->status)?>
                                        </select>
                                    </div>


                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/'); ?>','<?php echo '/Admin/Suppliers/'; ?>')" class="btn btn-default">Cancel</a>
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