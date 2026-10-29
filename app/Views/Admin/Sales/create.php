<?php
// Serial numbers / customer warranty selected per cart line (keyed by product_stock_relation_id).
$saleSerials = (isset($saleSerials) && is_array($saleSerials)) ? $saleSerials : [];
?>
<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Sales <small>Sales Create</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>

            <div class="col-md-12" style="margin-top: 20px;" >
                <div id="message"></div>
                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
            </div>
            <div class="col-md-8">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Sales Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-8">
                            <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-pencil-square-o fa-lg" style="color: white;"></i>
                                    </span>
                                <input type="text" class="form-control input-lg" onkeypress="findResult()"
                                       name="keyWord" id="keyWord" placeholder="Product name, serial number or warranty date">

                                <span class="input-group-btn">
                                      <button class="btn btn-primary btn-lg" type="submit">Search</button>
                                    </span>
                            </div>
                            <p class="help-block" style="margin-top:5px;">You can search by product name, product ID  or
                                <b>serial number</b>.</p>

                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-barcode fa-lg" style="color: white;"></i>
                                    </span>
                                <input type="text" class="form-control input-lg" oninput="QrScan(this.value)"
                                       name="qrKey" id="qrKey" placeholder="Scan barcode">
                            </div>

                        </div>
                        <div class="input-group col-md-12">
                            <ul style="list-style-type:none;" id="result"></ul>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

                <form id="saleForm" action="<?php echo $action; ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-fw fa-cart-plus"></i> Product cart list</h3>
                            <div class="box-tools">
                                <a href="<?php echo site_url('/Admin/Sales/clearCart/'); ?>"
                                   onclick="return confirm('Are You Sure ?')"
                                   class="btn btn-info btn-xs">Clear Cart</a>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- Scrollable table list. The #TFtable id is kept because QrScan() refreshes the
                                 cart area with $("#TFtable").load(location.href + " #TFtable"). -->
                            <div class="table-responsive" id="TFtable" style="max-height:65vh;overflow-y:auto;">
                                <table class="table table-bordered table-condensed" style="min-width:1000px;margin-bottom:0;">
                                    <thead>
                                    <tr>
                                        <th style="width:40px;">No</th>
                                        <th style="min-width:160px;">Name</th>
                                        <th style="min-width:130px;">Quantity</th>
                                        <th style="min-width:110px;">Price</th>
                                        <th style="min-width:90px;">Subtotal</th>
                                        <th style="min-width:80px;">Action</th>
                                        <th style="min-width:220px;">Serial</th>
                                        <th style="min-width:150px;">Customer Warranty</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0;
                                    $k = 0;
                                    $l = 0;
                                    $m = 0;
                                    foreach (Cart()->contents() as $row) {
                                    $rowProdId = isset($row['prod_id']) ? $row['prod_id'] : 0;
                                    $unitId = productIdByDefaultStoreUnit($rowProdId);
                                    $conversion_factor = get_data_by_id('conversion_factor', 'units', 'units_id', $unitId);
                                    $uPrice = unitOrBasePriceByUnitPrice($unitId, $row['price']);

                                    // Serial numbers / customer warranty selected for this cart line.
                                    $psrKey = (string)$row['id'];
                                    $serialOn = ($rowProdId && get_data_by_id('serial_status', 'products', 'prod_id', $rowProdId) == 1);
                                    $warrantyOn = ($rowProdId && get_data_by_id('warranty_status', 'products', 'prod_id', $rowProdId) == 1);
                                    $needQty = (int)round((float)$row['qty']);
                                    $savedIds = !empty($saleSerials[$psrKey]['ids']) ? (array)$saleSerials[$psrKey]['ids'] : [];
                                    $savedNumbers = !empty($saleSerials[$psrKey]['serial_numbers']) ? (array)$saleSerials[$psrKey]['serial_numbers'] : [];
                                    $savedWarranty = !empty($saleSerials[$psrKey]['warranty']) ? $saleSerials[$psrKey]['warranty'] : '';
                                    $dataPsr = htmlspecialchars($psrKey, ENT_QUOTES, 'UTF-8');
                                    $dataName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                                    $getUrl = !empty($salesSave) ? '?sale_save_id=' . $salesSave->sale_save_id : '';
                                    ?>
                                        <tr class="product-card" data-psr="<?= $dataPsr; ?>"
                                            data-qty="<?= (int)$needQty; ?>">
                                            <td>
                                                <?php echo ++$i; ?>
                                                <input type="hidden" name="productId[]" value="<?php echo $rowProdId; ?>">
                                                <input type="hidden"  name="product_stock_relation_id[]" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="qty[]" value="<?php echo $row['qty']; ?>">
                                                <input type="hidden" class="upprice" id="qtyUp_<?= $row['id']; ?>" name="price[]" value="<?php echo $row['price']; ?>">
                                                <input type="hidden" step=any class="form-control disc" oninput="minusValueCheck(this.value,this),validationDiscount('disc_<?= $row['id']?>')" name="disc[]" id="disc_<?= $row['id']?>">
                                                <input type="hidden" readonly class="form-control subtotal" name="subtotal[]" id="subt_<?php print $m++; ?>" value="<?php echo $row['subtotal'] ?>">
                                                <input type="hidden" name="suballtotal[]" id="subtl2_<?php print $k++; ?>" value="<?php echo $row['subtotal']; ?>">
                                            </td>
                                            <td>
                                                <b><?php echo $row['name']; ?></b>
                                                <?php if ($serialOn || $warrantyOn) { ?>
                                                    <br>
                                                    <?php if ($serialOn) { ?><span class="label label-primary">Serial: ON</span><?php } ?>
                                                    <?php if ($warrantyOn) { ?><span class="label label-warning">Warranty: ON</span><?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control" readonly value="<?php echo unitOrQtyByUnitQty($unitId,$row['qty']); ?>">
                                                    <span class="input-group-addon"><?php echo showUnitName($unitId) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control input-sm" style="max-width:110px;" oninput="priceMakeBase(this.value,'<?= $conversion_factor;?>','<?= $row['id'];?>' )" value="<?= $uPrice ?>">
                                            </td>
                                            <td style="white-space:nowrap;"><span id="subtl_<?php print $l++; ?>"><?php echo (float)$row['subtotal']; ?></span></td>
                                            <td style="white-space:nowrap;">
                                                <a href="<?php echo site_url('/Admin/Sales/remove_cart/' . $row['rowid'].$getUrl); ?>"
                                                   onclick="return confirm('Are You Sure ?')"
                                                   class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                                            </td>
                                            <td>
                                                <?php if ($serialOn) { ?>
                                                    <div class="sale-serial-summary" id="saleSerialSummary_<?= $dataPsr; ?>" style="margin-bottom:4px;">
                                                        <small>Serial (<?= count($savedIds); ?>/<?= (int)$needQty; ?>):
                                                            <?php if (!empty($savedNumbers)) { ?>
                                                                <b><?= htmlspecialchars(implode(', ', $savedNumbers), ENT_QUOTES, 'UTF-8'); ?></b>
                                                            <?php } else { ?>
                                                                <span class="text-danger">not added</span>
                                                            <?php } ?>
                                                        </small>
                                                    </div>
                                                    <button type="button" class="btn btn-info btn-xs add-serial-btn"
                                                            onclick="saleOpenSerialModal(this)"
                                                            data-psr="<?= $dataPsr; ?>"
                                                            data-qty="<?= (int)$needQty; ?>"
                                                            data-serial-status="<?= $serialOn ? '1' : '0'; ?>"
                                                            data-warranty-status="<?= $warrantyOn ? '1' : '0'; ?>"
                                                            data-product-name="<?= $dataName; ?>"
                                                            data-saved="<?= count($savedIds) >= $needQty && ($warrantyOn ? $savedWarranty !== '' : true) ? '1' : '0'; ?>">
                                                        <i class="fa fa-barcode"></i> Add Serial
                                                    </button>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td id="saleWarrantyCell_<?= $dataPsr; ?>">
                                                <?php if ($warrantyOn) { ?>
                                                    <?php if ($serialOn) { ?>
                                                        <b><?= $savedWarranty !== '' ? htmlspecialchars($savedWarranty, ENT_QUOTES, 'UTF-8') : '<span class="text-danger">not added</span>'; ?></b>
                                                    <?php } else { ?>
                                                        <span class="text-muted">needs serial tracking</span>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($i === 0) { ?>
                                        <tr>
                                            <td colspan="8" class="text-muted" style="padding:10px;">No product in the cart yet &mdash; search or scan a product above.</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

            </div>

            <div class="col-md-4" style="background-color: #e8b96f;padding: 10px;">
                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;padding-bottom: 10px;">
                    <label>Sale Date</label>
                    <input type="date" class="form-control" name="date" value="<?= !empty($salesSave)?$salesSave->date:date('Y-m-d'); ?>">
                    <input type="hidden" name="sale_save_id" id="sale_save_id" value="<?= !empty($salesSave)?$salesSave->sale_save_id:'';?>">
                </div>
                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                    <label>Customer</label>
                    <div class="panel with-nav-tabs panel-default nav-tabs-custom"
                         style="background-color: #e8b96f; border-color: ;" >
                        <ul class="nav nav-tabs" >
                            <?php
                            $cusActive = '';
                            $cusNameActive = '';
                            $customerId = '';
                            $customerName = '';
                            $discountVal = '';
                            $vatVal = '';
                            $cashPay = '';
                            $bankId = '';
                            $bankPay = '';
                            $chequeNo = '';
                            $chequePay = '';
                            if (!empty($salesSave)){
                                $cusActive = !empty($salesSave->customer_id)?'active':'';
                                $cusNameActive = !empty($salesSave->customer_name)?'active':'';
                                $customerId = $salesSave->customer_id;
                                $customerName = $salesSave->customer_name;
                                $discountVal = $salesSave->discount;
                                $vatVal = $salesSave->vat;
                                $cashPay = $salesSave->cash_pay;
                                $bankId = $salesSave->bank_id;
                                $bankPay = $salesSave->bank_paid;
                                $chequeNo = $salesSave->cheque_no;
                                $chequePay = $salesSave->cheque_amount;
                            }

                            $existingActive = !empty($cusActive) || (empty($cusActive) && empty($cusNameActive));
                            $newActive = !empty($cusNameActive);
                            ?>
                            <li class="<?= $existingActive ? 'active' : ''; ?>"><a href="#existing" data-toggle="tab">Existing Customer</a></li>
                            <li class="<?= $newActive ? 'active' : ''; ?>"><a href="#new" data-toggle="tab">New Customer</a></li>
                        </ul>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade <?= $existingActive ? 'active in' : ''; ?> " id="existing">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <select class="form-control select2"
                                                    onchange="createBtnShow(),customerBalanceShow(this.value),emptyOponentCustomer() " style=" width: 100%;"
                                                    name="customer_id"
                                                    id="cus">

                                                <option selected="selected" value="">Please Select</option>
                                                <?php echo getAllListInOptionWithStatus($customerId, 'customer_id', 'customer_name', 'customers','customer_name'); ?>
                                            </select>
                                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-customer">Create new</a><br>
                                            <span id="balance"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade <?= $newActive ? 'active in' : ''; ?>" id="new">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="text" class="form-control " oninput="emptyOponentCustomer()" name="name" id="name"
                                                   placeholder="Name" value="<?= $customerName;?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php if (isset($discount) AND ($discount == 1)) { ?>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">

                        <label>Entire Sale Discount: %</label>

                        <input type="number" step=any class="form-control saleDisc" oninput="minusValueCheck(this.value,this)" name="saleDisc" id="saleDisc"
                               placeholder="Input Discount %" value="<?= $discountVal;?>" >
                        <input type="hidden" class="form-control totalamount" name="total" id="totalamount"
                               readonly value="<?php echo Cart()->total() ?>">
                        <!--  </div> -->
                    </div>

                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px; ">
                        <label>Discount Amount</label>
                        <input type="text" class="form-control " name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" readonly>
                        <input type="hidden" name="granddiscountlast" id="granddiscountlast">
                    </div>
                <?php } ?>
                <?php if (isset($vat_option) AND ($vat_option == 1)) { ?>
                    <div class="col-xs-12"
                         style="border:1px dashed #D0D3D8 ; padding:5px; ">
                        <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <label>Vat: %</label>
                            <input type="number" step=any class="form-control vat" oninput="minusValueCheck(this.value,this)" name="vat" id="vat" placeholder="vat %" value="<?= $vatVal;?>">

                            <input type="hidden" class="form-control vatTotallast" name="vatTotallast"
                                   id="vatTotallast" readonly value="<?php echo Cart()->total() ?>">
                        </div>
                        <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <label>Vat Amount</label>
                            <input type="text" onchange="checkBankId()" class="form-control vatAmount"
                                   name="vatAmount" id="vatAmount" placeholder="Vat Amount" readonly>

                        </div>
                    </div>
                <?php } ?>

                <div class="col-xs-12"
                     style="border:1px dashed #D0D3D8 ; padding:5px; ">

                    <div class="col-xs-12" style="padding:5px;">
                        <label>Grand Total</label>
                        <input type="hidden" class="form-control" name="grandtotal2" readonly id="grandtotal2"
                               value="<?php echo Cart()->total() ?>">

                        <input type="text" class="form-control" name="grandtotal" readonly id="grandtotal"
                               value="<?php echo Cart()->total() ?>">

                    </div>

                    <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                        <label>Payment</label>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <label>Cash</label>
                            <input type="number" step=any class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod"
                                   placeholder="Input Cash Amount" value="<?= $cashPay;?>">
                        </div>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Bank</label>
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    <?php echo getTwoValueInOption($bankId, 'bank_id', 'name', 'account_no', 'bank'); ?>
                                </select>
                            </div>
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Bank Amount</label>
                                <input type="number" step=any onchange="checkBankId()" class="form-control bankAmount"
                                       name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this)" placeholder="input Bank Amount" value="<?= $bankPay;?>" >
                                <b id="Bank_valid"></b>
                            </div>
                        </div>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Cheque No</label>
                                <input type="text" class="form-control" name="chequeNo" id="chequeNo"
                                       placeholder="Input Cheque No " value="<?= $chequeNo;?>">
                            </div>
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Cheque Amount</label>
                                <input type="number" step=any onchange="cheque()" class="form-control chequeAmount"
                                       name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount " value="<?= $chequePay;?>">
                                <b id="cheque_valid"></b>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                        <label>Total Amount </label>
                        <input type="text"  class="form-control " name="grandtotallast" readonly
                               id="grandtotallast" value="<?php echo (float)Cart()->total() ?>">

                    </div>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8; padding:5px;">
                        <label>Total Due</label>
                        <input type="number" step=any class="form-control" name="grandtotaldue" readonly id="grandtotaldue"
                               value="<?php echo Cart()->total() ?>">
                    </div>
                </div>
                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="sms" id="sms">
                        <label class="form-check-label" for="sms">Send SMS</label>
                    </div>
                </div>
                <div class="col-xs-12" style="padding:20px; ">
                    <button  type="button" class="btn btn-secondary" onclick="saveToDraft()" >Save to draft </button>

                    <button style="float: right;" id="btn" type="submit"
                            class="btn btn-primary">Sale</button>
                    <b id="mess"></b>
                </div>



            </div>

            </form>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<!-- Serial / Warranty picker modal (start) -->
<div class="modal fade" id="saleSerialModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Serial Numbers &amp; Warranty <small id="saleSerialProduct"></small></h4>
            </div>
            <div class="modal-body">
                <div id="saleSerialSerialWrap">
                    <div class="alert alert-info" style="padding:8px 12px;margin-bottom:12px;">
                        Quantity: <b id="saleSerialQty">0</b> &nbsp;|&nbsp; Available serials in stock: <b id="saleSerialAvailable">0</b>
                        &nbsp;<span class="pull-right">Selected: <b id="saleSerialSelected">0</b></span>
                    </div>
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-xs-7">
                            <input type="text" class="form-control input-sm" id="saleSerialFilter"
                                   placeholder="Search serial number or warranty date (YYYY-MM-DD)"
                                   onkeypress="return saleSerialFilterKey(event)">
                        </div>
                        <div class="col-xs-5">
                            <button type="button" class="btn btn-default btn-sm" onclick="saleSerialLoad()"><i class="fa fa-search"></i> Filter</button>
                            <button type="button" class="btn btn-link btn-sm" onclick="saleSerialToggleAll('all')">Select all</button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                        <table class="table table-condensed table-bordered" style="margin-bottom:0;">
                            <thead>
                            <tr>
                                <th style="width:34px;"><input type="checkbox" id="saleSerialCheckAll" onclick="saleSerialToggleAll(this)"></th>
                                <th>Serial Number</th>
                                <th style="width:220px;">Company Warranty End Date</th>
                            </tr>
                            </thead>
                            <tbody id="saleSerialRows"></tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group" id="saleSerialWarrantyWrap" style="margin-top:15px;">
                    <label for="saleSerialWarranty">Customer Warranty End Date</label>
                    <input type="date" class="form-control" id="saleSerialWarranty">
                    <div class="btn-group btn-group-xs" style="margin-top:6px;" role="group" aria-label="Warranty quick options">
                        <button type="button" class="btn btn-default" onclick="saleSetWarrantyDate(0, 6)">6 Months</button>
                        <button type="button" class="btn btn-default" onclick="saleSetWarrantyDate(1, 0)">1 Year</button>
                        <button type="button" class="btn btn-default" onclick="saleSetWarrantyDate(2, 0)">2 Years</button>
                        <button type="button" class="btn btn-default" onclick="saleSetWarrantyDate(3, 0)">3 Years</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saleSaveSerials()"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Serial / Warranty picker modal (end) -->

<script>
    var SALE_SERIAL_LIST_URL = '<?php echo site_url('Admin/Sales/serial_list'); ?>';
    var SALE_SERIAL_SAVE_URL = '<?php echo site_url('Admin/Sales/save_serials'); ?>';

    // Global helpers: the page is either fully loaded or injected by showData(),
    // so every handler is called inline instead of being bound to the element.
    function saleEscapeHtml(value) {
        return jQuery('<i>').text(value == null ? '' : String(value)).html();
    }

    function saleSerialCtx() {
        return jQuery('#saleSerialModal').data('ctx') || {};
    }

    // Quick warranty options: fill the Customer Warranty End Date with
    // today + N years/months. The date input stays editable afterwards.
    function saleSetWarrantyDate(years, months) {
        var today = new Date();
        var target = new Date(today.getTime());
        target.setMonth(target.getMonth() + (years || 0) * 12 + (months || 0));
        // Month overflow guard (e.g. Jan 31 + 1 month should not land on Mar 3)
        if (target.getDate() !== today.getDate()) {
            target.setDate(0);
        }
        var pad = function (n) {
            return (n < 10 ? '0' : '') + n;
        };
        jQuery('#saleSerialWarranty').val(target.getFullYear() + '-' + pad(target.getMonth() + 1) + '-' + pad(target.getDate()));
    }

    // Quantity prompt + serial availability check on the product search result form.
    function saleAddCartCheck(form) {
        var $form = jQuery(form);
        var totalQty = 0;

        $form.find('.unit-qty').each(function () {
            var factor = parseFloat(jQuery(this).data('factor')) || 1;
            var value = parseFloat(jQuery(this).val());
            if (!isNaN(value) && value > 0) {
                totalQty += factor * value;
            }
        });

        if (totalQty <= 0) {
            alert('Please enter the quantity you are selling.');
            return false;
        }

        if (String($form.data('serial-status')) === '1') {
            var available = parseInt($form.data('serial-available'), 10) || 0;
            if (Math.round(totalQty) > available) {
                alert('Only ' + available + ' serial number(s) are available in stock for ' + $form.data('product-name') + '.');
                return false;
            }
        }

        return true;
    }

    function saleOpenSerialModal(el) {
        var $btn = jQuery(el);
        var ctx = {
            psr: $btn.data('psr'),
            qty: parseInt($btn.data('qty'), 10) || 0,
            serialOn: String($btn.data('serial-status')) === '1',
            warrantyOn: String($btn.data('warranty-status')) === '1',
            name: $btn.data('product-name') || ''
        };

        jQuery('#saleSerialModal').data('ctx', ctx);
        jQuery('#saleSerialProduct').text('- ' + ctx.name);
        jQuery('#saleSerialQty').text(ctx.qty);
        jQuery('#saleSerialFilter').val('');
        jQuery('#saleSerialWarranty').val('');
        jQuery('#saleSerialRows').empty();
        jQuery('#saleSerialCheckAll').prop('checked', false);
        jQuery('#saleSerialSerialWrap').toggle(ctx.serialOn);
        jQuery('#saleSerialWarrantyWrap').toggle(ctx.warrantyOn && ctx.serialOn);

        if (ctx.serialOn) {
            saleSerialLoad();
        }

        jQuery('#saleSerialModal').modal('show');
    }

    function saleSerialFilterKey(event) {
        if (event.key === 'Enter') {
            saleSerialLoad();
            return false;
        }
        return true;
    }

    function saleSerialLoad() {
        var ctx = saleSerialCtx();
        if (!ctx.serialOn) {
            return;
        }

        jQuery.post(SALE_SERIAL_LIST_URL, {
            product_stock_relation_id: ctx.psr,
            filter: jQuery('#saleSerialFilter').val()
        }, function (res) {
            if (!res || res.status != 1) {
                jQuery('#saleSerialRows').html('<tr><td colspan="3" class="text-danger">'
                    + saleEscapeHtml(res && res.message ? res.message : 'Unable to load serial numbers.') + '</td></tr>');
                return;
            }

            var selected = res.selected || [];
            var html = '';

            jQuery.each(res.serials || [], function (i, serial) {
                var id = parseInt(serial.product_serial_id, 10);
                var checked = selected.indexOf(id) > -1 ? ' checked' : '';
                html += '<tr>'
                    + '<td class="text-center"><input type="checkbox" class="sale-serial-check" value="' + id + '"' + checked + ' onchange="saleSerialCount()"></td>'
                    + '<td>' + saleEscapeHtml(serial.serial_number) + '</td>'
                    + '<td>' + saleEscapeHtml(serial.company_warranty_end_date || '-') + '</td>'
                    + '</tr>';
            });

            if (html === '') {
                html = '<tr><td colspan="3" class="text-warning">No serial number is available in stock for this product.</td></tr>';
            }

            jQuery('#saleSerialRows').html(html);
            jQuery('#saleSerialQty').text(res.qty);
            jQuery('#saleSerialAvailable').text(res.available);
            jQuery('#saleSerialWarranty').val(res.warranty || '');
            jQuery('#saleSerialCheckAll').prop('checked', false);
            saleSerialCount();
        }, 'json').fail(function () {
            jQuery('#saleSerialRows').html('<tr><td colspan="3" class="text-danger">Unable to load serial numbers.</td></tr>');
        });
    }

    function saleSerialSelectedIds() {
        var ids = [];
        jQuery('#saleSerialRows .sale-serial-check:checked').each(function () {
            ids.push(parseInt(this.value, 10));
        });
        return ids;
    }

    function saleSerialCount() {
        var ctx = saleSerialCtx();
        jQuery('#saleSerialSelected').text(saleSerialSelectedIds().length + ' / ' + (ctx.qty || 0));
    }

    function saleSerialToggleAll(source) {
        var checked = (typeof source === 'string') ? true : jQuery(source).is(':checked');
        jQuery('#saleSerialRows .sale-serial-check').prop('checked', checked);
        jQuery('#saleSerialCheckAll').prop('checked', checked);
        saleSerialCount();
    }

    function saleSaveSerials() {
        var ctx = saleSerialCtx();
        var ids = saleSerialSelectedIds();
        var warranty = jQuery('#saleSerialWarranty').val() || '';

        if (ctx.serialOn) {
            if (ids.length !== ctx.qty) {
                alert('Please select exactly ' + ctx.qty + ' serial number(s), one for each unit.');
                return;
            }
            if (ids.filter(function (v, i) { return ids.indexOf(v) === i; }).length !== ids.length) {
                alert('Duplicate serial numbers are not allowed.');
                return;
            }
        }

        if (ctx.serialOn && ctx.warrantyOn && warranty === '') {
            alert('Customer Warranty End Date is required for this product.');
            return;
        }

        var data = {
            product_stock_relation_id: ctx.psr,
            customer_warranty_end_date: warranty
        };
        jQuery.each(ids, function (i, id) {
            data['serials[' + i + ']'] = id;
        });

        jQuery.post(SALE_SERIAL_SAVE_URL, data, function (res) {
            if (!res || res.status != 1) {
                alert(res && res.message ? res.message : 'Could not save the serial numbers.');
                return;
            }
            saleUpdateSerialSummary(ctx, res);
            jQuery('#saleSerialModal').modal('hide');
        }, 'json').fail(function () {
            alert('Could not save the serial numbers.');
        });
    }

    function saleUpdateSerialSummary(ctx, res) {
        var numbers = res.serial_numbers || [];

        if (ctx.serialOn) {
            var serialHtml = '<small>Serial (' + numbers.length + '/' + res.qty + '): <b>'
                + saleEscapeHtml(numbers.join(', ')) + '</b></small>';
            jQuery('#saleSerialSummary_' + ctx.psr).html(serialHtml);
        }
        if (ctx.warrantyOn) {
            var warrantyHtml = res.warranty
                ? '<b>' + saleEscapeHtml(res.warranty) + '</b>'
                : '<b><span class="text-danger">not added</span></b>';
            jQuery('#saleWarrantyCell_' + ctx.psr).html(warrantyHtml);
        }

        var complete = (!ctx.serialOn || numbers.length === Number(res.qty)) && (!ctx.warrantyOn || res.warranty !== '');
        jQuery('.add-serial-btn').filter(function () {
            return String(jQuery(this).data('psr')) === String(ctx.psr);
        }).attr('data-saved', complete ? '1' : '0');
    }

    // Sale submit guard (capture phase, works on AJAX injected pages too).
    // The flag keeps the listener from being registered twice when the page is
    // injected again by showData().
    if (!window.saleSerialGuardBound) {
        window.saleSerialGuardBound = true;

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'saleForm') {
                return;
            }

            // Draft saving is allowed without serials.
            var action = form.action || '';
            if (action.indexOf('saleSaveAction') > -1) {
                return;
            }

            var missing = null;
            jQuery('.add-serial-btn').each(function () {
                if (missing !== null) {
                    return;
                }
                if (String(jQuery(this).attr('data-saved')) !== '1') {
                    missing = jQuery(this).data('product-name') || 'the serialized product';
                }
            });

            if (missing !== null) {
                e.preventDefault();
                e.stopImmediatePropagation();
                alert('Please add the serial number(s) for: ' + missing);
                return false;
            }
        }, true);
    }
</script>

<!--customer modal-->
<div class="modal fade" id="modal-customer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Customer Add</h4>
            </div>

            <div class="modal-body">
                <div id="cusmessage2"></div>
                <div class="row">
                    <div class="col-md-6">
                        <form id="customerform" action="<?= base_url('Admin/Customers/create_action_ajax');?>" method="post">
                            <div class="form-group">
                                <label for="varchar">Customer Name </label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Customer Name" required />
                                <div class="error"></div>
                            </div>
                            <div class="form-group">
                                <label for="int">Mobile </label>
                                <input type="number" class="form-control" name="mobile" id="mobile" placeholder="Mobile"  required/>
                                <div class="error"></div>
                            </div>
                            <div class="form-group">
                                <label for="int">Customer Type</label>
                                <select class="form-control" name="cus_type_id" id="cus_type_id" required>
                                    <?php echo getAllListInOption('','cus_type_id','type_name','customer_type'); ?>
                                </select>
                                <div class="error"></div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="customerValidat()" >Save</button>
                        </form>
                    </div>
                    <div class="col-md-6" style="border-left: 1px solid #cecdcd;">
                        <form id="customerform2" action="<?= base_url('Admin/Customers/create_action_existing_ajax');?>" method="post">
                            <h4>Existing Customer</h4>
                            <div class="form-group">
                                <label for="varchar">Customer Name </label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name_ex" placeholder="Customer Name" required />
                                <div class="error"></div>
                            </div>
                            <div class="form-group">
                                <label for="int">Mobile</label>
                                <input type="number" class="form-control" name="mobile" id="mobile_ex" placeholder="Mobile" required/>
                                <div class="error"></div>
                            </div>
                            <div class="form-group">
                                <label for="int">Customer Type</label>

                                <select class="form-control" name="cus_type_id" id="cus_type_id_ex" required>
                                    <option value="">Please Select</option>
                                    <?php echo getAllListInOption('','cus_type_id','type_name','customer_type'); ?>
                                </select>
                                <div class="error"></div>
                            </div>

                            <div class="form-group">
                                <label for="enum">Transaction Type</label>
                                <select class="form-control input" name="transaction_type" id="transaction_type" required>
                                    <option value="">Please Select</option>
                                    <option value="1">খরচ (Cr.) /পাওনাদার</option>
                                    <option value="2">জমা (Dr.) /দেনাদার</option>
                                </select>
                                <div class="error"></div>
                            </div>

                            <div class="form-group databank" id="chaque">
                                <label for="int">Amount</label>
                                <input type="number" class="form-control input" name="amount" id="amount" placeholder="Amount" oninput="minusValueCheck(this.value,this)" required/>
                                <div class="error"></div>
                            </div>

                            <button type="button" class="btn btn-primary" onclick="customerExisValidat()"  >Save</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>

            </div>

        </div>
    </div>
</div>
<!--customer modal-->