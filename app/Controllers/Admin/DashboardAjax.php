<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\LicenseModel;
use App\Models\ShopsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardAjax extends BaseController
{
    protected $usersModel;
    protected $licenseModel;
    protected $shopsModel;
    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Dashboard';

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->licenseModel = new LicenseModel();
        $this->shopsModel = new ShopsModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
    }

    /**
     * @description This method provides view
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


            // Total Product count (start)
            $proTable = DB()->table('products');
            $totalProduct = $proTable->where('sch_id', $shopId)->countAllResults();
            // Total Product count (end)

            // Total Customer count (start)
            $cousTable = DB()->table('customers');
            $totalCustomer = $cousTable->where('sch_id', $shopId)->countAllResults();
            // Total Customer count (end)

            // Lone Due balance count (start)
            $totalLoneDue = 0;
            $lonProTable = DB()->table('loan_provider');
            $query = $lonProTable->where('sch_id', $shopId)->get()->getResult();
            foreach ($query as $result) {
                if ($result->balance < 0) {
                    $totalLoneDue += $result->balance;
                }
            }
            // Lone Due balance count (end)


            //Supplier Balance  total due count (start)
            $totalsuppDue = 0;
            $supTable = DB()->table('suppliers');
            $querySupp = $supTable->where('sch_id', $shopId)->get()->getResult();
            foreach ($querySupp as $result) {
                if ($result->balance < 0) {
                    $totalsuppDue += $result->balance;
                }
            }
            //Supplier Balance  total due count (end)

            //Total All Due calculet(start)
            $totalDue = $totalLoneDue + $totalsuppDue;
            //Total All Due calculet(end)

            //Lone provider total lone due amount calculet (start)
            $totalLoneGet = 0;
            $lonProDuTable = DB()->table('loan_provider');
            $queryLoneGet = $lonProDuTable->where('sch_id', $shopId)->get()->getResult();
            foreach ($queryLoneGet as $result) {
                if ($result->balance > 0) {
                    $totalLoneGet += $result->balance;
                }
            }
            //Lone provider total lone due amount calculet (end)

            //all Customers due amount calculet (start)
            $totalCusGet = 0;
            $cusDuTable = DB()->table('customers');
            $queryCusGet = $cusDuTable->where('sch_id', $shopId)->get()->getResult();
            foreach ($queryCusGet as $result) {
                if ($result->balance > 0) {
                    $totalCusGet += $result->balance;
                }
            }
            //all Customers due amount calculet (end)

            //Total due amount calculet (start)
            $totalGet = $totalLoneGet + $totalCusGet;
            //Total due amount calculet (end)

            //total Bank Balance calculet (start)
            $bankTable = DB()->table('bank');
            $bank = $bankTable->selectSum('balance')->where('sch_id', $shopId)->get()->getRow();
            $totalBankBal = $bank->balance;
            //total Bank Balance calculet (end)


            //purchase table null value delete (start)
            $purchTable = DB()->table('purchase');
            $purchId = $purchTable->where('sch_id', $shopId)->where('due', NULL)->get()->getResult();
            foreach ($purchId as $value) {
                // purchasa itame fiend count (start)
                $pruchItemTable = DB()->table('purchase_item');
                $purItem = $pruchItemTable->where('purchase_id', $value->purchase_id)->countAllResults();
//                $purItem = $this->db->select('purchase_item_id')->get_where('purchase_item',array('purchase_id' => $value->purchase_id,))->num_rows();
                // purchasa itame fiend count (end)

                //deleted Nul value in purchase (start)
                if ($purItem < 1) {
                    $purchTable2 = DB()->table('purchase');
                    $purchTable2->where('purchase_id', $value->purchase_id)->delete();
                }
                //deleted Nul value in purchase (end)
            }
            //purchase table null value delete (end)


            $data = array(
                'totalProduct' => $totalProduct,
                'totalCustomer' => $totalCustomer,
                'totalDue' => $totalDue,
                'totalGet' => $totalGet,
                'totalBankBal' => $totalBankBal,
            );


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Dashboard/dashboard', $data);
            } else {
                echo view('no_permission');
            }

        }
    }
}