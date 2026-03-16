<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\ShopsModel;
use CodeIgniter\HTTP\RedirectResponse;


class Capital extends BaseController
{


    protected $shopsModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Capital';

    public function __construct()
    {
        $this->shopsModel = new ShopsModel();
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
            $data['shopId'] = $shopId;

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Capital/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store Capital
     * @return void
     */
    public function deposit()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['particulars'] = $this->request->getPost('particulars');
        $data['amount'] = $this->request->getPost('amount');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'particulars' => ['label' => 'Particulars', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            DB()->transStart();

            $particulars = $this->request->getPost('particulars');
            $amount = $this->request->getPost('amount');

            $capital = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
            $cash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);


            // capital update and insert ledger
            $upCapital = $capital - $amount;
            $dataCapital = array(
                'capital' => $upCapital,
                'updatedBy' => $userId,
                'updatedDtm' => date('Y-m-d h:i:s')
            );
            $shopsTable = DB()->table('shops');
            $shopsTable->where('sch_id', $shopId)->update($dataCapital);

            $cpitalLedData = array(
                'sch_id' => $shopId,
                'particulars' => $particulars,
                'trangaction_type' => 'Cr.',
                'amount' => $amount,
                'rest_balance' => $upCapital,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_capitalTable = DB()->table('ledger_capital');
            $ledger_capitalTable->insert($cpitalLedData);
            // capital update and insert ledger


            // cash update and insert ledger
            $upcash = $cash + $amount;
            $datacash = array(
                'cash' => $upcash,
                'updatedBy' => $userId,
                'updatedDtm' => date('Y-m-d h:i:s')
            );
            $shops2Table = DB()->table('shops');
            $shops2Table->where('sch_id', $shopId)->update($datacash);

            $shopeLedData = array(
                'sch_id' => $shopId,
                'particulars' => $particulars,
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'rest_balance' => $upcash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_nagodanTable = DB()->table('ledger_nagodan');
            $ledger_nagodanTable->insert($shopeLedData);
            // cash update and insert ledger

            print '<div class="alert alert-success alert-dismissible" role="alert"> Deposit successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            DB()->transComplete();
        }
    }


}