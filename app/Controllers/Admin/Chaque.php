<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Chaque extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Chaque';

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

            $chaqueTable = DB()->table('chaque');
            $data['chaque'] = $chaqueTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();


            $data['menu'] = view('Admin/menu_bank');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Chaque/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store paid
     * @param int $id
     * @return RedirectResponse
     */
    public function paid($id)
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $customerId = get_data_by_id('customer_id', 'invoice', 'chaque_id', $id);
        $loneProviderId = get_data_by_id('from_loan_provider', 'chaque', 'chaque_id', $id);

        //shops cash calculet(start)
        $chaqueBalance = get_data_by_id('amount', 'chaque', 'chaque_id', $id);
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $totalBalance = $shopBalance + $chaqueBalance;
        //shops cash calculet(end)


        DB()->transStart();

        if ($customerId > 0) {
            //chaque amount calculet and update customer Balance (start)
            $customerBalance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $customerBalanceUpdate = $customerBalance - $chaqueBalance;
            $cusUpData = array(
                'balance' => $customerBalanceUpdate,
                'updatedBy' => $userId,
            );
            $customersTab = DB()->table('customers');
            $customersTab->where('customer_id', $customerId)->update($cusUpData);
            //chaque amount calculet and update customer Balance (end)


            //chaque amount insert in ledger table (start)
            $lgCusData = array(
                'sch_id' => $shopId,
                'chaque_id' => $id,
                'customer_id' => $customerId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Chaque Cash Approved',
                'amount' => $chaqueBalance,
                'rest_balance' => $customerBalanceUpdate,
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->insert($lgCusData);
            //chaque amount insert in ledger table (end)

        }

        if ($loneProviderId > 0) {

            //update loneprovider balance in loan_provider table(start)
            $loneProviderBalance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loneProviderId);
            $loneProviderBalanceUpdate = $loneProviderBalance - $chaqueBalance;

            $loneProUpData = array(
                'balance' => $loneProviderBalanceUpdate,
                'updatedBy' => $userId,
            );
            $loan_providerTab = DB()->table('loan_provider');
            $loan_providerTab->where('loan_pro_id', $loneProviderId)->update($loneProUpData);
            //update loneprovider balance in loan_provider table(end)


            //chaque amount insert in ledger_loan table (start)
            $lgloneProData = array(
                'sch_id' => $shopId,
                'chaque_id' => $id,
                'loan_pro_id' => $loneProviderId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Chaque Cash Approved',
                'amount' => $chaqueBalance,
                'rest_balance' => $loneProviderBalanceUpdate,
            );
            $ledger_loanTab = DB()->table('ledger_loan');
            $ledger_loanTab->insert($lgloneProData);
            //chaque amount insert in ledger_loan table (end)
        }


        //chaque amount calculet and shops balance update (start)
        $shopBalUpData = array(
            'cash' => $totalBalance,
            'updatedBy' => $userId,
        );
        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($shopBalUpData);
        //chaque amount calculet and shops balance update (end)


        //chaque amount insert in ledger_nagodan table (start)
        $lgNagData = array(
            'sch_id' => $shopId,
            'chaque_id' => $id,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Chaque Cash Approved',
            'amount' => $chaqueBalance,
            'rest_balance' => $totalBalance,
        );
        $ledger_nagodanTab = DB()->table('ledger_nagodan');
        $ledger_nagodanTab->insert($lgNagData);
        //chaque amount insert in ledger_nagodan table (end)


        //update chaque status in chaque table (start)
        $chaqueUpdata = array(
            'status' => 'Approved',
            'updatedBy' => $userId,
        );
        $chaqueTab = DB()->table('chaque');
        $chaqueTab->where('chaque_id', $id)->update($chaqueUpdata);
        //update chaque status in chaque table (end)

        DB()->transComplete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Chaque'));
    }


}