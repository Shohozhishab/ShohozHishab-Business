<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Libraries\TransactionLog;
use CodeIgniter\HTTP\RedirectResponse;


class Bank_withdraw extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $transactionLog;
    private $module_name = 'Bank_withdraw';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->transactionLog = new TransactionLog();
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
            $bank_withdrawTable = DB()->table('bank_withdraw');
            $data['bank_withdraw'] = $bank_withdrawTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $bank = DB()->table('bank');
            $data['bank'] = $bank->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();
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
                echo view('Admin/Bank_withdraw/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Bank_withdraw/create_action');

            $data['menu'] = view('Admin/menu_bank');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Bank_withdraw/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method create bank withdraw
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['bank_id'] = $this->request->getPost('bank_id');
        $data['amount'] = $this->request->getPost('amount');
        $data['commont'] = $this->request->getPost('commont');

        $this->validation->setRules([
            'bank_id' => ['label' => 'bank', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required|is_natural_no_zero|max_length[32]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $shopCheck = check_shop('bank', 'bank_id', $data['bank_id']);
            $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $data['bank_id']);

            if ($shopCheck == 1) {
                if ($bankBalance >= $data['amount']) {

                    DB()->transStart();

                    if ($data['amount'] > 0) {
                        //insert data
                        $data = array(
                            'bank_id' => $data['bank_id'],
                            'sch_id' => $shopId,
                            'amount' => $data['amount'],
                            'commont' => $data['commont'],
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $bank_withdrawTable = DB()->table('bank_withdraw');
                        $bank_withdrawTable->insert($data);
                        $wthd_id = DB()->insertID();

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank_withdraw',$wthd_id,null,$data['amount'],null,$wthd_id);
                        //insert log (end)

                        //bank deduct balance
                        $bankUpBalance = $bankBalance - $data['amount'];
                        $bankdata = array(
                            'balance' => $bankUpBalance,
                        );
                        $bankTable = DB()->table('bank');
                        $bankTable->where('bank_id', $data['bank_id'])->update($bankdata);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank',$data['bank_id'],null,$data['amount'],null,$wthd_id);
                        //insert log (end)

                        //insert ledger_bank
                        $ledBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $data['bank_id'],
                            'amount' => $data['amount'],
                            'particulars' => 'Bank Cash Withdraw',
                            'trangaction_type' => 'Cr.',
                            'rest_balance' => $bankUpBalance,
                            'createdDtm' => date('Y-m-d h:i:s'),
                        );
                        $ledger_bankTable = DB()->table('ledger_bank');
                        $ledger_bankTable->insert($ledBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,null,$data['amount'],null,$wthd_id);
                        //insert log (end)

                        //shops in balance
                        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                        $shopUpBalance = $shopBalance + $data['amount'];
                        $shopData = array(
                            'cash' => $shopUpBalance,
                        );
                        $shopsTable = DB()->table('shops');
                        $shopsTable->where('sch_id', $shopId)->update($shopData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops',$shopId,null,$data['amount'],null,$wthd_id);
                        //insert log (end)


                        //insert ledger_nagodan
                        $lgNagData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $data['bank_id'],
                            'trangaction_type' => 'Dr.',
                            'particulars' => 'Bank Cash Withdraw',
                            'amount' => $data['amount'],
                            'rest_balance' => $shopUpBalance,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTable = DB()->table('ledger_nagodan');
                        $ledger_nagodanTable->insert($lgNagData);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,null,$data['amount'],null,$wthd_id);
                        //insert log (end)

                        print '<div class="alert alert-success alert-dismissible" role="alert"> Withdraw data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    } else {
                        print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid Amount  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    }

                    DB()->transComplete();
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Bank balance is too low for this Withdraw  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Please input valid bank  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }


        }
    }

    public function withdrawDataEdit(){
        $wthd_id = $this->request->getPost('id');
        $table = DB()->table('bank_withdraw');
        $result = $table->where('wthd_id',$wthd_id)->get()->getRow();
        $formUrl = base_url('Admin/Bank_withdraw/withdrawEditAction');
        $name = get_data_by_id('name','bank','bank_id',$result->bank_id);

        $view = '';
        $view .= '<form id="withdrawUpdateform" action="'. $formUrl .'" method="post">
                        <div class="form-group">
                            <label for="int">Bank </label>
                            <select class="form-control "  aria-hidden="true" name="vat_id" required>
                                <option selected="selected" value="'.$result->bank_id.'">'.$name.' </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="int">Amount</label>
                            <input type="number" class="form-control" name="amount" min="1" id="amount" value="'.$result->amount.'" placeholder="Amount" required >
                            <input type="hidden" name="wthd_id" value="'.$result->wthd_id.'" required >
                            <div class="error"></div>
                        </div>
                        <div class="form-group">
                            <label for="int">Comment</label>
                            <input type="text" class="form-control" name="commont" id="commont" placeholder="Comment" value="'.$result->commont.'" >
                            <div class="error"></div>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .='</form>';

        print $view;
    }

    public function withdrawEditAction(){
        $wthd_id = $this->request->getPost('wthd_id');
        $amount = $this->request->getPost('amount');
        $commont = $this->request->getPost('commont');

        $table = DB()->table('bank_withdraw');
        $result = $table->where('wthd_id',$wthd_id)->get()->getRow();

        $shopId = $this->session->shopId;
        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $result->bank_id);

        if ($bankBalance >= $amount) {

            DB()->transStart();

            if ($amount > 0) {
                //insert data
                $data = array(
                    'amount' => $amount,
                    'commont' => $commont,
                );
                $bank_withdrawTable = DB()->table('bank_withdraw');
                $bank_withdrawTable->where('wthd_id',$wthd_id)->update($data);

                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('bank_withdraw',$wthd_id,'',$this->session->userId,$result->amount,$amount);
                //insert Transaction in transaction table (end)

                //bank deduct balance
                $bankInfo = $this->transactionLog->get_table_name_or_wthd_id_by_row('bank',$wthd_id);
                $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $result->bank_id);
                $bankUpBalance = $bankBalance + $bankInfo->amount - $amount;
                $bankdata = array(
                    'balance' => $bankUpBalance,
                );
                $bankTable = DB()->table('bank');
                $bankTable->where('bank_id',$result->bank_id)->update($bankdata);

                //insert ledger_bank
                $ledgerBankInfo = $this->transactionLog->get_table_name_or_wthd_id_by_row('ledger_bank',$wthd_id);
                $ledgBal = get_data_by_id('rest_balance','ledger_bank','ledgBank_id',$ledgerBankInfo->id);
                $restbalLedger = $ledgBal + $ledgerBankInfo->amount - $amount;
                $ledBankData = array(
                    'amount' => $amount,
                    'trangaction_type' => 'Cr.',
                    'rest_balance' => $restbalLedger,
                );
                $ledger_bankTable = DB()->table('ledger_bank');
                $ledger_bankTable->where('ledgBank_id',$ledgerBankInfo->id)->update($ledBankData);

                //bank all ledger rest balance update
                $this->bank_ledger_rest_balance_update($wthd_id,$result->bank_id,$amount,'Dr.');
                //bank all ledger rest balance update

                //shops in balance
                $shopsInfo = $this->transactionLog->get_table_name_or_wthd_id_by_row('shops',$wthd_id);
                $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                $shopUpBalance = ($shopBalance - $shopsInfo->amount) + $amount;
                $shopData = array(
                    'cash' => $shopUpBalance,
                );
                $shopsTable = DB()->table('shops');
                $shopsTable->where('sch_id', $shopId)->update($shopData);

                //insert ledger_nagodan
                $shopLedInfo = $this->transactionLog->get_table_name_or_wthd_id_by_row('ledger_nagodan',$wthd_id);
                $shopLedBal = get_data_by_id('rest_balance','ledger_nagodan','ledg_nagodan_id',$shopLedInfo->id);
                $restShopLedgBal = ($shopLedBal - $shopLedInfo->amount) + $amount;
                $lgNagData = array(
                    'amount' => $amount,
                    'rest_balance' => $restShopLedgBal,
                );
                $ledger_nagodanTable = DB()->table('ledger_nagodan');
                $ledger_nagodanTable->where('ledg_nagodan_id',$shopLedInfo->id)->update($lgNagData);

                //shop all ledger rest balance update
                $this->shop_ledger_rest_balance_update($wthd_id,$amount,'Dr.' );
                //shop all ledger rest balance update

                //update new balance transaction log
                $this->transactionLog->transaction_withdraw_log_update($wthd_id,$amount);
                //update new balance transaction log


                print '<div class="alert alert-success alert-dismissible" role="alert"> Withdraw data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid Amount  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

            DB()->transComplete();
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert"> Bank balance is too low for this Withdraw  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    private function shop_ledger_rest_balance_update($wthd_id,$amount,$type){
        // Get information about a specific transaction row from the 'ledger_nagodan' table.
        $shopInfo = $this->transactionLog->get_table_name_or_wthd_id_by_row('ledger_nagodan', $wthd_id);

        // Prepare a query builder for the 'ledger_nagodan' table.
        $tableShop = DB()->table('ledger_nagodan');

        // Get all transactions where the ID is greater than the current transaction's ID  and they belong to the same shop (session shop ID).
        $arrayLedData = $tableShop->where('ledg_nagodan_id >', $shopInfo->id)->where('sch_id', $this->session->shopId)->get()->getResult();

        $arrayUpData = [];

        // Loop through the retrieved ledger data to update their rest_balance
        foreach ($arrayLedData as $val) {
            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance - $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance - $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
//        return $arrayUpData;
    }

    private function bank_ledger_rest_balance_update($wthd_id,$bank_id,$amount,$type)
    {

        $ledLog = $this->transactionLog->get_table_name_or_wthd_id_by_row('ledger_bank', $wthd_id);


        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledLog->id)->where('bank_id', $bank_id)->get()->getResult();

        $arrayUpData = [];

        foreach ($result as $val) {
            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount-
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount;
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
//        return $arrayUpData;
    }


}