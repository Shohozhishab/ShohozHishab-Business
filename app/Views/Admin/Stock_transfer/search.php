<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transfer Products <small>Transfer Products List</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transfer Products</li>
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
                <form onsubmit="return validateTransferForm()" action="<?= base_url('Admin/Stock_transfer/transferAction') ?>" method="post" >
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="box-title">Transfer Products List</h3>
                                </div>
                                <div class="col-lg-6"></div>
                            </div>
                            <div id="errorMsg"></div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped dataTable" role="grid"
                                   aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th>Select</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($result as $row) { ?>
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" name="returnchecked[]" class="datatables" id="checkedProd" value="<?= $row->prod_id; ?>"> </td>
                                        <td><?= $row->name;?></td>
                                        <td><input type="number" class="quantity form-control" id="quantity" name="quantity[<?= $row->prod_id; ?>]" min="1" max="<?= $row->quantity ?>" placeholder="Quantity" value="<?php echo $row->quantity ?>"></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <div class="col-xs-12" >
                                <input type="hidden" name="from_stock_id" value="<?= $storeId ?>">
                                <div class="form-group col-md-6" >
                                    <label for="varchar">Store </label>
                                    <select class="form-control" name="store_id" id="store_id">
                                        <option value="">Please Select</option>
                                        <?php foreach ($stores as $val){ ?>
                                            <option value="<?= $val->store_id ?>"><?= $val->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-12" >
                                    <button type="submit" class="btn btn-primary">Transfer</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>


            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<script>
    function validateTransferForm() {

        let checkedProduct = document.querySelectorAll('.datatables:checked').length;

        let store = document.getElementById('store_id').value;

        let message = '';

        // Product validation
        if (checkedProduct == 0) {

            message += 'Please select at least one product .<br>';

        }

        // Quantity validation
        let checkedRows = document.querySelectorAll('.datatables:checked');

        checkedRows.forEach(function (item) {

            let row = item.closest('tr');

            let qty = row.querySelector('.quantity').value;

            let maxQty = row.querySelector('.quantity').max;

            if (qty == '' || qty <= 0) {

                message += 'Quantity must be greater than 0 .<br>';

            }

            if (parseInt(qty) > parseInt(maxQty)) {

                message += 'Quantity cannot exceed stock .<br>';

            }

        });
        // Store validation
        if (store == '') {

            message += 'Please select store <br>';

        }

        // Final validation
        if (message != '') {

            $('#errorMsg').html(
                '<div class="alert alert-danger">' + message + '</div>'
            );

            return false;

        }

        return true;

    }
</script>
