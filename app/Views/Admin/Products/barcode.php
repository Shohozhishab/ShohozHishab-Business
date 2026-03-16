<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Barcode <small>Barcode List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Barcode</li>
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
                            <div class="col-lg-9">
                                <h3 class="box-title">Barcode List</h3>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-12" style="padding: 20px; ">
                            <?php

                            if (is_array($barcodeqty)) {
                                foreach ($barcodeqty as $key => $row) {
                                    if ($barcodeqty[$key] > 0) {
                                        echo "<div class='row'>";
                                        for ($i = 1; $i <= $barcodeqty[$key]; $i++) {
                                            echo '<div style="padding:10px; float:left;"><img src="data:image/png;base64,' . base64_encode($generator->getBarcode((string)$key, $barcodeType)) . '" width="' . $barcodeSize . '"><br><center><small>Price:' . get_data_by_id('selling_price', 'products', 'prod_id', $key) . '</small></center></div>';
                                        }
                                        echo "</div><hr />";
                                    }
                                }
                            } else { ?>
                                <div style="padding:10px; float:left;">Please select any product to generate barcode.</div>
                            <?php  } ?>
                        </div>

                        <div class="row no-print">
                            <div class="col-md-12" style="margin-top: 20px;">
                                <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>
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
