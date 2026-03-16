<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Money Receipt <small>Money Receipt</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Money Receipt</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="invoice" style="border:5px solid #D0D3D8; padding: 30px;">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <?php foreach ($shops as $row) { ?>
                <h2 class="page-header">
                    <?php
                    if ($row->name == NULL) {
                        echo '<i class="fa fa-globe"></i> '.$row->name;
                    }else{
                        echo '<img src="'.base_url().'/uploads/schools/'.$row->logo.'" width="18%">';
                    }
                    ?>.
                    <small class="pull-right">Date: <?php echo get_data_by_id('createdDtm','transaction','trans_id',$transactionId);?></small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col" style="padding: 10px;">
                Address
                <address>

                    <strong><?php echo $row->name;?>.</strong><br>
                    <?php echo $row->address;?><br>
                    Phone: <?php echo $row->mobile;?><br>
                    Email: <?php echo $row->email;?>
                    <?php } ?>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">

            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col" style="padding: 10px;">
                <b>Transaction Id :</b> tran_<?php echo $transactionId;?>.
            </div>

        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12 " style="padding: 10px; border:1px dashed #D0D3D8;">
                <div class="col-sm-12" style="padding: 10px;">
                    <b>Received From :</b> <span style=" border-bottom:2px dashed #D0D3D8; "> <?php $cusId = get_data_by_id('customer_id','transaction','trans_id',$transactionId); echo get_data_by_id('customer_name','customers','customer_id',$cusId);?></span>
                </div>

                <div class="col-sm-8" style="padding: 10px;">
                    <b>Address : </b> <span style=" border-bottom:2px dashed #D0D3D8; "><?php echo get_data_by_id('address','customers','customer_id',$cusId);?></span>
                </div>
                <div class="col-sm-4" style="padding: 10px;">
                    <b>Phone :</b> <span style=" border-bottom:2px dashed #D0D3D8; "><?php echo get_data_by_id('mobile','customers','customer_id',$cusId);?></span>
                </div>

            </div>
        </div>

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-8" style="padding: 10px;">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><b>Total Amount:</b> <span style=" border-bottom:2px dashed #D0D3D8; "><?php echo showWithCurrencySymbol(get_data_by_id('amount','transaction','trans_id',$transactionId));?></span></td>
                    </tr>
                    <tr>
                        <td><b>Amount In Word:</b>  <span style=" border-bottom:2px dashed #D0D3D8; text-transform: capitalize; "><?php $bal = get_data_by_id('amount','transaction','trans_id',$transactionId); echo numberTowords($bal);?> Tk.</span></td>
                    </tr>


                    </tbody></table>
            </div>
            <!-- /.col -->
            <div class="col-xs-4" style="padding: 10px;">
                <div class="table-responsive">
                    <table class="table" style="margin-top: 40px;">
                        <tbody>
                        <tr>
                            <td><b>Received by:</b> ........................</td>
                        </tr>

                        </tbody></table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <div class="print_line btn btn-primary pull-right" onclick="print(document);"><i class="fa fa-print"></i> Print Now</div>

            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
