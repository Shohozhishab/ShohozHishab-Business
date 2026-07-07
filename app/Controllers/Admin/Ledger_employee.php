<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_employee extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_employee';

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
            $employee_id = $this->request->getGet('employee_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($employee_id)) {
                $table = DB()->table('ledger_employee');
                if (!empty($st_date) && !empty($en_date)) {
                    // Assuming your database column name is 'date'
                    $table->where('createdDtm >=', $st_date . ' 00:00:00');
                    $table->where('createdDtm <=', $en_date . ' 23:59:59');
                }
                $table->where("employee_id", $employee_id);
                $query = $table->get()->getResult();
            }
            $data['result'] = $query;

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['employee_id'] = $employee_id;

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_employee/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search ledger employee
     * @return void
     */
    public function search_ledger_employee()
    {
        $employeeId = $this->request->getPost('id');

        $ledger_employeeTable = DB()->table('ledger_employee');
        $ledger_employeeTable->where('employee_id', $employeeId);
        $ledger_employeeTable->orderBy('ledg_emp_id', "DESC");
        $query = $ledger_employeeTable->get();
        $data = $query->getResult();

        $view = '<table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Transaction" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . $row->createdDtm . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $row->trans_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . $row->rest_balance . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';

        $view .= '<div class="row no-print">
                                <div class="col-xs-12">
                                  <div class="print_line btn btn-primary pull-right" onclick="showHeader(),printDiv(\'ledgPrint\')"><i class="fa fa-print"></i> Print Now</div>
                                  
                                </div>
                              </div>';
        print $view;
    }

    /**
     * @description This method search ledger employee date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $employeeId = $this->request->getPost('employeeId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $ledger_employeeTable = DB()->table('ledger_employee');
        $ledger_employeeTable->where('employee_id', $employeeId);
        $ledger_employeeTable->where('createdDtm >=', $st_date . ' 00:00:00');
        $ledger_employeeTable->where('createdDtm <=', $en_date . ' 23:59:59');
        $ledger_employeeTable->orderBy('ledg_emp_id', "DESC");
        $query = $ledger_employeeTable->get();
        $data = $query->getResult();

        $view = '<table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Transaction" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . $row->createdDtm . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $row->trans_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . $row->rest_balance . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';

        $view .= '<div class="row no-print">
                                <div class="col-xs-12">
                                  <div class="print_line btn btn-primary pull-right" onclick="showHeader(),printDiv(\'ledgPrint\')"><i class="fa fa-print"></i> Print Now</div>
                                  
                                </div>
                              </div>';
        print $view;
    }


}