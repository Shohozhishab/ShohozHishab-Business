<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Mycart;
use App\Libraries\Permission;
use App\Libraries\TransactionLog;
use CodeIgniter\HTTP\RedirectResponse;


class Sales extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $cart;
    private $module_name = 'Sales';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = new Mycart();
        $this->transactionLog = new TransactionLog();
    }

    /**
     * @description This method provides sales view
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
            $salesTable = DB()->table('sales');
            $data['sales'] = $salesTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

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
                echo view('Admin/Sales/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides sales create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $salesTable = DB()->table('sales');
            $data['sales'] = $salesTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $data['action'] = base_url('Admin/Sales/create_action');
            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Sales/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method sales search products
     * @return void
     */
    public function search_prod()
    {
        $shopId = $this->session->shopId;

        $keyWord = $this->request->getPost("keyWord");

        $proTable = DB()->table('products');
        $whereLike = "(`name` LIKE '%{$keyWord}%' ESCAPE '!' OR  `prod_id` LIKE '%{$keyWord}%' ESCAPE '!')";
        $data = $proTable->where("sch_id", $shopId)->where("quantity >", 0)->where($whereLike)->get()->getResult();


        $view = '';
        foreach ($data as $sval) {
            $image = ($sval->picture == NULL) ? 'no_image.jpg' : $sval->picture;
            $unit = get_data_by_id('unit', 'products', 'prod_id', $sval->prod_id);

            $view = $view . '<li>
                            <form action="' . site_url('Admin/Sales/add_cart') . '" method="post">
                                <div class="col-xs-12" style="padding:15px; border-bottom: 1px solid;color: #d2d6de;" ><a>
                                <div class="col-xs-2">
                                    <img class="img-circle" src="' . base_url() . '/uploads/product_image/' . $image . '" width="60" height="60">
                                </div>
                                <div class="col-xs-4"><label for="usr">Product Name /Price:</label><h4 style="color:black;">' . $sval->name . '/' . $sval->selling_price . 'Tk.</h4><input class="form-control" type="hidden" readonly id="name" name="name" value="' . $sval->name . '"><input class="form-control" type="hidden" readonly id="price" name="price" value="' . $sval->selling_price . '"><input class="form-control" type="hidden" readonly id="prod_id" name="prod_id" value="' . $sval->prod_id . '"></div>
                                <div class="col-xs-2"><span for="usr">Product Category:</span><br><h4 style="color:black;">' . get_data_by_id('product_category', 'product_category', 'prod_cat_id', $sval->prod_cat_id) . '</h4>
                                </div>
                                <div class="col-xs-2"><span>Quantity:</span><input class="form-control" type="number" name="quantity" id="quantity" value="1"><br><span>' . showUnitName($unit) . '</span></div>
                                <div class="col-xs-2" style="padding-top:28px; ">
                                    <button  type="subbmit" class="add_cart btn btn-success btn-xs" >Add To Cart</button>
                                </div></a></div>
                                </form>
                                </li>';

        }
        echo $view;

    }

    /**
     * @description This method add to cart
     * @return RedirectResponse
     */
    public function add_cart()
    {

        $proId = $this->request->getPost('prod_id');
        $proName = $this->request->getPost('name');
        $proPrice = $this->request->getPost('price');
        $quantity = $this->request->getPost('quantity');

        $productQnt = get_data_by_id('quantity', 'products', 'prod_id', $proId);

        $qty = 0;
        foreach ($this->cart->contents() as $row) {
            if ($proId == $row['id']) {
                $qty = $row['qty'];
            }
        }
        $totalquantity = $quantity + $qty;

        if ($productQnt >= $totalquantity) {
            if ($quantity > 0) {
                $data = array(
                    'id' => $proId,
                    'name' => strval($proName),
                    'qty' => $quantity,
                    'price' => $proPrice
                );
                $this->cart->insert($data);
            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid Quantity  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Sales/create'));
            }
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-warning alert-dismissible" role="alert">Warning: You have no available product quantity to sale<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

        }
        $this->session->set('cartType', 'sale');
        return redirect()->to(site_url('Admin/Sales/create'));
    }

    /**
     * @description This method clear cart
     * @return RedirectResponse
     */
    public function clearCart()
    {

        $this->cart->destroy();
        return redirect()->to(site_url('Admin/Sales/create'));

    }

    /**
     * @description This method remove cart
     * @param int $id
     * @return RedirectResponse
     */
    public function remove_cart($id)
    {
        $this->cart->remove($id);
        return redirect()->to(site_url('Admin/Sales/create'));
    }

    /**
     * @description This method store sales
     * @return RedirectResponse
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;


        $customerId = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('name');

        $proId = $this->request->getPost('productId[]');
        $quantity = $this->request->getPost('qty[]');
        $proPrice = $this->request->getPost('price[]');

        $number = count($proId);
        for ($i = 0; $i < $number; $i++) {
            $prodsaleDiscSingle[] = '';
        }

        $prodsaleDisc = !empty($this->request->getPost('disc[]')) ? $this->request->getPost('disc[]') : $prodsaleDiscSingle;
        $prodsubtotal = $this->request->getPost('subtotal[]');
        $prosubTo = $this->request->getPost('suballtotal[]');

        $entiresaleDisc = $this->request->getPost('saleDisc');
        $vat = !empty($this->request->getPost('vat')) ? $this->request->getPost('vat') : '';
        $vatAmount = $this->request->getPost('vatAmount');


        $amount = $this->request->getPost('grandtotal2');
        $finalAmount = $this->request->getPost('grandtotal');

        $nagod = $this->request->getPost('nagod');
        $bankAmount = $this->request->getPost('bankAmount');
        $bankId = $this->request->getPost('bank_id');
        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = $this->request->getPost('chequeAmount');
        $sms = $this->request->getPost('sms');

        $dueAmount = $this->request->getPost('grandtotaldue');
        $singDiscount = empty($this->request->getPost('granddiscountlast')) ? 0 : $this->request->getPost('granddiscountlast');

        $discountAmount = $amount - $finalAmount;
        $alldiscount = $discountAmount + $singDiscount;

        //customer shop check(start)
        if (!empty($customerId)) {
            $shopCheck = check_shop('customers', 'customer_id', $customerId);
            if ($shopCheck != 1) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid customer <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Sales/create'));
            }
        }
        //customer shop check(end)


        // If customer name of Id not selected (start)
        if (empty($customerName) && empty($customerId)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">please enter valid customer!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Sales/create'));
        }
        // If customer name of Id not selected (End)


        // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (Start)
        if (!empty($customerName)) {
            if (($chequeAmount > 0) || ($dueAmount > 0)) {
                return redirect()->to(site_url('Admin/Sales/create'));
            }
        }
        // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (End)


        if (empty($proId)) {
            return redirect()->to(site_url('Admin/Sales/create'));
        }

        $toAm = (double)$nagod + (double)$bankAmount + (double)$chequeAmount + (double)$dueAmount;

        if ($toAm != $finalAmount) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">wrong input!! please correct inputs to proceed.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Sales/create'));
        }

        if (!empty($nagod) && $nagod < 0) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Sales/create'));
        }

        if (!empty($bankAmount) && $bankAmount < 0) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Sales/create'));
        }

        if (!empty($chequeAmount) && $chequeAmount < 0) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Sales/create'));
        }


        DB()->transStart();


        //create invoice in invoice table (start)
        $invData = array(
            'sch_id' => $shopId,
            'amount' => $amount,
            'entire_sale_discount' => $entiresaleDisc,
            'vat' => $vat,
            'final_amount' => $finalAmount,
            'nagad_paid' => $nagod,
            'bank_paid' => $bankAmount,
            'bank_id' => $bankId,
            'chaque_paid' => $chequeAmount,
            'due' => $dueAmount,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );


        if (!empty($customerId)) {
            $invData['customer_id'] = $customerId;
        } else {
            $invData['customer_name'] = $customerName;
        }
        $invoiceTab = DB()->table('invoice');
        $invoiceTab->insert($invData);
        $invoiceId = DB()->insertID();
        //create invoice in invoice table (end)


        //discount ledger make (start)
        if (!empty($alldiscount)) {
            $prevdis = get_data_by_id('discount', 'shops', 'sch_id', $shopId);
            $disRestBel = $prevdis + $alldiscount;

            $disLedgher = array(
                'sch_id' => $shopId,
                'invoice_id' => $invoiceId,
                'amount' => $alldiscount,
                'particulars' => 'Sale discount',
                'trangaction_type' => 'Dr.',
                'rest_balance' => $disRestBel,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_discountTab = DB()->table('ledger_discount');
            $ledger_discountTab->insert($disLedgher);
            $discount_ledg_id = DB()->insertID();

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_discount',$discount_ledg_id,'',$alldiscount,'','',$invoiceId,'');
            //insert log (end)

            //update discount balance(start)
            $disData = array(
                'discount' => $disRestBel,
                'updatedBy' => $userId,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $shopId)->update($disData);
            //update discount balance(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$alldiscount,'','',$invoiceId,'','discount');
            //insert log (end)
        }
        //discount ledger make (end)


        //vat ledgher insert (start)
        if (!empty($vatAmount)) {
            $vatId = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
            $previousVat = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
            $vatRestBalance = $previousVat - $vatAmount;

            $VatLedgher = array(
                'sch_id' => $shopId,
                'vat_id' => $vatId,
                'invoice_id' => $invoiceId,
                'amount' => $vatAmount,
                'particulars' => 'Sale Vat Earn ',
                'trangaction_type' => 'Cr.',
                'rest_balance' => $vatRestBalance,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_vatTab = DB()->table('ledger_vat');
            $ledger_vatTab->insert($VatLedgher);
            $ledg_vat_id = DB()->insertID();

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_vat',$ledg_vat_id,'',$vatAmount,'','',$invoiceId,'');
            //insert log (end)

            //update vat register table(start)
            $vatRegData = array(
                'balance' => $vatRestBalance,
                'updatedBy' => $userId,
            );
            $ledger_vatTab = DB()->table('vat_register');
            $ledger_vatTab->where('sch_id', $shopId)->update($vatRegData);
            //update vat register table(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('vat_register',$shopId,'',$vatAmount,'','',$invoiceId,'');
            //insert log (end)
        }
        //vat ledgher insert (end)


        //invoice itame insert
        $totalpurPrice = 0;
        $number = count($proId);
        for ($i = 0; $i < $number; $i++) {

            // Inserting invoice item data into invoice_item table(Start)
            $invItemData = array(
                'sch_id' => $shopId,
                'invoice_id' => $invoiceId,
                'prod_id' => $proId[$i],
                'price' => $proPrice[$i],
                'quantity' => $quantity[$i],
                'total_price' => $prodsubtotal[$i],
                'discount' => $prodsaleDisc[$i],
                'final_price' => $prosubTo[$i],
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $invoice_itemTab = DB()->table('invoice_item');
            $invoice_itemTab->insert($invItemData);
            //print $this->db->last_query();
            // Inserting invoice item data into invoice_item table(End)


            //calculating profit for individual item and updating the profit column (start)
            $productPurPrice = get_data_by_id('purchase_price', 'products', 'prod_id', $proId[$i]);
            $purPrice = $productPurPrice * $quantity[$i];
            $totalpurPrice += $productPurPrice * $quantity[$i];
            $profit = $prosubTo[$i] - $purPrice;
            $profitData = array('profit' => $profit);

            $where = array(
                'invoice_id' => $invoiceId,
                'prod_id' => $proId[$i],
            );
            $invoice_itemTab2 = DB()->table('invoice_item');
            $invoice_itemTab2->where($where)->update($profitData);
            //calculating profit for individual item and updating the profit column (end)


            //product Qnt Update in product table (start)
            $productQnt = get_data_by_id('quantity', 'products', 'prod_id', $proId[$i]);
            $qnt = $productQnt - $quantity[$i];
            $qntProData = array(
                'quantity' => $qnt,
                'updatedBy' => $userId,
            );
            $productsTable = DB()->table('products');
            $productsTable->where('prod_id', $proId[$i])->update($qntProData);
            //product Qnt Update in product table (end)
            //insert log (start)
            $this->transactionLog->insert_log_data('products',$proId[$i],'',$quantity[$i],'','',$invoiceId,'','quantity');
            //insert log (end)
        }


        //create sals in sales table(start)
        $saleData = array(
            'sch_id' => $shopId,
            'invoice_id' => $invoiceId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $salesTab = DB()->table('sales');
        $salesTab->insert($saleData);
        $sales_id = DB()->insertID();
        //create salse in sales table(end)


        //sale balance update and ledger create (start)
        $withoutVat = $finalAmount - (int)$vatAmount;
        $saleBal = get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $restBalSale = $saleBal - $withoutVat;


        $saleUpdata = array('sale_balance' => $restBalSale);
        $shopsTabl = DB()->table('shops');
        $shopsTabl->where('sch_id', $shopId)->update($saleUpdata);
        //insert log (start)
        $this->transactionLog->insert_log_data('shops',$shopId,'',$withoutVat,'','',$invoiceId,'','sale_balance');
        //insert log (end)

        $saleLedgData = array(
            'sch_id' => $shopId,
            'invoice_id' => $invoiceId,
            'trangaction_type' => 'Cr.',
            'particulars' => 'New Sale amount',
            'amount' => $withoutVat,
            'rest_balance' => $restBalSale,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_salesTab = DB()->table('ledger_sales');
        $ledger_salesTab->insert($saleLedgData);
        $ledgSale_id = DB()->insertID();
        //sale balance update and ledger create (end)
        //insert log (start)
        $this->transactionLog->insert_log_data('ledger_sales',$ledgSale_id,'',$withoutVat,'','',$invoiceId,'','sale_balance');
        //insert log (end)


        //Update salse profit in invoice table (start)
        $invoice_itemT = DB()->table('invoice_item');
        $totalProfit = $invoice_itemT->selectSum('profit')->where('invoice_id', $invoiceId)->get()->getRow()->profit;
        $invoiceT = DB()->table('invoice');
        $invData = $invoiceT->where('invoice_id', $invoiceId)->get()->getRow();
        $invProfit = $invData->amount - $invData->final_amount;
        $prifitAll = $totalProfit - $invProfit;

        $inData = array(
            'profit' => $prifitAll,
            'updatedBy' => $userId,
        );
        $invoiceTabl = DB()->table('invoice');
        $invoiceTabl->where('invoice_id', $invoiceId)->update($inData);
        //insert log (start)
        $this->transactionLog->insert_log_data('invoice_id',$invoiceId,'',$invProfit,'','',$invoiceId,'','profit');
        //insert log (end)


        $shopProfit = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $totShopPro = $shopProfit - $totalProfit + $discountAmount + (double)$vatAmount;

        $dataShoproUp = array(
            'profit' => $totShopPro,
        );
        $shopsTable = DB()->table('shops');
        $shopsTable->where('sch_id', $shopId)->update($dataShoproUp);
        //insert log (start)
        $this->transactionLog->insert_log_data('shops',$shopId,'',$totalProfit,'','',$invoiceId,'','profit');
        //insert log (end)

        $profitLedData = array(
            'sch_id' => $shopId,
            'invoice_id' => $invoiceId,
            'trangaction_type' => 'Cr.',
            'particulars' => 'Sales profit get',
            'amount' => $totalProfit,
            'rest_balance' => $totShopPro,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_profitTab = DB()->table('ledger_profit');
        $ledger_profitTab->insert($profitLedData);
        $profit_id = DB()->insertID();
        //insert log (start)
        $this->transactionLog->insert_log_data('ledger_profit',$profit_id,'',$totalProfit,'','',$invoiceId,'');
        //insert log (end)


        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = $stockBal - $totalpurPrice;


        $stockUpdata = array('stockAmount' => $restBalStock);
        $shopsTabl = DB()->table('shops');
        $shopsTabl->where('sch_id', $shopId)->update($stockUpdata);

        //insert log (start)
        $this->transactionLog->insert_log_data('shops',$shopId,'',$totalpurPrice,'','',$invoiceId,'','stockAmount');
        //insert log (end)

        $stockLedgData = array(
            'sch_id' => $shopId,
            'invoice_id' => $invoiceId,
            'trangaction_type' => 'Cr.',
            'particulars' => 'Sale amount',
            'amount' => $totalpurPrice,
            'rest_balance' => $restBalStock,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_stockTabl = DB()->table('ledger_stock');
        $ledger_stockTabl->insert($stockLedgData);
        $stock_id = DB()->insertID();
        //Update salse profit in invoice table (end)
        //insert log (start)
        $this->transactionLog->insert_log_data('ledger_stock',$stock_id,'',$totalpurPrice,'','',$invoiceId,'');
        //insert log (end)


        //existing customer balance update and customer ledger create (start)
        if ($customerId) {


            //customer balance update in customer table (start)
            $customerCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $newCash = $customerCash + $finalAmount;
            //update balance
            $custData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $customersTab = DB()->table('customers');
            $customersTab->where('customer_id', $customerId)->update($custData);
            //customer balance update in customer table (end)
            //insert log (start)
            $this->transactionLog->insert_log_data('customers',$customerId,'',$finalAmount,'','',$invoiceId,'');
            //insert log (end)


            //insert customer ledger in ledger(start)
            $ledgerData = array(
                'sch_id' => $shopId,
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'Sales Cash Due',
                'amount' => $finalAmount,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->insert($ledgerData);
            $ledg_id = DB()->insertID();
            //insert customer ledger in ledger(end)

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger',$ledg_id,'',$finalAmount,'','',$invoiceId,'');
            //insert log (end)
            if(!empty($sms)) {
                $message = 'Thank you for your order.Your order amount is-' . $finalAmount;
                $phone = get_data_by_id('mobile', 'customers', 'customer_id', $customerId);
                send_sms($phone, $message);
            }

        }
        //existing customer balance update and customer ledger create (end)


        //cash pay shop cash update and create nagod ledger (start)
        if ($nagod > 0) {
            //cash pay amount update shops cash (start)
            $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $upCahs = $shopsCash + $nagod;

            $shopsData = array(
                'cash' => $upCahs,
                'updatedBy' => $userId,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $shopId)->update($shopsData);
            //cash pay amount update shops cash (end)
            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$nagod,'','',$invoiceId,'','cash');
            //insert log (end)


            //insert ledger in ledger_nagodan cash pay amount(start)
            $lgNagData = array(
                'sch_id' => $shopId,
                'invoice_id' => $invoiceId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'Sales Cash Pay',
                'amount' => $nagod,
                'rest_balance' => $upCahs,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $ledger_nagodanTab->insert($lgNagData);
            $ledg_nagodan_id = DB()->insertID();
            //insert ledger in ledger_nagodan cash pay amount(start)
            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$nagod,'','',$invoiceId,'');
            //insert log (end)


            //cash pay amount and customer balance amount calculate and update customer balance (start)
            if ($customerId) {
                //customer balance calculate (start)
                $custCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $newcastCash = $custCash - $nagod;
                //customer balance calculate (end)


                //update calculate balance in customer table(start)
                $custnewData = array(
                    'balance' => $newcastCash,
                    'updatedBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($custnewData);
                //update calculate balance in customer table(end)
                //insert log (start)
                $this->transactionLog->insert_log_data('customers',$customerId,'',$nagod,'','',$invoiceId,'');
                //insert log (end)


                //create ledger in ledger table
                $ledgernogodData = array(
                    'sch_id' => $shopId,
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => 'Sales Cash Pay',
                    'amount' => $nagod,
                    'rest_balance' => $newcastCash,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->insert($ledgernogodData);
                $ledg_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('ledger',$ledg_id,'',$nagod,'','',$invoiceId,'');
                //insert log (end)
            }
            //cash pay amount and customer balance amount calculate and update customer balance (end)
        }
        //cash pay shop cash update and create nagod ledger (end)


        // bank pay amount calculate and bank balance update (start)
        if ($bankAmount > 0) {
            //bank pay amount calculate and update bank balance (start)
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $upCahs = $bankCash + $bankAmount;

            $bankData = array(
                'balance' => $upCahs,
                'updatedBy' => $userId,
            );
            $bankTab = DB()->table('bank');
            $bankTab->where('bank_id', $bankId)->update($bankData);
            //bank pay amount calculate and update bank balance (end)
            //insert log (start)
            $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','',$invoiceId,'');
            //insert log (end)


            //insert ledger in table ledger_bank (start)
            $lgBankData = array(
                'sch_id' => $shopId,
                'bank_id' => $bankId,
                'invoice_id' => $invoiceId,
                'particulars' => 'Sales Bank Pay',
                'trangaction_type' => 'Dr.',
                'amount' => $bankAmount,
                'rest_balance' => $upCahs,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->insert($lgBankData);
            $ledgBank_id = DB()->insertID();
            //insert ledger in table ledger_bank (end)
            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','',$invoiceId,'');
            //insert log (end)

            if ($customerId) {
                //bank pay amount calculate and customer balance update (start)
                $cusCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $bankastCash = $cusCash - $bankAmount;

                $custnewData = array(
                    'balance' => $bankastCash,
                    'updatedBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($custnewData);
                //bank pay amount calculate and customer balance update (start)
                //insert log (start)
                $this->transactionLog->insert_log_data('customers',$customerId,'',$bankAmount,'','',$invoiceId,'');
                //insert log (end)

                //insert ledger in table ledger (start)
                $ledgerbankData = array(
                    'sch_id' => $shopId,
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => 'Sales Bank Pay',
                    'amount' => $bankAmount,
                    'rest_balance' => $bankastCash,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->insert($ledgerbankData);
                $ledg_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('ledger',$ledg_id,'',$bankAmount,'','',$invoiceId,'');
                //insert log (end)

            }

        }
        // bank pay amount calculate and bank balance update (end)


        // cheque pay amount calculate and insert cheque table (end)
        if ($chequeAmount > 0) {

            //cheque pay amount calculate and insert cheque tabile(start)
            $chequeData = array(
                'sch_id' => $shopId,
                'chaque_number' => $chequeNo,
                'to' => $userId,
                'from' => $customerId,
                'amount' => $chequeAmount,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            if (!empty($customerId)) {
                $chequeData ['from'] = $customerId;
            } else {
                $chequeData ['from_name'] = $customerName;
            }
            $chaqueTab = DB()->table('chaque');
            $chaqueTab->insert($chequeData);
            $chaqueId = DB()->insertID();
            //cheque pay amount calculate and insert cheque tabile(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('chaque',$chaqueId,'',$chequeAmount,'','',$invoiceId,'');
            //insert log (end)

            //chaque id update in invoice table(start)
            $invChaqueId = array(
                'chaque_id' => $chaqueId,
                'updatedBy' => $userId,
            );
            $invoiceTab = DB()->table('invoice');
            $invoiceTab->where('invoice_id', $invoiceId)->update($invChaqueId);
            //chaque id update in invoice table(end)
        }

        DB()->transComplete();

        $this->cart->destroy();
        return redirect()->to(site_url('Admin/Invoice/view/' . $invoiceId));
    }

    /**
     * @description This method scan add to cart
     * @return void
     */
    public function scanAddToCart()
    {
        $shopId = $this->session->shopId;

        $proId = $this->request->getPost('prod_id');

        $checkShop = get_data_by_id('sch_id', 'products', 'prod_id', $proId);

        if ($checkShop == $shopId) {

            $proName = get_data_by_id('name', 'products', 'prod_id', $proId);
            $proPrice = get_data_by_id('selling_price', 'products', 'prod_id', $proId);
            $quantity = 1;

            $productQnt = get_data_by_id('quantity', 'products', 'prod_id', $proId);

            $qty = 0;
            foreach ($this->cart->contents() as $row) {
                if ($proId == $row['id']) {
                    $qty = $row['qty'];
                }
            }
            $totalquantity = $quantity + $qty;

            if ($productQnt >= $totalquantity) {
                if ($quantity > 0) {
                    $data = array(
                        'id' => $proId,
                        'name' => strval($proName),
                        'qty' => $quantity,
                        'price' => $proPrice
                    );
                    $this->cart->insert($data);
                }
            }
            $this->session->set('cartType', 'sale');
        } else {
            print '<div class="alert alert-warning alert-dismissible" role="alert">Warning: You have no available product.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    public function customerBalance(){
        $customerId = $this->request->getPost('customer_id');
        $balance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
        return $balance;
    }

    public function salesEdit(){
        $salesId = $this->request->getPost('id');

        $salesTable = DB()->table('sales');
        $data['sales'] = $salesTable->where('sales_id', $salesId)->get()->getRow();

        $invoiceTable = DB()->table('invoice');
        $data['invoice'] = $invoiceTable->where('invoice_id', $data['sales']->invoice_id)->get()->getRow();

        $invoiceItemTable = DB()->table('invoice_item');
        $data['invoiceItem'] = $invoiceItemTable->where('invoice_id', $data['sales']->invoice_id)->get()->getResult();

        echo view('Admin/Sales/edit', $data);
    }

    public function salesEdiAction(){
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;


        $invoiceId = $this->request->getPost('invoice_id');

        $customerId = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('name');

        $proId = $this->request->getPost('prod_id[]');
        $invItemId = $this->request->getPost('inv_item[]');

        $quantity = $this->request->getPost('qty[]');
        $proPrice = $this->request->getPost('price[]');
        $total = $this->request->getPost('total[]');
        $discount = $this->request->getPost('discount[]');
        $subTotal = $this->request->getPost('subTotal[]');
        $invItem = $this->request->getPost('inv_item[]');



        $entiresaleDisc = $this->request->getPost('saleDisc');
        $vat = !empty($this->request->getPost('vat')) ? $this->request->getPost('vat') : '';
        $vatAmount = $this->request->getPost('vatAmount');


        $amount = $this->request->getPost('grandtotal2');
        $finalAmount = $this->request->getPost('grandtotal');

        $nagod = $this->request->getPost('nagod');
        $bankAmount = $this->request->getPost('bankAmount');
        $bankId = $this->request->getPost('bank_id');
        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = $this->request->getPost('chequeAmount');

        $dueAmount = $this->request->getPost('grandtotaldue');


        $singDiscount = empty($this->request->getPost('granddiscountlast')) ? 0 : $this->request->getPost('granddiscountlast');

        $discountAmount = $amount - $finalAmount;
        $alldiscount = $discountAmount + $singDiscount;


        DB()->transStart();

        //update invoice start)
        $invData = array(
            'amount' => $amount,
            'entire_sale_discount' => $entiresaleDisc,
            'vat' => $vat,
            'final_amount' => $finalAmount,
            'nagad_paid' => $nagod,
            'bank_paid' => $bankAmount,
            'bank_id' => $bankId,
            'chaque_paid' => $chequeAmount,
            'due' => $dueAmount,
        );

        if (!empty($customerId)) {
            $invData['customer_id'] = $customerId;
        } else {
            $invData['customer_name'] = $customerName;
        }
        $invoiceTab = DB()->table('invoice');
        $invoiceTab->where('invoice_id',$invoiceId)->update($invData);
        //update invoice (end)


        //discount ledger make (start)
        if (!empty($alldiscount)) {
            $discountLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_discount',$invoiceId);
            $prevDisLed = get_data_by_id('amount', 'ledger_discount', 'discount_ledg_id', $discountLedInfo->id);
            $restDisBal = ($prevDisLed - $discountLedInfo->amount) + $alldiscount;
            $disLedgher = array(
                'amount' => $alldiscount,
                'rest_balance' => $restDisBal,
            );
            $ledger_discountTab = DB()->table('ledger_discount');
            $ledger_discountTab->where('discount_ledg_id', $discountLedInfo->id)->update($disLedgher);
            $this->ledger_discount_rest_balance_update($invoiceId,$alldiscount,$discountLedInfo->id,$discountLedInfo->amount);
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('ledger_discount','','',$this->session->userId,$discountLedInfo->amount,$alldiscount,$invoiceId,'');
            //insert Transaction in transaction table (end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('ledger_discount',$discountLedInfo->id,'',$this->session->userId,$discountLedInfo->amount,$alldiscount,$invoiceId,'' );
            //insert Transaction in transaction table (end)


            $prevDis = get_data_by_id('discount', 'shops', 'sch_id', $shopId);
            $disRestBel = ($prevDis - $discountLedInfo->amount) + $alldiscount;
            //update discount balance(start)
            $disData = array(
                'discount' => $disRestBel,
                'updatedBy' => $userId,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $shopId)->update($disData);
            //update discount balance(end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('shops','','',$this->session->userId,$discountLedInfo->amount,$alldiscount,$invoiceId,'','discount');
            //insert Transaction in transaction table (end)

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('shops',$shopId,'',$this->session->userId,$discountLedInfo->amount,$alldiscount,$invoiceId,'','discount' );
            //insert Transaction in transaction table (end)
        }
        //discount ledger make (end)

        //vat ledger insert (start)
        if (!empty($vatAmount)) {
            $vatLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_vat',$invoiceId);
            if (!empty($vatLedInfo)){
                $previousVat = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
                $vatRestBalance = ($previousVat + $vatLedInfo->amount) - $vatAmount;

                //update vat register table(start)
                $vatRegData = array(
                    'balance' => $vatRestBalance,
                    'updatedBy' => $userId,
                );
                $ledger_vatTab = DB()->table('vat_register');
                $ledger_vatTab->where('sch_id', $shopId)->update($vatRegData);
                //update vat register table(end)
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('vat_register',$shopId,'',$this->session->userId,$vatLedInfo->amount,$vatAmount,$invoiceId,'');
                //insert Transaction in transaction table (end)

                $vatLedRestBal = get_data_by_id('rest_balance', 'ledger_vat', 'ledg_vat_id', $vatLedInfo->id);
                $newVatLedRestBal = ($vatLedRestBal + $vatLedInfo->amount) - $vatAmount;

                $vatLedger = array(
                    'amount' => $vatAmount,
                    'rest_balance' => $newVatLedRestBal,
                );
                $ledger_vatTab = DB()->table('ledger_vat');
                $ledger_vatTab->where('ledg_vat_id', $vatLedInfo->id)->update($vatLedger);
                $this->vat_ledger_rest_balance_update($invoiceId,$vatAmount,$vatLedInfo->id,$vatLedInfo->amount);
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('ledger_vat',$vatLedInfo->id,'',$this->session->userId,$vatLedInfo->amount,$vatAmount,$invoiceId,'');
                //insert Transaction in transaction table (end)


            }else {
                $vatId = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
                $previousVat = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);
                $vatRestBalance = $previousVat - $vatAmount;

                $VatLedgher = array(
                    'sch_id' => $shopId,
                    'vat_id' => $vatId,
                    'invoice_id' => $invoiceId,
                    'amount' => $vatAmount,
                    'particulars' => 'Sale Vat Earn ',
                    'trangaction_type' => 'Cr.',
                    'rest_balance' => $vatRestBalance,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_vatTab = DB()->table('ledger_vat');
                $ledger_vatTab->insert($VatLedgher);
                $ledg_vat_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_vat', $ledg_vat_id, '', $vatAmount, '', '', $invoiceId, '');
                //insert log (end)

                //update vat register table(start)
                $vatRegData = array(
                    'balance' => $vatRestBalance,
                    'updatedBy' => $userId,
                );
                $ledger_vatTab = DB()->table('vat_register');
                $ledger_vatTab->where('sch_id', $shopId)->update($vatRegData);
                //update vat register table(end)
                //insert log (start)
                $this->transactionLog->insert_log_data('vat_register', $shopId, '', $vatAmount, '', '', $invoiceId, '');
                //insert log (end)
            }
        }
        //vat ledger insert (end)

        //invoice itame insert
        $table = DB()->table('transaction_log');
        $proQty = $table->where('invoice_id',$invoiceId)->where('table_name','products')->where('colum_name','quantity')->get()->getResult();
        $totalpurPrice = 0;
        $number = count($proId);
        for ($i = 0; $i < $number; $i++) {

            // Inserting invoice item data into table(Start)
            $invItemData = array(
                'price' => $proPrice[$i],
                'quantity' => $quantity[$i],
                'total_price' => $total[$i],
                'discount' => $discount[$i],
                'final_price' => $subTotal[$i],
            );
            $invoice_itemTab = DB()->table('invoice_item');
            $invoice_itemTab->where('inv_item',$invItem[$i])->update($invItemData);
            // Inserting invoice item data into table(End)


            //calculating profit for individual item and updating the profit column (start)
            $productPurPrice = get_data_by_id('purchase_price', 'products', 'prod_id', $proId[$i]);
            $purPrice = $productPurPrice * $quantity[$i];
            $totalpurPrice += $productPurPrice * $quantity[$i];
            $profit = $subTotal[$i] - $purPrice;
            $profitData = array('profit' => $profit);
            $where = array(
                'invoice_id' => $invoiceId,
                'prod_id' => $proId[$i],
            );
            $invoice_itemTab2 = DB()->table('invoice_item');
            $invoice_itemTab2->where($where)->update($profitData);
            //calculating profit for individual item and updating the profit column (end)


            //product Qnt Update in product table (start)
            foreach ($proQty as $pro) {
                if ($pro->id == $proId[$i]) {
                    $productQnt = get_data_by_id('quantity', 'products', 'prod_id', $proId[$i]);
                    $qnt = ($productQnt + $pro->amount) - $quantity[$i];
                    $qntProData = array(
                        'quantity' => $qnt,
                    );
                    $productsTable = DB()->table('products');
                    $productsTable->where('prod_id', $proId[$i])->update($qntProData);
                }
            }
            //product Qnt Update in product table (end)
        }

        //sale balance update and ledger create (start)
        $saleBalInfo = $this->transactionLog->get_table_name_by_row_invoice_id_by_colum_name('shops',$invoiceId,'sale_balance');
        $withoutVat = $finalAmount - (int)$vatAmount;
        $saleBal = get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $restBalSale = ($saleBal + $saleBalInfo->amount) - $withoutVat;

        $saleUpdata = array('sale_balance' => $restBalSale);
        $shopsTabl = DB()->table('shops');
        $shopsTabl->where('sch_id', $shopId)->update($saleUpdata);
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('shops',$shopId,'',$this->session->userId,$saleBalInfo->amount,$withoutVat,$invoiceId,'','sale_balance');
        //insert Transaction in transaction table (end)


        $saleLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_sales',$invoiceId);
        $saleBal = get_data_by_id('rest_balance', 'ledger_sales', 'ledgSale_id', $saleLedInfo->id);
        $restBalSaleLad = ($saleBal + $saleLedInfo->amount) - $withoutVat;
        $saleLedgData = array(
            'amount' => $withoutVat,
            'rest_balance' => $restBalSaleLad,
        );
        $ledger_salesTab = DB()->table('ledger_sales');
        $ledger_salesTab->where('ledgSale_id', $saleLedInfo->id)->update($saleLedgData);
        $this->ledger_sale_rest_balance_update($invoiceId,$withoutVat,$saleLedInfo->id,$saleLedInfo->amount);
        //sale balance update and ledger create (end)
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_sales',$saleLedInfo->id,'',$this->session->userId,$saleLedInfo->amount,$withoutVat,$invoiceId,'');
        //insert Transaction in transaction table (end)

        //Update salse profit in invoice table (start)
        $invoice_itemT = DB()->table('invoice_item');
        $totalProfit = $invoice_itemT->selectSum('profit')->where('invoice_id', $invoiceId)->get()->getRow()->profit;
        $invoiceT = DB()->table('invoice');
        $invData = $invoiceT->where('invoice_id', $invoiceId)->get()->getRow();
        $invProfit = $invData->amount - $invData->final_amount;
        $prifitAll = $totalProfit - $invProfit;

        $inData = array(
            'profit' => $prifitAll,
            'updatedBy' => $userId,
        );
        $invoiceTabl = DB()->table('invoice');
        $invoiceTabl->where('invoice_id', $invoiceId)->update($inData);
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('invoice',$invoiceId,'',$this->session->userId,$invData->amount,$invProfit,$invoiceId,'');
        //insert Transaction in transaction table (end)


        $saleBalInfo = $this->transactionLog->get_table_name_by_row_invoice_id_by_colum_name('shops',$invoiceId,'profit');
        $shopProfit = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $totShopPro = $shopProfit + $saleBalInfo->amount - $totalProfit;

        $dataShoproUp = array(
            'profit' => $totShopPro,
        );
        $shopsTable = DB()->table('shops');
        $shopsTable->where('sch_id', $shopId)->update($dataShoproUp);
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('shops',$shopId,'',$this->session->userId,$saleBalInfo->amount,$totalProfit,$invoiceId,'');
        //insert Transaction in transaction table (end)


        $saleLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_profit',$invoiceId);
        $ledgerProfit = get_data_by_id('rest_balance', 'ledger_profit', 'profit_id', $saleLedInfo->id);
        $ledgerRestProfit = ($ledgerProfit + $saleLedInfo->amount) - $totalProfit;
        $profitLedData = array(
            'amount' => $totalProfit,
            'rest_balance' => $ledgerRestProfit,
        );
        $ledger_profitTab = DB()->table('ledger_profit');
        $ledger_profitTab->where('profit_id', $saleLedInfo->id)->update($profitLedData);
        $this->ledger_profit_rest_balance_update($invoiceId,$totalProfit,$saleLedInfo->id,$saleLedInfo->amount);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_profit',$saleLedInfo->id,'',$this->session->userId,$saleLedInfo->amount,$totalProfit,$invoiceId,'');
        //insert Transaction in transaction table (end)

        $stockBalInfo = $this->transactionLog->get_table_name_by_row_invoice_id_by_colum_name('shops',$invoiceId,'stockAmount');
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = ($stockBal + $stockBalInfo->amount) - $totalpurPrice;
        $stockUpdata = array('stockAmount' => $restBalStock);
        $shopsTabl = DB()->table('shops');
        $shopsTabl->where('sch_id', $shopId)->update($stockUpdata);
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('shops',$shopId,'',$this->session->userId,$stockBalInfo->amount,$totalpurPrice,$invoiceId,'');
        //insert Transaction in transaction table (end)


        $stockLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_stock',$invoiceId);
        $stockLedBal = get_data_by_id('rest_balance', 'ledger_stock', 'stock_id', $stockLedInfo->id);
        $restBalStockTotal = ($stockLedBal + $stockLedInfo->amount) - $totalpurPrice;
        $stockLedgData = array(
            'amount' => $totalpurPrice,
            'rest_balance' => $restBalStockTotal,
        );
        $ledger_stockTabl = DB()->table('ledger_stock');
        $ledger_stockTabl->where('stock_id', $stockLedInfo->id)->update($stockLedgData);
        $this->ledger_stock_rest_balance_update($invoiceId,$totalpurPrice,$stockLedInfo->id,$stockLedInfo->amount);
        //Update salse profit in invoice table (end)
        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_stock',$stockLedInfo->id,'',$this->session->userId,$stockLedInfo->amount,$totalpurPrice,$invoiceId,'');
        //insert Transaction in transaction table (end)


        //existing customer balance update and customer ledger create (start)
        if ($customerId) {
            //customer balance update in customer table (start)
            $customerInfo = $this->transactionLog->get_table_name_by_row_invoice_id('customers',$invoiceId);
            $customerCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $newCash = ($customerCash - $customerInfo->amount) + $finalAmount;
            //update balance
            $custData = array(
                'balance' => $newCash,
            );
            $customersTab = DB()->table('customers');
            $customersTab->where('customer_id', $customerId)->update($custData);
            //customer balance update in customer table (end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('customers',$customerId,'',$this->session->userId,$customerInfo->amount,$finalAmount,$invoiceId,'');
            //insert Transaction in transaction table (end)


            //insert customer ledger in ledger(start)
            $customerLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger',$invoiceId);
            $customerOldCash = get_data_by_id('rest_balance', 'ledger', 'ledg_id', $customerLedInfo->id);
            $customerLedRestCash = ($customerOldCash - $customerLedInfo->amount) + $finalAmount;
            $ledgerData = array(
                'amount' => $finalAmount,
                'rest_balance' => $customerLedRestCash,
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->where('ledg_id', $customerLedInfo->id)->update($ledgerData);
            $this->ledger_customer_rest_balance_update($invoiceId,$finalAmount,$customerLedInfo->id,$customerLedInfo->amount);
            //insert customer ledger in ledger(end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('ledger',$customerLedInfo->id,'',$this->session->userId,$customerLedInfo->amount,$finalAmount,$invoiceId,'');
            //insert Transaction in transaction table (end)

        }
        //existing customer balance update and customer ledger create (end)

        //cash pay shop cash update and create nagod ledger (start)
        if ($nagod > 0) {
            //cash pay amount update shops cash (start)
            $shopBalInfo = $this->transactionLog->get_table_name_by_row_invoice_id_by_colum_name('shops',$invoiceId,'cash');
            $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $upCahs = ($shopsCash - $shopBalInfo->amount) + $nagod;

            $shopsData = array(
                'cash' => $upCahs,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $shopId)->update($shopsData);
            //cash pay amount update shops cash (end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('shops',$shopId,'',$this->session->userId,$shopBalInfo->amount,$nagod,$invoiceId,'','cash');
            //insert Transaction in transaction table (end)



            //insert ledger in ledger_nagodan cash pay amount(start)
            $shopLedInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_nagodan',$invoiceId);
            $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
            $ledgerUpCahs = ($shopLedBal - $shopLedInfo->amount) + $nagod;
            $lgNagData = array(
                'amount' => $nagod,
                'rest_balance' => $ledgerUpCahs,
            );
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($lgNagData);
            $this->cash_ledger_rest_balance_update($invoiceId,$amount,$shopLedInfo->id,$shopLedInfo->amount);
            //insert ledger in ledger_nagodan cash pay amount(start)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('ledger_nagodan',$shopLedInfo->id,'',$this->session->userId,$shopLedInfo->amount,$nagod,$invoiceId,'');
            //insert Transaction in transaction table (end)


            //cash pay amount and customer balance amount calculate and update customer balance (start)
            if ($customerId) {
                //customer balance calculate (start)
                $queryLed = DB()->table('transaction_log')
                    ->where('table_name', 'customers')
                    ->where('invoice_id', $invoiceId)
                    ->orderBy('transaction_log_id', 'ASC')
                    ->limit(1, 1)->get();
                $customerMidInfo = $queryLed->getRow();

                $custCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $newcastCash = ($custCash + $customerMidInfo->amount) - $nagod;
                //customer balance calculate (end)
                //update calculate balance in customer table(start)
                $custnewData = array(
                    'balance' => $newcastCash,
                    'updatedBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($custnewData);
                //update calculate balance in customer table(end)
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('customers',$customerId,'',$this->session->userId,$customerMidInfo->amount,$nagod,$invoiceId,'');
                //insert Transaction in transaction table (end)


                //create ledger in ledger table
                $queryLed = DB()->table('transaction_log')
                    ->where('table_name', 'ledger')
                    ->where('invoice_id', $invoiceId)
                    ->orderBy('transaction_log_id', 'ASC')
                    ->limit(1, 1)->get();
                $customerLedgerMidInfo = $queryLed->getRow();
                $custCash = get_data_by_id('rest_balance', 'ledger', 'ledg_id', $customerLedgerMidInfo->id);
                $newCastRestBal = ($custCash + $customerLedgerMidInfo->amount) - $nagod;
                $ledgernogodData = array(
                    'amount' => $nagod,
                    'rest_balance' => $newCastRestBal,
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->where('ledg_id', $customerLedgerMidInfo->id)->update($ledgernogodData);
                $this->ledger_customer_rest_balance_update($invoiceId,$nagod,$customerLedgerMidInfo->id,$customerLedgerMidInfo->amount);
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('ledger',$customerLedgerMidInfo->id,'',$this->session->userId,$customerLedgerMidInfo->amount,$nagod,$invoiceId,'');
                //insert Transaction in transaction table (end)
            }
            //cash pay amount and customer balance amount calculate and update customer balance (end)
        }
        //cash pay shop cash update and create nagod ledger (end)

        // bank pay amount calculate and bank balance update (start)
        if ($bankAmount > 0) {
            //bank pay amount calculate and update bank balance (start)
            $bankInfo = $this->transactionLog->get_table_name_by_row_invoice_id('bank',$invoiceId);
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $upCahs = ($bankCash - $bankInfo->amount) + $bankAmount;

            $bankData = array(
                'balance' => $upCahs,
            );
            $bankTab = DB()->table('bank');
            $bankTab->where('bank_id', $bankId)->update($bankData);
            //bank pay amount calculate and update bank balance (end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('bank',$bankId,'',$this->session->userId,$bankInfo->amount,$bankAmount,$invoiceId,'');
            //insert Transaction in transaction table (end)


            //insert ledger in table ledger_bank (start)
            $bankLedgerInfo = $this->transactionLog->get_table_name_by_row_invoice_id('ledger_bank',$invoiceId);
            $bankLedgerCash = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgerInfo->id);
            $upRestBal = ($bankLedgerCash - $bankLedgerInfo->amount) + $bankAmount;
            $lgBankData = array(
                'amount' => $bankAmount,
                'rest_balance' => $upRestBal,
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->where('ledgBank_id', $bankLedgerInfo->id)->update($lgBankData);
            //insert ledger in table ledger_bank (end)
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('ledger_bank',$bankLedgerInfo->id,'',$this->session->userId,$bankLedgerInfo->amount,$bankAmount,$invoiceId,'');
            //insert Transaction in transaction table (end)

            if ($customerId) {
                //bank pay amount calculate and customer balance update (start)
                $queryLed = DB()->table('transaction_log')
                    ->where('table_name', 'customers')
                    ->where('invoice_id', $invoiceId)->get();
                $customerMidInfo = $queryLed->getLastRow();
                $cusCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $bankastCash = ($cusCash + $customerMidInfo->amount) - $bankAmount;

                $custnewData = array(
                    'balance' => $bankastCash,
                    'updatedBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($custnewData);
                //bank pay amount calculate and customer balance update (start)
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('customers',$customerId,'',$this->session->userId,$customerMidInfo->amount,$bankAmount,$invoiceId,'');
                //insert Transaction in transaction table (end)


                //insert ledger in table ledger (start)
                $queryLed = DB()->table('transaction_log')
                    ->where('table_name', 'ledger')
                    ->where('invoice_id', $invoiceId)->get();
                $customerLedMidInfo = $queryLed->getLastRow();
                $cusOldBal = get_data_by_id('rest_balance', 'ledger', 'ledg_id', $customerLedMidInfo->id);
                $bankRestBal = ($cusOldBal + $customerLedMidInfo->amount) - $bankAmount;
                $ledgerbankData = array(
                    'amount' => $bankAmount,
                    'rest_balance' => $bankRestBal,
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->where('ledg_id', $customerLedMidInfo->id)->update($ledgerbankData);
                $this->ledger_customer_rest_balance_update($invoiceId,$bankAmount,$customerLedMidInfo->id,$customerLedMidInfo->amount);
                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('ledger',$customerLedMidInfo->id,'',$this->session->userId,$customerLedMidInfo->amount,$bankAmount,$invoiceId,'');
                //insert Transaction in transaction table (end)
            }

        }
        // bank pay amount calculate and bank balance update (end)


        // cheque pay amount calculate and insert cheque table (end)
        if ($chequeAmount > 0) {
            //cheque pay amount calculate and insert cheque tabile(start)
            $chaqueInfo = $this->transactionLog->get_table_name_by_row_invoice_id('chaque',$invoiceId);
            $chequeData = array(
                'chaque_number' => $chequeNo,
                'amount' => $chequeAmount,
            );
            if (!empty($customerId)) {
                $chequeData ['from'] = $customerId;
            } else {
                $chequeData ['from_name'] = $customerName;
            }
            $chaqueTab = DB()->table('chaque');
            $chaqueTab->where('chaque_id',$chaqueInfo->id)->update($chequeData);
        }

        DB()->transComplete();
        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Sales'));
    }

    private function ledger_discount_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_discount');
        $result = $ledgerTable->where('discount_ledg_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            $data['discount_ledg_id']  = $val->discount_ledg_id;
            $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
            array_push($arrayUpData, $data);
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_discount');
            $table->updateBatch($arrayUpData, 'discount_ledg_id');
        }
    }
    private function vat_ledger_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_vat');
        $result = $ledgerTable->where('ledg_vat_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_vat_id']  = $val->ledg_vat_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_vat_id']  = $val->ledg_vat_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_vat');
            $table->updateBatch($arrayUpData, 'ledg_vat_id');
        }
    }
    private function ledger_sale_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_sales');
        $result = $ledgerTable->where('ledgSale_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledgSale_id']  = $val->ledgSale_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledgSale_id']  = $val->ledgSale_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_sales');
            $table->updateBatch($arrayUpData, 'ledgSale_id');
        }
    }
    private function ledger_profit_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_sales');
        $result = $ledgerTable->where('ledgSale_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledgSale_id']  = $val->ledgSale_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledgSale_id']  = $val->ledgSale_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_sales');
            $table->updateBatch($arrayUpData, 'ledgSale_id');
        }
    }
    private function ledger_stock_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_stock');
        $result = $ledgerTable->where('stock_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['stock_id']  = $val->stock_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['stock_id']  = $val->stock_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_stock');
            $table->updateBatch($arrayUpData, 'stock_id');
        }
    }
    private function ledger_customer_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger');
        $result = $ledgerTable->where('ledg_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_id']  = $val->ledg_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_id']  = $val->ledg_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger');
            $table->updateBatch($arrayUpData, 'ledg_id');
        }
    }
    private function cash_ledger_rest_balance_update($invoiceId,$amount,$ledgerId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_nagodan');
        $result = $ledgerTable->where('ledg_nagodan_id >', $ledgerId)->where('invoice_id', $invoiceId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
    }






}