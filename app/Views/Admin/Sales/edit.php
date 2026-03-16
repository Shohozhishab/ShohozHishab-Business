
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
                                <?php $i=0; foreach ($invoiceItem as $item){ ?>
                                    <tr>
                                        <td><?= ++$i;?></td>
                                        <td><?php
                                            $catId =  get_data_by_id('prod_cat_id','products','prod_id',$item->prod_id);
                                            $parent_pro_cat = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$catId);
                                            $category = get_data_by_id('product_category','product_category','prod_cat_id',$parent_pro_cat);
                                            $subCategory = get_data_by_id('product_category','product_category','prod_cat_id',$catId);
                                            $productName =  get_data_by_id('name','products','prod_id',$item->prod_id);
                                            $unit =  get_data_by_id('unit','products','prod_id',$item->prod_id);

                                            echo $productName.'<br> <small>('.$category.' > '.$subCategory .')</small>';
                                            ?></td>
                                        <td>
                                            <input type="hidden" name="prod_id[]" value="<?= $item->prod_id;?>">
                                            <input type="hidden" name="inv_item[]" value="<?= $item->inv_item;?>">
                                            <input type="hidden" name="price[]" value="<?= $item->price;?>">
                                            <?= showWithCurrencySymbol($item->price);?>
                                        </td>
                                        <td><input type="number" name="qty[]" value="<?= $item->quantity;?>" class="qty"></td>
                                        <td>
                                            <input type="hidden" name="total[]" value="<?= $item->final_price;?>" class="totalVal">
                                            <span class="rowTotal"><?= showWithCurrencySymbol($item->total_price);?></span>
                                        </td>

                                        <td><input type="text" name="discount[]" value="<?= $item->discount;?>" class="discount"></td>
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
                                    <label>Customer</label>
                                    <div class="panel with-nav-tabs panel-default nav-tabs-custom"
                                         style="background-color: #e8b96f; border-color: ;" >
                                        <ul class="nav nav-tabs" >
                                            <li class="<?= !empty($invoice->customer_id)?'active':'';?>"><a href="#existing" data-toggle="tab">Existing Customer</a></li>
                                            <li class="<?= !empty($invoice->customer_id)?'':'active';?>"><a href="#new" data-toggle="tab">New Customer</a></li>
                                        </ul>
                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade <?= !empty($invoice->customer_id)?'active':'';?> in" id="existing">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <select class="form-control" onchange="createBtnShow(),customerBalanceShow(this.value)" style=" width: 100%;" name="customer_id"
                                                                    id="cus">

                                                                <option value="">Please Select</option>
                                                                <?php echo getAllListInOptionWithStatus($invoice->customer_id, 'customer_id', 'customer_name', 'customers','customer_name'); ?>
                                                            </select>
                                                            <span id="balance"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade <?= !empty($invoice->customer_id)?'':'active';?> in" id="new">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <input type="text" class="form-control " name="name" id="name"
                                                                   placeholder="Name" value=""/>
                                                        </div>
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
                                    <button style="float: right;" id="btn" type="submit"
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

    function calculateAll() {

        let itemsTotal = 0;

        /* =========================
           1️⃣ ROW CALCULATION
        ==========================*/
        $('tbody tr').each(function () {
            let row = $(this);

            let price    = parseFloat(row.find('input[name="price[]"]').val()) || 0;
            let qty      = parseFloat(row.find('.qty').val()) || 0;
            let discount = parseFloat(row.find('.discount').val()) || 0;

            let total = price * qty;
            let subTotal = total - (total * discount / 100);

            row.find('.rowTotal').text('৳ ' + total.toFixed(2) + ' /-');
            row.find('.totalVal').val(total.toFixed(2));
            row.find('.subTotalVal').val(subTotal.toFixed(2));
            row.find('.subTotal').text('৳ ' + subTotal.toFixed(2) + ' /-');

            itemsTotal += subTotal;
        });

        /* =========================
           2️⃣ SALE DISCOUNT
        ==========================*/
        let saleDisc = parseFloat($('#saleDisc').val()) || 0;
        let saleDiscAmount = (itemsTotal * saleDisc) / 100;
        let afterDiscount = itemsTotal - saleDiscAmount;

        $('#saleDiscshow').val(saleDiscAmount.toFixed(2));
        $('#granddiscountlast').val(saleDiscAmount.toFixed(2));

        /* =========================
           3️⃣ VAT
        ==========================*/
        let vat = parseFloat($('#vat').val()) || 0;
        let vatAmount = (afterDiscount * vat) / 100;

        $('#vatAmount').val(vatAmount.toFixed(2));
        $('#vatTotallast').val(vatAmount.toFixed(2));

        /* =========================
           4️⃣ GRAND TOTAL
        ==========================*/
        let grandTotal = afterDiscount + vatAmount;

        $('#grandtotal').val(grandTotal.toFixed(2));
        $('#grandtotal2').val(itemsTotal.toFixed(2));
        $('#grandtotallast').val(grandTotal.toFixed(2));

        /* =========================
           5️⃣ PAYMENT & DUE
        ==========================*/
        let cash   = parseFloat($('#nagod').val()) || 0;
        let bank   = parseFloat($('#bankAmount').val()) || 0;
        let cheque = parseFloat($('#chequeAmount').val()) || 0;

        let paid = cash + bank + cheque;
        let due = grandTotal - paid;

        $('#grandtotaldue').val(due.toFixed(2));

        /* =========================
           6️⃣ VALIDATION
        ==========================*/
        if (due < 0) {
            $('#btn').prop('disabled', true);
            $('#mess').text('Over payment not allowed').css('color', 'red');
        } else {
            $('#btn').prop('disabled', false);
            $('#mess').text('');
        }
    }

    /* =========================
       🔥 AUTO UPDATE EVENTS
    ==========================*/
    $(document).on('input change',
        '.qty, .discount, #saleDisc, #vat, #nagod, #bankAmount, #chequeAmount',
        function () {
            calculateAll();
        });

    /* =========================
       INITIAL LOAD
    ==========================*/
    $(document).ready(function () {
        calculateAll();
    });


//     function calculateFinal() {
//
//         /* 1️⃣ Item Subtotal Sum */
//         let itemTotal = 0;
//         $('.subTotalVal').each(function () {
//             itemTotal += parseFloat($(this).val()) || 0;
//         });
//
//         /* 2️⃣ Sale Discount */
//         let saleDisc = parseFloat($('#saleDisc').val()) || 0;
//         let saleDiscAmount = (itemTotal * saleDisc) / 100;
//         let afterDiscount = itemTotal - saleDiscAmount;
//
//         $('#saleDiscshow').val(saleDiscAmount.toFixed(2));
//         $('#granddiscountlast').val(saleDiscAmount.toFixed(2));
//
//         /* 3️⃣ VAT */
//         let vat = parseFloat($('#vat').val()) || 0;
//         let vatAmount = (afterDiscount * vat) / 100;
//
//         $('#vatAmount').val(vatAmount.toFixed(2));
//         $('#vatTotallast').val(vatAmount.toFixed(2));
//
//         /* 4️⃣ Grand Total */
//         let grandTotal = afterDiscount + vatAmount;
//
//         $('#grandtotal').val(grandTotal.toFixed(2));
//         $('#grandtotal2').val(grandTotal.toFixed(2));
//         $('#grandtotallast').val(grandTotal.toFixed(2));
//
//         /* 5️⃣ Payments */
//         let cash   = parseFloat($('#nagod').val()) || 0;
//         let bank   = parseFloat($('#bankAmount').val()) || 0;
//         let cheque = parseFloat($('#chequeAmount').val()) || 0;
//
//         let paidTotal = cash + bank + cheque;
//
//         /* 6️⃣ Due */
//         let due = grandTotal - paidTotal;
//         $('#grandtotaldue').val(due.toFixed(2));
//
//         /* Button disable if overpaid */
//         if (due < 0) {
//             $('#btn').prop('disabled', true);
//             $('#mess').text('Over payment not allowed').css('color','red');
//         } else {
//             $('#btn').prop('disabled', false);
//             $('#mess').text('');
//         }
//     }
//
//     /* 🔥 Trigger recalculation */
//     $(document).on('input', `
//     .qty, .discount,
//     #saleDisc, #vat,
//     #nagod, #bankAmount, #chequeAmount
// `, function () {
//         calculateFinal();
//     });
//
//     /* Initial load */
//     $(document).ready(function () {
//         calculateFinal();
//     });
</script>

<script>
    // function updateRow(row) {
    //     let price    = parseFloat(row.find('input[name="price[]"]').val()) || 0;
    //     let qty      = parseFloat(row.find('.qty').val()) || 0;
    //     let discount = parseFloat(row.find('.discount').val()) || 0;
    //
    //     let total = price * qty;
    //     let subTotal = total - (total * discount / 100);
    //
    //     row.find('.rowTotal').text('৳ ' + total.toFixed(2) + ' /-');
    //     row.find('.totalVal').text(total.toFixed(2));
    //     row.find('.subTotalVal').val(subTotal.toFixed(2));
    //     row.find('.subTotal').text('৳ ' + subTotal.toFixed(2) + ' /-');
    // }
    //
    // function updateGrandTotal() {
    //     let grandTotal = 0;
    //
    //     $('.subTotalVal').each(function () {
    //         grandTotal += parseFloat($(this).val()) || 0;
    //     });
    //
    //     $('#grandtotal').val(grandTotal.toFixed(2));
    // }
    //
    // /* 🔥 Update on qty or discount */
    // $(document).on('input', '.qty, .discount', function () {
    //     let row = $(this).closest('tr');
    //     updateRow(row);
    //     updateGrandTotal();
    // });
    //
    // /* Initial load */
    // $(document).ready(function () {
    //     $('tbody tr').each(function () {
    //         updateRow($(this));
    //     });
    //     updateGrandTotal();
    // });
</script>