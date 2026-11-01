<?php
$editCurSymbol = !empty($editCurrencySymbol) ? $editCurrencySymbol : '৳ ';
$saleEditIndex = 0;

// Seed the totals panel with the stored invoice values so VAT / discount
// amounts are visible even before the live JS recalculation runs.
$editAmountBase      = (float)$invoice->amount;                                  // Σ after line discount
$editLineDiscSum     = 0.0;
foreach ($invoiceItem as $it) {
    $editLineDiscSum += (float)$it->total_price - (float)$it->final_price;
}
$editSaleDiscPct     = (float)$invoice->entire_sale_discount;
$editSaleDiscAmount  = $editAmountBase * $editSaleDiscPct / 100;
$editAfterDisc       = $editAmountBase - $editSaleDiscAmount;
$editVatPct          = (float)$invoice->vat;
$editVatAmount       = $editAfterDisc * $editVatPct / 100;
?>
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
                    <form action="<?= base_url('Admin/Sales/salesEdiAction')?>" method="post" id="saleEditForm">
                        <div class="col-md-12">
                            <div class="table-responsive" id="TFtable" style="max-height:65vh;overflow-y:auto;">
                                <table class="table table-bordered table-condensed" style="min-width:1100px;">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">No</th>
                                            <th style="min-width:160px;">Product</th>
                                            <th style="min-width:110px;">Price</th>
                                            <th style="min-width:170px;">Quantity</th>
                                            <th style="min-width:110px;">Sale Discount (%)</th>
                                            <th style="min-width:110px;">Total</th>
                                            <th style="min-width:220px;">Serial</th>
                                            <th style="min-width:150px;">Customer Warranty End</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($invoiceItem as $item):
                                    $jsonArray = get_data_by_id('sale_units', 'products', 'prod_id', $item->prod_id);
                                    $catId = get_data_by_id('prod_cat_id', 'products', 'prod_id', $item->prod_id);
                                    $parent_pro_cat = get_data_by_id('parent_pro_cat', 'product_category', 'prod_cat_id', $catId);
                                    $category = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $parent_pro_cat);
                                    $subCategory = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $catId);
                                    $productName = get_data_by_id('name', 'products', 'prod_id', $item->prod_id);
                                    $unit = productIdByDefaultStoreUnit($item->prod_id);

                                    $dataPsr = htmlspecialchars((string)$item->product_stock_relation_id, ENT_QUOTES, 'UTF-8');
                                    $dataName = htmlspecialchars((string)$productName, ENT_QUOTES, 'UTF-8');
                                    $lineDisc = (float)$item->discount;
                                    $lineDiscAmount = (float)$item->total_price * $lineDisc / 100;

                                    // Serial / warranty tracking of this invoice line.
                                    $serialInfo = $serialMap[$item->inv_item] ?? array(
                                        'serial_status' => false,
                                        'warranty_status' => false,
                                        'serials' => array(),
                                        'warranty' => '',
                                        'available' => 0,
                                    );
                                    $serialOn = !empty($serialInfo['serial_status']);
                                    $warrantyOn = !empty($serialInfo['warranty_status']);
                                    $assignedSerials = $serialInfo['serials'];
                                    $savedWarranty = (string)$serialInfo['warranty'];
                                    $needQty = (int)round((float)$item->quantity);
                                    $savedIds = array_map(function ($row) {
                                        return (int)$row['product_serial_id'];
                                    }, $assignedSerials);
                                    $savedNumbers = array_map(function ($row) {
                                        return (string)$row['serial_number'];
                                    }, $assignedSerials);
                                    $serialComplete = (!$serialOn || count($savedIds) >= $needQty)
                                        && (!$warrantyOn || ($savedWarranty !== '' || !empty($savedIds)));
                                    $saleEditIndex++;
                                    ?>
                                    <tr class="product-card" data-psr="<?= $dataPsr; ?>" data-qty="<?= $needQty; ?>">
                                        <td>
                                            <?= $saleEditIndex ?>
                                            <input type="hidden" name="prod_id[]" value="<?= $item->prod_id;?>">
                                            <input type="hidden" name="product_stock_relation_id[]" value="<?= $item->product_stock_relation_id;?>">
                                            <input type="hidden" name="inv_item[]" value="<?= $item->inv_item;?>">
                                            <input type="hidden" name="price[]" value="<?= $item->price;?>">
                                            <input type="hidden" name="totalAm[]" value="<?= $item->total_price;?>" class="totalVal">
                                            <input type="hidden" name="qty[]" id="qtyUp_<?= $dataPsr; ?>" value="<?= $item->quantity;?>" class="qty">
                                            <input type="hidden" name="discount[]" value="<?= $item->discount;?>" class="discount">
                                            <input type="hidden" name="subTotal[]" value="<?= $item->final_price;?>" class="subTotalVal">
                                            <?php if ($serialOn) {
                                                // The serial numbers already sold on this line.
                                                foreach ($assignedSerials as $savedRow) {
                                                    $savedRowNum = htmlspecialchars((string)$savedRow['serial_number'], ENT_QUOTES, 'UTF-8');
                                                    $savedRowCw = htmlspecialchars((string)($savedRow['company_warranty_end_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                                    <input type="hidden" name="sale_serials[<?= $dataPsr; ?>][]" value="<?= (int)$savedRow['product_serial_id']; ?>" data-number="<?= $savedRowNum; ?>" data-company-warranty="<?= $savedRowCw; ?>">
                                            <?php } } ?>
                                            <?php if ($warrantyOn || $serialOn) { ?>
                                                <input type="hidden" class="sale-edit-warranty" name="sale_serial_warranty[<?= $dataPsr; ?>]" data-psr="<?= $dataPsr; ?>" value="<?= htmlspecialchars($savedWarranty, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <b><?= $dataName; ?></b>
                                            <br><small>(<?= htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8'); ?> &gt; <?= htmlspecialchars((string)$subCategory, ENT_QUOTES, 'UTF-8'); ?>)</small>
                                        
                                            <?php if ($serialOn || $warrantyOn) { ?>
                                                <br>
                                                <?php if ($serialOn) { ?><span class="label label-primary">Serial: ON</span><?php } ?>
                                                <?php if ($warrantyOn) { ?><span class="label label-warning">Warranty: ON</span><?php } ?>
                                            <?php } ?>
                                            
                                        </td>
                                        <td style="white-space:nowrap;">
                                            <?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit, $item->price)); ?>
                                        </td>
                                        <td>
                                            <div style="display:flex;flex-wrap:wrap;">
                                                <?php $unitsArray = convertRevertToArray($item->quantity, json_decode($jsonArray));
                                                foreach ($unitsArray as $input){ ?>
                                                    <div style="margin-right:8px;">
                                                        <label class="text-capitalize" style="font-weight:normal;margin-right:4px;"><?= $input['name'];?></label>
                                                        <input type="text" class="unit-qty" style="width:60px;"
                                                               oninput="qtyMakeBaseUnit('<?= $dataPsr ?>')"
                                                               data-factor="<?= $input['conversion_factor']?>" value="<?= $input['qty']; ?>">
                                                    </div>
                                                <?php } ?>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control input-sm sale-line-disc"
                                                   style="max-width:90px;display:inline-block;"
                                                   oninput="saleEditLineDisc(this)"
                                                   placeholder="Discount %" value="<?= $lineDisc; ?>">
                                            <small class="text-muted" id="lineDiscAmount_<?= $dataPsr; ?>">− <?= number_format($lineDiscAmount, 2); ?></small>
                                        </td>
                                        <td><span class="subTotal"><?= showWithCurrencySymbol($item->final_price); ?></span></td>
                                        <td>
                                            <?php if ($serialOn) { ?>
                                                <div class="sale-edit-serial-summary" id="saleSerialSummary_<?= $dataPsr; ?>" style="margin-bottom:4px;">
                                                    <small>Serial (<?= count($savedIds); ?>/<?= $needQty; ?>):
                                                        <?php if (!empty($savedNumbers)) { ?>
                                                            <b><?= htmlspecialchars(implode(', ', $savedNumbers), ENT_QUOTES, 'UTF-8'); ?></b>
                                                        <?php } else { ?>
                                                            <span class="text-danger">not added</span>
                                                        <?php } ?>
                                                    </small>
                                                    <br><small class="text-muted">In stock serials: <?= (int)$serialInfo['available']; ?></small>
                                                </div>
                                                <button type="button" class="btn btn-info btn-xs add-serial-btn"
                                                        onclick="saleEditOpenSerialModal(this)"
                                                        data-psr="<?= $dataPsr; ?>"
                                                        data-qty="<?= $needQty; ?>"
                                                        data-serial-status="<?= $serialOn ? '1' : '0'; ?>"
                                                        data-warranty-status="<?= $warrantyOn ? '1' : '0'; ?>"
                                                        data-product-name="<?= $dataName; ?>"
                                                        data-saved="<?= $serialComplete ? '1' : '0'; ?>">
                                                    <i class="fa fa-barcode"></i> Edit Serial
                                                </button>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($warrantyOn) { ?>
                                                <input type="date" class="form-control input-sm sale-edit-warranty-input"
                                                       style="max-width:150px;display:inline-block;"
                                                       value="<?= htmlspecialchars($savedWarranty, ENT_QUOTES, 'UTF-8'); ?>"
                                                       onchange="saleEditWarrantyChanged(this)">
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6" ></div>
                            <div class="col-md-6" style="background-color: #e8b96f;padding: 10px;">
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                                    <div class="panel with-nav-tabs panel-default nav-tabs-custom"
                                         style="background-color: #e8b96f;" >

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
                                    <input type="hidden" class="form-control totalamount" name="total" id="totalamount" readonly value="<?= $editAmountBase ?>">
                                    <!--  </div> -->
                                </div>
                                <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                    <label>Discount Amount</label>
                                    <input type="text" class="form-control " name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" readonly value="<?= $editSaleDiscAmount !== 0.0 ? $editSaleDiscAmount : '' ?>">
                                    <input type="hidden" name="granddiscountlast" id="granddiscountlast" value="<?= $editLineDiscSum !== 0.0 ? $editLineDiscSum : '' ?>">
                                </div>
                                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px; ">
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat: %</label>
                                        <input type="number" step=any class="form-control vat" oninput="minusValueCheck(this.value,this)" name="vat" id="vat" placeholder="vat %" value="<?= $invoice->vat ?>">

                                        <input type="hidden" class="form-control vatTotallast" name="vatTotallast" id="vatTotallast" readonly value="<?= $editAfterDisc ?>">
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Vat Amount</label>
                                        <input type="text" onchange="checkBankId()" class="form-control vatAmount" name="vatAmount" id="vatAmount" placeholder="Vat Amount" readonly value="<?= $editVatAmount !== 0.0 ? $editVatAmount : '' ?>">
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
                                            <input type="number" step=any class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod" placeholder="Input Cash Amount" value="<?= (float)$invoice->nagad_paid ?>">
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
                                                       name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this)" placeholder="input Bank Amount" value="<?= (float)$invoice->bank_paid?>">
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
                                                       name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount " value="<?= (float)$invoice->chaque_paid?>">
                                                <b id="cheque_valid"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                                        <label>Total Amount </label>
                                        <input type="text"  class="form-control " name="grandtotallast" readonly
                                               id="grandtotallast" value="<?= (float)$invoice->final_amount ?>">

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
                                            class="btn btn-primary">Update</button>
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

<!-- Serial / Warranty picker modal for the sale edit screen (start) -->
<style>
    /* The edit screen is itself shown inside #modal-default (z-index 1050), so this
       picker modal is a nested modal and must stack above the parent modal. */
    #saleEditSerialModal { z-index: 1060; }
    #saleEditSerialModal .modal-dialog { margin: 60px auto; }
    #saleEditSerialModal .modal-content { border: none; border-radius: 5px; box-shadow: 0 8px 30px rgba(0, 0, 0, .35); }
    #saleEditSerialModal .modal-header { background: #3c8dbc; color: #fff; border-radius: 5px 5px 0 0; }
    #saleEditSerialModal .modal-header .close { color: #fff; opacity: .85; text-shadow: none; }
    #saleEditSerialModal .modal-header .modal-title { font-weight: 600; }
    #saleEditSerialModal .modal-header .modal-title small { color: #e6f2fb; font-weight: normal; }
    .sale-serial-stat { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
    .sale-serial-stat .stat { flex: 1 1 90px; background: #f4f8fb; border: 1px solid #e3ecf3; border-radius: 4px; padding: 8px 10px; text-align: center; }
    .sale-serial-stat .stat .num { display: block; font-size: 18px; font-weight: 700; color: #3c8dbc; line-height: 1.2; }
    .sale-serial-stat .stat .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #7a8a99; }
    .sale-serial-stat .stat.is-selected .num { color: #00a65a; }
    #saleEditSerialModal .table > thead > tr > th { background: #f7f9fb; }
    #saleEditSerialModal .table > tbody > tr > td { vertical-align: middle; }
    #saleEditSerialModal .well { background: #fbfcfd; border: 1px solid #eef2f5; border-radius: 4px; padding: 12px 14px; }
    #saleEditSerialModal .sale-serial-empty { text-align: center; color: #9aa7b2; padding: 22px 0; }
    @media (max-width: 767px) { #saleEditSerialModal .modal-dialog { margin: 20px 10px; } }
</style>
<div class="modal fade sale-edit-serial-modal" id="saleEditSerialModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-barcode"></i> Serial Numbers &amp; Warranty <small id="saleEditSerialProduct"></small></h4>
            </div>
            <div class="modal-body">
                <div id="saleEditSerialWrap">
                    <div class="sale-serial-stat">
                        <div class="stat"><span class="num" id="saleEditSerialQty">0</span><span class="lbl">Quantity</span></div>
                        <div class="stat"><span class="num" id="saleEditSerialAvailable">0</span><span class="lbl">Available</span></div>
                        <div class="stat is-selected"><span class="num" id="saleEditSerialSelected">0 / 0</span><span class="lbl">Selected</span></div>
                    </div>
                    <p id="saleEditSerialHint" class="text-danger" style="display:none;margin:-6px 0 12px;"><i class="fa fa-exclamation-circle"></i> <span></span></p>
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-xs-7">
                            <input type="text" class="form-control input-sm sale-serial-filter" id="saleEditSerialFilter"
                                   placeholder="Search serial number or warranty date (YYYY-MM-DD)"
                                   onkeypress="return saleEditSerialFilterKey(event)">
                        </div>
                        <div class="col-xs-5 text-right">
                            <button type="button" class="btn btn-default btn-sm" onclick="saleEditSerialLoad()"><i class="fa fa-search"></i> Filter</button>
                            <button type="button" class="btn btn-link btn-sm" onclick="saleEditToggleAll(null, 'all')">Select all</button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                        <table class="table table-condensed table-bordered table-hover" style="margin-bottom:0;">
                            <thead>
                            <tr>
                                <th style="width:36px;text-align:center;"><input type="checkbox" id="saleEditSerialCheckAll" onclick="saleEditToggleAll(this)"></th>
                                <th>Serial Number</th>
                                <th style="width:200px;">Company Warranty End Date</th>
                            </tr>
                            </thead>
                            <tbody id="saleEditSerialRows"></tbody>
                        </table>
                    </div>
                </div>

                <div class="well" id="saleEditWarrantyWrap" style="margin-top:15px;">
                    <label for="saleEditWarrantyDate" style="margin-bottom:6px;"><i class="fa fa-calendar"></i> Customer Warranty End Date</label>
                    <input type="date" class="form-control" id="saleEditWarrantyDate" onchange="saleEditModalWarrantyChanged(this)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-success" onclick="saleEditSaveSerials()"><i class="fa fa-check"></i> Save Selection</button>
            </div>
        </div>
    </div>
</div>
<!-- Serial / Warranty picker modal for the sale edit screen (end) -->

<script>
    var SALE_EDIT_SERIAL_LIST_URL = '<?php echo site_url('Admin/Sales/edit_serial_list'); ?>';
    var SALE_EDIT_INVOICE_ID = '<?= (int)$invoice->invoice_id; ?>';
    var SALE_EDIT_CURRENCY = '<?= htmlspecialchars($editCurSymbol, ENT_QUOTES, 'UTF-8'); ?>';

    function saleEditEscapeHtml(value) {
        return jQuery('<i>').text(value == null ? '' : String(value)).html();
    }

    function saleEditCurrency() {
        return SALE_EDIT_CURRENCY || '৳ ';
    }

    // Recalculate the whole invoice from the product cards.
    function calculate() {
        var grandTotal = 0;
        var lineDiscountTotal = 0;

        jQuery('#TFtable .product-card').each(function () {
            var $card = jQuery(this);
            var price = parseFloat($card.find("input[name='price[]']").val()) || 0;
            var qty = parseFloat($card.find("input[name='qty[]']").val()) || 0;
            var percent = parseFloat($card.find('.sale-line-disc').val()) || 0;

            var total = price * qty;
            var discountAmount = total * percent / 100;
            var subTotal = Math.max(total - discountAmount, 0);

            // totalAm[] / subTotal[] are the plain values the controller stores.
            $card.find("input[name='totalAm[]']").val(total.toFixed(8));
            $card.find("input[name='discount[]']").val(percent);
            $card.find("input[name='subTotal[]']").val(subTotal.toFixed(8));

            $card.find('.subTotal').text(subTotal !== 0 ? saleEditCurrency() + subTotal : '');
            $card.find('#lineDiscAmount_' + $card.data('psr')).text('− ' + discountAmount.toFixed(2));

            // Serialized lines need one serial number per unit.
            var $btn = $card.find('.add-serial-btn');
            if ($btn.length && String($btn.attr('data-serial-status')) === '1') {
                $btn.attr('data-qty', Math.round(qty));
                saleEditRefreshSelected($card);
            }

            grandTotal += subTotal;
            lineDiscountTotal += discountAmount;
        });

        // Store the line discount amount, the controller expects an amount here.
        jQuery('#granddiscountlast').val(lineDiscountTotal !== 0 ? lineDiscountTotal : '');

        // grandTotal is already after each line discount (subTotal = total - line discount),
        // so it is the base for the entire sale discount and VAT (same rule as the create page).
        var totalAfterLineDiscount = grandTotal;
        jQuery('#totalamount').val(totalAfterLineDiscount);
        jQuery('#vatTotallast').val(totalAfterLineDiscount);

        var saleDisc = parseFloat(jQuery('#saleDisc').val()) || 0;
        var saleDiscAmount = totalAfterLineDiscount * saleDisc / 100;
        jQuery('#saleDiscshow').val(saleDiscAmount !== 0 ? saleDiscAmount : '');

        var afterDiscount = totalAfterLineDiscount - saleDiscAmount;

        var vat = parseFloat(jQuery('#vat').val()) || 0;
        var vatAmount = afterDiscount * vat / 100;
        jQuery('#vatAmount').val(vatAmount !== 0 ? vatAmount : '');

        var finalTotal = afterDiscount + vatAmount;

        // grandtotal2 = invoice.amount, the subtotal after line discount but
        // before the entire sale discount and VAT (same rule as the create page).
        jQuery('#grandtotal2').val(totalAfterLineDiscount !== 0 ? totalAfterLineDiscount : '');
        jQuery('#grandtotal').val(finalTotal !== 0 ? finalTotal : '');
        jQuery('#grandtotallast').val(finalTotal !== 0 ? finalTotal : '');

        var paid = (parseFloat(jQuery('#nagod').val()) || 0)
            + (parseFloat(jQuery('#bankAmount').val()) || 0)
            + (parseFloat(jQuery('#chequeAmount').val()) || 0);

        var totaldue = finalTotal - paid;
        jQuery('#grandtotaldue').val(totaldue !== 0 ? totaldue : '');
        jQuery('#dueBtn').prop('disabled', totaldue < 0);
    }

    // Line discount percentage changed on a card.
    function saleEditLineDisc(el) {
        var value = parseFloat(jQuery(el).val());
        if (isNaN(value) || value < 0) {
            jQuery(el).val(0);
        }
        calculate();
    }

    // Unit quantity inputs of a card add up to the base unit quantity.
    window.qtyMakeBaseUnit = function (psrId) {
        var $card = jQuery('#TFtable .product-card[data-psr="' + psrId + '"]');
        var total = 0;
        $card.find("input[data-factor]").each(function () {
            var factor = parseFloat(jQuery(this).data("factor")) || 0;
            var qty = parseFloat(jQuery(this).val()) || 0;
            total += qty * factor;
        });
        $card.find("input[name='qty[]']").val(total.toFixed(8));

        calculate();
    };

    // The edit screen is injected into #modal-default, so this picker is a nested
    // modal. It stacks above the parent (see the CSS) and the handlers below keep
    // the parent modal usable after the picker closes.
    function saleEditModal() {
        // Prefer the picker that is actually visible; a re-injected edit screen can leave a
        // second (empty) modal in the DOM, and reading checkboxes from the wrong one would
        // wipe the card's serials on Save Selection.
        var $visible = jQuery('.sale-edit-serial-modal.in');
        if ($visible.length) {
            return $visible.last();
        }
        return jQuery('.sale-edit-serial-modal').last();
    }

    if (!window.saleEditModalWired) {
        window.saleEditModalWired = true;
        jQuery(document)
            .on('shown.bs.modal', '.sale-edit-serial-modal', function () {
                // Dim the parent edit modal too, by lifting this modal's backdrop above it.
                jQuery('.modal-backdrop').last().css('z-index', 1055);
            })
            .on('hidden.bs.modal', '.sale-edit-serial-modal', function () {
                jQuery('.modal-backdrop').last().css('z-index', '');
                // Bootstrap clears the scroll lock on close; the outer edit modal still needs it.
                if (jQuery('#modal-default').hasClass('in')) {
                    jQuery('body').addClass('modal-open');
                }
            });
    }

    function saleEditCtxCard() {
        var $modal = saleEditModal();
        var $card = $modal.data('card');
        if ($card && $card.length) {
            // The edit screen is AJAX-injected into #formData. Re-injecting it detaches
            // the old cards, so modal.data('card') can point at a node that is no longer
            // inside <form>. Writes to that stale node never get submitted, which shows up
            // server side as sale_serials arriving empty. Always re-resolve the LIVE card
            // from #TFtable by its stock line so the hidden inputs land in the real form.
            var psr = String($card.data('psr'));
            var $live = jQuery('#TFtable .product-card[data-psr="' + psr + '"]').first();
            if ($live.length) {
                $modal.data('card', $live);
                return $live;
            }
        }
        return $card || jQuery();
    }

    function saleEditOpenSerialModal(el) {
        var $btn = jQuery(el);
        var $card = $btn.closest('.product-card');

        // The edit screen is injected into #modal-default, whose .modal-dialog carries a
        // CSS transform. That transform becomes the containing block for a fixed
        // descendant, so the picker must live directly under <body> to overlay correctly.
        // Reuse one instance: re-injecting the edit screen leaves a fresh copy in
        // #formData while a previous copy may already sit in <body> — keep one and drop the rest.
        var $pickers = jQuery('.sale-edit-serial-modal');
        var modal = $pickers.last();
        $pickers.not(modal).remove();
        if (modal.parent('body').length === 0) {
            modal.detach().appendTo('body');
        }

        modal.data('card', $card)
            .data('serial-status', String($btn.attr('data-serial-status')) === '1')
            .data('warranty-status', String($btn.attr('data-warranty-status')) === '1');

        modal.find('#saleEditSerialProduct').text('— ' + ($btn.data('product-name') || ''));
        modal.find('#saleEditSerialQty').text(Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0));
        modal.find('#saleEditSerialFilter').val('');
        modal.find('#saleEditSerialCheckAll').prop('checked', false);
        saleEditHideLimitHint(modal);
        modal.find('#saleEditWarrantyDate').val($card.find('.sale-edit-warranty').val() || $card.find('.sale-edit-warranty-input').val() || '');

        var warrantyOn = String($btn.attr('data-warranty-status')) === '1';
        modal.find('#saleEditWarrantyWrap').toggle(warrantyOn);
        modal.find('#saleEditSerialWrap').toggle(String($btn.attr('data-serial-status')) === '1');

        saleEditUpdateSelectedCount();
        modal.modal('show');

        if (String($btn.attr('data-serial-status')) === '1') {
            saleEditSerialLoad();
        }
    }

    // Customer warranty date changed straight on the card.
    function saleEditWarrantyChanged(el) {
        var $card = jQuery(el).closest('.product-card');
        var warranty = jQuery(el).val() || '';
        $card.find('.sale-edit-warranty').val(warranty);
        saleEditRefreshSelected($card);
    }

    function saleEditSerialFilterKey(event) {
        if (event.key === 'Enter') {
            saleEditSerialLoad();
            return false;
        }
        return true;
    }

    // Serial numbers of this stock line that other cards already claim.
    function saleEditCurrentSerials(exceptPsr) {
        var data = {};
        jQuery('#TFtable .product-card').each(function () {
            var psr = String(jQuery(this).data('psr'));
            if (psr === String(exceptPsr)) {
                return;
            }
            var key = 'current_serials[' + psr + '][]';
            data[key] = data[key] || [];
            jQuery(this).find('input[name="sale_serials[' + psr + '][]"]').each(function () {
                data[key].push(parseInt(jQuery(this).val(), 10));
            });
        });
        return data;
    }

    function saleEditSerialLoad() {
        var $card = saleEditCtxCard();
        if (!$card.length) {
            return;
        }
        var psr = $card.data('psr');
        var modal = saleEditModal();
        var rows = modal.find('#saleEditSerialRows');

        var postData = {
            product_stock_relation_id: psr,
            invoice_id: SALE_EDIT_INVOICE_ID,
            qty: parseFloat($card.find("input[name='qty[]']").val()) || 0,
            filter: modal.find('.sale-serial-filter').val() || '',
            customer_warranty_end_date: $card.find('.sale-edit-warranty').val() || ''
        };
        jQuery.extend(postData, saleEditCurrentSerials(psr));

        rows.html('<tr><td colspan="3" class="sale-serial-empty"><i class="fa fa-spinner fa-spin"></i> Loading serial numbers&hellip;</td></tr>');

        jQuery.post(SALE_EDIT_SERIAL_LIST_URL, postData, function (res) {
            if (!res || res.status != 1) {
                rows.html('<tr><td colspan="3" class="sale-serial-empty text-danger">'
                    + saleEditEscapeHtml(res && res.message ? res.message : 'Unable to load serial numbers.') + '</td></tr>');
                return;
            }

            var checkedIds = [];
            $card.find('input[name="sale_serials[' + psr + '][]"]').each(function () {
                checkedIds.push(parseInt(jQuery(this).val(), 10));
            });

            var html = '';
            jQuery.each(res.serials || [], function (i, serial) {
                var id = parseInt(serial.product_serial_id, 10);
                var checked = checkedIds.indexOf(id) > -1 ? ' checked' : '';
                var badge = String(serial.status) === 'in_stock' ? '' : ' <span class="label label-default">on this invoice</span>';
                html += '<tr>'
                    + '<td class="text-center"><input type="checkbox" class="sale-serial-check" value="' + id + '"'
                    + ' data-number="' + saleEditEscapeHtml(serial.serial_number) + '"'
                    + ' data-company-warranty="' + saleEditEscapeHtml(serial.company_warranty_end_date || '') + '"'
                    + checked + ' onchange="saleEditSerialCheckChange(this)"></td>'
                    + '<td>' + saleEditEscapeHtml(serial.serial_number) + badge + '</td>'
                    + '<td>' + saleEditEscapeHtml(serial.company_warranty_end_date || '-') + '</td>'
                    + '</tr>';
            });

            if (html === '') {
                html = '<tr><td colspan="3" class="sale-serial-empty text-warning">No serial number is available for this product.</td></tr>';
            }

            rows.html(html);
            modal.find('#saleEditSerialAvailable').text(res.available);
            modal.find('#saleEditSerialQty').text(res.qty);
            modal.find('#saleEditSerialCheckAll').prop('checked', false);

            if (!$card.find('.sale-edit-warranty').val() && res.warranty) {
                $card.find('.sale-edit-warranty').val(res.warranty);
                $card.find('.sale-edit-warranty-input').val(res.warranty);
                modal.find('#saleEditWarrantyDate').val(res.warranty);
            }

            saleEditUpdateSelectedCount();
        }, 'json').fail(function () {
            rows.html('<tr><td colspan="3" class="sale-serial-empty text-danger">Unable to load serial numbers.</td></tr>');
        });
    }

    // Selections already on the card that the active filter is hiding from the picker.
    function saleEditKeptHidden($card, modal) {
        // Only meaningful while a search filter is actually hiding rows. With no filter
        // every selected serial is visible, so counting card inputs would double-count
        // the checked boxes and wrongly drive the allowed limit to 0 (locking the picker).
        var filter = jQuery.trim(modal.find('.sale-serial-filter').val() || '');
        if (filter === '') {
            return 0;
        }
        var renderedIds = [];
        modal.find('#saleEditSerialRows .sale-serial-check').each(function () {
            renderedIds.push(parseInt(this.value, 10));
        });
        var kept = 0;
        $card.find('input[name="sale_serials[' + $card.data('psr') + '][]"]').each(function () {
            if (renderedIds.indexOf(parseInt(jQuery(this).val(), 10)) === -1) {
                kept++;
            }
        });
        return kept;
    }

    // How many checkboxes the picker may keep checked given the line qty and hidden kept picks.
    function saleEditAllowedVisible($card, modal) {
        var need = Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0);
        return Math.max(need - saleEditKeptHidden($card, modal), 0);
    }

    function saleEditToggleAll(source, force) {
        var modal = saleEditModal();
        var $card = saleEditCtxCard();
        if (!$card.length) {
            return;
        }
        var checked = (force === 'all') ? true : jQuery(source).is(':checked');
        var $boxes = modal.find('#saleEditSerialRows .sale-serial-check');
        if (!checked) {
            $boxes.prop('checked', false);
        } else {
            // Respect the quantity limit: only tick as many as are still allowed.
            var allowed = saleEditAllowedVisible($card, modal);
            $boxes.each(function (i) {
                this.checked = i < allowed;
            });
        }
        modal.find('#saleEditSerialCheckAll').prop('checked', checked);
        saleEditHideLimitHint(modal);
        saleEditUpdateSelectedCount();
        saleEditSyncCardFromModal($card, modal);
    }

    function saleEditShowLimitHint(modal, allowed) {
        var $hint = modal.find('#saleEditSerialHint');
        $hint.find('span').text(allowed > 0
            ? 'Only ' + allowed + ' serial number(s) can be selected for this quantity. Deselect one first.'
            : 'The required serial numbers are already selected (hidden by the current filter).');
        $hint.show();
    }

    function saleEditHideLimitHint(modal) {
        modal.find('#saleEditSerialHint').hide();
    }

    // A checkbox was toggled: it responds only to its own click. If checking it would
    // exceed the line quantity, revert that click and hint, instead of un-checking a
    // different serial (which made the checkboxes look broken).
    function saleEditSerialCheckChange(el) {
        var modal = saleEditModal();
        var $card = saleEditCtxCard();
        if (!$card.length) {
            saleEditUpdateSelectedCount();
            return;
        }
        var allowed = saleEditAllowedVisible($card, modal);
        var $checked = modal.find('#saleEditSerialRows .sale-serial-check:checked');
        if (jQuery(el).is(':checked') && $checked.length > allowed) {
            jQuery(el).prop('checked', false);
            saleEditShowLimitHint(modal, allowed);
        } else {
            saleEditHideLimitHint(modal);
        }
        var nowChecked = modal.find('#saleEditSerialRows .sale-serial-check:checked').length;
        modal.find('#saleEditSerialCheckAll').prop('checked', nowChecked >= allowed && allowed > 0);
        saleEditUpdateSelectedCount();
        // Apply to the card immediately so the change survives however the modal closes.
        saleEditSyncCardFromModal($card, modal);
    }

    // Live counter inside the picker modal (the card is also synced on each change).
    function saleEditUpdateSelectedCount() {
        var $card = saleEditCtxCard();
        if (!$card.length) {
            return;
        }
        var modal = saleEditModal();
        var need = Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0);
        var selected = modal.find('#saleEditSerialRows .sale-serial-check:checked').length
            + saleEditKeptHidden($card, modal);
        modal.find('#saleEditSerialSelected').text(selected + ' / ' + need);
    }

    // Mirror the picker's current checkboxes / warranty date onto the invoice line card.
    // Runs live on every change so the card is correct however the modal is closed.
    function saleEditSyncCardFromModal($card, modal) {
        if (!$card || !$card.length) {
            return;
        }
        var psr = String($card.data('psr'));
        var need = Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0);

        // Serial rows currently rendered in THIS picker. If the picker never loaded rows
        // (a warranty-only change, or a stale/duplicate modal), we must NOT rebuild the
        // card's sale_serials inputs, otherwise the existing serials get wiped on save.
        var $checks = modal.find('#saleEditSerialRows .sale-serial-check');
        if ($checks.length > 0) {
            var renderedIds = [];
            $checks.each(function () {
                renderedIds.push(parseInt(this.value, 10));
            });

            // Keep selections that are not visible because a filter is active.
            var ids = [];
            var numbers = [];
            var companyWarranties = [];
            $card.find('input[name="sale_serials[' + psr + '][]"]').each(function () {
                var id = parseInt(jQuery(this).val(), 10);
                if (renderedIds.indexOf(id) === -1) {
                    ids.push(id);
                    numbers.push(jQuery(this).data('number') || String(id));
                    companyWarranties.push(jQuery(this).data('company-warranty') || '');
                }
            });

            $checks.filter(':checked').each(function () {
                if (ids.length >= need) {
                    return;
                }
                var number = jQuery(this).data('number') || jQuery(this).closest('tr').find('td').eq(1).text().trim();
                ids.push(parseInt(this.value, 10));
                numbers.push(String(number).replace('on this invoice', '').trim());
                companyWarranties.push(jQuery(this).data('company-warranty') || '');
            });

            var $summary = $card.find('.sale-edit-serial-summary');
            $card.find('input[name="sale_serials[' + psr + '][]"]').remove();

            // Fixed reliable creation of hidden inputs (value + data attributes written to DOM)
            jQuery.each(ids, function (i, id) {
                jQuery('<input type="hidden">')
                    .attr({
                        name: 'sale_serials[' + psr + '][]',
                        value: id,
                        'data-number': numbers[i] || '',
                        'data-company-warranty': companyWarranties[i] || ''
                    })
                    .insertBefore($summary.length ? $summary : $card.find('.add-serial-btn'));
            });

            if ($summary.length) {
                var html = '<small>Serial (' + ids.length + '/' + need + '): '
                    + (ids.length ? '<b>' + saleEditEscapeHtml(numbers.join(', ')) + '</b>' : '<span class="text-danger">not added</span>')
                    + '</small>';
                html += '<br><small class="text-muted">In stock serials: ' + parseInt(modal.find('#saleEditSerialAvailable').text() || 0, 10) + '</small>';
                $summary.html(html);
            }
        }

        var warranty = modal.find('#saleEditWarrantyDate').val() || '';
        if (!warranty) {
            warranty = $card.find('.sale-edit-warranty').val() || '';
        }
        $card.find('.sale-edit-warranty').val(warranty);
        $card.find('.sale-edit-warranty-input').val(warranty);

        saleEditRefreshSelected($card);
    }

    // Warranty date changed inside the picker: apply it to the card immediately.
    function saleEditModalWarrantyChanged(el) {
        var $card = saleEditCtxCard();
        if (!$card.length) {
            return;
        }
        saleEditSyncCardFromModal($card, saleEditModal());
    }

    // Write the picked serials / warranty date back to the invoice line card and close.
    function saleEditSaveSerials() {
        var modal = saleEditModal();
        var $card = saleEditCtxCard();
        if (!$card || !$card.length) {
            return;
        }
        saleEditSyncCardFromModal($card, modal);
        modal.modal('hide');
    }

    function saleEditRefreshSelected($card) {
        var need = Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0);
        var selected = $card.find('input[name="sale_serials[' + $card.data('psr') + '][]"]').length;
        var modal = saleEditModal();
        if (modal.data('card') && modal.data('card').is($card)) {
            modal.find('#saleEditSerialSelected').text(selected + ' / ' + need);
        }

        var serialOn = String($card.find('.add-serial-btn').attr('data-serial-status')) === '1';
        var warrantyOn = String($card.find('.add-serial-btn').attr('data-warranty-status')) === '1';
        var warranty = $card.find('.sale-edit-warranty').val() || '';
        var complete = (!serialOn || selected === need) && (!warrantyOn || warranty !== '' || selected > 0);
        $card.find('.add-serial-btn').attr('data-saved', complete ? '1' : '0');
    }

    $(function () {
        $(document).on(
            "keyup change",
            "#saleDisc, #vat, #nagod, #bankAmount, #chequeAmount",
            calculate
        );

        calculate();
    });

    // Sale submit guard: serialized lines must carry their serial numbers.
    if (!window.saleEditSerialGuardBound) {
        window.saleEditSerialGuardBound = true;

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'saleEditForm') {
                return;
            }

            // If the picker is still open when the user submits, mirror its latest
            // checkboxes onto its card first, so the counter (modal) and the form
            // (card hidden inputs) cannot disagree.
            var $openModal = jQuery('.sale-edit-serial-modal.in');
            if ($openModal.length) {
                var openCard = $openModal.data('card');
                if (openCard && openCard.length) {
                    // Re-resolve the live in-form card so the mirror never targets a
                    // detached copy left over from a previous AJAX injection.
                    var openPsr = String(openCard.data('psr'));
                    var $liveOpen = jQuery('#TFtable .product-card[data-psr="' + openPsr + '"]').first();
                    if ($liveOpen.length) {
                        openCard = $liveOpen;
                        $openModal.data('card', $liveOpen);
                    }
                    saleEditSyncCardFromModal(openCard, $openModal);
                }
            }

            // Validate against the ACTUAL submitted inputs (count per serialized line),
            // mirroring the server rule, instead of trusting only the data-saved flag.
            var problem = null;
            jQuery('#TFtable .product-card').each(function () {
                if (problem !== null) {
                    return;
                }
                var $card = jQuery(this);
                var $btn = $card.find('.add-serial-btn');
                if (!$btn.length) {
                    return;
                }
                var serialOn = String($btn.attr('data-serial-status')) === '1';
                var warrantyOn = String($btn.attr('data-warranty-status')) === '1';
                if (!serialOn && !warrantyOn) {
                    return;
                }
                var psr = String($card.data('psr'));
                var need = Math.round(parseFloat($card.find("input[name='qty[]']").val()) || 0);
                var name = $btn.data('product-name') || 'this product';

                if (serialOn) {
                    var have = $card.find('input[name="sale_serials[' + psr + '][]"]').length;
                    if (have !== need) {
                        problem = name + ': ' + need + ' serial number(s) must be added (quantity is ' + need + '). Selected: ' + have + '.';
                        return;
                    }
                }
                if (warrantyOn) {
                    var warranty = $card.find('.sale-edit-warranty').val() || $card.find('.sale-edit-warranty-input').val() || '';
                    if (warranty === '') {
                        problem = name + ': the warranty end date is required.';
                        return;
                    }
                }
            });

            if (problem !== null) {
                e.preventDefault();
                e.stopImmediatePropagation();
                alert(problem);
                return false;
            }
        }, true);
    }
</script>