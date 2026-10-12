<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Invoice <small>Invoice</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Invoice</li>
        </ol>

        <div class="row">
            <div class="col-xs-12" style="margin-top: 15px;">
                <?php echo $menu; ?>
            </div>

            <div class="col-xs-12 no-print">
                <div class="">
                    <?php
                    $returnStatus = get_return_status_by_invoice_id($invoiceId);
                    if ($returnStatus == 0){
                        ?>
                        <div class="pull-right" style="margin-left: 10px;">
                            <a href="<?php echo site_url('Admin/Return_sale/return/'.$invoiceId) ?>" style="margin-top: 25px;" class="btn btn-warning " type="submit">Return Sales</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>


    <section class="content">
        <style>
            @page {
                size: 58mm auto;
                margin: 0;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .receipt {
                width: 58mm;
                margin: 0 auto;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #000;
            }

            .img-logo {
                width: 38mm;
                max-width: 100%;
            }

            /* Header */
            .header {
                text-align: center;
                margin-bottom: 6px;
            }

            .shop-name {
                font-size: 16px;
                font-weight: 700;
                letter-spacing: 0.5px;
                margin-top: 3px;
            }

            .address {
                font-size: 10px;
                margin-top: 2px;
            }

            .test-note {
                font-size: 9px;
                font-weight: 600;
                margin: 3px 0;
            }

            .contact {
                font-size: 9px;
                margin-top: 2px;
                line-height: 1.4;
            }

            /* Divider */
            .dashed {
                border-top: 1px dashed #000;
                margin: 6px 0;
            }

            .solid {
                border-top: 1px solid #000;
                margin: 6px 0;
            }

            /* Info rows */
            .info-table {
                width: 100%;
                font-size: 10px;
            }

            .info-table td {
                padding: 1px 0;
                vertical-align: top;
            }

            .info-table .label {
                width: 48%;
                color: #333;
            }

            .info-table .value {
                width: 52%;
                text-align: right;
                font-weight: 600;
            }

            /* Billing To */
            .section-title {
                font-size: 11px;
                font-weight: 700;
                margin: 4px 0 2px;
            }

            /* Items */
            .items-header {
                display: flex;
                font-size: 10px;
                font-weight: 700;
                border-bottom: 1px solid #000;
                padding-bottom: 2px;
                margin-bottom: 3px;
            }

            .items-header .col-item { width: 45%; }
            .items-header .col-qty  { width: 20%; text-align: center; }
            .items-header .col-amt  { width: 35%; text-align: right; }

            .item-row {
                display: flex;
                font-size: 10px;
                margin-bottom: 5px;
            }

            .item-row .col-item { width: 45%; }
            .item-row .col-qty  { width: 20%; text-align: center; }
            .item-row .col-amt  { width: 35%; text-align: right; }

            .item-name {
                font-weight: 600;
            }

            .item-detail {
                font-size: 9px;
                color: #333;
            }

            /* Totals */
            .totals {
                font-size: 10px;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 2px;
            }

            .total-row.bold {
                font-weight: 700;
            }

            .total-row.grand {
                font-size: 12px;
                font-weight: 700;
                margin-top: 4px;
                padding-top: 4px;
                border-top: 1px solid #000;
            }

            /* Barcode */
            .qr-section {
                text-align: center;
                margin: 12px 0 8px;
            }

            .qr-box {
                margin: 0 auto 4px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .upi-logo {
                font-size: 10px;
                font-weight: 700;
                margin-top: 3px;
            }

            /* ========== FOOTER (matching your image) ========== */
            .footer {
                margin-top: 10px;
                padding-top: 6px;
            }

            .footer-message {
                background: #1a3a6e;
                color: #fff;
                border-radius: 6px;
                padding: 6px 8px;
                font-size: 9px;
                line-height: 1.35;
                text-align: center;
                margin-bottom: 8px;
            }

            .footer-info {
                text-align: center;
                font-size: 9px;
                color: #1a3a6e;
                margin-bottom: 10px;
                line-height: 1.4;
            }

            .signature-row {
                display: flex;
                justify-content: space-between;
                margin-top: 12px;
            }

            .signature {
                width: 45%;
                text-align: center;
            }

            .signature-line {
                border-top: 1px solid #1a3a6e;
                margin-bottom: 3px;
                height: 18px;
            }

            .signature span {
                font-size: 9px;
                color: #1a3a6e;
            }

            @media print {
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .no-print {
                    display: none !important;
                }
                .receipt {
                    width: 58mm;
                }
                .label {
                    border: unset !important;
                }
                .footer-message {
                    background: #1a3a6e !important;
                    color: #fff !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }

                .footer-info,
                .signature span {
                    color: #1a3a6e !important;
                }
                .signature-line {
                    border-top-color: #1a3a6e !important;
                }
            }
        </style>

        <div class="receipt">

            <!-- HEADER -->
            <div class="header">
                <img src="<?= base_url('uploads/logo.png')?>" class="img-logo">
                <div class="shop-name">MIR TRADING</div>
                <div class="address">Importer &amp; Wholesaler</div>
                <div class="test-note">Khulna Road, Bokchor Hustala Moor, Jashore</div>
                <div class="contact">
                    Email : mirtrading.jsr@gmail.com<br>
                    Khurshid : 01919-812281<br>
                    Shamim : 01937-238798<br>
                    Rumi : 01775-011922<br>
                    Sazzad : 01712-921951
                </div>
            </div>

            <div class="dashed"></div>

            <!-- BILL INFO -->
            <table class="info-table">
                <tr>
                    <td class="label">Invoice Number</td>
                    <td class="value">Inv_<?= $invoiceId ?></td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td class="value"><?= invoiceDateFormat($invoiceData->createdDtm); ?></td>
                </tr>
            </table>

            <div class="dashed"></div>

            <!-- BILLING TO -->
            <div class="section-title">Billing To</div>
            <table class="info-table">
                <tr>
                    <td class="label">Name:</td>
                    <td class="value"><?= ($invoiceData->customer_id == 0) ? $invoiceData->customer_name : get_data_by_id('customer_name', 'customers', 'customer_id', $invoiceData->customer_id); ?></td>
                </tr>
                <?php if (!empty($invoiceData->customer_id)) { ?>
                    <tr>
                        <td class="label">Contact No.:</td>
                        <td class="value"><?= showWithPhoneNummberCountryCode(get_data_by_id('mobile', 'customers', 'customer_id', $invoiceData->customer_id)) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Address:</td>
                        <td class="value"><?= get_data_by_id('address', 'customers', 'customer_id', $invoiceData->customer_id) ?></td>
                    </tr>
                <?php } ?>
            </table>

            <div class="dashed"></div>

            <!-- ITEMS -->
            <div class="items-header">
                <div class="col-item">Items</div>
                <div class="col-qty">Qty</div>
                <div class="col-amt">Amount</div>
            </div>

            <?php foreach ($invoiceItame as $row) {
                $catId = get_data_by_id('prod_cat_id', 'products', 'prod_id', $row->prod_id);
                $parent_pro_cat = get_data_by_id('parent_pro_cat', 'product_category', 'prod_cat_id', $catId);
                $category = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $parent_pro_cat);
                $subCategory = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $catId);
                $productName = get_data_by_id('name', 'products', 'prod_id', $row->prod_id);
                $unit = productIdByDefaultStoreUnit($row->prod_id);
                ?>
                <div class="item-row">
                    <div class="col-item">
                        <div class="item-name"><?= $productName; ?></div>
                        <div class="item-detail">(<?= $category . ' > ' . $subCategory; ?>)</div>
                        <div class="item-detail"><?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit, $row->price)); ?></div>
                    </div>
                    <div class="col-qty"><?= unitOrQtyByUnitQty($unit, $row->quantity); ?>/<?= showUnitName($unit) ?></div>
                    <div class="col-amt"><?= showWithCurrencySymbol($row->total_price); ?></div>
                </div>
            <?php } ?>

            <div class="dashed"></div>

            <!-- TOTALS -->
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span><?= showWithCurrencySymbol($invoiceData->amount); ?></span>
                    <input type="hidden" id="totalAmount" value="<?= $invoiceData->amount ?>">
                </div>

                <?php if (!empty($invoiceData->entire_sale_discount)) { ?>
                    <div class="total-row">
                        <span>Discount (<?= $invoiceData->entire_sale_discount ?>%):</span>
                        <input type="hidden" id="discount" value="<?= $invoiceData->entire_sale_discount ?>">
                        <span id="discountAmount"></span>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->vat)) { ?>
                    <div class="total-row">
                        <span>Vat (<?= $invoiceData->vat ?>%):</span>
                        <input type="hidden" id="vat" value="<?= $invoiceData->vat ?>">
                        <span id="vatAmount"></span>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->final_amount)) { ?>
                    <div class="total-row">
                        <span>Final Amount:</span>
                        <span><?= showWithCurrencySymbol($invoiceData->final_amount) ?></span>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->nagad_paid)) { ?>
                    <div class="total-row">
                        <span>Cash Pay:</span>
                        <span><?= showWithCurrencySymbol($invoiceData->nagad_paid) ?></span>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->bank_id)) {
                    $bankDetail = getTotalRow('bank', 'bank_id', $invoiceData->bank_id);
                    ?>
                    <div class="total-row">
                        <span>Bank Pay:</span>
                        <span><?= showWithCurrencySymbol($invoiceData->bank_paid) ?></span>
                    </div>
                <?php } ?>

                <?php if (!empty($invoiceData->chaque_id)) { ?>
                    <div class="total-row">
                        <span>Cheque Pay:</span>
                        <span><?= showWithCurrencySymbol($invoiceData->chaque_paid) ?></span>
                    </div>
                <?php } ?>

                <div class="total-row bold">
                    <span>Today Due:</span>
                    <span><?= showWithCurrencySymbol($invoiceData->due) ?></span>
                </div>

                <?php if (!empty($invoiceData->previous_due)) { ?>
                    <div class="total-row bold">
                        <span>Previous <?= ($invoiceData->previous_due > 0) ? 'Due' : 'Balance'; ?>:</span>
                        <span><?= showWithCurrencySymbol($invoiceData->previous_due) ?></span>
                    </div>
                <?php } ?>

                <div class="total-row grand">
                    <span>TOTAL DUE:</span>
                    <span><?= !empty($invoiceData->previous_due) ? showWithCurrencySymbol($invoiceData->total) : showWithCurrencySymbol($invoiceData->due); ?></span>
                </div>
            </div>

            <div class="dashed"></div>

            <!-- BARCODE -->
            <div class="qr-section">
                <div class="qr-box">
                    <?= barcodeGenaret($invoiceId); ?>
                </div>
                <div class="upi-logo">Invoice Id: Inv_<?= $invoiceId ?></div>
            </div>

            <div class="dashed"></div>

            <!-- ========== FOOTER ========== -->
            <div class="footer">
                <div class="footer-message">
                    ফোলা, ফাটা, চাপ খাওয়া, পোড়া, ভাঙ্গা, ডেট ওভার, জিরো ভোল্টেজ মাল ফেরত যোগ্য নয়।
                </div>

                <div class="footer-info">
                    Email : mirtrading.jsr@gmail.com<br>
                    Bin: 003174613-0901
                </div>

                <div class="signature-row">
                    <div class="signature">
                        <div class="signature-line"></div>
                        <span>Buyer Signature</span>
                    </div>
                    <div class="signature">
                        <div class="signature-line"></div>
                        <span>Authorised Officer</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row no-print">
            <div class="col-xs-12" style="margin-top: 15px;">
                <div class="print_line btn btn-primary pull-right" onclick="print(document);">
                    <i class="fa fa-print"></i> Print Now
                </div>
            </div>
        </div>
    </section>
</div>