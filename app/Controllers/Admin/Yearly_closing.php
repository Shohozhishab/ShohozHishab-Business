<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Yearly_closing extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $forge;
    private $module_name = 'Yearly_closing';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->forge = \Config\Database::forge();
    }

    /**
     * @description This method provides yearly closing view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Yearly_closing/list');
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method server config and yearly closing
     * @return void
     */
    public function server()
    {
//        $year = date("Y");
//        $db = \Config\Database::connect('custom');
        $newDb = BACKUP_DB_PREFIX . date("Y");
        DB()->transStart();
        $this->create_product_table_backup($newDb);
        $this->create_admin_table_backup($newDb);
        $this->create_bank_table_backup($newDb);
        $this->create_bank_deposit_table_backup($newDb);
        $this->create_bank_withdraw_table_backup($newDb);
        $this->create_brand_table_backup($newDb);
        $this->create_chaque_table_backup($newDb);
        $this->create_customers_table_backup($newDb);
        $this->create_customer_type_table_backup($newDb);
        $this->create_employee_table_backup($newDb);
        $this->create_gen_settings_table_backup($newDb);
        $this->create_gen_settings_super_table_backup($newDb);

        $this->create_invoice_table_backup($newDb);
        $this->create_invoice_item_table_backup($newDb);

        $this->create_ledger_table_backup($newDb);
        $this->create_ledger_bank_table_backup($newDb);
        $this->create_ledger_employee_table_backup($newDb);
        $this->create_ledger_expense_table_backup($newDb);
        $this->create_ledger_loan_table_backup($newDb);
        $this->create_ledger_nagodan_table_backup($newDb);
        $this->create_ledger_other_sales_table_backup($newDb);
        $this->create_ledger_suppliers_table_backup($newDb);
        $this->create_ledger_vat_table_backup($newDb);
        $this->create_ledger_purchase_table_backup($newDb);
        $this->create_ledger_sales_table_backup($newDb);
        $this->create_ledger_capital_table_backup($newDb);
        $this->create_ledger_profit_table_backup($newDb);
        $this->create_ledger_stock_table_backup($newDb);

        $this->create_ledger_discount_table_backup($newDb);


        $this->create_license_table_backup($newDb);
        $this->create_loan_provider_table_backup($newDb);
        $this->create_money_receipt_table_backup($newDb);
        $this->create_payment_type_table_backup($newDb);
        $this->create_product_category_table_backup($newDb);
        $this->create_purchase_table_backup($newDb);
        $this->create_purchase_item_table_backup($newDb);
        $this->create_return_purchase_table_backup($newDb);
        $this->create_return_purchase_item_table_backup($newDb);
        $this->create_return_sale_table_backup($newDb);
        $this->create_return_sale_item_table_backup($newDb);
        $this->create_roles_table_backup($newDb);
        $this->create_shops_table_backup($newDb);
        $this->create_sale_table_backup($newDb);


        $this->create_stores_table_backup($newDb);
        $this->create_suppliers_table_backup($newDb);
        $this->create_transaction_table_backup($newDb);
        $this->create_users_table_backup($newDb);
        $this->create_vat_register_table_backup($newDb);
        $this->create_warranty_manage_table_backup($newDb);


        // delete data all
        $this->delete_table_data();
        $this->make_new_ledger();

        DB()->transComplete();
        print '<div class="alert alert-success alert-dismissible" role="alert"> Backup Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

    }

    /**
     * @description This method local config and yearly closing
     * @return void
     */
    public function yearly_closing_local()
    {

        DB()->transStart();

//            $newDb = 'shohoz_hishab_backup_'.date('Y');
        $newDb = BACKUP_DB_PREFIX . date('Y');
        $this->forge->createDatabase($newDb, true);


        $this->create_product_table_backup($newDb);
        $this->create_admin_table_backup($newDb);
        $this->create_bank_table_backup($newDb);
        $this->create_bank_deposit_table_backup($newDb);
        $this->create_bank_withdraw_table_backup($newDb);
        $this->create_brand_table_backup($newDb);
        $this->create_chaque_table_backup($newDb);
        $this->create_customers_table_backup($newDb);
        $this->create_customer_type_table_backup($newDb);
        $this->create_employee_table_backup($newDb);
        $this->create_gen_settings_table_backup($newDb);
        $this->create_gen_settings_super_table_backup($newDb);

        $this->create_invoice_table_backup($newDb);
        $this->create_invoice_item_table_backup($newDb);

        $this->create_ledger_table_backup($newDb);
        $this->create_ledger_bank_table_backup($newDb);
        $this->create_ledger_employee_table_backup($newDb);
        $this->create_ledger_expense_table_backup($newDb);
        $this->create_ledger_loan_table_backup($newDb);
        $this->create_ledger_nagodan_table_backup($newDb);
        $this->create_ledger_other_sales_table_backup($newDb);
        $this->create_ledger_suppliers_table_backup($newDb);
        $this->create_ledger_vat_table_backup($newDb);
        $this->create_ledger_purchase_table_backup($newDb);
        $this->create_ledger_sales_table_backup($newDb);
        $this->create_ledger_capital_table_backup($newDb);
        $this->create_ledger_profit_table_backup($newDb);
        $this->create_ledger_stock_table_backup($newDb);

        $this->create_ledger_discount_table_backup($newDb);


        $this->create_license_table_backup($newDb);
        $this->create_loan_provider_table_backup($newDb);
        $this->create_money_receipt_table_backup($newDb);
        $this->create_payment_type_table_backup($newDb);
        $this->create_product_category_table_backup($newDb);
        $this->create_purchase_table_backup($newDb);
        $this->create_purchase_item_table_backup($newDb);
        $this->create_return_purchase_table_backup($newDb);
        $this->create_return_purchase_item_table_backup($newDb);
        $this->create_return_sale_table_backup($newDb);
        $this->create_return_sale_item_table_backup($newDb);
        $this->create_roles_table_backup($newDb);
        $this->create_shops_table_backup($newDb);
        $this->create_sale_table_backup($newDb);


        $this->create_stores_table_backup($newDb);
        $this->create_suppliers_table_backup($newDb);
        $this->create_transaction_table_backup($newDb);
        $this->create_users_table_backup($newDb);
        $this->create_vat_register_table_backup($newDb);
        $this->create_warranty_manage_table_backup($newDb);
        $this->create_package_table_backup($newDb);


        // delete data all
        $this->delete_table_data();
        $this->make_new_ledger();

        DB()->transComplete();

        print '<div class="alert alert-success alert-dismissible" role="alert"> Backup Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }

    /**
     * @description This method backup bank table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_bank_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;

        $tableMain = DB()->table($database . '.bank');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();

        $mastertable = $database . '.bank';
        $tablename = $newDb . '.bank';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'bank';
        foreach ($data as $row) {
            $check = $this->check_data_exists_in_table($table, 'bank_id', $row->bank_id);
            if ($check == true) {
                $tableIn = DB()->table('bank');
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup bank deposit table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_bank_deposit_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.bank_deposit');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.bank_deposit';
        $tablename = $newDb . '.bank_deposit';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'bank_deposit';
        foreach ($data as $row) {
            $check = $this->check_data_exists_in_table($table, 'dep_id', $row->dep_id);
            if ($check == true) {
                $tableIn = DB()->table('bank_deposit');
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup bank withdraw table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_bank_withdraw_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.bank_withdraw');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();

        $mastertable = $database . '.bank_withdraw';
        $tablename = $newDb . '.bank_withdraw';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'bank_withdraw';
        foreach ($data as $row) {
            $tableIn = DB()->table('bank_withdraw');
            $check = $this->check_data_exists_in_table($table, 'wthd_id', $row->wthd_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup admin table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_admin_table_backup($newDb)
    {
        $database = DB()->database;
        $tableMain = DB()->table($database . '.admin');
        $data = $tableMain->get()->getResult();


        $mastertable = $database . '.admin';
        $tablename = $newDb . '.admin';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'admin';
        foreach ($data as $row) {
            $tableIn = DB()->table('admin');
            $check = $this->check_data_exists_in_table($table, 'user_id', $row->user_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup product table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_product_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.products');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.products';
        $tablename = $newDb . '.products';

        DB()->query('use ' . $newDb);

        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");

        $table = 'products';
        foreach ($data as $row) {
            $tableIn = DB()->table('products');
            $check = $this->check_data_exists_in_table($table, 'prod_id', $row->prod_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup brand table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_brand_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.brand');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.brand';
        $tablename = $newDb . '.brand';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'brand';
        foreach ($data as $row) {
            $tableIn = DB()->table('brand');
            $check = $this->check_data_exists_in_table($table, 'brand_id', $row->brand_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup cheque table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_chaque_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.chaque');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.chaque';
        $tablename = $newDb . '.chaque';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'chaque';
        foreach ($data as $row) {
            $tableIn = DB()->table('chaque');
            $check = $this->check_data_exists_in_table($table, 'chaque_id', $row->chaque_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup customers table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_customers_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.customers');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.customers';
        $tablename = $newDb . '.customers';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'customers';
        foreach ($data as $row) {
            $tableIn = DB()->table('customers');
            $check = $this->check_data_exists_in_table($table, 'customer_id', $row->customer_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup customers type table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_customer_type_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.customer_type');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.customer_type';
        $tablename = $newDb . '.customer_type';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'customer_type';
        foreach ($data as $row) {
            $tableIn = DB()->table('customer_type');
            $check = $this->check_data_exists_in_table($table, 'cus_type_id', $row->cus_type_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup employee table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_employee_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.employee');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.employee';
        $tablename = $newDb . '.employee';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'employee';
        foreach ($data as $row) {
            $tableIn = DB()->table('employee');
            $check = $this->check_data_exists_in_table($table, 'employee_id', $row->employee_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup gen settings table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_gen_settings_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.gen_settings');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.gen_settings';
        $tablename = $newDb . '.gen_settings';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'gen_settings';
        foreach ($data as $row) {
            $tableIn = DB()->table('gen_settings');
            $check = $this->check_data_exists_in_table($table, 'settings_id', $row->settings_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**@description This method backup gen settings super table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_gen_settings_super_table_backup($newDb)
    {
        $database = DB()->database;
        $tableMain = DB()->table($database . '.gen_settings_super');
        $data = $tableMain->get()->getResult();


        $mastertable = $database . '.gen_settings_super';
        $tablename = $newDb . '.gen_settings_super';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'gen_settings_super';
        foreach ($data as $row) {
            $tableIn = DB()->table('gen_settings_super');
            $check = $this->check_data_exists_in_table($table, 'settings_id_sup', $row->settings_id_sup);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup invoice table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_invoice_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.invoice');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.invoice';
        $tablename = $newDb . '.invoice';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'invoice';
        foreach ($data as $row) {
            $tableIn = DB()->table('invoice');
            $check = $this->check_data_exists_in_table($table, 'invoice_id', $row->invoice_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup invoice item table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_invoice_item_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.invoice_item');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.invoice_item';
        $tablename = $newDb . '.invoice_item';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'invoice_item';
        foreach ($data as $row) {
            $tableIn = DB()->table('invoice_item');
            $check = $this->check_data_exists_in_table($table, 'inv_item', $row->inv_item);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger';
        $tablename = $newDb . '.ledger';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger');
            $check = $this->check_data_exists_in_table($table, 'ledg_id', $row->ledg_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger bank table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_bank_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_bank');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_bank';
        $tablename = $newDb . '.ledger_bank';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_bank';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_bank');
            $check = $this->check_data_exists_in_table($table, 'ledgBank_id', $row->ledgBank_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger employee table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_employee_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_employee');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_employee';
        $tablename = $newDb . '.ledger_employee';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_employee';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_employee');
            $check = $this->check_data_exists_in_table($table, 'ledg_emp_id', $row->ledg_emp_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger expense table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_expense_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_expense');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_expense';
        $tablename = $newDb . '.ledger_expense';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_expense';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_expense');
            $check = $this->check_data_exists_in_table($table, 'ledg_exp_id', $row->ledg_exp_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger loan table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_loan_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_loan');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_loan';
        $tablename = $newDb . '.ledger_loan';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_loan';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_loan');
            $check = $this->check_data_exists_in_table($table, 'ledg_loan_id', $row->ledg_loan_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger nagodan table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_nagodan_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_nagodan');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_nagodan';
        $tablename = $newDb . '.ledger_nagodan';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_nagodan';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_nagodan');
            $check = $this->check_data_exists_in_table($table, 'ledg_nagodan_id', $row->ledg_nagodan_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger other sales table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_other_sales_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_other_sales');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_other_sales';
        $tablename = $newDb . '.ledger_other_sales';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_other_sales';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_other_sales');
            $check = $this->check_data_exists_in_table($table, 'ledg_oth_sales_id', $row->ledg_oth_sales_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger suppliers table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_suppliers_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_suppliers');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_suppliers';
        $tablename = $newDb . '.ledger_suppliers';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_suppliers';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_suppliers');
            $check = $this->check_data_exists_in_table($table, 'ledg_sup_id', $row->ledg_sup_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger vat table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_vat_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_vat');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_vat';
        $tablename = $newDb . '.ledger_vat';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_vat';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_vat');
            $check = $this->check_data_exists_in_table($table, 'ledg_vat_id', $row->ledg_vat_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger license table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_license_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.license');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.license';
        $tablename = $newDb . '.license';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'license';
        foreach ($data as $row) {
            $tableIn = DB()->table('license');
            $check = $this->check_data_exists_in_table($table, 'lic_id', $row->lic_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger loan provider table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_loan_provider_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.loan_provider');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.loan_provider';
        $tablename = $newDb . '.loan_provider';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'loan_provider';
        foreach ($data as $row) {
            $tableIn = DB()->table('loan_provider');
            $check = $this->check_data_exists_in_table($table, 'loan_pro_id', $row->loan_pro_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger money receipt table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_money_receipt_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.money_receipt');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.money_receipt';
        $tablename = $newDb . '.money_receipt';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'money_receipt';
        foreach ($data as $row) {
            $tableIn = DB()->table('money_receipt');
            $check = $this->check_data_exists_in_table($table, 'money_receipt_id', $row->money_receipt_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger payment type table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_payment_type_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.payment_type');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.payment_type';
        $tablename = $newDb . '.payment_type';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'payment_type';
        foreach ($data as $row) {
            $tableIn = DB()->table('payment_type');
            $check = $this->check_data_exists_in_table($table, 'pymnt_type_id', $row->pymnt_type_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger product category table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_product_category_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.product_category');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.product_category';
        $tablename = $newDb . '.product_category';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'product_category';
        foreach ($data as $row) {
            $tableIn = DB()->table('product_category');
            $check = $this->check_data_exists_in_table($table, 'prod_cat_id', $row->prod_cat_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger purchase table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_purchase_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.purchase');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.purchase';
        $tablename = $newDb . '.purchase';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'purchase';
        foreach ($data as $row) {
            $tableIn = DB()->table('purchase');
            $check = $this->check_data_exists_in_table($table, 'purchase_id', $row->purchase_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger purchase item table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_purchase_item_table_backup($newDb)
    {
        $database = DB()->database;
        $tableMain = DB()->table($database . '.purchase_item');
        $data = $tableMain->get()->getResult();


        $mastertable = $database . '.purchase_item';
        $tablename = $newDb . '.purchase_item';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'purchase_item';
        foreach ($data as $row) {
            $tableIn = DB()->table('purchase_item');
            $check = $this->check_data_exists_in_table($table, 'purchase_item_id', $row->purchase_item_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger return purchase table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_return_purchase_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.return_purchase');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.return_purchase';
        $tablename = $newDb . '.return_purchase';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'return_purchase';
        foreach ($data as $row) {
            $tableIn = DB()->table('return_purchase');
            $check = $this->check_data_exists_in_table($table, 'rtn_purchase_id', $row->rtn_purchase_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger return purchase item table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_return_purchase_item_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.return_purchase_item');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.return_purchase_item';
        $tablename = $newDb . '.return_purchase_item';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'return_purchase_item';
        foreach ($data as $row) {
            $tableIn = DB()->table('return_purchase_item');
            $check = $this->check_data_exists_in_table($table, 'rtn_purchase_item_id', $row->rtn_purchase_item_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger return sale table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_return_sale_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.return_sale');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.return_sale';
        $tablename = $newDb . '.return_sale';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'return_sale';
        foreach ($data as $row) {
            $tableIn = DB()->table('return_sale');
            $check = $this->check_data_exists_in_table($table, 'rtn_sale_id', $row->rtn_sale_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger return sale item table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_return_sale_item_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.return_sale_item');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.return_sale_item';
        $tablename = $newDb . '.return_sale_item';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'return_sale_item';
        foreach ($data as $row) {
            $tableIn = DB()->table('return_sale_item');
            $check = $this->check_data_exists_in_table($table, 'rtn_sale_item_id', $row->rtn_sale_item_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup roles table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_roles_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.roles');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.roles';
        $tablename = $newDb . '.roles';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'roles';
        foreach ($data as $row) {
            $tableIn = DB()->table('roles');
            $check = $this->check_data_exists_in_table($table, 'role_id', $row->role_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup shops table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_shops_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.shops');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.shops';
        $tablename = $newDb . '.shops';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'shops';
        foreach ($data as $row) {
            $tableIn = DB()->table('shops');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup sale table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_sale_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.sales');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.sales';
        $tablename = $newDb . '.sales';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'sales';
        foreach ($data as $row) {
            $tableIn = DB()->table('sales');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger purchase table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_purchase_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_purchase');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_purchase';
        $tablename = $newDb . '.ledger_purchase';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_purchase';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_purchase');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger sales table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_sales_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_sales');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_sales';
        $tablename = $newDb . '.ledger_sales';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_sales';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_sales');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger capital table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_capital_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_capital');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_capital';
        $tablename = $newDb . '.ledger_capital';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_capital';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_capital');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger profit table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_profit_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_profit');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_profit';
        $tablename = $newDb . '.ledger_profit';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_profit';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_profit');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger stock table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_stock_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_stock');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_stock';
        $tablename = $newDb . '.ledger_stock';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_stock';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_stock');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger discount table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_ledger_discount_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.ledger_discount');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.ledger_discount';
        $tablename = $newDb . '.ledger_discount';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'ledger_discount';
        foreach ($data as $row) {
            $tableIn = DB()->table('ledger_discount');
            $check = $this->check_data_exists_in_table($table, 'sch_id', $row->sch_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup ledger stores table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_stores_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.stores');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.stores';
        $tablename = $newDb . '.stores';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'stores';
        foreach ($data as $row) {
            $tableIn = DB()->table('stores');
            $check = $this->check_data_exists_in_table($table, 'store_id', $row->store_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup suppliers table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_suppliers_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.suppliers');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.suppliers';
        $tablename = $newDb . '.suppliers';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'suppliers';
        foreach ($data as $row) {
            $tableIn = DB()->table('suppliers');
            $check = $this->check_data_exists_in_table($table, 'supplier_id', $row->supplier_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup transaction table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_transaction_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.transaction');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.transaction';
        $tablename = $newDb . '.transaction';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'transaction';
        foreach ($data as $row) {
            $tableIn = DB()->table('transaction');
            $check = $this->check_data_exists_in_table($table, 'trans_id', $row->trans_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup users table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_users_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.users');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.users';
        $tablename = $newDb . '.users';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'users';
        foreach ($data as $row) {
            $tableIn = DB()->table('users');
            $check = $this->check_data_exists_in_table($table, 'user_id', $row->user_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup vat register table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_vat_register_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.vat_register');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.vat_register';
        $tablename = $newDb . '.vat_register';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'vat_register';
        foreach ($data as $row) {
            $tableIn = DB()->table('vat_register');
            $check = $this->check_data_exists_in_table($table, 'vat_id', $row->vat_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup warranty manage table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_warranty_manage_table_backup($newDb)
    {
        $shopId = $this->session->shopId;
        $database = DB()->database;
        $tableMain = DB()->table($database . '.warranty_manage');
        $data = $tableMain->where('sch_id', $shopId)->get()->getResult();


        $mastertable = $database . '.warranty_manage';
        $tablename = $newDb . '.warranty_manage';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'warranty_manage';
        foreach ($data as $row) {
            $tableIn = DB()->table('warranty_manage');
            $check = $this->check_data_exists_in_table($table, 'prod_id', $row->prod_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup package table
     * @param string $newDb
     * @return void
     * @throws \Exception
     */
    private function create_package_table_backup($newDb)
    {
        $database = DB()->database;
        $tableMain = DB()->table($database . '.package');
        $data = $tableMain->get()->getResult();


        $mastertable = $database . '.package';
        $tablename = $newDb . '.package';

        DB()->query('use ' . $newDb);
        DB()->query("CREATE TABLE IF NOT EXISTS $tablename LIKE $mastertable");


        $table = 'package';
        foreach ($data as $row) {
            $tableIn = DB()->table('package');
            $check = $this->check_data_exists_in_table($table, 'package_id', $row->package_id);
            if ($check == true) {
                foreach ($row as $key => $val) {
                    $tableIn->set($key, $val);
                }
                $tableIn->insert();
            }
        }
    }

    /**
     * @description This method backup data exists table
     * @param string $table
     * @param string $key
     * @param string $value
     * @return bool
     */
    private function check_data_exists_in_table($table, $key, $value)
    {
        $tab = DB()->table($table);
        $query = $tab->where($key, $value)->countAllResults();

        if ($query == 0) {
            $data = true;
        } else {
            $data = false;
        }
        return $data;
    }

    /**
     * @description This method backup delete table
     * @return void
     * @throws \Exception
     */
    private function delete_table_data()
    {
        $database = DB()->database;
        DB()->query('use ' . $database);

        $shopId = $this->session->shopId;

        $ledgerMain = DB()->table('ledger');
        $ledgerMain->where('sch_id', $shopId)->delete();

        $ledger_bankMain = DB()->table('ledger_bank');
        $ledger_bankMain->where('sch_id', $shopId)->delete();

        $ledger_employeeMain = DB()->table('ledger_employee');
        $ledger_employeeMain->where('sch_id', $shopId)->delete();

        $ledger_expenseMain = DB()->table('ledger_expense');
        $ledger_expenseMain->where('sch_id', $shopId)->delete();

        $ledger_loanMain = DB()->table('ledger_loan');
        $ledger_loanMain->where('sch_id', $shopId)->delete();

        $ledger_nagodanMain = DB()->table('ledger_nagodan');
        $ledger_nagodanMain->where('sch_id', $shopId)->delete();

        $ledger_other_salesMain = DB()->table('ledger_other_sales');
        $ledger_other_salesMain->where('sch_id', $shopId)->delete();

        $ledger_suppliersMain = DB()->table('ledger_suppliers');
        $ledger_suppliersMain->where('sch_id', $shopId)->delete();

        $ledger_purchaseMain = DB()->table('ledger_purchase');
        $ledger_purchaseMain->where('sch_id', $shopId)->delete();

        $ledger_salesMain = DB()->table('ledger_sales');
        $ledger_salesMain->where('sch_id', $shopId)->delete();

        $ledger_vatMain = DB()->table('ledger_vat');
        $ledger_vatMain->where('sch_id', $shopId)->delete();

        $ledger_capitalMain = DB()->table('ledger_capital');
        $ledger_capitalMain->where('sch_id', $shopId)->delete();

        $ledger_profitMain = DB()->table('ledger_profit');
        $ledger_profitMain->where('sch_id', $shopId)->delete();

        $ledger_stockMain = DB()->table('ledger_stock');
        $ledger_stockMain->where('sch_id', $shopId)->delete();

        $ledger_discountMain = DB()->table('ledger_discount');
        $ledger_discountMain->where('sch_id', $shopId)->delete();


        $invoice_itemMain = DB()->table('invoice_item');
        $invoice_itemMain->where('sch_id', $shopId)->delete();

        $return_purchase_itemMain = DB()->table('return_purchase_item');
        $return_purchase_itemMain->where('sch_id', $shopId)->delete();

        $return_sale_itemMain = DB()->table('return_sale_item');
        $return_sale_itemMain->where('sch_id', $shopId)->delete();


        $purchaseTab = DB()->table('purchase');
        $purchasedt = $purchaseTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($purchasedt as $rowpur) {
            $purchase_itemTab = DB()->table('purchase_item');
            $purchase_itemTab->where('purchase_id', $rowpur->purchase_id)->delete();
        }


        $bank_depositTab = DB()->table('bank_deposit');
        $bank_depositTab->where('sch_id', $shopId)->delete();

        $bank_withdrawTab = DB()->table('bank_withdraw');
        $bank_withdrawTab->where('sch_id', $shopId)->delete();

        $chaqueTab = DB()->table('chaque');
        $chaqueTab->where('sch_id', $shopId)->delete();


        $salesTab = DB()->table('sales');
        $salesTab->where('sch_id', $shopId)->delete();

        $invoiceTab = DB()->table('invoice');
        $invoiceTab->where('sch_id', $shopId)->delete();

        $money_receiptTab = DB()->table('money_receipt');
        $money_receiptTab->where('sch_id', $shopId)->delete();

        $payment_typeTab = DB()->table('payment_type');
        $payment_typeTab->where('sch_id', $shopId)->delete();


        $purchaseTab = DB()->table('purchase');
        $purchaseTab->where('sch_id', $shopId)->delete();

        $return_purchaseTab = DB()->table('return_purchase');
        $return_purchaseTab->where('sch_id', $shopId)->delete();

        $return_saleTab = DB()->table('return_sale');
        $return_saleTab->where('sch_id', $shopId)->delete();

        $transactionTab = DB()->table('transaction');
        $transactionTab->where('sch_id', $shopId)->delete();

        $warranty_manageTab = DB()->table('warranty_manage');
        $warranty_manageTab->where('sch_id', $shopId)->delete();

    }

    /**
     * @description This method backup make new table
     * @return void
     * @throws \Exception
     */
    private function make_new_ledger()
    {
        $database = DB()->database;
        DB()->query('use ' . $database);

        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        // Bank Balance Ledger create (start)
        $bankTab = DB()->table('bank');
        $bank = $bankTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($bank as $rowbank) {
            $lgBankData = array(
                'sch_id' => $shopId,
                'bank_id' => $rowbank->bank_id,
                'trangaction_type' => 'Dr.',
                'particulars' => 'Previous balance',
                'amount' => $rowbank->balance,
                'rest_balance' => $rowbank->balance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->insert($lgBankData);
        }
        // Bank Balance Ledger create (end)


        // Customer Balance Ledger create (start)
        $customersTab = DB()->table('customers');
        $customers = $customersTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($customers as $rowcus) {
            $cusLedger = array(
                'sch_id' => $shopId,
                'customer_id' => $rowcus->customer_id,
                'particulars' => 'Previous balance',
                'trangaction_type' => 'Dr.',
                'amount' => $rowcus->balance,
                'rest_balance' => $rowcus->balance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->insert($cusLedger);
        }
        // Customer Balance Ledger create (end)


        // Loneprovider Balance Ledger create (start)
        $loan_providerTab = DB()->table('loan_provider');
        $loan_provider = $loan_providerTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($loan_provider as $rowloan) {
            $dataledg = array(
                'sch_id' => $shopId,
                'loan_pro_id' => $rowloan->loan_pro_id,
                'particulars' => 'Previous balance',
                'trangaction_type' => 'Dr.',
                'amount' => $rowloan->balance,
                'rest_balance' => $rowloan->balance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_loanTab = DB()->table('ledger_loan');
            $ledger_loanTab->insert($dataledg);
        }
        // Loneprovider Balance Ledger create (end)


        // supplier Balance Ledger create (start)
        $suppliersTab = DB()->table('suppliers');
        $suppliers = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
        foreach ($suppliers as $rowsupp) {
            $dataSup = array(
                'sch_id' => $shopId,
                'supplier_id' => $rowsupp->supplier_id,
                'particulars' => 'Previous balance',
                'trangaction_type' => 'Dr.',
                'amount' => $rowsupp->balance,
                'rest_balance' => $rowsupp->balance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_suppliersTab = DB()->table('ledger_suppliers');
            $ledger_suppliersTab->insert($dataSup);
        }
        // supplier Balance Ledger create (start)


        // Shop Balance Ledger create (start)
        $shopsTab = DB()->table('shops');
        $shops = $shopsTab->where('sch_id', $shopId)->get()->getRow();
        $lgNagData2 = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $shops->cash,
            'rest_balance' => $shops->cash,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_nagodanTab = DB()->table('ledger_nagodan');
        $ledger_nagodanTab->insert($lgNagData2);
        // Shop Balance Ledger create (start)


        // purchase ledger create(start)
        $shopPurch = $shops;
        $lgPurcData = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $shopPurch->purchase_balance,
            'rest_balance' => $shopPurch->purchase_balance,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_purchaseTab = DB()->table('ledger_purchase');
        $ledger_purchaseTab->insert($lgPurcData);
        // purchase ledger create(end)


        // sale ledger create(start)
        $shopSale = $shops;
        $lgSalData = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $shopSale->sale_balance,
            'rest_balance' => $shopSale->sale_balance,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_salesTab = DB()->table('ledger_sales');
        $ledger_salesTab->insert($lgSalData);
        // sale ledger create(end)


        // vat ledger create(start)
        $vat_registerTab = DB()->table('vat_register');
        $vat = $vat_registerTab->where('sch_id', $shopId)->get()->getRow();
        $vatData = array(
            'sch_id' => $shopId,
            'vat_id' => $vat->vat_id,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $vat->balance,
            'rest_balance' => $vat->balance,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_vatTab = DB()->table('ledger_vat');
        $ledger_vatTab->insert($vatData);
        // vat ledger create(end)


        // profit ledger create(start)
        $shopprofit = $shops;
        $newCapi = $shopprofit->capital + $shopprofit->profit;

//        $lgProData = array(
//            'sch_id' => $shopId,
//            'trangaction_type'=>'Dr.',
//            'particulars' => 'Previous balance',
//            'amount'=>$shopprofit->profit,
//            'rest_balance'=> $shopprofit->profit,
//            'createdBy'=>$userId,
//            'createdDtm' => date('Y-m-d h:i:s')
//        );
//        DB()->insert($database.'.ledger_profit',$lgProData);

        $profitData = array('capital' => $newCapi, 'profit' => 0);
        $shopsMain = DB()->table('shops');
        $shopsMain->where('sch_id', $shopId)->update($profitData);

        // profit ledger create(end)


        // capital ledger create(start)
        $shopcapital = $shops;
        $lgCapiData = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $shopcapital->capital,
            'rest_balance' => $shopcapital->capital,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_capitalMain = DB()->table('ledger_capital');
        $ledger_capitalMain->insert($lgCapiData);
        // capital ledger create(end)


        // stock ledger create(start)
        $shopstock = $shops;
        $lgStocData = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous balance',
            'amount' => $shopstock->stockAmount,
            'rest_balance' => $shopstock->stockAmount,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_stockMain = DB()->table('ledger_stock');
        $ledger_stockMain->insert($lgStocData);
        // stock ledger create(end)


        // ledger discount create(start)
        $shopdiscount = $shops;
        $lgdiscountData = array(
            'sch_id' => $shopId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Previous discount',
            'amount' => $shopdiscount->discount,
            'rest_balance' => $shopdiscount->discount,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_discountMain = DB()->table('ledger_discount');
        $ledger_discountMain->insert($lgdiscountData);
        // ledger discount create(end)


    }


}