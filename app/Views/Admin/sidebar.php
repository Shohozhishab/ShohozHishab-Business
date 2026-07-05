
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="min-height: 65px;">
            <div class="pull-left image">
<!--                <img src="--><?php //echo base_url()?><!--/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">-->
                <?php $imglogo =  no_image_view('/uploads/schools/'.logo_image(),'/uploads/schools/no_image_logo.jpg',logo_image()); ?>
                <img src="<?php echo $imglogo; ?>" class="" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo profile_name();?></p>
                <a href="#" ><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <?php $role_id = newSession()->role;?>


            <li class="treeview Active">
                <?php
                    echo add_main_ajax_based_menu_with_permission('Dashboard', '/Admin/Dashboard', $role_id, 'fa fa-dashboard', '/Admin/DashboardAjax','Dashboard');
                ?>
            </li>

            <?php
                $modArray = ['Sales','Return_sale','Invoice'];
                $menuAccess = all_menu_permission_check($modArray,$role_id);
                if ($menuAccess == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-cart-plus"></i>
                    <span>Sales</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Sales List', '/Admin/Sales', $role_id, 'fa fa-cart-plus', '/Admin/Sales_ajax','Sales'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Return  Sales', '/Admin/Return_sale', $role_id, 'fa fa-cart-plus', '/Admin/Return_sale_ajax','Return_sale'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Invoice', '/Admin/Invoice', $role_id, 'fa fa fa-tasks', '/Admin/Invoice_ajax','Invoice'); ?>
                </ul>
            </li>
            <?php } ?>

            <?php
            $modArray = ['Service','Service_invoice'];
            $menuAccess = all_menu_permission_check($modArray,$role_id);
            if ($menuAccess == true){
                ?>
                <li class="treeview">
                    <a href="#" >
                        <i class="fa fa-cart-plus"></i>
                        <span>Service</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php echo add_main_ajax_based_menu_with_permission('Service List', '/Admin/Service', $role_id, 'fa fa-cart-plus', '/Admin/Service_ajax','Service'); ?>
                        <?php echo add_main_ajax_based_menu_with_permission('Service Invoice', '/Admin/Service_invoice', $role_id, 'fa fa fa-tasks', '/Admin/Service_invoice_ajax','Service_invoice'); ?>
                    </ul>
                </li>
            <?php } ?>

            <?php
                $modArrayPur = ['Purchase'];
                $menuAccessPur = all_menu_permission_check($modArrayPur,$role_id);
                if ($menuAccessPur == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-briefcase"></i>
                    <span>Purchase </span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Purchase', '/Admin/Purchase', $role_id, 'fa fa-briefcase', '/Admin/Purchase_ajax','Purchase'); ?>
                </ul>
            </li>

            <?php } ?>

            <?php
                $modArrayTr = ['Transaction'];
                $menuAccessTr = all_menu_permission_check($modArrayTr,$role_id);
                if ($menuAccessTr == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-exchange"></i>
                    <span>Transaction</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Process Transaction', '/Admin/Transaction/create', $role_id, 'fa fa-exchange', '/Admin/Transaction_ajax/create','Transaction'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Transaction List', '/Admin/Transaction', $role_id, 'fa fa-exchange', '/Admin/Transaction_ajax','Transaction'); ?>
                </ul>
            </li>
            <?php } ?>

            <?php
                $modArraySt = ['Stores','Products','Product_category','Brand'];
                $menuAccessSt = all_menu_permission_check($modArraySt,$role_id);
                if ($menuAccessSt == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-tasks"></i>
                    <span>Stock</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Stores', '/Admin/Stores', $role_id, 'fa fa-tasks', '/Admin/Stores_ajax' ,'Stores'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Products', '/Admin/Products', $role_id, 'fa fa-tasks', '/Admin/Products_ajax','Products'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Product Category', '/Admin/Product_category', $role_id, 'fa fa-tasks', '/Admin/Product_category_ajax','Product_category'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Brand', '/Admin/Brand', $role_id, 'fa fa-tasks', '/Admin/Brand_ajax','Brand'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Stock Transfer', '/Admin/Stock_transfer', $role_id, 'fa fa-tasks', '/Admin/Stock_transfer_ajax','StockTransfer'); ?>                </ul>
            </li>
            <?php } ?>

            <?php
                $modArrayLed = ['Ledger_nagodan','Ledger_bank','Ledger_loan','Ledger','Ledger_suppliers','Ledger_purchase','Ledger_sales','Ledger_stock','Ledger_expense','Ledger_profit','Ledger_capital','Ledger_vat','Ledger_other_sales','Ledger_discount'];
                $menuAccessLed = all_menu_permission_check($modArrayLed,$role_id);
                if ($menuAccessLed == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-book"></i>
                    <span>Ledger</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Cash Ledger', '/Admin/Ledger_nagodan', $role_id, 'fa fa-book', '/Admin/Ledger_nagodan_ajax','Ledger_nagodan'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Bank Ledger', '/Admin/Ledger_bank', $role_id, 'fa fa-book', '/Admin/Ledger_bank_ajax','Ledger_bank'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Account Head Ledger', '/Admin/Ledger_loan', $role_id, 'fa fa-book', '/Admin/Ledger_loan_ajax','Ledger_loan'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Customer Ledger', '/Admin/Ledger', $role_id, 'fa fa-book', '/Admin/Ledger_ajax','Ledger'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Supplier Ledger', '/Admin/Ledger_suppliers', $role_id, 'fa fa-book', '/Admin/Ledger_suppliers_ajax','Ledger_suppliers'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Purchase Ledger', '/Admin/Ledger_purchase', $role_id, 'fa fa-book', '/Admin/Ledger_purchase_ajax','Ledger_purchase'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Sale Ledger', '/Admin/Ledger_sales', $role_id, 'fa fa-book', '/Admin/Ledger_sales_ajax','Ledger_sales'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Stock', '/Admin/Ledger_stock', $role_id, 'fa fa-book', '/Admin/Ledger_stock_ajax','Ledger_stock'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Expense', '/Admin/Ledger_expense', $role_id, 'fa fa-book', '/Admin/Ledger_expense_ajax','Ledger_expense'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Profit', '/Admin/Ledger_profit', $role_id, 'fa fa-book', '/Admin/Ledger_profit_ajax','Ledger_profit'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Capital', '/Admin/Ledger_capital', $role_id, 'fa fa-book', '/Admin/Ledger_capital_ajax','Ledger_capital'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Vat', '/Admin/Ledger_vat', $role_id, 'fa fa-book', '/Admin/Ledger_vat_ajax','Ledger_vat'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger other sales', '/Admin/Ledger_other_sales', $role_id, 'fa fa-book', '/Admin/Ledger_other_sales_ajax','Ledger_other_sales'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Ledger Service Charge', '/Admin/Ledger_service_charge', $role_id, 'fa fa-book', '/Admin/Ledger_service_charge_ajax','Ledger_service_charge'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Discount Ledger', '/Admin/Ledger_discount', $role_id, 'fa fa-book', '/Admin/Ledger_discount_ajax','Ledger_discount'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Accounts Ledger', '/Admin/Ledger_accounts', $role_id, 'fa fa-book', '/Admin/Ledger_accounts_ajax','Ledger_accounts'); ?>

                </ul>
            </li>
            <?php } ?>

            <?php echo add_main_ajax_based_menu_with_permission('Daily Book', '/Admin/Daily_book', $role_id, 'fa fa-book', '/Admin/Daily_book_ajax','Daily_book'); ?>

            <?php echo add_main_ajax_based_menu_with_permission('Transaction Events', '/Admin/Transaction_events', $role_id, 'fa fa-book', '/Admin/Transaction_events_ajax','TransactionEvents'); ?>

            <li><?php echo add_main_ajax_based_menu_with_permission('Trial Balance', '/Admin/Trial_balance', $role_id, 'fa fa-list-alt', '/Admin/Trial_balance_ajax','Trial_balance'); ?> </li>

            <?php
                $modArrayRe = ['Balance_report','Stock_report','Sales_report','Purchase_report','Acquisition_due','Owe_amount'];
                $menuAccessRe = all_menu_permission_check($modArrayRe,$role_id);
                if ($menuAccessRe == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-line-chart"></i>
                    <span>Report</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Report', '/Admin/Report', $role_id, 'fa fa-line-chart', '/Admin/Report_ajax','Report'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Balance Report', '/Admin/Balance_report', $role_id, 'fa fa-line-chart', '/Admin/Balance_report_ajax','Balance_report'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Stock Report', '/Admin/Stock_report', $role_id, 'fa fa-line-chart', '/Admin/Stock_report_ajax','Stock_report'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Sale Report', '/Admin/Sales_report', $role_id, 'fa fa-line-chart', '/Admin/Sales_report_ajax','Sales_report'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Purchase Report', '/Admin/Purchase_report', $role_id, 'fa fa-line-chart', '/Admin/Purchase_report_ajax','Purchase_report'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Accounts Receivable', '/Admin/Acquisition_due', $role_id, 'fa fa-line-chart', '/Admin/Acquisition_due_ajax','Acquisition_due'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Accounts Payable', '/Admin/Owe_amount', $role_id, 'fa fa-line-chart', '/Admin/Owe_amount_ajax','Owe_amount'); ?>


                </ul>
            </li>
            <?php } ?>

            <?php
                $modArrayCus = ['Customer_type','Customers'];
                $menuAccessCus = all_menu_permission_check($modArrayCus,$role_id);
                if ($menuAccessCus == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-users"></i>
                    <span>Customer </span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Customer type', '/Admin/Customer_type', $role_id, 'fa fa-child', '/Admin/Customer_type_ajax','Customer_type'); ?>

                    <?php echo add_main_ajax_based_menu_with_permission('Customers', '/Admin/Customers', $role_id, 'fa fa-child', '/Admin/Customers_ajax','Customers'); ?>
                </ul>
            </li>
            <?php } ?>

            <?php echo add_main_ajax_based_menu_with_permission('Suppliers', '/Admin/Suppliers', $role_id, 'fa fa-user', '/Admin/Suppliers_ajax','Suppliers'); ?>



            <?php
            $modArraySt = ['Loan_provider','Assets','Expenses'];
            $menuAccessSt = all_menu_permission_check($modArraySt,$role_id);
            if ($menuAccessSt == true){
                ?>
                <li class="treeview">
                    <a href="#" >
                        <i class="fa fa-tasks"></i>
                        <span>Chart Of Accounts</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php echo add_main_ajax_based_menu_with_permission('Account Head', '/Admin/Loan_provider', $role_id, 'fa fa-user-plus', '/Admin/Loan_provider_ajax','Loan_provider'); ?>
                        <?php echo add_main_ajax_based_menu_with_permission('Assets', '/Admin/Assets', $role_id, 'fa fa-user-plus', '/Admin/Assets_ajax','Assets'); ?>
                        <?php echo add_main_ajax_based_menu_with_permission('Expenses', '/Admin/Expenses', $role_id, 'fa fa-user-plus', '/Admin/Expenses_ajax','Expenses'); ?>

                    </ul>
                </li>
            <?php } ?>



            <?php
                $modArrayBank = ['Bank','Bank_deposit','Bank_withdraw','Chaque'];
                $menuAccessBank = all_menu_permission_check($modArrayBank,$role_id);
                if ($menuAccessBank == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-bank"></i>
                    <span>Bank</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Bank', '/Admin/Bank', $role_id, 'fa fa-th-list', '/Admin/Bank_ajax','Bank'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Deposit', '/Admin/Bank_deposit', $role_id, 'fa fa-th-list', '/Admin/Bank_deposit_ajax','Bank_deposit'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Withdraw', '/Admin/Bank_withdraw', $role_id, 'fa fa-th-list', '/Admin/Bank_withdraw_ajax','Bank_withdraw'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Chaque', '/Admin/Chaque', $role_id, 'fa fa-th-list', '/Admin/Chaque_ajax','Chaque'); ?>
                </ul>
            </li>
            <?php } ?>

            <?php
                $modArrayEmp = ['Employee','Ledger_employee'];
                $menuAccessEmp = all_menu_permission_check($modArrayEmp,$role_id);
                if ($menuAccessEmp == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-line-chart"></i>
                    <span>Employee</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Employee', '/Admin/Employee', $role_id, 'fa fa-line-chart', '/Admin/Employee_ajax','Employee'); ?>
                    <?php echo add_main_ajax_based_menu_with_permission('Salary', '/Admin/Ledger_employee', $role_id, 'fa fa-line-chart', '/Admin/Ledger_employee_ajax','Ledger_employee'); ?>
                </ul>
            </li>
            <?php } ?>

            <?php
                $modArrayUse = ['User','Role'];
                $menuAccessUse = all_menu_permission_check($modArrayUse,$role_id);
                if ($menuAccessUse == true){
            ?>
            <li class="treeview">
                <a href="#" >
                    <i class="fa fa-line-chart"></i>
                    <span>Users </span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <?php echo add_main_ajax_based_menu_with_permission('Users', '/Admin/User', $role_id, 'fa fa-hospital-o', '/Admin/User_ajax','User'); ?>

                    <?php echo add_main_ajax_based_menu_with_permission('Users Role', '/Admin/Role', $role_id, 'fa fa-hospital-o', '/Admin/Role_ajax','Role'); ?>
                </ul>
            </li>
            <?php } ?>
            <?php echo add_main_ajax_based_menu_with_permission('Capital List', '/Admin/Capital/list', $role_id, 'fa fa-book', '/Admin/Capital_ajax/list','Capital'); ?>

            <?php echo add_main_ajax_based_menu_with_permission('Settings', '/Admin/Settings', $role_id, 'fa fa-hospital-o', '/Admin/Settings_ajax','Settings'); ?>


            <?php echo add_main_ajax_based_menu_with_permission('Previous Data Show', '/Admin/Previous_data_show', $role_id, 'fa fa-exchange', '/Admin/Previous_data_show_ajax','Previous_data_show'); ?>



        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

