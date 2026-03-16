<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Acquisition_due extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Acquisition_due';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides acquisition due view
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
            $cusCash = $customersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow();
            $customerCash = 0;
            if (!empty($cusCash)) {
                $customerCash = $cusCash->balance;
            }

            $custTab = DB()->table('customers');
            $cusData = $custTab->where('sch_id', $shopId)->get()->getResult();
            //total customer balance calculet (end)


            //total Lone provider balance calculet(start)
            $loan_providerTab = DB()->table('loan_provider');
            $loanProCash = $loan_providerTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow();
            $loanCash = 0;
            if (!empty($loanProCash)) {
                $loanCash = $loanProCash->balance;
            }

            $loaTab = DB()->table('loan_provider');
            $loanPro = $loaTab->where('sch_id', $shopId)->get()->getResult();
            //total Lone provider balance calculet(end)


            //total supplier due balance calculet (start)
            $suppliersTab = DB()->table('suppliers');
            $supCash = $suppliersTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow();
            $supplierCash = 0;
            if (!empty($supCash)) {
                $supplierCash = $supCash->balance;
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Acquisition_due/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}