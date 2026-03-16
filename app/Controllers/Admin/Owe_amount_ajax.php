<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Owe_amount_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Owe_amount';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
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


            //total customer balance calculet (start)
            $customersTab = DB()->table('customers');
            $cusCash = $customersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
            $customerCash = 0;
            if ($cusCash < 0) {
                $customerCash = $cusCash;
            }

            $custTab = DB()->table('customers');
            $cusData = $custTab->where('sch_id', $shopId)->get()->getResult();
            //total customer balance calculet (end)


            //total Lone provider balance calculet(start)
            $loan_providerTab = DB()->table('loan_provider');
            $loanProCash = $loan_providerTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
            $loanCash = 0;
            if ($loanProCash < 0) {
                $loanCash = $loanProCash;
            }

            $loanTab = DB()->table('loan_provider');
            $loanPro = $loanTab->where('sch_id', $shopId)->get()->getResult();
            //total Lone provider balance calculet(end)


            //total supplier due balance calculet (start)
            $suppliersTab = DB()->table('suppliers');
            $supCash = $suppliersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
            $supplierCash = 0;
            if ($supCash < 0) {
                $supplierCash = $supCash;
            }

            $suppTab = DB()->table('suppliers');
            $suppl = $suppTab->where('sch_id', $shopId)->get()->getResult();
            //total supplier due balance calculet (end)

            $totalDue = $customerCash + $loanCash + $supplierCash;

            $data = array(
                'customer' => $customerCash,
                'customerData' => $cusData,
                'loanProvider' => $loanCash,
                'loanProData' => $loanPro,
                'supplier' => $supplierCash,
                'supplierData' => $suppl,
                'totalAcquisitionDue' => $totalDue
            );

            $data['menu'] = view('Admin/menu_report');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Owe_amount/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}