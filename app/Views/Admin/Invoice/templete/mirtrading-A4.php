<?php
// Preload sold/returned serial numbers and the customer warranty end date for
// this invoice, grouped by stock line (product_stock_relation_id) so each item
// row can show its serial number(s) and warranty date.
$invoiceSerials = array();
if (!empty($invoiceId)) {
    $serialRows = DB()->table('product_serial')
        ->select('product_stock_relation_id, serial_number, customer_warranty_end_date')
        ->where('invoice_id', $invoiceId)
        ->whereIn('status', array('sold', 'returned'))
        ->orderBy('product_serial_id', 'ASC')
        ->get()
        ->getResultArray();
    foreach ($serialRows as $sr) {
        $key = (string) $sr['product_stock_relation_id'];
        if (!isset($invoiceSerials[$key])) {
            $invoiceSerials[$key] = array('serials' => array(), 'warranty' => '');
        }
        $invoiceSerials[$key]['serials'][] = $sr['serial_number'];
        if ($invoiceSerials[$key]['warranty'] === '' && !empty($sr['customer_warranty_end_date'])) {
            $invoiceSerials[$key]['warranty'] = $sr['customer_warranty_end_date'];
        }
    }
}

// Null-safe invoice settings lookup (uses the current session shop).
$__inv = function ($label) {
    $row = DB()->table('gen_settings')
        ->where('label', $label)
        ->where('sch_id', $_SESSION['shopId'])
        ->get()->getRow();
    return $row ? (string) $row->value : '';
};
$invTitle = $__inv('invoice_title');
$invSubTitle = $__inv('invoice_sub_title');
$invAddress = $__inv('invoice_address');
$invBinNo = $__inv('invoice_bin_no');
$invFooter = $__inv('invoice_footer_text');
$invLogo = $__inv('invoice_logo');
$invEmails = array_values(array_filter(array_map('trim', explode(',', $__inv('invoice_gmail'))), function ($v) {
    return $v !== '';
}));
$invContacts = array_values(array_filter(array_map('trim', explode(',', $__inv('invoice_name_phone'))), function ($v) {
    return $v !== '';
}));
?>
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
        <div class="invoice-page" >
            <table class="print-wrapper">
                <!-- ===================== HEADER (repeats on every page) ===================== -->
                <thead>
                    <tr>
                        <td>
                            <header class="invoice-header print-header">

                                <div class="header-left">
<!--                                    <img src="--><?php //= base_url('uploads/mirlogo.png')?><!--" class="" alt="">-->
<!--                                    <img src="--><?php //= base_url('uploads/scooter.png')?><!--" class="left-img" alt="">-->
                                    <img src="<?= no_image_view('/uploads/schools/' . $invLogo, '/uploads/logo.png', $invLogo) ?>" >
                                </div>

                                <div class="header-right">
<!--                                    <img src="--><?php //= base_url('uploads/rickshaw.png')?><!--" class="right-img" alt="">-->
                                </div>

                                <div class="invoice-badge">
                                    Invoice/Bill
                                </div>

                                <?php if ($invTitle !== '') { ?>
                                <h1 class="company-name">
                                    <?= esc($invTitle) ?>
                                </h1>
                                <?php } ?>

                                <?php if ($invSubTitle !== '') { ?>
                                <div class="company-subtitle">
                                    <?= esc($invSubTitle) ?>
                                </div>
                                <?php } ?>

                                <?php if ($invAddress !== '') { ?>
                                <div class="company-address">
                                    <?= esc($invAddress) ?>
                                </div>
                                <?php } ?>

                                <?php if (!empty($invEmails)) { ?>
                                <div class="company-email">
                                    <?php foreach ($invEmails as $__email) { ?>Email : <?= esc($__email) ?><br><?php } ?>
                                </div>
                                <?php } ?>

                                <?php if (!empty($invContacts)) { ?>
                                <div class="header-contacts">
                                    <?= esc(implode(', ', $invContacts)) ?>
                                </div>
                                <?php } ?>

                            </header>
                        </td>
                    </tr>
                </thead>
                <!-- ===================== CONTENT ===================== -->
                <tbody>
                    <tr>
                        <td>
                            <main class="invoice-content" >
                                <!-- BILL TO + INVOICE DETAILS -->
                                <div class="content-top">
                                    <div class="bill-to">
                                        <p class="invoice-label"> BILL TO </p>
                                        <h3> <?= ($invoiceData->customer_id == 0) ? $invoiceData->customer_name : get_data_by_id('customer_name', 'customers', 'customer_id', $invoiceData->customer_id); ?> </h3>
                                        <p>
                                            <?php if (!empty($invoiceData->customer_id)) { ?>
                                                Phone: <?= showWithPhoneNummberCountryCode(get_data_by_id('mobile', 'customers', 'customer_id', $invoiceData->customer_id)) ?>
                                                <br>
                                                Address: <?= get_data_by_id('address', 'customers', 'customer_id', $invoiceData->customer_id) ?>
                                            <?php } ?>
                                        </p>
                                    </div>
                                    <div class="invoice-info">
                                        <div class="invoice-row">
                                            <span>INVOICE NUMBER</span>
                                            <strong>Inv_<?= $invoiceId ?></strong>
                                        </div>
                                        <div class="invoice-row">
                                            <span>DATE OF ISSUE</span>
                                            <strong><?= invoiceDateFormat($invoiceData->createdDtm); ?></strong>
                                        </div>

                                    </div>
                                </div>
                                <table class="invoice-table text-capitalize">
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>WARRANTY DATE</th>
                                            <th>PRICE</th>
                                            <th>QTY</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($invoiceItame as $row) {
                                        $catId = get_data_by_id('prod_cat_id', 'products', 'prod_id', $row->prod_id);
                                        $parent_pro_cat = get_data_by_id('parent_pro_cat', 'product_category', 'prod_cat_id', $catId);
                                        $category = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $parent_pro_cat);
                                        $subCategory = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $catId);
                                        $productName = get_data_by_id('name', 'products', 'prod_id', $row->prod_id);
                                        $unit = productIdByDefaultStoreUnit($row->prod_id);

                                        $psrKey = (string) ($row->product_stock_relation_id ?? '');
                                        $lineSerial = $invoiceSerials[$psrKey] ?? null;
                                        $serialHtml = ($lineSerial && !empty($lineSerial['serials'])) ? '<br><small><b>Serial:</b> ' . esc(implode(', ', $lineSerial['serials'])) . '</small>' : '';
                                        $warrantyVal = ($lineSerial && $lineSerial['warranty'] !== '') ? esc($lineSerial['warranty']) : '---';
                                        ?>
                                        <tr>
                                            <td><?= $productName . '<br> <small>(' . $category . ' > ' . $subCategory . ')</small>' . $serialHtml; ?></td>
                                            <td><?= $warrantyVal; ?></td>
                                            <td><?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit, $row->price)); ?></td>
                                            <td><?= unitOrQtyByUnitQty($unit, $row->quantity); ?>/<?php echo showUnitName($unit) ?></td>
                                            <td><?= showWithCurrencySymbol($row->total_price); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <div class="totals">
                                    <div class="total-row">
                                        <div class="total-label"> Products Total </div>
                                        <div class="total-value">
                                            <?= showWithCurrencySymbol($invoiceData->amount); ?>
                                            <input type="hidden" id="totalAmount" value="<?= $invoiceData->amount ?>" >
                                        </div>
                                    </div>
                                    <?php if (!empty($invoiceData->entire_sale_discount)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Discount (<?= $invoiceData->entire_sale_discount ?>%)
                                                <input type="hidden" id="discount" value="<?= $invoiceData->entire_sale_discount ?>">
                                            </div>

                                            <div class="total-value">
                                                <span id="discountAmount"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->vat)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Vat (<?= $invoiceData->vat ?>%)
                                                <input type="hidden" id="vat" value="<?= $invoiceData->entire_sale_discount ?>">
                                            </div>

                                            <div class="total-value">
                                                <span id="vatAmount"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->final_amount)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Subtotal
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->final_amount) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->nagad_paid)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Cash Pay
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->nagad_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->bank_id)) {
                                        $bankDetail = getTotalRow('bank', 'bank_id', $invoiceData->bank_id); ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Bank Pay
                                                <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                                        <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    <span class="tooltip-text"><?= $bankDetail->name ?>--<?= $bankDetail->account_no ?></span>
                                                </div>
                                            </div>
                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->bank_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->chaque_id)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Cheque Pay
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->chaque_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="total-row">
                                        <div class="total-label">
                                            Today Due
                                        </div>

                                        <div class="total-value">
                                            <?= showWithCurrencySymbol($invoiceData->due) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($invoiceData->previous_due)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Previous <?= ($invoiceData->previous_due > 0) ? 'Due' : 'Balance'; ?>
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->previous_due) ?>
                                            </div>
                                        </div>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Total <?= ($invoiceData->total > 0) ? 'Due' : 'Balance'; ?>
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->total) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="balance-due">
                                    <span class="balance-label">
                                        TOTAL DUE
                                    </span>
                                    <span class="balance-amount">
                                        <?= !empty($invoiceData->previous_due) ? showWithCurrencySymbol($invoiceData->total) : showWithCurrencySymbol($invoiceData->due); ?>
                                    </span>
                                </div>
                            </main>
                        </td>
                    </tr>
                </tbody>
                <!-- ===================== FOOTER (repeats on every page) ===================== -->
                <tfoot>
                    <tr>
                        <td>
                            <footer class="invoice-footer print-footer">
                                <div class="signature-row">
                                    <div class="signature">

                                        <div class="signature-line"></div>

                                        <span>
                                            Buyer Signature
                                        </span>

                                    </div>
                                    <div class="footer-center">

                                        <?php if ($invFooter !== '') { ?>
                                        <div class="footer-message">
                                            <?= esc($invFooter) ?>
                                        </div>
                                        <?php } ?>

                                        <?php if (!empty($invEmails)) { ?>
                                        <div class="footer-email">
                                            <?php foreach ($invEmails as $__email) { ?>Email : <?= esc($__email) ?><br><?php } ?>
                                        </div>
                                        <?php } ?>

                                        <?php if ($invBinNo !== '') { ?>
                                        <div class="footer-bin">
                                            Bin: <?= esc($invBinNo) ?>
                                        </div>
                                        <?php } ?>

                                    </div>
                                    <div class="signature">

                                        <div class="signature-line"></div>

                                        <span>
                                            Authorised Officer
                                        </span>

                                    </div>
                                </div>
                            </footer>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="invoice-page" id="invoicePage" style="display: none;">
            <style>
                #invoicePage.invoice-page { min-height: auto; }
                #invoicePage .invoice-content { min-height: 0 !important; }
                #invoicePage .invoice-header,
                #invoicePage .invoice-footer,
                #invoicePage .invoice-table tr,
                #invoicePage .totals,
                #invoicePage .balance-due {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }

                .invoice-page {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0 auto 30px;
                    background: #fff;
                    padding: 0;
                    box-shadow: 0 0 12px rgba(0,0,0,.25);
                }

                /* Header */
                .invoice-header {
                    position: relative;
                    padding: 12px 20px 10px;
                    border-bottom: 1px solid #3155a0;
                    text-align: center;
                }

                .header-left {
                    position: absolute;
                    left: 80px;
                    top: 20px;
                    width:184px;
                }

                .header-left img {
                    /*max-width: 65px;*/
                    /*display: block;*/

                    max-width: 184px;
                    display: block;
                    margin-left: -59px;
                }

                .header-right {
                    position: absolute;
                    right: 80px;
                    top: 85px;
                    width: 90px;
                }

                .header-right img {
                    max-width: 85px;
                    display: block;
                }

                .invoice-badge {
                    display: inline-block;
                    margin: 0 auto 6px;
                    padding: 4px 22px;
                    border-radius: 20px;
                    background: #3155a0;
                    color: #fff;
                    font-family: Georgia, serif;
                    font-weight: bold;
                    font-size: 13px;
                }

                .company-name {
                    margin: 0;
                    color: #3155a0;
                    font-family: Georgia, "Times New Roman", serif;
                    font-size: 36px;
                    line-height: 1.1;
                    letter-spacing: 1.5px;
                    font-weight: bold;
                }

                .company-subtitle {
                    margin: 4px 0 0;
                    color: #3155a0;
                    font-family: Georgia, serif;
                    font-size: 15px;
                    font-weight: bold;
                    font-style: italic;
                }

                .company-address,
                .company-email {
                    margin: 3px 0 0;
                    color: #3155a0;
                    font-family: Georgia, serif;
                    font-size: 12px;
                }

                .header-contacts {
                    margin-top: 8px;
                    color: #3155a0;
                    font-size: 11px;
                    font-weight: bold;
                }

                /* Content */
                .invoice-content {
                    padding: 18px 20px 10px;
                    min-height: 220mm !important;
                }

                .content-top {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    padding: 20px !important;
                }

                .bill-to {
                    width: 55%;
                }

                .invoice-label {
                    margin: 0 0 6px;
                    color: #506582;
                    font-size: 11px;
                    font-weight: bold;
                    letter-spacing: 0.5px;
                }

                .bill-to h3 {
                    margin: 0 0 4px;
                    font-size: 17px;
                    color: #172033;
                }

                .bill-to p {
                    margin: 0;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                }

                .invoice-info {
                    width: 42%;
                    text-align: right;
                }

                .invoice-row {
                    display: flex;
                    justify-content: flex-end;
                    gap: 14px;
                    margin-bottom: 5px;
                    font-size: 11px;
                }

                .invoice-row span {
                    color: #506582;
                }

                .invoice-row strong {
                    min-width: 90px;
                    color: #172033;
                }

                /* Table */
                .invoice-table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                    margin-bottom: 12px;
                }

                .invoice-table thead {
                    background: #f4f6f8;
                }

                .invoice-table th {
                    padding: 8px 6px;
                    border-bottom: 2px solid #172033;
                    color: #506582;
                    font-size: 10px;
                    text-align: right;
                }

                .invoice-table th:first-child {
                    width: 48%;
                    text-align: left;
                }

                .invoice-table td {
                    padding: 6px;
                    border-bottom: 1px solid #dfe3e8;
                    font-size: 11px;
                    text-align: right;
                    color: #172033;
                }

                .invoice-table td:first-child {
                    text-align: left;
                }

                /* Totals */
                .totals {
                    width: 100%;
                    margin-top: 10px;
                }

                .total-row {
                    display: flex;
                    justify-content: flex-end;
                    margin-bottom: 6px;
                    font-size: 11px;
                }

                .total-label {
                    width: 120px;
                    color: #506582;
                }

                .total-value {
                    width: 110px;
                    text-align: right;
                    color: #172033;
                }

                .balance-due {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 12px;
                    padding: 8px 10px;
                    border: 1px solid #c5cbd4;
                    border-radius: 3px;
                    background: #f4f6f8;
                }

                .balance-label {
                    font-size: 11px;
                    font-weight: bold;
                    color: #172033;
                }

                .balance-amount {
                    font-size: 13px;
                    font-weight: bold;
                    color: #172033;
                }

                /* Footer */
                .invoice-footer {
                    padding: 10px 20px 12px;
                    border-top: 1px solid #3155a0;
                    color: #3155a0;
                }

                .signature-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                }

                .signature {
                    width: 140px;
                    text-align: center;
                }

                .signature-line {
                    border-top: 1px solid #3155a0;
                    margin-bottom: 4px;
                }

                .signature span {
                    font-family: Georgia, serif;
                    font-size: 11px;
                }

                .footer-center {
                    width: 320px;
                    text-align: center;
                }

                .footer-message {
                    background: #3155a0;
                    color: #fff;
                    border-radius: 6px;
                    padding: 6px 10px;
                    font-family: Georgia, serif;
                    font-size: 10px;
                    line-height: 1.3;
                }

                .footer-email,
                .footer-bin {
                    margin-top: 6px;
                    font-size: 11px;
                }


                @media print {

                    *, *::before, *::after {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }

                    @page {
                        size: A4 portrait;
                        margin: 10mm 8mm;
                    }

                    html,
                    body {
                        margin: 0 !important;
                        padding: 0 !important;
                    }

                    .print-btn,
                    .no-print {
                        display: none !important;
                    }

                    .invoice-page {
                        width: 100% !important;
                        min-height: 277mm !important; /* A4 printable height */
                        margin: 0 !important;
                        padding: 0 !important;
                        box-shadow: none !important;
                        background: white !important;
                    }

                    /* Main print table */
                    .print-wrapper {
                        width: 100% !important;
                        min-height: 277mm !important;
                        border-collapse: collapse !important;
                        border-spacing: 0 !important;
                    }

                    .print-wrapper > thead {
                        display: table-header-group !important;
                    }

                    .print-wrapper > tbody {
                        display: table-row-group !important;
                    }

                    .print-wrapper > tfoot {
                        display: table-footer-group !important;
                    }

                    .print-wrapper > thead > tr > td,
                    .print-wrapper > tbody > tr > td,
                    .print-wrapper > tfoot > tr > td {
                        padding: 0 !important;
                        border: none !important;
                        vertical-align: top !important;
                    }

                    /* Header */
                    .invoice-header {
                        position: relative !important;
                        padding: 8px 12px 6px !important;
                        border-bottom: 1px solid #3155a0 !important;
                        background: white !important;
                    }

                    /* Content */
                    .invoice-content {
                        padding: 20px 20px 8px !important;
                        min-height: 220mm !important;
                    }

                    /* Invoice table */
                    .invoice-table {
                        width: 100% !important;
                        border-collapse: collapse !important;
                    }

                    .invoice-table thead {
                        display: table-header-group !important;
                        background: #f4f6f8 !important;
                    }

                    .invoice-table tr {
                        page-break-inside: avoid !important;
                        break-inside: avoid !important;
                    }

                    /* Footer */
                    .invoice-footer {
                        padding: 8px 12px 6px !important;
                        border-top: 1px solid #3155a0 !important;
                        background: white !important;
                    }

                    /* Prevent totals from splitting */
                    .totals,
                    .balance-due {
                        page-break-inside: avoid !important;
                        break-inside: avoid !important;
                    }

                    /* Colors */
                    .invoice-header,
                    .company-name,
                    .company-subtitle,
                    .company-address,
                    .company-email,
                    .header-contacts,
                    .footer-email,
                    .footer-bin,
                    .signature span {
                        color: #3155a0 !important;
                    }

                    .invoice-badge {
                        background: #3155a0 !important;
                        color: #fff !important;
                    }

                    .footer-message {
                        background: #3155a0 !important;
                        color: #fff !important;
                    }

                    .balance-due {
                        background: #f4f6f8 !important;
                        border-color: #c5cbd4 !important;
                    }
                }

            </style>
            <table class="print-wrapper">
                <!-- ===================== HEADER (repeats on every page) ===================== -->
                <thead>
                    <tr>
                        <td>
                            <header class="invoice-header print-header">

                                <div class="header-left">
                                    <img src="<?= no_image_view('/uploads/schools/' . $invLogo, '/uploads/logo.png', $invLogo) ?>" class="" alt="">
<!--                                    <img src="--><?php //= base_url('uploads/mirlogo.png')?><!--" class="" alt="">-->
<!--                                    <img src="--><?php //= base_url('uploads/scooter.png')?><!--" class="left-img" alt="">-->
                                </div>

                                <div class="header-right">
<!--                                    <img src="--><?php //= base_url('uploads/rickshaw.png')?><!--" class="right-img" alt="">-->
                                </div>

                                <div class="invoice-badge">
                                    Invoice/Bill
                                </div>

                                <?php if ($invTitle !== '') { ?>
                                <h1 class="company-name">
                                    <?= esc($invTitle) ?>
                                </h1>
                                <?php } ?>

                                <?php if ($invSubTitle !== '') { ?>
                                <div class="company-subtitle">
                                    <?= esc($invSubTitle) ?>
                                </div>
                                <?php } ?>

                                <?php if ($invAddress !== '') { ?>
                                <div class="company-address">
                                    <?= esc($invAddress) ?>
                                </div>
                                <?php } ?>

                                <?php if (!empty($invEmails)) { ?>
                                <div class="company-email">
                                    <?php foreach ($invEmails as $__email) { ?>Email : <?= esc($__email) ?><br><?php } ?>
                                </div>
                                <?php } ?>

                                <?php if (!empty($invContacts)) { ?>
                                <div class="header-contacts">
                                    <?= esc(implode(', ', $invContacts)) ?>
                                </div>
                                <?php } ?>

                            </header>
                        </td>
                    </tr>
                </thead>
                <!-- ===================== CONTENT ===================== -->
                <tbody>
                    <tr>
                        <td>
                            <main class="invoice-content" >

                                <!-- BILL TO + INVOICE DETAILS -->

                                <div class="content-top">

                                    <div class="bill-to">

                                        <p class="invoice-label">
                                            BILL TO
                                        </p>

                                        <h3> <?= ($invoiceData->customer_id == 0) ? $invoiceData->customer_name : get_data_by_id('customer_name', 'customers', 'customer_id', $invoiceData->customer_id); ?> </h3>
                                        <p>
                                            <?php if (!empty($invoiceData->customer_id)) { ?>
                                                Phone: <?= showWithPhoneNummberCountryCode(get_data_by_id('mobile', 'customers', 'customer_id', $invoiceData->customer_id)) ?>
                                                <br>
                                                Address: <?= get_data_by_id('address', 'customers', 'customer_id', $invoiceData->customer_id) ?>
                                            <?php } ?>
                                        </p>

                                    </div>


                                    <div class="invoice-info">

                                        <div class="invoice-row">
                                            <span>INVOICE NUMBER</span>
                                            <strong>Inv_<?= $invoiceId ?></strong>
                                        </div>

                                        <div class="invoice-row">
                                            <span>DATE OF ISSUE</span>
                                            <strong><?= invoiceDateFormat($invoiceData->createdDtm); ?></strong>
                                        </div>

                                    </div>

                                </div>

                                <table class="invoice-table text-capitalize">

                                    <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>WARRANTY DATE</th>
                                        <th>PRICE</th>
                                        <th>QTY</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($invoiceItame as $row) {
                                        $catId = get_data_by_id('prod_cat_id', 'products', 'prod_id', $row->prod_id);
                                        $parent_pro_cat = get_data_by_id('parent_pro_cat', 'product_category', 'prod_cat_id', $catId);
                                        $category = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $parent_pro_cat);
                                        $subCategory = get_data_by_id('product_category', 'product_category', 'prod_cat_id', $catId);
                                        $productName = get_data_by_id('name', 'products', 'prod_id', $row->prod_id);
                                        $unit = productIdByDefaultStoreUnit($row->prod_id);

                                        $psrKey = (string) ($row->product_stock_relation_id ?? '');
                                        $lineSerial = $invoiceSerials[$psrKey] ?? null;
                                        $serialHtml = ($lineSerial && !empty($lineSerial['serials'])) ? '<br><small><b>Serial:</b> ' . esc(implode(', ', $lineSerial['serials'])) . '</small>' : '';
                                        $warrantyVal = ($lineSerial && $lineSerial['warranty'] !== '') ? esc($lineSerial['warranty']) : '---';
                                        ?>
                                        <tr>
                                            <td><?= $productName . '<br> <small>(' . $category . ' > ' . $subCategory . ')</small>' . $serialHtml; ?></td>
                                            <td><?= $warrantyVal; ?></td>
                                            <td><?= showWithCurrencySymbol(unitOrBasePriceByUnitPrice($unit, $row->price)); ?></td>
                                            <td><?= unitOrQtyByUnitQty($unit, $row->quantity); ?>/<?php echo showUnitName($unit) ?></td>
                                            <td><?= showWithCurrencySymbol($row->total_price); ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>

                                </table>

                                <div class="totals">

                                    <div class="total-row">
                                        <div class="total-label">
                                            Products Total
                                        </div>

                                        <div class="total-value">
                                            <?= showWithCurrencySymbol($invoiceData->amount); ?>
                                            <input type="hidden" id="totalAmount" value="<?= $invoiceData->amount ?>">
                                        </div>
                                    </div>
                                    <?php if (!empty($invoiceData->entire_sale_discount)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Discount (<?= $invoiceData->entire_sale_discount ?>%)
                                            </div>

                                            <div class="total-value">
                                                <span id="discountAmount2"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->vat)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Vat (<?= $invoiceData->vat ?>%)
                                            </div>

                                            <div class="total-value">
                                                <span id="vatAmount2"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->final_amount)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Subtotal
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->final_amount) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($invoiceData->nagad_paid)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Cash Pay
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->nagad_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($invoiceData->bank_id)) {
                                        $bankDetail = getTotalRow('bank', 'bank_id', $invoiceData->bank_id); ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Bank Pay<br>
                                                <span class="tooltip-text"><?= $bankDetail->name ?>--<?= $bankDetail->account_no ?></span>
                                            </div>
                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->bank_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($invoiceData->chaque_id)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Cheque Pay
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->chaque_paid) ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="total-row">
                                        <div class="total-label">
                                            Today Due
                                        </div>

                                        <div class="total-value">
                                            <?= showWithCurrencySymbol($invoiceData->due) ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($invoiceData->previous_due)) { ?>
                                        <div class="total-row">
                                            <div class="total-label">
                                                Previous <?= ($invoiceData->previous_due > 0) ? 'Due' : 'Balance'; ?>
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->previous_due) ?>
                                            </div>
                                        </div>

                                        <div class="total-row">
                                            <div class="total-label">
                                                Total <?= ($invoiceData->total > 0) ? 'Due' : 'Balance'; ?>
                                            </div>

                                            <div class="total-value">
                                                <?= showWithCurrencySymbol($invoiceData->total) ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>

                                <div class="balance-due">

                                <span class="balance-label">
                                    TOTAL DUE
                                </span>

                                    <span class="balance-amount">
                                        <?= !empty($invoiceData->previous_due) ? showWithCurrencySymbol($invoiceData->total) : showWithCurrencySymbol($invoiceData->due); ?>
                                    </span>

                                </div>

                            </main>
                        </td>
                    </tr>
                </tbody>
                <!-- ===================== FOOTER (repeats on every page) ===================== -->
                <tfoot>
                    <tr>
                        <td>
                            <footer class="invoice-footer print-footer">

                                <div class="signature-row">

                                    <div class="signature">

                                        <div class="signature-line"></div>

                                        <span>
                                Buyer Signature
                            </span>

                                    </div>


                                    <div class="footer-center">

                                        <?php if ($invFooter !== '') { ?>
                                        <div class="footer-message">
                                            <?= esc($invFooter) ?>
                                        </div>
                                        <?php } ?>

                                        <?php if (!empty($invEmails)) { ?>
                                        <div class="footer-email">
                                            <?php foreach ($invEmails as $__email) { ?>Email : <?= esc($__email) ?><br><?php } ?>
                                        </div>
                                        <?php } ?>

                                        <?php if ($invBinNo !== '') { ?>
                                        <div class="footer-bin">
                                            Bin: <?= esc($invBinNo) ?>
                                        </div>
                                        <?php } ?>

                                    </div>


                                    <div class="signature">

                                        <div class="signature-line"></div>

                                        <span>
                                            Authorised Officer
                                        </span>

                                    </div>

                                </div>

                            </footer>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row no-print">
            <div class="col-xs-12" style="margin-top: 20px;">
                <div class="print_line btn btn-primary pull-right" onclick="printDiv('invoicePage')" >
                    <i class="fa fa-print"></i> Print Now
                </div>
                <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('invoicePage','Invoice','Inv_<?= $invoiceId ?>.pdf')"><i class="fa fa-file-pdf-o"></i> Download PDF </button>
                <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="sharePDFChoose('invoicePage','Invoice','Inv_<?= $invoiceId ?>.pdf')"><i class="fa fa-share-square-o"></i> Share </button>
            </div>
        </div>
    </section>

</div>