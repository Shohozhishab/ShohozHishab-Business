
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="box-title">Purchase Item</h3>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form action="<?= base_url('Admin/Purchase/purchaseEdiAction')?>" method="post">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Quantity </th>
                                    <th>Purchase Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach ($purchaseItem as $item){ ?>
                                <tr>
                                    <td width="80px"><?= $i++ ?></td>
                                    <td>
                                        <?= get_data_by_id('name','products','prod_id',$item->prod_id); ?>
                                        <input type="hidden" name="prod_id[]" value="<?= $item->prod_id ?>">
                                    </td>
                                    <td><input type="text" name="qty[]" value="<?= $item->quantity; ?>"></td>
                                    <td><input type="text" name="price[]" value="<?= $item->purchase_price?>"></td>
                                    <td><input type="text" name="total_price[]" value="<?= $item->total_price?>" readonly></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div class="col-xs-6"></div>
                        <div class="col-xs-6">
                            <div class="col-xs-6">
                                <label for="int">Total Amount</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="number" class="form-control" name="totalPrice"
                                       id="totalPrice" value="<?= $purchase->amount ?>"
                                       readonly>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Cash</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="number" onchange="checkShopsBalance(this.value),calculateDueTotal()"
                                       class="cash form-control"
                                       oninput="minusValueCheck(this.value,this)" name="cash"
                                       id="cash" value="<?= $purchase->nagad_paid ?>"><b id="Balance_valid"></b>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Bank</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    <?php echo getTwoValueInOption($purchase->bank_id, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                </select><br>
                                <input type="number" onchange="checkBankBalance(this.value),calculateDueTotal()"
                                       class="bank form-control"
                                       oninput="minusValueCheck(this.value,this)" name="bank"
                                       id="bank" value="<?= $purchase->bank_paid ?>">
                                <b id="Bank_valid"></b>
                            </div>
                            <div class="col-xs-6">
                                <label for="int">Due</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <input type="hidden" class="form-control"  id="totalDue2" readonly value="<?= $purchase->due ?>">
                                <input type="number" class="form-control" name="due" id="totaldue" readonly value="<?= $purchase->due ?>">
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <input type="hidden" name="purchase_id" value="<?= $purchase->purchase_id ?>">
                            <button type="submit" class="btn btn-success " style="float: right;" id="dueBtn">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->


<script>

    function calculateDueTotal(){
        let totalDue = $('#totaldue').val();
        if (totalDue >= 0 ){
            // $('#createBtn').hide();
            document.getElementById("dueBtn").disabled = false;
        }else{
            // $('#createBtn').show();
            document.getElementById("dueBtn").disabled = true;
        }
    }
    function calculateGrandTotal() {
        let grandTotal = 0;

        $('input[name="total_price[]"]').each(function () {
            grandTotal += parseFloat($(this).val()) || 0;
        });

        $('#totalPrice').val(grandTotal);


        let oldDue = $('#totalDue2').val();

        $('#totalDue2').val(grandTotal);

        let dueTotal = grandTotal - oldDue;
        $('#totaldue').val(dueTotal);

        if (dueTotal >= 0 ){
            // $('#createBtn').hide();
            document.getElementById("dueBtn").disabled = false;
        }else{
            // $('#createBtn').show();
            document.getElementById("dueBtn").disabled = true;
        }

    }
    // Update row total + grand total
    $(document).on('input', 'input[name="qty[]"], input[name="price[]"]', function () {

        let row   = $(this).closest('tr');
        let qty   = row.find('input[name="qty[]"]').val() || 0;
        let price = row.find('input[name="price[]"]').val() || 0;

        row.find('input[name="total_price[]"]').val(qty * price);

        calculateGrandTotal();
    });
    // Initial load
    $(document).ready(function () {
        // calculateGrandTotal();
    });
</script>