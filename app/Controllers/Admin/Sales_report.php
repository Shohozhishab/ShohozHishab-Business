<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Sales_report extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Sales_report';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides sales report view
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

            //all customer total balance show (start)
            $customersTb = DB()->table('customers');
            $customers = $customersTb->where('sch_id', $shopId)->get()->getResult();
            //all customer total balance show (end)


            //All invoice item show list (start)
            $invoice_itemTb = DB()->table('invoice_item');
            $sale = $invoice_itemTb->where('sch_id', $shopId)->orderBy('inv_item', 'DESC')->get()->getResult();
            //All invoice item show list (start)


            //All sale profite Show in invoice item table (atrat)
            $invTb = DB()->table('invoice_item');
            $saleprofit = $invTb->selectSum('profit')->where('sch_id', $shopId)->get()->getRow()->profit;
            //All sale profite Show in invoice item table (end)


            $data = array(
                'customers' => $customers,
                'sale' => $sale,
                'sale2' => $sale,
                'saleprofit' => $saleprofit,
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
                echo view('Admin/Sales_report/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search
     * @return void
     */
    public function search()
    {
        $shopId = $this->session->shopId;

        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $invoice_itemTab = DB()->table('invoice_item');
        $data['sale'] = $invoice_itemTab->where('sch_id', $shopId)->where('createdDtm >=', $st_date . ' 00:00:00')->where('createdDtm <=', $en_date . ' 23:59:59')->get()->getResult();

        $invTab = DB()->table('invoice_item');
        $data['saleprofit'] = $invTab->selectSum('profit')->where('sch_id', $shopId)->where('createdDtm >=', $st_date . ' 00:00:00')->where('createdDtm <=', $en_date . ' 23:59:59')->get()->getRow()->profit;

        $data['sale2'] = $data['sale'];

        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Sales_report/search', $data);
        echo view('Admin/footer');
    }


}