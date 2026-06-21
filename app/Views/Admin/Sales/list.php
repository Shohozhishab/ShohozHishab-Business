<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Sales <small>Sales List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu; ?>
            </div>

            <div class="col-xs-12" >
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Sales</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?= base_url('Admin/Sales') ?>" method="get">
                            <div class="row">
                                <div class="col-lg-2 ">
                                    <label>Customer</label><br>
                                    <select class="form-control select2" name="customer" >
                                        <option value="">Please Select</option>
                                        <?= getAllListInOptionWithStatus( $customer_id, 'customer_id', 'customer_name', 'customers', 'customer_name' );?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="st_date" value="<?= $st_date; ?>"
                                           id="st_date" >
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="en_date" value="<?= $en_date; ?>"
                                           id="en_date" >
                                </div>

                                <div class="col-md-2" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2" style="margin-top: 25px;">
                                    <a href="<?= base_url('Admin/Sales') ?>" class="btn btn-default btn-block"><i
                                                class="fa fa-refresh"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="javascript:void(0)"
                   onclick="showData('<?php echo site_url('/Admin/Sales_ajax/create/'); ?>','<?php echo '/Admin/Sales/create/'; ?>')"
                   class="btn  btn-success"><i class="fa fa-plus"></i> Add Sales</a>
            </div>
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Invoice Id</th>
                                <th>Total Amount</th>
                                <th>Total Due</th>
                                <th>Profit</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1;
                            foreach ($sales as $val) {
                                $cus_id = get_data_by_id('customer_id', 'invoice', 'invoice_id', $val->invoice_id);
                                $cusName = !empty($cus_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $cus_id) : get_data_by_id('customer_name', 'invoice', 'invoice_id', $val->invoice_id);
                                $profit = get_data_by_id('profit', 'invoice', 'invoice_id', $val->invoice_id);
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo invoiceDateFormat($val->createdDtm) ?></td>
                                    <td><?php echo $cusName ?></td>
                                    <td><?php echo $val->invoice_id ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                    <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $val->invoice_id)) ?></td>
                                    <td><?php echo showWithCurrencySymbol($profit) ?></td>
                                    <td>

                                        <a href="javascript:void(0)"
                                           onclick="showData('<?php echo site_url('/Admin/Invoice_ajax/view/' . $val->invoice_id); ?>','<?php echo '/Admin/Invoice/view/' . $val->invoice_id; ?>')"
                                           class="btn btn-primary btn-xs">View</a>
                                        <?php if (edit_expire_check($val->createdDtm) == true) { ?>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-warning"
                                               onclick="saleEdit('<?= $val->sales_id; ?>')" data-toggle="modal"
                                               data-target="#modal-default">Edit</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div class="row no-print">
                            <div class="col-xs-12">
                                <button onclick="printDiv('ledgPrint')" class="print_line btn btn-primary pull-right"><i
                                            class="fa fa-print "></i> Print Now
                                </button>
                                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;"
                                        onclick="downloadPDF('ledgPrint','sales')"><i class="fa fa-file-pdf-o "></i>
                                    Download PDF
                                </button>
                                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;"
                                        onclick="downloadCSV('ledgPrint','sales')"><i class="fa fa-file-excel-o "></i>
                                    Download CSV
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12" id="ledgPrint" style="display: none; text-transform: capitalize; ">
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <?php if (logo_image() == NULL) { ?>
                                        <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg"
                                             alt="User Image">
                                    <?php } else { ?>
                                        <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>"
                                             class="" alt="User Image">
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6">
                                    <?php print address(); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped text-capitalize">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Invoice Id</th>
                                        <th>Total Amount</th>
                                        <th>Total Due</th>
                                        <th>Profit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $j = 1;
                                    foreach ($sales as $item) {
                                        $cus_id = get_data_by_id('customer_id', 'invoice', 'invoice_id', $item->invoice_id);
                                        $cusName = !empty($cus_id) ? get_data_by_id('customer_name', 'customers', 'customer_id', $cus_id) : get_data_by_id('customer_name', 'invoice', 'invoice_id', $item->invoice_id);
                                        $profit = get_data_by_id('profit', 'invoice', 'invoice_id', $item->invoice_id);
                                        ?>
                                        <tr>
                                            <td><?php echo $j++ ?></td>
                                            <td><?php echo invoiceDateFormat($item->createdDtm) ?></td>
                                            <td><?php echo $cusName ?></td>
                                            <td><?php echo $item->invoice_id ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('amount', 'invoice', 'invoice_id', $item->invoice_id)) ?></td>
                                            <td><?php echo showWithCurrencySymbol(get_data_by_id('due', 'invoice', 'invoice_id', $item->invoice_id)) ?></td>
                                            <td><?php echo showWithCurrencySymbol($profit) ?></td>

                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
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

<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Data</h4>
            </div>
            <div class="modal-body" id="formData">


            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    function saleEdit(salesId) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Sales/salesEdit') ?>",
            data: {id: salesId},
            success: function (data) {
                $('#formData').html(data);
            }
        });
    }
</script>