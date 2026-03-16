<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transaction <small>Transaction Process</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transaction</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax'); ?>','<?php echo '/Admin/Transaction';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Transaction Process</h3>
                            </div>
                            <div class="col-lg-3">

                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="panel with-nav-tabs panel-default " id="nav_panel">
                            <ul class="nav nav-tabs" id="ul_border ">
                                <li class="tab-pane active in"><a href="#customer" data-toggle="tab">Customer</a></li>
                                <li class="tab-pane "><a href="#supplier" data-toggle="tab">Supplier</a></li>
                                <li class="tab-pane "><a href="#loanProvider" data-toggle="tab">Account Head</a></li>
                                <li class="tab-pane "><a href="#bank" data-toggle="tab">Fund Transfer</a></li>
                                <li class="tab-pane "><a href="#expense" data-toggle="tab">Expense</a></li>
                                <li class="tab-pane "><a href="#othersales" data-toggle="tab">Other Sales</a></li>
                                <li class="tab-pane "><a href="#employeeSalary" data-toggle="tab">Employee Salary</a>
                                </li>
                                <li class="tab-pane "><a href="#vatPay" data-toggle="tab">Vat Pay</a></li>
                            </ul>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active in" id="customer">
                                        <div class="box-header">
                                            <h3 class="box-title">Customer Transaction</h3>
                                        </div>
                                        <div class="box-body">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                                        <div class="form-group">
                                                            <label for="int">Customer </label>
                                                            <select class="form-control select2 select2-hidden-accessible input" id="cusData" onchange="custoTranDet(this.value)" style=" width: 100%;" tabindex="-1" aria-hidden="true" name="customer_id" required>
                                                                <option value="">Please Select </option>
                                                                <?php echo getAllListInOptionWithStatus('', 'customer_id', 'customer_name', 'customers','customer_name'); ?>
                                                            </select>
                                                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-customer">Create new</a>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="enum">Transaction
                                                                Type</label>
                                                            <select class="form-control input" name="trangaction_type"
                                                                    onchange="changepaymenttype(this.value)"
                                                                    id="trangaction_type" required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">খরচ (Cr.)</option>
                                                                <option value="2">জমা (Dr.)</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" id="paymentCus">
                                                            <label for="payment_type">Payment
                                                                Type </label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" name="payment_type"
                                                                    required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">Bank</option>
                                                                <option value="3">Chaque</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>
                                                        <!-- <span id="databank"></span> -->
                                                        <div class="form-group databank" id="chaque">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>

                                                        <div class="form-group form-check">
                                                            <input type="checkbox" class="form-check-input" name="sms" id="sms">
                                                            <label class="form-check-label" for="sms">Send SMS</label>
                                                        </div>

                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                                <div class="col-md-8" id="reloadledg">
                                                    <div id="custData"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="supplier">
                                        <div class="box-header">

                                            <h3 class="box-title">Supplier Transaction</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $actionsuppl; ?>"
                                                          method="post">

                                                        <div class="form-group">
                                                            <label for="int">Supplier</label>
                                                            <select class="form-control select2 select2-hidden-accessible input"
                                                                    onchange="supplTransView(this.value)"
                                                                    style=" width: 100%;"  name="supplier_id"
                                                                    id="supplier_id" required>
                                                                <option selected="selected" value="">Please Select
                                                                </option>
                                                                <?php echo getAllListInOptionWithStatus('', 'supplier_id', 'name', 'suppliers','name'); ?>
                                                            </select>
                                                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-default">Create new</a>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="enum">Transaction
                                                                Type</label>
                                                            <select class="form-control input" name="trangaction_type"
                                                                    onchange="changepaymenttype(this.value)"
                                                                    id="trangaction_type" required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">খরচ (Cr.)</option>
                                                                <option value="2">জমা (Dr.)</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group" id="paymentsup">
                                                            <label for="payment_type">Payment
                                                                Type </label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" name="payment_type"
                                                                    required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">Bank</option>
                                                                <option value="3">Chaque</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group form-check">
                                                            <input type="checkbox" class="form-check-input" name="sms" id="smsSup">
                                                            <label class="form-check-label" for="smsSup">Send SMS</label>
                                                        </div>
                                                        <div class="form-group" id="databankSup">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>

                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                                <div class="col-xs-8">
                                                    <div id="suppData"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="loanProvider">
                                        <div class="box-header">

                                            <h3 class="box-title">Account Head Transaction</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $actionLoanPro; ?>"  method="post">
                                                        <div class="form-group">
                                                            <label for="int">Account
                                                                Head</label>
                                                            <select class="form-control select2 select2-hidden-accessible input" onchange="lonProvTransView(this.value)" style=" width: 100%;" tabindex="-1" id="loanData"  aria-hidden="true" name="loan_pro_id" required>
                                                                <option selected="selected" value="">Please Select
                                                                </option>
                                                                <?php echo getAllListInOptionWithStatus('', 'loan_pro_id', 'name', 'loan_provider','name'); ?>
                                                            </select>
                                                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-account">Create new</a>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="enum">Transaction
                                                                Type</label>
                                                            <select class="form-control input" name="trangaction_type"
                                                                    onchange="changepaymenttype(this.value)"
                                                                    id="trangaction_type" required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">খরচ (Cr.)</option>
                                                                <option value="2">জমা (Dr.)</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" id="paymentloPo">
                                                            <label for="payment_type">Payment
                                                                Type </label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" name="payment_type"
                                                                    required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">Chaque/Bank</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group " id="databankloPo">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>

                                                        <div class="form-group form-check">
                                                            <input type="checkbox" class="form-check-input" name="sms" id="smsEM">
                                                            <label class="form-check-label" for="smsEM">Send SMS</label>
                                                        </div>

                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                                <div class="col-md-8">
                                                    <div id="lonProvData"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade  in" id="bank">
                                        <div id="er_msg"></div>
                                        <div class="box-header">

                                            <h3 class="box-title">Bank Transaction</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <form id="geniusform" action="<?php echo $actionBank; ?>" method="post">
                                                    <div class="form-group">
                                                        <label for="int">Bank</label>
                                                        <select class="form-control input"
                                                                onchange="bankTransView(this.value)" name="bank_id"
                                                                id="bank_id" required>
                                                            <option value="">Please select</option>
                                                            <?php echo getTwoValueInOptionWithStatus('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="int">To
                                                            Bank</label>
                                                        <select class="form-control input" name="bank_id2" required>
                                                            <option value="">Please select</option>
                                                            <?php echo getTwoValueInOptionWithStatus('', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="particulars">Particulars </label>
                                                        <textarea class="form-control input" rows="3" name="particulars"
                                                                  id="particulars" placeholder="Particulars"
                                                                  required></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="int">Amount </label>
                                                        <input type="number" step=any class="form-control input"
                                                               onchange="checkAvailableBankAmount(this.value)"
                                                               name="amount" oninput="minusValueCheck(this.value,this)" id="amount9090" placeholder="Amount"
                                                               required/>
                                                        <div id="Bank_valid"></div>
                                                    </div>
                                                    <button type="submit"
                                                            class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                       class="btn btn-default">Cancel</a>
                                                </form>
                                            </div>
                                            <div class="col-md-8">
                                                <div id="bankData"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="expense">
                                        <div class="box-header">
                                            <h3 class="box-title">Expense Transaction</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $actionExpense; ?>"
                                                          method="post">
                                                        <div class="form-group">
                                                            <label for="particulars">Memo
                                                                Number </label>

                                                            <input type="text" class="form-control input"
                                                                   name="memo_number" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="payment_type">Payment
                                                                Type </label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" required
                                                                    name="payment_type">
                                                                <option value="">Please Select</option>
                                                                <option value="1">Chaque/Bank</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group" id="dataexpense">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>
                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                                <div class="col-md-8">
                                                    <div id="bankData"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="othersales">
                                        <div class="box-header">
                                            <h3 class="box-title">Other Sales Transaction</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $actionOtherSales; ?>"
                                                          method="post">
                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control" rows="3" name="particulars"
                                                                      id="particulars" placeholder="Particulars"
                                                                      required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control" name="amount"
                                                                   id="amount" oninput="minusValueCheck(this.value,this)" placeholder="Amount" required/>
                                                        </div>
                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="employeeSalary">
                                        <div class="box-header">

                                            <h3 class="box-title">Employee Salary</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <form id="geniusform" action="<?php echo $actionSalaryEmployee; ?>"
                                                          method="post">

                                                        <div class="form-group">
                                                            <label for="int">Employee </label>
                                                            <select class="form-control select2 select2-hidden-accessible input"
                                                                    onchange="employeeSearch(this.value)"
                                                                    style=" width: 100%;" tabindex="-1"
                                                                    aria-hidden="true" name="employee_id" required>
                                                                <option selected="selected" value="">Please Select
                                                                </option>
                                                                <?php echo getAllListInOptionWithStatus('', 'employee_id', 'name', 'employee','name'); ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="int">Salary </label>
                                                            <input type="text" class="form-control input" name="salary"
                                                                   id="salary"
                                                                   placeholder="Salary" readonly>

                                                        </div>

                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="payment_type">Payment
                                                                Type </label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" required
                                                                    name="payment_type">
                                                                <option value="">Please Select</option>
                                                                <option value="1">Chaque/Bank</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group" id="employee">
                                                            <label for="int">Amount</label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>

                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>
                                                <div class="col-md-8">
                                                    <div id="ledger_employee"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane " id="vatPay">
                                        <div class="box-header">
                                            <h3 class="box-title">Vat Pay</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <form id="geniusform" action="<?php echo $actionVatPay; ?>" method="post">


                                                        <div class="form-group">
                                                            <label for="int">Vat
                                                                Register</label>
                                                            <select class="form-control select2 select2-hidden-accessible input"
                                                                    onchange="vatLedgerView(this.value)"
                                                                    style=" width: 100%;" tabindex="-1"
                                                                    aria-hidden="true" name="vat_id">
                                                                <option selected="selected" value="">Please Select
                                                                </option>
                                                                <?php echo getAllListInOption('', 'vat_id', 'name', 'vat_register'); ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="particulars">Particulars </label>
                                                            <textarea class="form-control input" rows="3"
                                                                      name="particulars" id="particulars"
                                                                      placeholder="Particulars" required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="payment_type">Payment
                                                                Type</label>
                                                            <select class="form-control input"
                                                                    onchange="checkBank(this.value)" name="payment_type"
                                                                    required>
                                                                <option value="">Please Select</option>
                                                                <option value="1">Chaque/Bank</option>
                                                                <option value="2" selected>Cash</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group" id="vatpayId">
                                                            <label for="int">Amount </label>
                                                            <input type="number" step=any class="form-control input"
                                                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount"
                                                                   required/>
                                                        </div>
                                                        <button type="submit"
                                                                class="btn btn-primary geniusSubmit-btn"><?php echo $button ?></button>
                                                        <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Transaction_ajax/'); ?>','<?php echo '/Admin/Transaction/'; ?>')"
                                                           class="btn btn-default">Cancel</a>
                                                    </form>
                                                </div>

                                                <div class="col-md-8">
                                                    <div id="vatledger"></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<!--customer modal-->
<div class="modal fade" id="modal-customer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Customer Add</h4>
            </div>
            <form id="customerform" action="<?= base_url('Admin/Customers/create_action_ajax');?>" method="post">
                <div class="modal-body">
                    <div id="cusmessage2"></div>
                    <div class="form-group">
                        <label for="varchar">Customer Name </label>
                        <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Customer Name" required />
                        <div class="error"></div>
                    </div>
                    <div class="form-group">
                        <label for="int">Mobile </label>
                        <input type="number" class="form-control" name="mobile" id="mobile" placeholder="Mobile"  required/>
                        <div class="error"></div>
                    </div>
                    <div class="form-group">
                        <label for="int">Customer Type</label>
                        <select class="form-control" name="cus_type_id" id="cus_type_id" required>
                            <?php echo getAllListInOption('','cus_type_id','type_name','customer_type'); ?>
                        </select>
                        <div class="error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="customerValidat()" >Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--customer modal-->

<!--supplier modal-->
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Suppliers Add</h4>
            </div>
            <form id="supplierform" action="<?= base_url('Admin/Suppliers/create_action_ajax');?>" method="post">
                <div class="modal-body">
                    <div id="message2"></div>
                    <div class="form-group">
                        <label for="varchar">Name </label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required />
                        <div class="error"></div>
                    </div>
                    <div class="form-group">
                        <label for="int">Phone </label>
                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone" required/>
                        <div class="error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="suppliersValidat()" >Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--supplier modal-->

<!--account head modal-->
<div class="modal fade" id="modal-account">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Account Head Add</h4>
            </div>
            <form id="accountform" action="<?= base_url('Admin/Loan_provider/create_action_ajax');?>" method="post">
                <div class="modal-body">
                    <div id="messageAcc"></div>
                    <div class="form-group">
                        <label for="varchar">Name</label>
                        <input type="text" class="form-control" name="nameAcc" id="nameAcc" placeholder="Name" required/>
                        <div class="error"></div>
                    </div>
                    <div class="form-group">
                        <label for="int">Phone</label>
                        <input type="number" class="form-control" name="phoneAcc" id="phoneAcc" placeholder="Phone" required />
                        <div class="error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="accountHeadValidat()" >Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--account head modal-->
