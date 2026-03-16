<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Daily_book extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Daily_book';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides  view
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

            //Show today all cash transaction list in ledger_nagodan table (start)
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $data['cashLedger'] = $ledger_nagodanTab->where('sch_id', $shopId)->like('createdDtm', date('Y-m-d'))->orderBy("createdDtm", "DESC")->get()->getResult();

            $nagodTab = DB()->table('ledger_nagodan');
            $rest = $nagodTab->where('sch_id', $shopId)->like('createdDtm', date('Y-m-d'))->orderBy("createdDtm", "DESC")->limit(1)->get()->getRow();
            $data['cashrest_balance'] = empty($rest) ? 0 : $rest->rest_balance;

            //Show today all cash transaction list in ledger_nagodan table (end)

            $prevTab = DB()->table('ledger_nagodan');
            $prevbalance = $prevTab->where("sch_id", $shopId)->where('createdDtm <', date('Y-m-d'))->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['prev_balance'] = empty($prevbalance) ? 0 : $prevbalance->rest_balance;


            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Daily_book/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
    /**
     * @description This method bank ledger search
     * @return void
     */
    public function search_bankLedg()
    {

        $bankId = $this->request->getPost('id');
        $date = $this->request->getPost('date');
        $searchDate = (empty($date)) ? date('Y-m-d') : $date;

        $ledger_bankTab = DB()->table('ledger_bank');
        $balance = $ledger_bankTab->where("bank_id", $bankId)->like('createdDtm', $searchDate)->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
        $restBalance = empty($balance) ? 0 : $balance->rest_balance;


        $ledgerTab = DB()->table('ledger_bank');
        $data = $ledgerTab->where("bank_id", $bankId)->like('createdDtm', $searchDate)->limit(30)->orderBy("createdDtm", "DESC")->get()->getResult();


        $view = '<span class="pull-right">Last Balance ' . showWithCurrencySymbol($restBalance) . '</span>';

        $view .= '<table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <th>Particulars</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                        <td>' . $row->createdDtm . '</td>
                                        <td>' . get_data_by_id('name', 'bank', 'bank_id', $row->bank_id) . '</td>
                                        <td>' . $particulars . '</td>
                                        <td>' . $amountDr . '</td>
                                        <td>' . $amountCr . '</td>
                                        <td>' . showWithCurrencySymbol($row->rest_balance) . '</td>
                                    </tr>';
        }

        $view .= '</tbody>
                                <tfoot>
                                    <tr>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <th>Particulars</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                </tfoot>
                            </table>';

        print $view;


    }

    /**
     * @description This method search ledger
     * @return RedirectResponse|void
     */
    public function search()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $date = $this->request->getPost('date');
            $data['date'] = $date;


            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $prevbalance = $ledger_nagodanTab->where('createdDtm <', $date)->where("sch_id", $shopId)->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['prev_balance'] = empty($prevbalance) ? 0 : $prevbalance->rest_balance;

            $ledgerTab = DB()->table('ledger_nagodan');
            $data['cashledger'] = $ledgerTab->where("sch_id", $shopId)->like('createdDtm', $date)->orderBy("createdDtm", "DESC")->get()->getResult();

            $ledger_nagTab = DB()->table('ledger_nagodan');
            $balance = $ledger_nagTab->where("sch_id", $shopId)->like('createdDtm', $date)->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['cashrest_balance'] = empty($balance) ? 0 : $balance->rest_balance;


            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Daily_book/search', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method print preview
     * @return RedirectResponse|void
     */
    public function print_preview()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $ledgerTab = DB()->table('ledger_nagodan');
            $data['cashLedger'] = $ledgerTab->where("sch_id", $shopId)->like('createdDtm', date('Y-m-d'))->orderBy("createdDtm", "DESC")->get()->getResult();


            $ledger_nagTab = DB()->table('ledger_nagodan');
            $balance = $ledger_nagTab->where("sch_id", $shopId)->like('createdDtm', date('Y-m-d'))->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['cashrest_balance'] = empty($balance) ? 0 : $balance->rest_balance;


            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $prevbalance = $ledger_nagodanTab->where('createdDtm <', date('Y-m-d'))->where("sch_id", $shopId)->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['prevAll_balance'] = empty($prevbalance) ? 0 : $prevbalance->rest_balance;


            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Daily_book/print', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}