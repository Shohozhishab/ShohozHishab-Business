
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="box-title">Sales Item</h3>
                        </div>
                        <div class="col-lg-12" style="margin-top: 20px;"></div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form action="<?= base_url('Admin/Sales/salesEdiAction')?>" method="post">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product</th>
                                        <th>Price </th>
                                        <th>Quantity </th>
                                        <th>Total </th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i=0; foreach ($invoiceItem as $item){
                                    $jsonArray = get_data_by_id('sale_units', 'products', 'prod_id', $item->prod_id);
                                    ?>
                                    <tr>
                                        <td><?= ++$i;?></td>
                                        <td><?php
                                            $catId =  get_data_by_id('prod_cat_id','products','prod_id',$item->prod_id);
                                            $parent_pro_cat = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$catId);
                                            $category = get_data_by_id('product_category','product_category','prod_cat_id',$parent_pro_cat);
                                            $subCategory = get_data_by_id('product_category','product_category','prod_cat_id',$catId);
                                            $productName =  get_data_by_id('name','products','prod_id',$item->prod_id);
                                            $unit =  productIdByDefaultStoreUnit($item->prod_id);

                                            echo $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>';
                                            ?></td>
                                        <td>
                                            <input type="hidden" name="prod_id[]" value="<?= $item->prod_id;?>">
                                            <input type="hidden" name="inv_item[]" value="<?= $item->inv_item;?>">
                                            <input type="hidden" name="price[]" value="<?= $item->price;?>">
                                            <label for="int" class="text-capitalize"><?= showUnitName($unit)?></label><?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit,$item->price));?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="qty[]" id="qtyUp_<?= $item->prod_id; ?>" value="<?= $item->quantity;?>" class="qty">
                                            <div style="display: flex;">
                                                <?php $unitsArray = convertRevertToArray($item->quantity,json_decode($jsonArray));
                                                foreach ($unitsArray as $input){
                                                    ?>
                                                    <div style="margin-right: 5px;">
                                                        <label for="int" class="text-capitalize"><?= $input['name'];?></label>
                                                        <input type="text" oninput="qtyMakeBaseUnit('<?= $item->prod_id ?>')" data-factor="<?= $input['conversion_factor']?>"  style="width: 50px;" value="<?= $input['qty']; ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>



                                        </td>
                                        <td>
                                            <input type="hidden" name="total[]" value="<?= $item->final_price;?>" class="totalVal">
                                            <span ><?= showWithCurrencySymbol($item->final_price);?></span>
                                        </td>

                                        <td><input type="text" name="discount[]" style="width: 100px;" value="<?= $item->discount;?>" class="discount"></td>
                                        <td>
                                            <input type="hidden" name="subTotal[]" value="<?= $item->final_price;?>" class="subTotalVal">
                                            <span id="subTotal" class="subTotal"><?= showWithCurrencySymbol($item->final_price);?></span>
                                        </td>

                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6" ></div>
                            <div class="col-md-6" style="background-color: #e8b96f;padding: 10px;">
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                                    <div class="panel with-nav-tabs panel-default nav-tabs-custom"
                                         style="background-color: #e8b96f; border-color: ;" >

                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <?php if (!empty($invoice->customer_id)){ ?>
                                                            <input type="hidden" name="customer_id" value="<?= $invoice->customer_id; ?>">
                                                            <p><b>Customer:</b> <?= get_data_by_id('customer_name','customers','customer_id',$invoice->customer_id)?></p>
                                                        <?php }else{ ?>
                                                            <input type="text" class="form-control" name="name"   value="<?= $invoice->customer_name; ?>"/>
                                                            <p><b>Customer:</b> <?= $invoice->customer_name; ?></p>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">

                                    <label>Entire Sale Discount: %</label>

                                    <input type="number" step=any class="form-control saleDisc" oninput="minusValueCheck(this.value,this)" name="saleDisc" id="saleDisc" placeholder="Input Discount %" value="<?= $invoice->entire_sale_discount ?>">
                                    <input type="hidden" class="form-control totalamount" name="total" id="totalamount" readonly value="<?= $invoice->final_amount ?>">
                                    <!--  </div> -->
                                </div>
                                <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px; ">
                                    <label>Discount Amount</label>
                                    <input type="text" class="form-control " name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" readonly>
                                    <input type="hidden" name="granddiscountlast" id="granddiscountlast">
                                </div>
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px; ">
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat: %</label>
                                        <input type="number" step=any class="form-control vat" oninput="minusValueCheck(this.value,this)" name="vat" id="vat" placeholder="vat %" value="<?= $invoice->vat ?>">

                                        <input type="hidden" class="form-control vatTotallast" name="vatTotallast" id="vatTotallast" readonly value="<?= $invoice->final_amount ?>">
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat Amount</label>
                                        <input type="text" onchange="checkBankId()" class="form-control vatAmount" name="vatAmount" id="vatAmount" placeholder="Vat Amount" readonly>
                                    </div>
                                </div>
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px; ">

                                    <div class="col-xs-12" style="padding:5px;">
                                        <label>Grand Total</label>
                                        <input type="hidden" class="form-control" name="grandtotal2" readonly id="grandtotal2" value="<?= $invoice->amount ?>">

                                        <input type="text" class="form-control" name="grandtotal" readonly id="grandtotal" value="<?= $invoice->final_amount ?>">

                                    </div>

                                    <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Payment</label>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Cash</label>
                                            <input type="number" step=any class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod" placeholder="Input Cash Amount" value="<?= $invoice->nagad_paid?>">
                                        </div>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Bank</label>
                                                <select class="form-control" name="bank_id" id="bank_id">
                                                    <option value="">Select Bank</option>
                                                    <?php echo getTwoValueInOption($invoice->bank_id, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Bank Amount</label>
                                                <input type="number" step=any onchange="checkBankId()" class="form-control bankAmount"
                                                       name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this)" placeholder="input Bank Amount" value="<?= $invoice->bank_paid?>">
                                                <b id="Bank_valid"></b>
                                            </div>
                                        </div>
                                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Cheque No</label>
                                                <input type="text" class="form-control" name="chequeNo" id="chequeNo"
                                                       placeholder="Input Cheque No " value="<?= $invoice->chaque_id?>">
                                            </div>
                                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                                <label>Cheque Amount</label>
                                                <input type="number" step=any onchange="cheque()" class="form-control chequeAmount"
                                                       name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount " value="<?= $invoice->chaque_paid?>">
                                                <b id="cheque_valid"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                        <label>Total Amount </label>
                                        <input type="text"  class="form-control " name="grandtotallast" readonly
                                               id="grandtotallast" value="<?= number_format($invoice->final_amount) ?>">

                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8; padding:5px;">
                                        <label>Total Due</label>
                                        <input type="number" step=any class="form-control" name="grandtotaldue" readonly id="grandtotaldue"
                                               value="<?= $invoice->due ?>">
                                    </div>
                                </div>
                                <div class="col-xs-12" style="padding:20px; ">
                                    <input type="hidden"  name="invoice_id"  value="<?= $invoice->invoice_id ?>">
                                    <button style="float: right;" id="dueBtn" type="submit"
                                            class="btn btn-primary">Sale</button>
                                    <b id="mess"></b>
                                </div>
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

    function calculate() {
        let grandTotal = 0;

        $("tbody tr").each(function () {

            let price = parseFloat($(this).find("input[name='price[]']").val()) || 0;
            let qty = parseFloat($(this).find("input[name='qty[]']").val()) || 0;
            let discount = parseFloat($(this).find("input[name='discount[]']").val()) || 0;

            let total = price * qty;
            $(this).find("input[name='total[]']").val(total.toFixed(8));

            let subTotal = Math.max(total - discount, 0);

            $(this).find("input[name='subTotal[]']").val(subTotal.toFixed(8));
            $(this).find(".subTotal").text("৳ " + subTotal.toFixed(8));

            grandTotal += subTotal;
        });

        let saleDisc = parseFloat($("#saleDisc").val()) || 0;
        let saleDiscAmount = grandTotal * saleDisc / 100;

        $("#saleDiscshow").val(saleDiscAmount.toFixed(8));

        let afterDiscount = grandTotal - saleDiscAmount;

        let vat = parseFloat($("#vat").val()) || 0;
        let vatAmount = afterDiscount * vat / 100;

        $("#vatAmount").val(vatAmount.toFixed(8));

        let finalTotal = afterDiscount + vatAmount;

        $("#grandtotal").val(finalTotal.toFixed(8));
        $("#grandtotal2").val(finalTotal.toFixed(8));

        let paid =
            (parseFloat($("#nagod").val()) || 0) +
            (parseFloat($("#bankAmount").val()) || 0) +
            (parseFloat($("#chequeAmount").val()) || 0);

        if (finalTotal - paid < 0) {
            $('#dueBtn').prop('disabled', true);
        } else {
            $('#dueBtn').prop('disabled', false);
        }

        $("#grandtotaldue").val((finalTotal - paid).toFixed(8));
    }

    // Global function
    window.qtyMakeBaseUnit = function (printId) {
        let row = $("#qtyUp_" + printId).closest("td");
        let total = 0;
        row.find("input[data-factor]").each(function () {
            let factor = parseFloat($(this).data("factor")) || 0;
            let qty = parseFloat($(this).val()) || 0;
            total += qty * factor;
        });
        $("#qtyUp_" + printId).val(total.toFixed(8));

        calculate();
    };

    $(function () {
        $(document).on(
            "keyup change",
            "input[name='qty[]'], input[name='discount[]'], #saleDisc, #vat, #nagod, #bankAmount, #chequeAmount",
            calculate
        );

        calculate();
    });

</script>