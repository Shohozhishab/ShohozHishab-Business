<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_purchase extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return purchase view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $shopId = $this->session->shopId;

            $supplier_id = $this->request->getGet('supplier_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('return_purchase');
            $table->where('return_purchase.sch_id', $shopId);
            $table->where('return_purchase.deleted', null);
            if (!empty($supplier_id)){
                $table->where('return_purchase.supplier_id', $supplier_id);
            }
            if (!empty($st_date) && !empty($en_date)) {
                $table->where('return_purchase.createdDtm >=', $st_date . ' 00:00:00');
                $table->where('return_purchase.createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['return_purchase_data'] = $table->get()->getResult();

            $data['supplierId'] = isset($supplier_id)?$supplier_id:'';
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method purchase search
     * @return void
     */
    public function purchase_search()
    {

        $shopId = $this->session->shopId;
        $purchaseId = $this->request->getPost('purchaseId');

        $purchaseTable = DB()->table('purchase');
        $data['purchase_data'] = $purchaseTable->where('sch_id', $shopId)->where('purchase_id', $purchaseId)->get()->getResult();

        $data['return_status'] = get_return_status_by_purchase_id($purchaseId);

        $data['menu'] = view('Admin/menu_purchase', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Return_purchase/search', $data);
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

            $purchase_itemTab = DB()->table('purchase_item');
            $data['purchase_item'] = $purchase_itemTab->where('purchase_id', $id)->get()->getResult();

            $purchaseTab = DB()->table('purchase');
            $data['purchase'] = $purchaseTab->where('purchase_id', $id)->where('sch_id', $shopId)->get()->getRow();

            $data['action'] = site_url('Admin/Return_purchase/create_action');
            $data['purchaseId'] = $id;

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Return_purchase/return', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description Flash an error message and send the user back to the return screen
     * @param int $purchaseId
     * @param string $message
     * @return RedirectResponse
     */
    private function back_to_return_with_error($purchaseId, string $message): RedirectResponse
    {
        $this->session->setFlashdata('message', '
        <div class="alert alert-danger alert-dismissible" role="alert">
            ' . $message . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_purchase/return/' . $purchaseId));
    }

    /**
     * @description This method store return purchase
     * @return RedirectResponse
     */
    public function create_action()
    {
        $userId   = $this->session->userId;
        $shopId   = $this->session->shopId;

        $supplierId   = $this->request->getPost('supplier_id');
        $purchaseId    = $this->request->getPost('purchase_id');

        // Lines arrive keyed by purchase item row (returnchecked[itemId] = prod_id),
        // so the quantity, the price and the serial numbers of a line always stay
        // paired even when only some of the rows are selected.
        $checkedLines   = $this->request->getPost('returnchecked');
        $quantities     = $this->request->getPost('quantity');
        $purchasePrices = $this->request->getPost('purchase_price');
        $postedSerials  = $this->request->getPost('return_serials');

        $checkedLines   = is_array($checkedLines) ? $checkedLines : array();
        $quantities     = is_array($quantities) ? $quantities : array();
        $purchasePrices = is_array($purchasePrices) ? $purchasePrices : array();
        $postedSerials  = is_array($postedSerials) ? $postedSerials : array();

        $amount     = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankAmount = $this->request->getPost('bank');
        $bankId     = $this->request->getPost('bank_id');
        $dueAmount  = $this->request->getPost('due');

        // The purchase must belong to the active shop, otherwise nothing is returned
        $purchase = DB()->table('purchase')
            ->where('purchase_id', $purchaseId)
            ->where('sch_id', $shopId)
            ->get()->getRow();

        if (empty($purchase)) {
            return redirect()->to(site_url('Admin/Return_purchase'));
        }

        // Supplier validation
        if (empty($supplierId)) {
            return $this->back_to_return_with_error($purchaseId, 'Supplier is missing on this purchase');
        }

        if (empty($amount)) {
            return $this->back_to_return_with_error($purchaseId, 'Please select any product');
        }

        if ($dueAmount < 0) {
            return $this->back_to_return_with_error($purchaseId, 'Please enter a valid due amount');
        }

        // Collect the checked lines and resolve their stock row from the purchase
        // itself, so a posted item id can never point at another shop's stock.
        $lines    = array();
        $itemIds  = array();

        foreach ($checkedLines as $itemId => $prodId) {
            $qty = isset($quantities[$itemId]) ? (float)$quantities[$itemId] : 0;
            if ($qty <= 0) {
                continue;
            }

            $serialIds = array();
            if (!empty($postedSerials[$itemId]) && is_array($postedSerials[$itemId])) {
                $serialIds = array_values(array_unique(array_map('intval', $postedSerials[$itemId])));
            }

            $itemIds[] = (int)$itemId;
            $lines[]   = [
                'item_id' => (int)$itemId,
                'prod_id' => (int)$prodId,
                'psr'     => 0,
                'qty'     => $qty,
                'price'   => isset($purchasePrices[$itemId]) ? (float)$purchasePrices[$itemId] : 0,
                'serials' => $serialIds,
            ];
        }

        if (empty($lines)) {
            return $this->back_to_return_with_error($purchaseId, 'Please select any product');
        }

        $itemRows = DB()->table('purchase_item')
            ->select('purchase_item_id, prod_id, product_stock_relation_id, quantity')
            ->where('purchase_id', $purchaseId)
            ->whereIn('purchase_item_id', $itemIds)
            ->get()->getResultArray();
        $itemById = array_column($itemRows, null, 'purchase_item_id');

        foreach ($lines as $index => $line) {
            $item = $itemById[$line['item_id']] ?? null;

            if (empty($item) || (int)$item['prod_id'] !== $line['prod_id']) {
                return $this->back_to_return_with_error($purchaseId, 'One or more return lines are not part of this purchase');
            }

            if ($line['qty'] > (float)$item['quantity']) {
                return $this->back_to_return_with_error($purchaseId, 'Quantity cannot exceed the purchased quantity');
            }

            $lines[$index]['psr'] = (int)$item['product_stock_relation_id'];
        }

        // Serial validation: every returned unit of a serialized product must carry
        // one of the serial numbers that are still in stock on this purchase line.
        foreach ($lines as $line) {
            $product = DB()->table('products')
                ->select('name, serial_status')
                ->where('prod_id', $line['prod_id'])
                ->get()->getRow();

            if (empty($product) || (int)$product->serial_status !== 1) {
                continue;
            }

            $lineSerials = get_return_purchase_line_serials($line['psr'], $line['prod_id']);
            if (empty($lineSerials['serials'])) {
                // Purchased before serial numbers were attached to the purchase line
                continue;
            }

            $needQty = (int)round($line['qty']);

            if (count($line['serials']) !== $needQty) {
                return $this->back_to_return_with_error($purchaseId, esc($product->name) . ': ' . $needQty . ' serial number(s) must be selected (quantity is ' . $needQty . ').');
            }

            $inStockIds = array();
            foreach ($lineSerials['serials'] as $serialRow) {
                if ($serialRow['status'] === 'in_stock') {
                    $inStockIds[] = (int)$serialRow['product_serial_id'];
                }
            }

            if (!empty(array_diff($line['serials'], $inStockIds))) {
                return $this->back_to_return_with_error($purchaseId, esc($product->name) . ': one or more serial numbers are not available for return.');
            }
        }

        DB()->transStart();

        // 1. Insert into return_purchase
        $returnData = [
            'sch_id'       => $shopId,
            'purchase_id'  => $purchaseId,
            'supplier_id'  => $supplierId,
            'amount'       => $amount,
            'nagad_paid'   => $cashAmount,
            'bank_paid'    => $bankAmount,
            'bank_id'      => $bankId,
            'createdBy'    => $userId,
            'createdDtm'   => date('Y-m-d H:i:s'),
        ];


        DB()->table('return_purchase')->insert($returnData);
        $returnId = DB()->insertID();

        // 2. Update shop purchase balance + ledger
        $purchaseBalance    = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $newPurchaseBalance = $purchaseBalance - $amount;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['purchase_balance' => $newPurchaseBalance]);

        DB()->table('ledger_purchase')->insert([
            'sch_id'           => $shopId,
            'rtn_purchase_id'  => $returnId,
            'trangaction_type' => 'Cr.',
            'particulars'      => 'Return purchase amount',
            'amount'           => $amount,
            'rest_balance'     => $newPurchaseBalance,
            'createdBy'        => $userId,
            'createdDtm'       => date('Y-m-d H:i:s'),
        ]);

        // stock balance update and ledger create (start)
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = $stockBal - $amount;


        $stockUpdata = array('stockAmount' => $restBalStock);
        $tabshopsSt = DB()->table('shops');
        $tabshopsSt->where('sch_id', $shopId)->update($stockUpdata);


        $stockLedgData = array(
            'sch_id' => $shopId,
            'purchase_id' => $purchaseId,
            'trangaction_type' => 'Cr.',
            'particulars' => 'Return purchase amount',
            'amount' => $amount,
            'rest_balance' => $restBalStock,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $tabledger_stock = DB()->table('ledger_stock');
        $tabledger_stock->insert($stockLedgData);
        $ledg_stock_id = DB()->insertID();
        // stock balance update and ledger create (end)

        // 3. Process each returned item
        foreach ($lines as $line) {
            $qty   = $line['qty'];
            $price = $line['price'];
            $total = $qty * $price;

            // Insert return item
            DB()->table('return_purchase_item')->insert([
                'sch_id'            => $shopId,
                'rtn_purchase_id'   => $returnId,
                'prod_id'           => $line['prod_id'],
                'price'             => $price,
                'quantity'          => $qty,
                'total_price'       => $total,
                'createdBy'         => $userId,
                'createdDtm'        => date('Y-m-d H:i:s'),
            ]);

            // Update product stock quantity (subtract returned qty)
            $stock = DB()->table('product_stock_relation')
                ->where('product_stock_relation_id', $line['psr'])
                ->where('product_id', $line['prod_id'])
                ->get()
                ->getRow();

            if ($stock) {
                $newQty = $stock->quantity - $qty;

                DB()->table('product_stock_relation')
                    ->where('product_stock_relation_id', $line['psr'])
                    ->where('product_id', $line['prod_id'])
                    ->update(['quantity' => $newQty]);
            }

            // Mark the returned units: they leave the stock for good and remember
            // this return slip, so the slip can list exactly its own serial numbers.
            if (!empty($line['serials'])) {
                DB()->table('product_serial')
                    ->whereIn('product_serial_id', $line['serials'])
                    ->where('product_stock_relation_id', $line['psr'])
                    ->where('status', 'in_stock')
                    ->update([
                        'status'          => 'purchase_returned',
                        'rtn_purchase_id' => $returnId,
                        'updatedBy'       => $userId,
                        'updatedDtm'      => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        // 4. Supplier balance + ledger (existing supplier only)
        if (!empty($supplierId)) {
            $oldSupBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newSupBalance = $oldSupBalance + $amount;

            DB()->table('suppliers')
                ->where('supplier_id', $supplierId)
                ->update([
                    'balance'   => $newSupBalance,
                    'createdBy' => $userId,
                ]);

            DB()->table('ledger_suppliers')->insert([
                'sch_id'           => $shopId,
                'supplier_id'      => $supplierId,
                'rtn_purchase_id'  => $returnId,
                'particulars'      => 'Return Purchase Product',
                'trangaction_type' => 'Dr.',
                'amount'           => $amount,
                'rest_balance'     => $newSupBalance,
                'createdBy'        => $userId,
                'createdDtm'       => date('Y-m-d H:i:s'),
            ]);
        }


        DB()->transComplete();

        $this->session->setFlashdata('message', '
        <div class="alert alert-success alert-dismissible" role="alert">
            Return Product Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');

        return redirect()->to(site_url('Admin/Return_purchase/'));
    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnPurchaseTable = DB()->table('return_purchase');
            $data['returnPurchaseData'] = $returnPurchaseTable->where('rtn_purchase_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_purchase_item');
            $data['purchaseItame'] = $returnItem->where('rtn_purchase_id', $id)->get()->getResult();

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

}
