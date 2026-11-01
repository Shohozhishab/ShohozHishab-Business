<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_sale extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_sale';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return sale view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $customer_id = $this->request->getGet('customer');

            $shopId = $this->session->shopId;

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('return_sale');
            $table->where('return_sale.sch_id', $shopId);
            $table->where('return_sale.deleted', null);
            if (!empty($customer_id)) {
                $table->join('invoice', 'invoice.invoice_id = return_sale.invoice_id');
                $table->where('invoice.customer_id', $customer_id);
            }
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('return_sale.createdDtm >=', $st_date . ' 00:00:00');
                $table->where('return_sale.createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['return_sale_data'] = $table->get()->getResult();

            $data['customerId'] = $customer_id ?? '';
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method invoice search
     * @return void
     */
    public function invoice_search()
    {

        $shopId = $this->session->shopId;
        $invoiceId = $this->request->getPost('invoiceId');

        $invoiceTable = DB()->table('invoice');
        $data['invoice_data'] = $invoiceTable->where('sch_id', $shopId)->where('invoice_id', $invoiceId)->get()->getResult();

        $data['return_status'] = get_return_status_by_invoice_id($invoiceId);

        $data['menu'] = view('Admin/menu_sales', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Return_sale/search', $data);
        echo view('Admin/footer');
    }

    /**
     * @description This method provides return view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function return($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $invoice_itemTab = DB()->table('invoice_item');
            $data['invoice_item'] = $invoice_itemTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();

            $invoiceTab = DB()->table('invoice');
            $data['invoice'] = $invoiceTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();


            $data['action'] = site_url('Admin/Return_sale/create_action');
            $data['invoiceId'] = $id;


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Return_sale/return', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description Flash an error message and send the user back to the return screen
     * @param int $invoiceId
     * @param string $message
     * @return RedirectResponse
     */
    private function back_to_return_with_error($invoiceId, string $message): RedirectResponse
    {
        $this->session->setFlashdata('message', '
        <div class="alert alert-danger alert-dismissible" role="alert">
            ' . $message . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
    }

    /**
     * @description This method store return sale
     * @return RedirectResponse
     */
    public function create_action()
    {
        $userId   = $this->session->userId;
        $shopId   = $this->session->shopId;

        $customerId   = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('customer_name');
        $invoiceId    = $this->request->getPost('invoice_id');

        // Lines arrive keyed by stock row (returnchecked[psr] = prod_id), so the
        // quantity, the price and the serial numbers of a line always stay paired.
        $checkedLines   = $this->request->getPost('returnchecked');
        $quantities     = $this->request->getPost('quantity');
        $purchasePrices = $this->request->getPost('purchase_price');
        $postedSerials  = $this->request->getPost('return_serials');

        $checkedLines   = is_array($checkedLines) ? $checkedLines : array();
        $quantities     = is_array($quantities) ? $quantities : array();
        $purchasePrices = is_array($purchasePrices) ? $purchasePrices : array();
        $postedSerials  = is_array($postedSerials) ? $postedSerials : array();

        $lines = array();
        foreach ($checkedLines as $psrId => $prodId) {
            $qty = isset($quantities[$psrId]) ? (float)$quantities[$psrId] : 0;
            if ($qty <= 0) {
                continue;
            }

            $serialIds = array();
            if (!empty($postedSerials[$psrId]) && is_array($postedSerials[$psrId])) {
                $serialIds = array_values(array_unique(array_map('intval', $postedSerials[$psrId])));
            }

            $lines[] = [
                'psr'     => (int)$psrId,
                'prod_id' => (int)$prodId,
                'qty'     => $qty,
                'price'   => isset($purchasePrices[$psrId]) ? (float)$purchasePrices[$psrId] : 0,
                'serials' => $serialIds,
            ];
        }

        $amount     = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankAmount = $this->request->getPost('bank');
        $bankId     = $this->request->getPost('bank_id');
        $dueAmount  = $this->request->getPost('due');
        $totalVat   = empty($this->request->getPost('vatAmount')) ? 0 : $this->request->getPost('vatAmount');
        $totalDiscount   = empty($this->request->getPost('discountAmount')) ? 0 : $this->request->getPost('discountAmount');

        // Customer validation
        if (empty($customerName) && empty($customerId)) {
            return redirect()->to(site_url('Admin/Return_sale/return/' . $invoiceId));
        }

        if (empty($amount)) {
            return $this->back_to_return_with_error($invoiceId, 'Please select any product');
        }

        // New customer must clear full amount (no due)
        if (!empty($customerName) && $dueAmount != 0) {
            return $this->back_to_return_with_error($invoiceId, 'Please clear due');
        }

        if ($dueAmount < 0) {
            return $this->back_to_return_with_error($invoiceId, 'Please enter a valid due amount');
        }

        // Serial validation: every returned unit of a serialized product must
        // carry one of the serial numbers that is still sold on this invoice line.
        foreach ($lines as $line) {
            $product = DB()->table('products')
                ->select('name, serial_status')
                ->where('prod_id', $line['prod_id'])
                ->get()->getRow();

            if (empty($product) || (int)$product->serial_status !== 1) {
                continue;
            }

            $lineSerials = get_return_sale_line_serials($invoiceId, $line['psr'], $line['prod_id']);
            if (empty($lineSerials['serials'])) {
                // Sold before serial numbers were attached to the invoice line
                continue;
            }

            $needQty = (int)round($line['qty']);

            if (count($line['serials']) !== $needQty) {
                return $this->back_to_return_with_error($invoiceId, esc($product->name) . ': ' . $needQty . ' serial number(s) must be selected (quantity is ' . $needQty . ').');
            }

            $soldIds = array();
            foreach ($lineSerials['serials'] as $serialRow) {
                if ($serialRow['status'] === 'sold') {
                    $soldIds[] = (int)$serialRow['product_serial_id'];
                }
            }

            if (!empty(array_diff($line['serials'], $soldIds))) {
                return $this->back_to_return_with_error($invoiceId, esc($product->name) . ': one or more serial numbers are not available for return.');
            }
        }

        DB()->transStart();

        // 1. Insert into return_sale
        $returnData = [
            'sch_id'       => $shopId,
            'invoice_id'   => $invoiceId,
            'amount'       => $amount,
            'nagad_paid'   => $cashAmount,
            'bank_paid'    => $bankAmount,
            'bank_id'      => $bankId,
            'createdBy'    => $userId,
            'createdDtm'   => date('Y-m-d H:i:s'),
        ];

        if (!empty($customerId)) {
            $returnData['customer_id'] = $customerId;
        } else {
            $returnData['customer_name'] = $customerName;
        }

        DB()->table('return_sale')->insert($returnData);
        $returnId = DB()->insertID();

        // 2. Update shop sale balance + ledger
        $saleBalance     = get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $newSaleBalance  = $saleBalance + $amount - $totalVat;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['sale_balance' => $newSaleBalance]);

        DB()->table('ledger_sales')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'return sale',
            'amount'           => $amount,
            'rest_balance'     => $newSaleBalance,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 3. Process each returned item
        $totalPurchasePrice = 0;

        foreach ($lines as $line) {
            $qty   = $line['qty'];
            $price = $line['price'];
            $total = $qty * $price;

            // Insert return item
            DB()->table('return_sale_item')->insert([
                'sch_id'      => $shopId,
                'rtn_sale_id' => $returnId,
                'prod_id'     => $line['prod_id'],
                'price'       => $price,
                'quantity'    => $qty,
                'total_price' => $total,
                'createdBy'   => $userId,
                'createdDtm'  => date('Y-m-d H:i:s'),
            ]);

            // Update product stock quantity
            $stock = DB()->table('product_stock_relation')
                ->where('product_stock_relation_id', $line['psr'])
                ->where('product_id', $line['prod_id'])
                ->get()
                ->getRow();

            if ($stock) {
                $newQty = $stock->quantity + $qty;

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $line['psr'])
                    ->where('product_id', $line['prod_id'])
                    ->update(['quantity' => $newQty]);

                // Calculate total purchase cost
                $totalPurchasePrice += ($stock->purchase_price * $qty);
            }

            // Reverse the returned serial numbers: they stay linked to the invoice
            // (so the invoice keeps showing them) but leave the sold state, and they
            // remember this return slip.
            if (!empty($line['serials'])) {
                DB()->table('product_serial')
                    ->whereIn('product_serial_id', $line['serials'])
                    ->where('invoice_id', $invoiceId)
                    ->where('product_stock_relation_id', $line['psr'])
                    ->where('status', 'sold')
                    ->update([
                        'status'      => 'returned',
                        'rtn_sale_id' => $returnId,
                        'updatedBy'   => $userId,
                        'updatedDtm'  => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        // 4. VAT handling
        if (!empty($totalVat)) {
            $vatId      = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
            $vatBalance = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
            $newVatBal  = $vatBalance + $totalVat;

            DB()->table('vat_register')
                ->where('sch_id', $shopId)
                ->update(['balance' => $newVatBal]);

            DB()->table('ledger_vat')->insert([
                'vat_id'           => $vatId,
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Vat return',
                'trangaction_type' => 'Dr.',
                'amount'           => $totalVat,
                'rest_balance'     => $newVatBal,
                'createdBy'        => $shopId,
            ]);
        }

        if (!empty($totalDiscount)) {
            $discountBalance = get_data_by_id('discount', 'shops', 'sch_id', $shopId);
            $newDiscountBal  = $discountBalance + $totalDiscount;

            DB()->table('shops')
                ->where('sch_id', $shopId)
                ->update(['discount' => $newDiscountBal]);

            DB()->table('ledger_discount')->insert([
                'invoice_id'       => $invoiceId,
                'sch_id'           => $shopId,
                'particulars'      => 'Return Sale Discount return',
                'trangaction_type' => 'Cr.',
                'amount'           => $totalDiscount,
                'rest_balance'     => $newDiscountBal,
                'createdBy'        => $userId,
            ]);
        }

        // 5. Customer balance + ledger (existing customer only)
        if (!empty($customerId)) {
            $oldCusBalance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $newCusBalance = $oldCusBalance - $amount;

            DB()->table('customers')->where('customer_id', $customerId)->update([
                    'balance'   => $newCusBalance,
                    'createdBy' => $userId,
                ]);

            DB()->table('ledger')->insert([
                'sch_id'           => $shopId,
                'customer_id'      => $customerId,
                'rtn_sale_id'      => $returnId,
                'particulars'      => 'Return Sale Product',
                'trangaction_type' => 'Cr.',
                'amount'           => $amount,
                'rest_balance'     => $newCusBalance,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 6. Calculate & update return profit
        $returnProfit = $amount - $totalPurchasePrice - $totalVat;

        DB()->table('return_sale')
            ->where('rtn_sale_id', $returnId)
            ->update(['rtn_profit' => $returnProfit]);

        $shopProfit    = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $newShopProfit = $shopProfit + $returnProfit;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update([
                'profit'    => $newShopProfit,
                'updatedBy' => $userId,
            ]);

        DB()->table('ledger_profit')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return Profit',
            'amount'           => $returnProfit,
            'rest_balance'     => $newShopProfit,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // 7. Update stock amount + ledger
        $stockAmount    = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $newStockAmount = $stockAmount + $totalPurchasePrice;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['stockAmount' => $newStockAmount]);

        DB()->table('ledger_stock')->insert([
            'sch_id'           => $shopId,
            'rtn_sale_id'      => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars'      => 'Return sale',
            'amount'           => $totalPurchasePrice,
            'rest_balance'     => $newStockAmount,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        DB()->transComplete();

        $this->session->setFlashdata('message', '
        <div class="alert alert-success alert-dismissible" role="alert">
            Return Product Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_sale/'));
    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnSaleTable = DB()->table('return_sale');
            $data['returnSaleData'] = $returnSaleTable->where('rtn_sale_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_sale_item');
            $data['invoiceItame'] = $returnItem->where('rtn_sale_id', $id)->get()->getResult();


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

}