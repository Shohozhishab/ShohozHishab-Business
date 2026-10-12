<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Bank extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Bank';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides bank view
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
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('name', 'ASC')->get()->getResult();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Bank/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides bank create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Bank/create_action');
            $data['action2'] = base_url('Admin/Bank/existing_create_action');


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Bank/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store bank
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['account_name'] = $this->request->getPost('account_name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_name' => ['label' => 'Account Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[20]|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $accCheck = is_unique_bank('bank', 'account_no', $data['account_no'], 'name', $data['name']);
            if ($accCheck == true) {
                $bankTable = DB()->table('bank');
                if ($bankTable->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }


        }
    }

    /**
     * @description This method provides bank update view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function update($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Bank/update_action');
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('bank_id', $id)->get()->getRow();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Bank/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update bank
     * @return void
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $bank_id = $this->request->getPost('bank_id');
        $data['name'] = $this->request->getPost('name');
        $data['account_name'] = $this->request->getPost('account_name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_name' => ['label' => 'Account Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[20]|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $accCheck = is_unique_update('bank', 'account_no', $data['account_no'], 'bank_id', $bank_id);
            if ($accCheck == true) {
                $bankTable = DB()->table('bank');
                if ($bankTable->where('bank_id', $bank_id)->update($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }


        }
    }

    /**
     * @description This method store existing bank
     * @return void
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = $this->request->getPost('amount');
        $name = $this->request->getPost('name');

        $data['name'] = $this->request->getPost('name');
        $data['account_name'] = $this->request->getPost('account_name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['amount'] = $this->request->getPost('amount');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_name' => ['label' => 'Account Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[32]|alpha_numeric_space'],
            'amount' => ['label' => 'amount', 'rules' => 'required|max_length[32]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($amount !== '0') {
                $accCheck = is_unique_bank('bank', 'account_no', $data['account_no'], 'name', $data['name']);
                if ($accCheck == true) {
                    DB()->transStart();

                    $dataBank = array(
                        'sch_id' => $shopId,
                        'name' => $data['name'],
                        'account_name' => $data['account_name'],
                        'account_no' => $data['account_no'],
                        'balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->insert($dataBank);
                    $bankId = DB()->insertID();

                    // Existing transaction data insert
                    DB()->table('existing_transaction')->insert([
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    ]);
                    $existing_trans_id = DB()->insertID();


                    //insert Bank ledger table (start)
                    $bankLedgdata = array(
                        'sch_id' => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'bank_id' => $bankId,
                        'particulars' => 'Bank last balance ',
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTable = DB()->table('ledger_bank');
                    $ledger_bankTable->insert($bankLedgdata);
                    //insert Bank ledger table (end)


                    //update capital (start)
                    $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                    $newcap = $oldCap - $amount;

                    $capData = array(
                        'capital' => $newcap
                    );
                    $shopsTable = DB()->table('shops');
                    $shopsTable->where('sch_id', $shopId)->update($capData);

                    $capLedgdata = array(
                        'sch_id' => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars' => 'Existing Bank (' . $name . ') is added with existing balance',
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => $newcap,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_capitalTable = DB()->table('ledger_capital');
                    $ledger_capitalTable->insert($capLedgdata);
                    //update capital (end)

                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    DB()->transComplete();

                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }else{
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }
    }
    /**
     * @param int $bank_id
     * @return void
     */
    public function delete($bank_id){
        $table = DB()->table('bank');
        $table->where('bank_id',$bank_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Bank'));
    }

    public function bankDataEdit()
    {
        $bankId = $this->request->getPost('bank_id');

        $query = DB()->table('existing_transaction')
            ->where('bank_id', $bankId)
            ->get()
            ->getRow();

        if (!$query) {
            print '<div class="alert alert-danger">No existing transaction found for this Bank.</div>';
            return;
        }

        $name   = get_data_by_id('name', 'bank', 'bank_id', $bankId);
        $action = base_url('Admin/Bank/bankEditAction'); // adjust path if needed

        $view  = '<form action="'.$action.'" method="post">';
        $view .= '<div class="form-group">
                <label for="bank_name_ex">Bank Name</label>
                <input type="text" class="form-control" name="name" id="bank_name_ex" 
                       value="' . htmlspecialchars($name) . '" readonly />
                <input type="hidden" name="bank_id" value="'.$bankId.'">
                <div class="error"></div>
              </div>';

        $view .= '<div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control input" name="amount" id="amount" 
                       placeholder="Amount" oninput="minusValueCheck(this.value,this)" 
                       value="' . htmlspecialchars($query->amount) . '" required />
                <div class="error"></div>
              </div>';

        $view .= '<div class="form-group">
                <button type="submit" class="btn btn-success">Submit</button>
              </div>';

        $view .= '</form>';

        print $view;
    }

    public function bankEditAction()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $bank_id = $this->request->getPost('bank_id');
        $amount  = (float) $this->request->getPost('amount');

        $data = [
            'amount' => $amount,
        ];

        $this->validation->setRules([
            'amount' => ['label' => 'Amount', 'rules' => 'required|greater_than[0]'],
        ]);

        if ($this->validation->run($data) === false) {
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger alert-dismissible" role="alert">' .
                $this->validation->listErrors() .
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            );
            return redirect()->to(site_url('Admin/Bank'));
        }

        // Get old transaction
        $transaction = DB()->table('existing_transaction')
            ->where('sch_id', $shopId)
            ->where('bank_id', $bank_id)
            ->get()
            ->getRow();

        if (!$transaction) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Existing transaction not found.</div>');
            return redirect()->to(site_url('Admin/Bank'));
        }

        $oldAmount = (float) $transaction->amount;
        // Always Dr. for bank opening balance
        $newType   = 'Dr.';

        // ========== Start DB transaction ==========
        DB()->transStart();

        // ---------- 1. Bank balance ----------
        // Reverse old effect + apply new (both are Dr. so simply replace)
        $newBalance = $amount;

        DB()->table('bank')
            ->where('bank_id', $bank_id)
            ->update(['balance' => $newBalance]);

        // ---------- 2. Bank ledger ----------
        $ledger = DB()->table('ledger_bank')
            ->where('bank_id', $bank_id)
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledger) {
            $newRestBal = ($ledger->rest_balance - $oldAmount) + $amount;
            DB()->table('ledger_bank')
                ->where('ledgBank_id', $ledger->ledgBank_id)
                ->update([
                    'trangaction_type' => $newType,
                    'amount'           => $amount,
                    'rest_balance'     => $newRestBal,
                    'particulars'      => 'Bank last balance',
                ]);
        }

        // ---------- 3. Capital ----------
        $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);

        // Reverse old Dr. effect (add back) then apply new Dr. effect (subtract)
        $newCap = ($oldCap + $oldAmount) - $amount;

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['capital' => $newCap]);

        // ---------- 4. Capital ledger (UPDATE) ----------
        $ledgerCapital = DB()->table('ledger_capital')
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledgerCapital) {
            DB()->table('ledger_capital')
                ->where('capital_id', $ledgerCapital->capital_id) // change if your PK name is different
                ->update([
                    'trangaction_type' => 'Cr.',   // opposite of bank Dr.
                    'amount'           => $amount,
                    'rest_balance'     => $newCap,
                    'particulars'      => 'Existing Bank (' . get_data_by_id('name', 'bank', 'bank_id', $bank_id) . ') is added with existing balance',
                ]);
        }

        // ---------- 5. Update existing_transaction ----------
        DB()->table('existing_transaction')
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->update([
                'amount'           => $amount,
                'trangaction_type' => $newType,
            ]);

        DB()->transComplete();

        if (DB()->transStatus() === false) {
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger">Something went wrong. Please try again.</div>'
            );
        } else {
            $this->session->setFlashdata('message',
                '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            );
        }

        return redirect()->to(site_url('Admin/Bank'));
    }

    public function csv_action()
    {
        $redirectUrl = site_url('Admin/Bank/create');
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger alert-dismissible" role="alert">' .
                $this->validator->listErrors() .
                '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
            );
            return redirect()->to($redirectUrl);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Invalid file upload.</div>');
            return redirect()->to($redirectUrl);
        }

        $handle = fopen($file->getTempName(), 'r');
        if ($handle === false) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Unable to open CSV file.</div>');
            return redirect()->to($redirectUrl);
        }

        $db = DB();
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;
        $now = date('Y-m-d H:i:s');
        $header = null;
        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $required = ['name', 'account_name', 'account_no'];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count(array_filter($row, static fn($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(static fn($value) => strtolower(trim((string) $value)), $row);
                if (isset($header[0])) {
                    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
                }
                foreach ($required as $column) {
                    if (!in_array($column, $header, true)) {
                        fclose($handle);
                        $this->session->setFlashdata('message',
                            '<div class="alert alert-danger">CSV must contain columns: ' . implode(', ', $required) . '</div>'
                        );
                        return redirect()->to($redirectUrl);
                    }
                }
                continue;
            }

            if (count($row) !== count($header)) {
                $skipped++;
                $errors[] = 'A row has a different number of columns than the header.';
                continue;
            }

            $data = array_combine($header, $row);
            $name = trim($data['name'] ?? '');
            $accountName = trim($data['account_name'] ?? '');
            $accountNo = trim($data['account_no'] ?? '');

            if ($name === '' || $accountNo === '') {
                $errors[] = 'Name and Account No are required.';
                $skipped++;
                continue;
            }

            $existing = $db->table('bank')
                ->where('account_no', $accountNo)
                ->where('sch_id', $shopId)
                ->get()
                ->getRow();
            if ($existing) {
                $errors[] = 'Account No already exists: ' . esc($accountNo);
                $skipped++;
                continue;
            }

            $saved = $db->table('bank')->insert([
                'sch_id' => $shopId,
                'name' => $name,
                'account_name' => $accountName !== '' ? $accountName : null,
                'account_no' => $accountNo,
                'createdBy' => $userId,
                'createdDtm' => $now,
            ]);

            if ($saved) {
                $inserted++;
            } else {
                $errors[] = 'Could not save bank account: ' . esc($accountNo);
                $skipped++;
            }
        }

        fclose($handle);
        $message = 'CSV processed. Inserted: ' . $inserted . ', Skipped: ' . $skipped;
        $alert = $errors ? 'alert-warning' : 'alert-success';
        if ($errors) {
            $message .= '<br><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
        }

        $this->session->setFlashdata('message',
            '<div class="alert ' . $alert . ' alert-dismissible">' . $message .
            '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
        );
        return redirect()->to($redirectUrl);
    }

    public function csv_existing_action()
    {
        $redirectUrl = site_url('Admin/Bank/create');
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger alert-dismissible" role="alert">' .
                $this->validator->listErrors() .
                '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
            );
            return redirect()->to($redirectUrl);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Invalid file upload.</div>');
            return redirect()->to($redirectUrl);
        }

        $handle = fopen($file->getTempName(), 'r');
        if ($handle === false) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Unable to open CSV file.</div>');
            return redirect()->to($redirectUrl);
        }

        $db = DB();
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;
        $now = date('Y-m-d H:i:s');
        $header = null;
        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $required = ['name', 'account_name', 'account_no', 'amount'];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count(array_filter($row, static fn($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(static fn($value) => strtolower(trim((string) $value)), $row);
                if (isset($header[0])) {
                    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
                }
                foreach ($required as $column) {
                    if (!in_array($column, $header, true)) {
                        fclose($handle);
                        $this->session->setFlashdata('message',
                            '<div class="alert alert-danger">CSV must contain columns: ' . implode(', ', $required) . '</div>'
                        );
                        return redirect()->to($redirectUrl);
                    }
                }
                continue;
            }

            if (count($row) !== count($header)) {
                $skipped++;
                $errors[] = 'A row has a different number of columns than the header.';
                continue;
            }

            $data = array_combine($header, $row);
            $name = trim($data['name'] ?? '');
            $accountName = trim($data['account_name'] ?? '');
            $accountNo = trim($data['account_no'] ?? '');
            $amount = trim($data['amount'] ?? '');

            if ($name === '' || $accountName === '' || $accountNo === '') {
                $errors[] = 'Name, Account Name, and Account No are required.';
                $skipped++;
                continue;
            }
            if (!is_numeric($amount) || (float) $amount <= 0) {
                $errors[] = 'Invalid amount for ' . esc($name) . ' / ' . esc($accountNo) . '.';
                $skipped++;
                continue;
            }

            $existing = $db->table('bank')
                ->where('account_no', $accountNo)
                ->where('name', $name)
                ->where('sch_id', $shopId)
                ->get()
                ->getRow();
            if ($existing) {
                $errors[] = 'Account already exists: ' . esc($name) . ' - ' . esc($accountNo);
                $skipped++;
                continue;
            }

            $amount = (float) $amount;
            try {
                if (!$db->transBegin()) {
                    throw new \RuntimeException('Could not start database transaction.');
                }

                if (!$db->table('bank')->insert([
                    'sch_id' => $shopId,
                    'name' => $name,
                    'account_name' => $accountName,
                    'account_no' => $accountNo,
                    'balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => $now,
                ])) {
                    throw new \RuntimeException('Could not save bank account.');
                }
                $bankId = $db->insertID();

                if (!$db->table('existing_transaction')->insert([
                    'sch_id' => $shopId,
                    'bank_id' => $bankId,
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => $now,
                ])) {
                    throw new \RuntimeException('Could not save opening transaction.');
                }
                $transactionId = $db->insertID();

                if (!$db->table('ledger_bank')->insert([
                    'sch_id' => $shopId,
                    'existing_trans_id' => $transactionId,
                    'bank_id' => $bankId,
                    'particulars' => 'Bank last balance',
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => $now,
                ])) {
                    throw new \RuntimeException('Could not save bank ledger.');
                }

                $oldCapital = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                $newCapital = $oldCapital - $amount;
                if (!$db->table('shops')->where('sch_id', $shopId)->update(['capital' => $newCapital])) {
                    throw new \RuntimeException('Could not update shop capital.');
                }
                if (!$db->table('ledger_capital')->insert([
                    'sch_id' => $shopId,
                    'existing_trans_id' => $transactionId,
                    'particulars' => 'Existing Bank (' . $name . ') is added with existing balance',
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'rest_balance' => $newCapital,
                    'createdBy' => $userId,
                    'createdDtm' => $now,
                ])) {
                    throw new \RuntimeException('Could not save capital ledger.');
                }

                if (!$db->transCommit()) {
                    throw new \RuntimeException('Database transaction failed.');
                }
                $inserted++;
            } catch (\Throwable $exception) {
                $db->transRollback();
                $errors[] = 'Could not import ' . esc($name) . ' / ' . esc($accountNo) . '.';
                $skipped++;
            }
        }

        fclose($handle);
        $message = 'CSV processed. Inserted: ' . $inserted . ', Skipped: ' . $skipped;
        $alert = $errors ? 'alert-warning' : 'alert-success';
        if ($errors) {
            $message .= '<br><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
        }

        $this->session->setFlashdata('message',
            '<div class="alert ' . $alert . ' alert-dismissible">' . $message .
            '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
        );
        return redirect()->to($redirectUrl);
    }






}