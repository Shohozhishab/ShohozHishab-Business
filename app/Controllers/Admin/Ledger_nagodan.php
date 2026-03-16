<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_nagodan extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_nagodan';

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

            $table = DB()->table('ledger_nagodan');
            $data['ledger_nagodan_data'] = $table->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('ledg_nagodan_id', 'DESC')->get()->getResult();

            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_nagodan/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search data
     * @return void
     */
    public function search()
    {
        $shopId = $this->session->shopId;
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $table = DB()->table('ledger_nagodan');
        $table->where('createdDtm >=', $st_date . ' 00:00:00');
        $table->where('createdDtm <=', $en_date . ' 23:59:59');
        $table->where('sch_id', $shopId);
        $table->orderBy('ledg_nagodan_id', "DESC");
        $query = $table->get();
        $data = $query->getResult();

        $view = '<div class="box-body">
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
        $restBalance = 0;
        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Payment" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            if ($data[$i]->trangaction_type == 'Dr.') {
                $restBalance = $restBalance + $data[$i]->amount;
            }else {
                $restBalance = $restBalance - $data[$i]->amount;
            }
            $view .= '<tr>
                                    <td>' . $data[$i]->ledg_nagodan_id . '</td>
                                    <td>' . $data[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $data[$i]->trans_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($restBalance) . '</td>
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
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Dr.', $st_date, $en_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total_nogodBalance('ledger_nagodan', 'amount', 'Cr.', $st_date, $en_date)) . '</th>
                                    <th>= ' . admin_cash() . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div></div>
                        </div>';

        print $view;
    }

    /**
     * @description This method search data print
     * @return void
     */
    public function searchPrint()
    {
        $shopId = $this->session->shopId;
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');


        $table = DB()->table('ledger_nagodan');
        $table->where('createdDtm >=', $st_date . ' 00:00:00');
        $table->where('createdDtm <=', $en_date . ' 23:59:59');
        $table->where('sch_id', $shopId);
        $table->orderBy('ledg_nagodan_id', "DESC");
        $query = $table->get();
        $data = $query->getResult();

        $logo = (logo_image() == NULL) ? 'no_image.jpg' : logo_image();

        $view = '<div class="box-body">
                    <div class="row"/>
                    <div class="col-xs-12" style="text-transform: capitalize;" >
                        <div class="col-xs-12" style="margin-bottom: 20px;   ">
                            <div class="col-xs-6">
                                <img src="' . base_url() . 'uploads/schools/' . $logo . '" alt="User Image" >
                            </div>
                            <div class="col-xs-6">
                                ' . address() . '
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
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
                                    <td>' . bdDateFormat($data[$i]->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $data[$i]->trans_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                        </table>
                        </div></div>
                        </div>';

        print $view;
    }


}