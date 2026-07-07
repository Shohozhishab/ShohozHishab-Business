
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Stock Transfer <small>View</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stock Transfer</li>
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
                            <div class="col-lg-12">
                                <h4>Stock Transfer: <?= get_data_by_id('name','stores','store_id',$transfer->from_stock_id);?>  to <?= get_data_by_id('name','stores','store_id',$transfer->to_stock_id);?></h4>
                            </div>

                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h4>Products</h4>
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($transferItem as $item) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= globalTimeStamp($item->createdDtm); ?></td>
                                    <td><?= get_data_by_id('name','products','prod_id',$item->prod_id);?></td>
                                    <td><?= $item->quantity;?></td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
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
