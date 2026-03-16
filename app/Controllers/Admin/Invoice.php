<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Invoice extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Invoice';

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
            $invoiceTable = DB()->table('invoice');
            $data['invoice_data'] = $invoiceTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $data['menu'] = view('Admin/menu_report', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Invoice/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides invoice view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function view($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);
            $data['invoiceId'] = $id;

            $invoice_itemTable = DB()->table('invoice_item');
            $data['invoiceItame'] = $invoice_itemTable->where('invoice_id', $id)->get()->getResult();


            //Due calculation (start)
            $invoiceTable = DB()->table('invoice');
            $invoiceDueData = $invoiceTable->where('invoice_id', $id)->get()->getRow();
            $invoiceDue = !empty($invoiceDueData)?$invoiceDueData->due:0;

            $ledgerTable = DB()->table('ledger');
            $rest_balance_count = $ledgerTable->where('invoice_id', $id)->where('trangaction_type', 'Cr.')->countAllResults();

            if (!empty($rest_balance_count)) {
                $ledgerTab = DB()->table('ledger');
                $rest_balance_query = $ledgerTab->where('invoice_id', $id)->where('trangaction_type', 'Cr.')->get();
                $rest_balance = $rest_balance_query->getRow()->rest_balance;
            } else {
                $rest_balance = 0;
            }

            $ledgerT = DB()->table('ledger');
            $amount_query = $ledgerT->where('invoice_id', $id)->where('trangaction_type', 'Cr.')->countAllResults();
            if (!empty($amount_query)) {
                $ledgerTC = DB()->table('ledger');
                $amount_queryT = $ledgerTC->where('invoice_id', $id)->where('trangaction_type', 'Cr.')->get();
                $amount = $amount_queryT->getRow()->amount;
            } else {
                $amount = 0;
            }

            $data['oldDue'] = $rest_balance - $amount;
            $data['totalDue'] = $data['oldDue'] + $invoiceDue;
            //Due calculation (end)

            $data['menu'] = view('Admin/menu_report', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Invoice/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}