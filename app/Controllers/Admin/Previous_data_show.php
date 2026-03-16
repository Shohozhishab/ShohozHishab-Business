<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Previous_data_show extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $forge;
    protected $crop;
    private $module_name = 'Previous_data_show';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->forge = \Config\Database::forge();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Previous_data_show/list');
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides data view
     * @return RedirectResponse|void
     */
    public function data()
    {


        $year = $this->request->getPost('year');
        $newDb = BACKUP_DB_PREFIX . $year;

        $newdbExist = DB()->query("SHOW DATABASES LIKE '" . $newDb . "'")->getNumRows();

        if (empty($newdbExist)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> You did not generate yearly closing in the year ' . $year . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Previous_data_show'));
        } else {

            $this->session->set(['newBD' => $newDb]);

            DB()->query('use ' . $newDb);

            $shopId = $this->session->shopId;
            $tableExists = DB()->query("SHOW TABLES LIKE '%shops%'")->getNumRows();

            if (!empty($tableExists)) {
                $shopsCount = DB()->table('shops');
                $shopExist = $shopsCount->where('sch_id', $shopId)->countAllResults();

                if (!empty($shopExist)) {

                    $customersTab = DB()->table('customers');
                    $customer = $customersTab->where('sch_id', $shopId)->get()->getResult();


                    //shops cash search(start)
                    $shopsTab = DB()->table('shops');
                    $cash = $shopsTab->where('sch_id', $shopId)->get()->getRow()->cash;
                    //shops cash search(start)

                    //total bank amount calculet(start)
                    $bankTab = DB()->table('bank');
                    $bankCash = $bankTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
                    //total bank amount calculet(end)

                    //total customer balance calculet (start)
                    $customersBalTab = DB()->table('customers');
                    $customerCash = $customersBalTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
                    //total customer balance calculet (end)

                    //total Lone provider balance calculet(start)
                    $loan_providerTab = DB()->table('loan_provider');
                    $loanPro = $loan_providerTab->where('sch_id', $shopId)->get()->getResult();

                    $loanProGetCash = 0;
                    foreach ($loanPro as $result) {
                        if ($result->balance > 0) {
                            $loanProGetCash += $result->balance;
                        }
                    }
                    //total Lone provider balance calculet(end)


                    //total invoice profite calculet (start)
                    $invoiceTab = DB()->table('invoice');
                    $invoiceCash = $invoiceTab->selectSum('profit')->where('sch_id', $shopId)->get()->getRow()->profit;

                    $productsTab = DB()->table('products');
                    $product = $productsTab->where('sch_id', $shopId)->get()->getResult();
                    $totalProdPrice = 0;
                    foreach ($product as $row) {
                        $totalProdPrice += $row->quantity * $row->purchase_price;
                    }
                    //total invoice profite calculet (end)


                    //total other sale amount calculet (start)
                    $ledger_other_salesTab = DB()->table('ledger_other_sales');
                    $otherSaleCash = $ledger_other_salesTab->selectSum('amount')->where('sch_id', $shopId)->get()->getRow()->amount;
                    //total other sale amount calculet (end)


                    //Total balance calculet (start)
                    $totalGetCash = $customerCash + $loanProGetCash;
                    $totalCash = $cash + $bankCash + $totalGetCash + $totalProdPrice;
                    //Total balance calculet (end)


                    //total employee Balance (start)
                    $employeeTab = DB()->table('employee');
                    $employeeBalan = $employeeTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
                    //total employee Balance (end)


                    //total supplier due balance calculet (start)
                    $suppliersTab = DB()->table('suppliers');
                    $suppl = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
                    $suppCash = 0;
                    foreach ($suppl as $result) {
                        if ($result->balance < 0) {
                            $suppCash -= $result->balance;
                        }
                    }
                    //total supplier due balance calculet (end)


                    //total Lone due Cash calculet (start)
                    $loanProCash = 0;
                    foreach ($loanPro as $result) {
                        if ($result->balance < 0) {
                            $loanProCash -= $result->balance;
                        }
                    }
                    $totalDueCash = $suppCash + $loanProCash;
                    //total Lone due Cash calculet (end)


                    //total expens amount calculet (start)
                    $ledger_expenseTab = DB()->table('ledger_expense');
                    $expenseCash = $ledger_expenseTab->selectSum('amount')->where('sch_id', $shopId)->get()->getRow()->amount;
                    //total expens amount calculet (end)


                    //total return sale return profit(start)
                    $return_saleTab = DB()->table('return_sale');
                    $returnProfit = $return_saleTab->selectSum('rtn_profit')->where('sch_id', $shopId)->get()->getRow()->rtn_profit;
                    //total return sale return profit(start)

                    // Total vat earning (start)
                    $vat_registerTab = DB()->table('vat_register');
                    $vatEarning = $vat_registerTab->where('sch_id', $shopId)->get()->getRow()->balance;
                    // Total vat earning (end)


                    //Total due balance calculet (start)
                    $totalDue = $totalDueCash;
                    $totalBalance = $totalCash - $totalDue;
                    //Total due balance calculet (end)


                    $data = array(
                        'cash' => $cash,
                        'bankCash' => $bankCash,
                        'totalCash' => $totalCash,
                        'invoiceCash' => $invoiceCash,
                        'otherSaleCash' => $otherSaleCash,
                        'totalProdPrice' => $totalProdPrice,
                        'totalDue' => $totalDue,
                        'totalGetCash' => $totalGetCash,
                        'expenseCash' => $expenseCash,
                        'totalDueCash' => $totalDueCash,
                        'loanProGetCash' => $loanProGetCash,
                        'totalBalance' => $totalBalance,
                        'employeeBalan' => $employeeBalan,
                        'returnProfit' => $returnProfit,
                        'vatEarning' => $vatEarning,
                        'customer' => $customer,
                    );


                    echo view('Admin/header');
                    echo view('Admin/sidebar');
                    echo view('Admin/Previous_data_show/show', $data);
                    echo view('Admin/footer');
                } else {
                    $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> You did not generate yearly closing in the year ' . $year . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to(site_url('Admin/Previous_data_show'));
                }
            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> You did not generate yearly closing in the year ' . $year . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Previous_data_show'));
            }
        }

    }

    /**
     * @description This method customer ledger
     * @return void
     */
    public function customer_ledger()
    {
        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $customersTab = DB()->table('customers');

        $customers = $customersTab->where('sch_id', $shopId)->get()->getResult();;
        $url = "'" . site_url('Admin/previous_data_show/customer') . "'";

        $view = '<label>Customer name</label><select class="form-control select2 select2-hidden-accessible" onchange="searchLedger(this.value,' . $url . ')" id="customer_id" style=" width: 100%;" tabindex="-1" aria-hidden="true"><option selected="selected"  value="">Please Select</option>';
        foreach ($customers as $row) {
            $view .= '<option value="' . $row->customer_id . '" >' . $row->customer_name . '</option>';
        }
        $view .= '</select>';
        print $view;
    }

    /**
     * @description This method provides customer view
     * @return void
     */
    public function customer()
    {

        $newDb = $this->session->newBD;
        DB()->query('use ' . $newDb);

        $customerId = $this->request->getPost('Id');

        $ledgerTab = DB()->table('ledger');
        $data = $ledgerTab->where('customer_id', $customerId)->get()->getResult();

        $customersTab = DB()->table('customers');
        $cus = $customersTab->where('customer_id', $customerId)->get()->getRow();
        $name = $cus->customer_name;
        $balance = $cus->balance;

        $view = ' <div class="box" >
            <div class="box-header">  
                <h3 class="box-title">Customer: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable">
                            <tr><td>Last Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>                          
            </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="example1">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Invoice Id/Memo</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);

            $invoice_id = ($data[$i]->invoice_id == NULL) ? '<a href="' . site_url('transaction/moneyReceipt/' . $data[$i]->trans_id) . '">TRNS_' . $data[$i]->trans_id . '</a>' : '<a href="' . site_url('invoice/read/' . $data[$i]->invoice_id) . '">INV_' . $data[$i]->invoice_id . '</a>';
            $view .= '<tr>
                        <td>' . $data[$i]->ledg_id . '</td>
                        <td>' . $data[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $invoice_id . '</td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                <tfoot>
                    <tr>
                    <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Invoice Id/Memo</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th>= ' . showWithCurrencySymbol($balance) . '</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>';

        print $view;
    }

    /**
     * @description This method bank ledger
     * @return void
     */
    public function bank_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $url = "'" . site_url('Admin/previous_data_show/bank') . "'";
        $bankTab = DB()->table('bank');
        $bank = $bankTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<label>Bank name</label><select class="form-control select2 select2-hidden-accessible" onchange="searchLedger(this.value,' . $url . ')" id="bank_id" style=" width: 100%;" tabindex="-1" aria-hidden="true"><option selected="selected"  value="">Please Select</option>';
        foreach ($bank as $row) {
            $view .= '<option value="' . $row->bank_id . '" >' . $row->name . '</option>';
        }
        $view .= '</select>';
        print $view;
    }

    /**
     * @description This method provides bank view
     * @return void
     */
    public function bank()
    {

        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $bankId = $this->request->getPost('Id');

        $ledger_bankTab = DB()->table('ledger_bank');
        $data = $ledger_bankTab->where("bank_id", $bankId)->orderBy("ledgBank_id", "DESC")->get()->getResult();
        $bankTab = DB()->table('bank');
        $ban = $bankTab->where('bank_id', $bankId)->get()->getRow();
        $name = $ban->name;
        $account = $ban->account_no;
        $balance = $ban->balance;

        $view = ' <div class="box" >
                <div class="box-header">
                    <h3 class="box-title">
                    Bank: ' . $name . ' -- ' . $account . '</h3>
                    <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                </div>
            <div class="box-body">
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Tran Id</th>
                            <th>purc Id</th>
                            <th>inv Id</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $transId = ($data[$i]->trans_id == NULL) ? "---" : $data[$i]->trans_id;
            $purchaseId = ($data[$i]->purchase_id == NULL) ? "---" : $data[$i]->purchase_id;
            $invoiceId = ($data[$i]->invoice_id == 0) ? "---" : $data[$i]->invoice_id;
            $view .= '<tr>
                            <td>' . $data[$i]->ledgBank_id . '</td>
                            <td>' . $data[$i]->createdDtm . '</td>
                            <td>' . $particulars . '</td>
                            <td>' . $transId . '</td>
                            <td>' . $purchaseId . '</td>
                            <td>' . $invoiceId . '</td>
                            <td>' . $amountDr . '</td>
                            <td>' . $amountCr . '</td>
                            <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                        </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Tran Id</th>
                            <th>purc Id</th>
                            <th>inv Id</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>';

        print $view;
    }

    /**
     * @description This method loan ledger
     * @return void
     */
    public function loan_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $url = "'" . site_url('Admin/previous_data_show/loanProvider') . "'";

        $loan_providerTab = DB()->table('loan_provider');
        $loanProvider = $loan_providerTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<label>Lone Provide name</label><select class="form-control select2 select2-hidden-accessible" onchange="searchLedger(this.value,' . $url . ')" id="loan_pro_id" style=" width: 100%;" tabindex="-1" aria-hidden="true"><option selected="selected"  value="">Please Select</option>';
        foreach ($loanProvider as $row) {
            $view .= '<option value="' . $row->loan_pro_id . '" >' . $row->name . '</option>';
        }
        $view .= '</select>';
        print $view;
    }

    /**
     * @description This method loan provider
     * @return void
     */
    public function loanProvider()
    {

        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $lonproviId = $this->request->getPost('Id');

        $ledger_loanTab = DB()->table('ledger_loan');
        $data = $ledger_loanTab->where('loan_pro_id', $lonproviId)->orderBy('ledg_loan_id', "DESC")->get()->getResult();

        $loan_providerTab = DB()->table('loan_provider');
        $loan = $loan_providerTab->where('loan_pro_id', $lonproviId)->get()->getRow();
        $name = $loan->name;
        $balance = $loan->balance;


        $view = ' <div class="box" >
            <div class="box-header">
                <h3 class="box-title">Loan Head: ' . $name . '</h3>
                <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
            </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="example1">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Trangaction Id</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>';
        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $view .= '<tr>
                        <td>' . $data[$i]->ledg_loan_id . '</td>
                        <td>' . $data[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td><a href="' . site_url('transaction/read/' . $data[$i]->trans_id) . '">TRNS_' . $data[$i]->trans_id . '</a></td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Trangaction Id</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>';

        print $view;
    }

    /**
     * @description This method cash view
     * @return void
     */
    public function cash()
    {


        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_nagodanTab = DB()->table('ledger_nagodan');
        $data = $ledger_nagodanTab->where('sch_id', $shopId)->orderBy('ledg_nagodan_id', "DESC")->get()->getResult();

        $shopsTab = DB()->table('shops');
        $shop = $shopsTab->where('sch_id', $shopId)->get()->getRow();
        $balance = $shop->cash;

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Cash Ledger</h3>
                <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
            </div>
            <div class="box-body">
        <div class="row"/>
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="example1" >
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Trangaction Id</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Payment" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $view .= '<tr>
                            <td>' . $data[$i]->ledg_nagodan_id . '</td>
                            <td>' . $data[$i]->createdDtm . '</td>
                            <td>' . $particulars . '</td>
                            <td>' . $data[$i]->trans_id . '</td>
                            <td>' . $amountDr . '</td>
                            <td>' . $amountCr . '</td>
                            <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                        </tr>';
        }

        $view .= '</tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Trangaction Id</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </tfoot>
                </table>
                </div></div>
                </div></div>';

        print $view;
    }

    /**
     * @description This method suppliers ledger
     * @return void
     */
    public function suppliers_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $url = "'" . site_url('Admin/previous_data_show/suppliers') . "'";

        $suppliersTab = DB()->table('suppliers');
        $suppliers = $suppliersTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<label>Suppliers name</label><select class="form-control select2 select2-hidden-accessible" onchange="searchLedger(this.value,' . $url . ')" id="supplier_id" style=" width: 100%;" tabindex="-1" aria-hidden="true"><option selected="selected"  value="">Please Select</option>';
        foreach ($suppliers as $row) {
            $view .= '<option value="' . $row->supplier_id . '" >' . $row->name . '</option>';
        }
        $view .= '</select>';
        print $view;
    }

    /**
     * @description This method provides suppliers
     * @return void
     */
    public function suppliers()
    {

        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $supplierId = $this->request->getPost('Id');

        $ledger_suppliersTab = DB()->table('ledger_suppliers');
        $data = $ledger_suppliersTab->where('supplier_id', $supplierId)->get()->getResult();

        $suppliersTab = DB()->table('suppliers');
        $sup = $suppliersTab->where('supplier_id', $supplierId)->get()->getRow();
        $name = $sup->name;
        $balance = $sup->balance;

        $view = ' <div class="box" >
            <div class="box-header">
                <h3 class="box-title">Supplier: ' . $name . '</h3>
                <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td>Due Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>';
        $view .= '</div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="example1">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Memo</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>';
        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);

            $purchaseId = ($data[$i]->purchase_id == NULL) ? '<a href="' . site_url('transaction/read/' . $data[$i]->trans_id) . '">TRNS_' . $data[$i]->trans_id . '</a>' : '<a href="' . site_url('purchase/read/' . $data[$i]->purchase_id) . '">PURS_' . $data[$i]->purchase_id . '</a>';
            $view .= '<tr>
                        <td>' . $data[$i]->ledg_sup_id . '</td>
                        <td>' . $data[$i]->createdDtm . '</td>
                        <td>' . $particulars . '</td>
                        <td>' . $purchaseId . '</td>
                        <td>' . $amountDr . '</td>
                        <td>' . $amountCr . '</td>
                        <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                    </tr>';
        }

        $view .= '</tbody>
                <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Memo</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>';

        print $view;


    }

    /**
     * @description This method  purchase ledger
     * @return void
     */
    public function purchase_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_purchaseTab = DB()->table('ledger_purchase');
        $ledger_purchase_data = $ledger_purchaseTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Purchase Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_purchase_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_purchase_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_purchase_data[$i]->amount);
            $amountDr = ($ledger_purchase_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_purchase_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_purchase_data[$i]->ledgPurch_id . '</td>
                    <td>' . $ledger_purchase_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_purchase_data[$i]->particulars . '</td>
                    <td>' . $ledger_purchase_data[$i]->purchase_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_purchase_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;


    }

    /**
     * @description This method sale ledger
     * @return void
     */
    public function sale_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_salesTab = DB()->table('ledger_sales');
        $ledger_sale_data = $ledger_salesTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Sales Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_sale_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_sale_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_sale_data[$i]->amount);
            $amountDr = ($ledger_sale_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_sale_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_sale_data[$i]->ledgSale_id . '</td>
                    <td>' . $ledger_sale_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_sale_data[$i]->particulars . '</td>
                    <td>' . $ledger_sale_data[$i]->invoice_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_sale_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method stock ledger
     * @return void
     */
    public function stock_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $ledger_stockTab = DB()->table('ledger_stock');
        $ledger_stock_data = $ledger_stockTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Stock Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_stock_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_stock_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_stock_data[$i]->amount);
            $amountDr = ($ledger_stock_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_stock_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_stock_data[$i]->stock_id . '</td>
                    <td>' . $ledger_stock_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_stock_data[$i]->particulars . '</td>
                    <td>' . $ledger_stock_data[$i]->invoice_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_stock_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method expense ledger
     * @return void
     */
    public function expense_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_expenseTab = DB()->table('ledger_expense');
        $ledger_expense_data = $ledger_expenseTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Expense Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_expense_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_expense_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_expense_data[$i]->amount);
            $amountDr = ($ledger_expense_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_expense_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_expense_data[$i]->ledg_exp_id . '</td>
                    <td>' . $ledger_expense_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_expense_data[$i]->particulars . '</td>
                    <td>' . $ledger_expense_data[$i]->trans_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_expense_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method profit ledger
     * @return void
     */
    public function profit_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_profitTab = DB()->table('ledger_profit');
        $ledger_profit_data = $ledger_profitTab->where('sch_id', $shopId)->get()->getResult();


        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Profit Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_profit_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_profit_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_profit_data[$i]->amount);
            $amountDr = ($ledger_profit_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_profit_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_profit_data[$i]->profit_id . '</td>
                    <td>' . $ledger_profit_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_profit_data[$i]->particulars . '</td>
                    <td>' . $ledger_profit_data[$i]->invoice_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_profit_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method capital ledger
     * @return void
     */
    public function capital_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $ledger_capitalTab = DB()->table('ledger_capital');
        $ledger_capital_data = $ledger_capitalTab->where('sch_id', $shopId)->get()->getResult();


        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Capital Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_capital_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_capital_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_capital_data[$i]->amount);
            $amountDr = ($ledger_capital_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_capital_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_capital_data[$i]->capital_id . '</td>
                    <td>' . $ledger_capital_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_capital_data[$i]->particulars . '</td>
                    <td>' . $ledger_capital_data[$i]->trans_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_capital_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method vat ledger
     * @return void
     */
    public function vat_ledger()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $ledger_vatTab = DB()->table('ledger_vat');
        $ledger_vat_data = $ledger_vatTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box" >
            <div class="box-header">
                <h3 class="box-title">Vat Ledger</h3>
            </div><div class="box-body">
        
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </thead>
                    <tbody>';
        $totalRows = count($ledger_vat_data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {

            $amountCr = ($ledger_vat_data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($ledger_vat_data[$i]->amount);
            $amountDr = ($ledger_vat_data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($ledger_vat_data[$i]->amount);

            $view .= '<tr>
                    <td width="80px">' . $ledger_vat_data[$i]->ledg_vat_id . '</td>
                    <td>' . $ledger_vat_data[$i]->createdDtm . '</td>
                    <td>' . $ledger_vat_data[$i]->particulars . '</td>
                    <td>' . $ledger_vat_data[$i]->ledg_vat_id . '</td>
                    <td>' . $amountDr . '</td>
                    <td>' . $amountCr . '</td>
                    <td>' . showWithCurrencySymbol($ledger_vat_data[$i]->rest_balance) . '</td>                    
                </tr>';

        }

        $view .= '</tbody>                               
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>       
                            <th>Particulars</th>
                            <th>Memo </th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Rest Balance</th>
                        </tr>
                    </tfoot>                
                </table>     
            </div>';

        print $view;
    }

    /**
     * @description This method balance
     * @return void
     */
    public function balance()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);


        //shops cash search(start)
        $shopsTab = DB()->table('shops');
        $cash = $shopsTab->where('sch_id', $shopId)->get()->getRow()->cash;
        //shops cash search(start)

        //total bank amount calculet(start)
        $bankTab = DB()->table('bank');
        $bankCash = $bankTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        //total bank amount calculet(end)

        //total customer balance calculet (start)
        $customersTab = DB()->table('customers');
        $customerCash = $customersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        //total customer balance calculet (end)

        //total Lone provider balance calculet(start)
        $loan_providerTab = DB()->table('loan_provider');
        $loanPro = $loan_providerTab->where('sch_id', $shopId)->get()->getResult();

        $loanProGetCash = 0;
        foreach ($loanPro as $result) {
            if ($result->balance > 0) {
                $loanProGetCash += $result->balance;
            }
        }
        //total Lone provider balance calculet(end)


        //total invoice profite calculet (start)
        $invoiceTab = DB()->table('invoice');
        $invoiceCash = $invoiceTab->selectSum('profit')->where('sch_id', $shopId)->get()->getRow()->profit;

        $productsTab = DB()->table('products');
        $product = $productsTab->where('sch_id', $shopId)->get()->getResult();
        $totalProdPrice = 0;
        foreach ($product as $row) {
            $totalProdPrice += $row->quantity * $row->purchase_price;
        }
        //total invoice profite calculet (end)


        //total other sale amount calculet (start)
        $ledger_other_salesTab = DB()->table('ledger_other_sales');
        $otherSaleCash = $ledger_other_salesTab->selectSum('amount')->where('sch_id', $shopId)->get()->getRow()->amount;
        //total other sale amount calculet (end)


        //Total balance calculet (start)
        $totalGetCash = $customerCash + $loanProGetCash;
        $totalCash = $cash + $bankCash + $totalGetCash + $totalProdPrice;
        //Total balance calculet (end)


        //total employee Balance (start)
        $employeeTab = DB()->table('employee');
        $employeeBalan = $employeeTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        //total employee Balance (end)


        //total supplier due balance calculet (start)
        $suppliersTab = DB()->table('suppliers');
        $suppl = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
        $suppCash = 0;
        foreach ($suppl as $result) {
            if ($result->balance < 0) {
                $suppCash -= $result->balance;
            }
        }
        //total supplier due balance calculet (end)


        //total Lone due Cash calculet (start)
        $loanProCash = 0;
        foreach ($loanPro as $result) {
            if ($result->balance < 0) {
                $loanProCash -= $result->balance;
            }
        }
        $totalDueCash = $suppCash + $loanProCash;
        //total Lone due Cash calculet (end)


        //total expens amount calculet (start)
        $ledger_expenseTab = DB()->table('ledger_expense');
        $expenseCash = $ledger_expenseTab->selectSum('amount')->where('sch_id', $shopId)->get()->getRow()->amount;
        //total expens amount calculet (end)


        //total return sale return profit(start)
        $return_saleTab = DB()->table('return_sale');
        $returnProfit = $return_saleTab->selectSum('rtn_profit')->where('sch_id', $shopId)->get()->getRow()->rtn_profit;
        //total return sale return profit(start)

        // Total vat earning (start)
        $vat_registerTab = DB()->table('vat_register');
        $vatEarning = $vat_registerTab->where('sch_id', $shopId)->get()->getRow()->balance;
        // Total vat earning (end)


        //Total due balance calculet (start)
        $totalDue = $totalDueCash;
        $totalBalance = $totalCash - $totalDue;
        //Total due balance calculet (end)


        $view = '<div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Balance Report Generation</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="TFtable">
                        <tbody>
                            <tr>
                                <td>Cash </td>
                                <td>Bank </td>
                                <td>Acquisition Due</td>
                                <td>Other</td>
                                <td>Vat Earning</td>
                                <td>Total Product Price</td>
                                <td>Total Sale Profit</td>
                                <td>Total Amount</td>
                            </tr>
                            <tr>
                                <td>' . showWithCurrencySymbol($cash) . '</td>
                                <td>' . showWithCurrencySymbol($bankCash) . '</td>
                                <td>' . showWithCurrencySymbol($totalGetCash) . '</td>
                                <td>' . showWithCurrencySymbol($otherSaleCash) . '</td>
                                <td>' . showWithCurrencySymbol($vatEarning) . '</td>
                                <td>' . showWithCurrencySymbol($totalProdPrice) . '</td>
                                <td>' . showWithCurrencySymbol($invoiceCash) . '</td>
                                <td>' . showWithCurrencySymbol($totalCash) . '</td>
                            </tr>

                            <tr>
                                <td>Owe Amount</td>
                                <td>Expense </td>
                                <td>Salary</td>
                                <td>Return Sale Profit</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total Amount</td>
                            </tr>
                            <tr>
                                <td>' . showWithCurrencySymbol($totalDueCash) . '</td>
                                <td>' . showWithCurrencySymbol($expenseCash) . '</td>
                                <td>' . showWithCurrencySymbol($employeeBalan) . '</td>
                                <td>' . showWithCurrencySymbol($returnProfit) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>' . showWithCurrencySymbol($totalDue) . '</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total Balance</td>
                                <td>' . showWithCurrencySymbol($totalBalance) . '</td>
                            </tr>
                        </tbody>                                
                    </table>
                </div>
            </div>';


        print $view;


    }

    /**
     * @description This method provides invoice
     * @return void
     */
    public function invoice()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        $invoiceTab = DB()->table('invoice');
        $invoice_data = $invoiceTab->where('sch_id', $shopId)->get()->getResult();

        $view = '<div class="box">
            <div class="box-body">
                <table class="table table-bordered table-striped" id="TFtable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>';
        $start = 0;
        foreach ($invoice_data as $invoice) {
            $name = ($invoice->customer_id == NULL) ? $invoice->customer_name : get_data_by_id('customer_name', 'customers', 'customer_id', $invoice->customer_id);
            $amount = ($invoice->due == 0) ? "<button class='btn btn-xs btn-success'>Paid</button>" : showWithCurrencySymbol($invoice->due);

            $view .= '<tr>
                            <td width="80px">' . ++$start . '</td>
                            <td>' . $name . '</td>
                            <td>' . showWithCurrencySymbol($invoice->final_amount) . '</td>
                            <td>' . $amount . '</td>
                        </tr>';
        }
        $view .= '</tbody>                   
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Due</th>
                                </tr>
                            </tfoot>                    
                        </table>
                    </div>
                            
                    </div>';
        print $view;
    }

    /**
     * @description This method provides stock report
     * @return void
     */
    public function stockReport()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $storesTab = DB()->table('stores');
        $store = $storesTab->where('sch_id', $shopId)->get()->getResult();

        $url = "'" . site_url('Admin/previous_data_show/store') . "'";

        $view = '<div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-6">
                        <label>Stores</label>
                        <select class="form-control select2 select2-hidden-accessible" onchange="searchReport(this.value,' . $url . ');" id="store_id" style=" width: 100%;" tabindex="-1" aria-hidden="true">
                          <option selected="selected"  value="">Please Select</option>';
        foreach ($store as $row) {
            $view .= '<option value="' . $row->store_id . '" >' . $row->name . '</option>';
        }

        $view .= '</select>              
                    </div>
                    <div class="col-md-6"></div>            
                </div>
            </div>
            <div class="box-body">
                <div id="reportResult"></div>
            </div>
        </div>';

        print $view;
    }

    /**
     * @description This method provides store
     * @return void
     */
    public function store()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);
        $storeId = $this->request->getPost('Id');

        $productsTab = DB()->table('products');
        $data = $productsTab->where('store_id', $storeId)->orderBy('prod_id', "DESC")->get()->getResult();

        $productsQtyTab = DB()->table('products');
        $quantity = $productsQtyTab->selectSum("quantity")->where('store_id', $storeId)->where('sch_id', $shopId)->orderBy('prod_id', "DESC")->get()->getRow()->quantity;

        $productsPurTab = DB()->table('products');
        $purchasePrice = $productsPurTab->selectSum("purchase_price")->where('store_id', $storeId)->where('sch_id', $shopId)->orderBy('prod_id', "DESC")->get()->getRow()->purchase_price;


        $name = get_data_by_id('name', 'stores', 'store_id', $storeId);

        $view = '<div class="box" >
                <div class="box-header">
                    <h3 class="box-title" >Store Name: <b style="color:white;">' . $name . '</b></h3>
                    <span class="pull-right" style="color:white; margin-right:10px;" ><b>Storage Inventory Prices:</b> ' . showWithCurrencySymbol($purchasePrice) . '</span>
                    <span class="pull-right" style="color:white; margin-right:40px;"> <b>Storage Inventory Quantity:</b> ' . $quantity . '.</span>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Product Category</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                            </tr>
                        </thead>
                        <tbody>';
        $i = '';
        foreach ($data as $row) {
            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . $row->name . '</td>
                                    <td>' . get_data_by_id('product_category', 'product_category', 'prod_cat_id', $row->prod_cat_id) . '</td>
                                    <td>' . $row->quantity . '</td>
                                    <td>' . showWithCurrencySymbol($row->purchase_price) . '</td>
                                    <td>' . showWithCurrencySymbol($row->selling_price) . '</td>
                                </tr>';
        }
        $view .= '</tbody>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Product Category</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>';

        print $view;
    }

    /**
     * @description This method provides sale
     * @return void
     */
    public function sale()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        //all customer total balance show (start)
        $customersTab = DB()->table('customers');
        $customers = $customersTab->where('sch_id', $shopId)->get()->getResult();
        //all customer total balance show (end)


        //All invoice item show list (start)
        $invoice_itemTab = DB()->table('invoice_item');
        $sale = $invoice_itemTab->where('sch_id', $shopId)->orderBy('inv_item', 'DESC')->get()->getResult();
        //All invoice item show list (start)


        //All sale profite Show in invoice item table (atrat)
        $invoice_itemProfTab = DB()->table('invoice_item');
        $saleprofit = $invoice_itemProfTab->selectSum('profit')->where('sch_id', $shopId)->get()->getRow()->profit;
        //All sale profite Show in invoice item table (end)


        $view = '<div class="col-xs-8">
        <div class="box">
        <div class="box-header">
          <div class="input-group col-xs-12">
            <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Sale Report List</h3>
         <span class="pull-right"><b>Total Profit:' . showWithCurrencySymbol($saleprofit) . '</b></span>
          </div>            
        </div>
        <div class="box-body">
          <table class="table table-bordered table-striped" id="example1">
            <thead>
                  <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Discount</th>
                    <th>Final Price</th>
                    <th>Profit</th>
                  </tr>
            </thead>
            <tbody>';
        $i = '';
        foreach ($sale as $sale) {

            $view .= '<tr>
                <td width="80px">'.++$i.'</td>
                <td>' . get_data_by_id('name', 'products', 'prod_id', $sale->prod_id) . '</td>
                <td>' . showWithCurrencySymbol($sale->price) . '</td>
                <td>' . $sale->quantity . '</td>
                <td>' . showWithCurrencySymbol($sale->total_price) . '</td>
                <td>' . $sale->discount . '</td>
                <td>' . showWithCurrencySymbol($sale->final_price) . '</td>
                <td>' . showWithCurrencySymbol($sale->profit) . '</td>
            </tr>';
        }
        $view .= '</tbody>
                
                  </table>
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Customer Sale Report List</h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped" id="TFtable">
                    <thead>
                          <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Customer All Sale Amount</th>
                          </tr>
                    </thead>
                    <tbody>';
        $start = '';
        foreach ($customers as $customers) {
            $view .= '<tr>
                              <td width="80px">' . ++$start . '</td>
                              <td>' . $customers->customer_name . '</td>
                              <td>' . CustomerTotalSaleAmount($customers->customer_id) . '</td>
                          </tr>';
        }
        $view .= '</tbody>
                        
                  </table>
                </div>
            </div>
        </div>';

        print $view;

    }

    /**
     * @description This method provides purchase
     * @return void
     */
    public function purchase()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        //all suppliers purchase total amount show (start)
        $suppliersTab = DB()->table('suppliers');
        $suppliers = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
        //all suppliers purchase total amount show (start)

        $purchaseItem = array();
        //Purchase item show list (start)
        $i = 0;
        $purchaseTab = DB()->table('purchase');
        $purchaseId = $purchaseTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($purchaseId as $value) {
            $purchase_itemTab = DB()->table('purchase_item');
            $query = $purchase_itemTab->where('purchase_id', $value->purchase_id)->orderBy('purchase_item_id', 'DESC')->limit('10')->get()->getResult();
            $purchaseItem[$i] = $query;
            $i++;
        }
        //Purchase item show list (end)


        $view = '<div class="col-xs-8">    
    <div class="box">
        <div class="box-header">
          <div class="col-xs-12">
            <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Purchase Report List</h3>
          </div>
        </div>
        <div class="box-body">
          <table class="table table-bordered table-striped" id="TFtable">
            <thead>
                  <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Purchase Price</th>
                  </tr>
            </thead>
            <tbody>';
        $i = '';
        foreach ($purchaseItem as $value) {
            foreach ($value as $purchase) {
                $view .= '<tr>
                      <td width="80px">' . ++$i . '</td>
                      <td>' . get_data_by_id('name', 'products', 'prod_id', $purchase->prod_id) . '</td>
                      <td>' . $purchase->quantity . '</td>
                      <td>' . $purchase->purchase_price . '</td>
                  </tr>';
            }
        }

        $view .= '</tbody>                
                      </table>
                    </div>
                </div>
            </div>

            <div class="col-xs-4">    
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Suppliers Purchase Report List</h3>
                    </div>
                    <div class="box-body">
                      <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                              <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Suppliers All Purchase Amount</th>
                              </tr>
                        </thead>
                        <tbody>';
        $start = '';
        foreach ($suppliers as $suppliers) {

            $view .= '<tr>
                                  <td width="80px">'. ++$start .'</td>
                                  <td>' . $suppliers->name . '</td>
                                  <td>' . suppliersTotalPurchaseAmount($suppliers->supplier_id) . '</td>
                              </tr>';
        }
        $view .= '</tbody>
                            
                      </table>
                    </div>
                </div>
            </div>';
        print $view;
    }

    /**
     * @description This method provides acquisition due
     * @return void
     */
    public function acquisitionDue()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);


        //total customer balance calculet (start)
        $customersTab = DB()->table('customers');
        $cusCash = $customersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $customerCash = 0;
        if ($cusCash > 0) {
            $customerCash = $cusCash;
        }
        $customersAlTab = DB()->table('customers');
        $cusData = $customersAlTab->where('sch_id', $shopId)->get()->getResult();
        //total customer balance calculet (end)


        //total Lone provider balance calculet(start)
        $loan_providerTab = DB()->table('loan_provider');
        $loanProCash = $loan_providerTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $loanCash = 0;
        if ($loanProCash > 0) {
            $loanCash = $loanProCash;
        }

        $loan_providerAlTab = DB()->table('loan_provider');
        $loanPro = $loan_providerAlTab->where('sch_id', $shopId)->get()->getResult();
        //total Lone provider balance calculet(end)


        //total supplier due balance calculet (start)
        $suppliersTab = DB()->table('suppliers');
        $supCash = $suppliersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $supplierCash = 0;
        if ($supCash > 0) {
            $supplierCash = $supCash;
        }
        $suppliersAlTab = DB()->table('suppliers');
        $suppl = $suppliersAlTab->where('sch_id', $shopId)->get()->getResult();
        //total supplier due balance calculet (end)

        $totalDue = $customerCash + $loanCash + $supplierCash;


        $customer = $customerCash;
        $customerData = $cusData;
        $loanProvider = $loanCash;
        $loanProData = $loanPro;
        $supplier = $supplierCash;
        $supplierData = $suppl;
        $totalAcquisitionDue = $totalDue;


        $view = '<div class="box">
            <div class="box-header">
                <span style="float:right; " >Grand Total:' . showWithCurrencySymbol($totalAcquisitionDue) . '</span>
                <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Acquisition Due</h3>
            </div>
            <div class="box-body row">
                <div class="col-xs-4">
                    <span style="float:right; " >Total: ' . showWithCurrencySymbol($customer) . '</span>
                    <h4>Customer</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
        $i = 1;
        foreach ($customerData as $row) {
            if ($row->balance > 0) {
                $view .= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->customer_name . '</td>
                                <td>' . showWithCurrencySymbol($row->balance) . '</td>
                            </tr>';
            }
        }

        $view .= '</tbody>  
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                </tfoot>                              
            </table>
            
                </div>
                <div class="col-xs-12" style="display: none; text-transform: capitalize; "  id="aqu_customer" >
                    <div class="col-xs-12" style="margin-bottom: 20px;   ">
                        <div class="col-xs-6">';
        if (logo_image() == NULL) {
            $view .= '<img src="'.base_url().'/uploads/schools/no_image.jpg" alt="User Image" >';
        } else {
            $view .= '<img src="'.base_url().'/uploads/schools/'.logo_image().'" class="" alt="User Image">';
        }
        $view .= '</div>
                        <div class="col-xs-6">
                            ' . address() . '
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span style="float:right; " >Total: ' . showWithCurrencySymbol($customer) . '</span>
                        <h4>Customer</h4>
                        <table class="table table-bordered table-striped " >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = 1;
        foreach ($customerData as $row) {
            if ($row->balance > 0) {
                $view .= '<tr>
                                    <td>' . $i++ . '</td>
                                    <td>' . $row->customer_name . '</td>
                                    <td>' . showWithCurrencySymbol($row->balance) . '</td>
                                </tr>';
            }
        }
        $view .= '</tbody>                              
                        </table>
                    </div>
                </div>
                <div class="col-xs-4">
                    <span style="float:right; " >Total: ' . showWithCurrencySymbol($supplier) . '</span>
                    <h4>Suppliers</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
        $j = 1;
        foreach ($supplierData as $viewa) {
            if ($viewa->balance > 0) {

                $view .= '<tr>
                                <td>' . $j++ . '</td>
                                <td>' . $viewa->name . '</td>
                                <td>' . showWithCurrencySymbol($viewa->balance) . '</td>
                            </tr>';
            }
        }
        $view .= '</tbody>  
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </tfoot>                              
                    </table>
                    
                </div>
                <div class="col-xs-12" style="display: none;"   id="aqu_supplier" >
                    <div class="col-xs-12" style="margin-bottom: 20px;   ">
                        <div class="col-xs-6">';
        if (logo_image() == NULL) {
            $view .= '<img src="'.base_url().'/uploads/schools/no_image.jpg" alt="User Image" >';
        } else {
            $view .= '<img src="'. base_url().'/uploads/schools/'.logo_image().'" class="" alt="User Image">';
        }
        $view .= '</div>
                        <div class="col-xs-6">
                            ' . address() . '
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span style="float:right; " >Total: ' . showWithCurrencySymbol($supplier) . '</span>
                        <h4>Suppliers</h4>
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>';
        $j = 1;
        foreach ($supplierData as $viewb) {
            if ($viewb->balance > 0) {
                $view .= '<tr>
                                    <td>'. $j++.'</td>
                                    <td>'. $view->name .'</td>
                                    <td>'. showWithCurrencySymbol($view->balance).'</td>
                                </tr>';
            }
        }
        $view .= '</tbody>  
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </tfoot>                              
                        </table>
                    </div>
                </div>
                <div class="col-xs-4">
                    <span style="float:right; " >Total:' . showWithCurrencySymbol($loanProvider) . '</span>
                    <h4>Account Holder</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
        $l = 1;
        foreach ($loanProData as $value) {
            if ($value->balance > 0) {
                $view .= '<tr>
                                <td>'. $l++ .'</td>
                                <td>'. $value->name .'</td>
                                <td>'. showWithCurrencySymbol($value->balance) .'</td>
                            </tr>';
            }
        }
        $view .= '</tbody>  
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </tfoot>                              
                    </table>
                    
                </div>
                <div class="col-xs-12" style="display: none;"   id="aqu_lone" >
                    <div class="col-xs-12" style="margin-bottom: 20px;   ">
                        <div class="col-xs-6">';
        if (logo_image() == NULL) {
            $view .= '<img src="'. base_url().'/uploads/schools/no_image.jpg" alt="User Image" >';
        } else {
            $view .= '<img src="'.base_url().'/uploads/schools/'.logo_image().'" class="" alt="User Image">';
        }
        $view .= '</div>
                        <div class="col-xs-6">
                            ' . address() . '
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span style="float:right; " >Total:' . showWithCurrencySymbol($loanProvider) . '</span>
                        <h4>Account Holder</h4>
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>';
        $l = 1;
        foreach ($loanProData as $value) {
            if ($value->balance > 0) {
                $view .= '<tr>
                                    <td>' . $l++ . '</td>
                                    <td>' . $value->name . '</td>
                                    <td>' . showWithCurrencySymbol($value->balance) . '</td>
                                </tr>';
            }
        }
        $view .= '</tbody>  
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                            </tfoot>                              
                        </table>
                    </div>
                </div>
            </div>
        </div>';
        print $view;
    }

    /**
     * @description This method provides owe amount
     * @return void
     */
    public function oweAmount()
    {

        $shopId = $this->session->shopId;
        $newDb = $this->session->newBD;

        DB()->query('use ' . $newDb);

        //total customer balance calculet (start)
        $customersTab = DB()->table('customers');
        $cusCash = $customersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $customerCash = 0;
        if ($cusCash < 0) {
            $customerCash = $cusCash;
        }

        $customersAlTab = DB()->table('customers');
        $cusData = $customersAlTab->where('sch_id', $shopId)->get()->getResult();
        //total customer balance calculet (end)


        //total Lone provider balance calculet(start)
        $loan_providerTab = DB()->table('loan_provider');
        $loanProCash = $loan_providerTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $loanCash = 0;
        if ($loanProCash < 0) {
            $loanCash = $loanProCash;
        }

        $loan_providerAllTab = DB()->table('loan_provider');
        $loanPro = $loan_providerAllTab->where('sch_id', $shopId)->get()->getResult();
        //total Lone provider balance calculet(end)


        //total supplier due balance calculet (start)
        $suppliersTab = DB()->table('suppliers');
        $supCash = $suppliersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
        $supplierCash = 0;
        if ($supCash < 0) {
            $supplierCash = $supCash;
        }
        $suppliersAllTab = DB()->table('suppliers');
        $suppl = $suppliersAllTab->where('sch_id', $shopId)->get()->getResult();
        //total supplier due balance calculet (end)

        $totalDue = $customerCash + $loanCash + $supplierCash;


        $customer = $customerCash;
        $customerData = $cusData;
        $loanProvider = $loanCash;
        $loanProData = $loanPro;
        $supplier = $supplierCash;
        $supplierData = $suppl;
        $totalAcquisitionDue = $totalDue;

        $view = '<div class="box">
            <div class="box-header">
                <span style="float:right; " >Grand Total:' . showWithCurrencySymbol($totalAcquisitionDue) . '</span>
                <h3 class="box-title"><i class="fa fa-fw fa-line-chart"></i> Acquisition Due</h3>
            </div>
            <div class="box-body row">
                <div class="col-xs-4">
                    <span style="float:right; " >Total: ' . showWithCurrencySymbol($customer) . '</span>
                    <h4>Customer</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
        $i = 1;
        foreach ($customerData as $row) {
            if ($row->balance < 0) {
                $view .= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->customer_name . '</td>
                                <td>' . showWithCurrencySymbol($row->balance) . '</td>
                            </tr>';
            }
        }
        $view .= '</tbody>  
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </tfoot>                              
                    </table>
                </div>
                <div class="col-xs-4">
                    <span style="float:right; " >Total: ' . showWithCurrencySymbol($supplier) . '</span>
                    <h4>Suppliers</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>';
        $j = 1;
        foreach ($supplierData as $viewa) {
            if ($viewa->balance < 0) {

                $view .= '<tr>
                                <td>' . $j++ . '</td>
                                <td>' . $viewa->name . '</td>
                                <td>' . showWithCurrencySymbol($viewa->balance) . '</td>
                            </tr>';
            }
        }
        $view .= '</tbody>  
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </tfoot>                              
                    </table>
                </div>
                <div class="col-xs-4">
                    <span style="float:right; " >Total:' . showWithCurrencySymbol($loanProvider) . '</span>
                    <h4>Account Holder</h4>
                    <table class="table table-bordered table-striped" id="TFtable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody> ';
        $l = 1;
        foreach ($loanProData as $value) {
            if ($value->balance < 0) {
                $view .= '<tr>
                                <td>' . $l++ . '</td>
                                <td>' . $value->name . '</td>
                                <td>' . showWithCurrencySymbol($value->balance) . '</td>
                            </tr>';
            }
        }
        $view .= '</tbody>  
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </tfoot>                              
                    </table>
                </div>
            </div>
        </div>';

        print $view;
    }


}