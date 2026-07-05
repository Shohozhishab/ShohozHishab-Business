<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Sales_ajax extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Sales';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
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
            $customer_id = $this->request->getGet('customer');

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('sales');
            $table->where('sales.sch_id', $shopId);

            if (!empty($customer_id)) {
                $table->join('invoice', 'invoice.invoice_id = sales.invoice_id');
                $table->where('invoice.customer_id', $customer_id);
            }
            // Apply date filters only if they are present in the request
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('sales.date >=', $st_date . ' 00:00:00');
                $table->where('sales.date <=', $en_date . ' 23:59:59');
            }
            $data['sales'] = $table->get()->getResult();

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['customer_id'] = isset($customer_id)?$customer_id:'';

            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Sales/list', $data);
            } else {
                echo view('no_permission');
            }
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
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Sales/create', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    public function transaction_flow($sales_id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['flow'] = DB()->table('transaction_entries')
                ->where('sales_id',$sales_id)
                ->get()
                ->getResult();

            $data['saleData'] = DB()->table('sales')
                ->join('invoice', 'invoice.invoice_id = sales.invoice_id', 'left')
                ->join('customers', 'customers.customer_id = invoice.customer_id', 'left')
                ->where('sales.sales_id', $sales_id)
                ->get()
                ->getRow();


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Sales/transaction_flow', $data);
            } else {
                echo view('no_permission');
            }
        }
    }
}