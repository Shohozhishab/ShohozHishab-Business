<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Account Head <small>Account Head Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Account Head </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Account Head Create
                        <?php
                            $str = '1';
                            $usernamePreg = "/^[a-zA-Z0-9 ]+$/";
                            print is_numeric($str);
                        ?>

                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <h4>New Account Head</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Phone</label>
                                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Phone" required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Adress </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"></textarea>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="accountValidat()"  >Register</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Loan_provider_ajax/'); ?>','<?php echo '/Admin/Loan_provider/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <form id="geniusform3" action="<?php echo $action2; ?>" method="post">
                                    <h4>Existing Account Head</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name </label>
                                        <input type="text" class="form-control" name="name" id="name_ex" placeholder="Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Phone </label>
                                        <input type="number" class="form-control" name="phone" id="phone_ex" placeholder="Phone" required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="enum">Transaction Type </label>
                                        <select class="form-control input" name="transaction_type" id="transaction_type" required>
                                            <option value="">Please Select</option>
                                            <option value="1">খরচ (Cr.) /পাওনাদার</option>
                                            <option value="2">জমা (Dr.) /দেনাদার</option>
                                        </select>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group databank" id="chaque">
                                        <label for="int">Amount </label>
                                        <input type="number" class="form-control input" name="amount" id="amount" oninput="minusValueCheck(this.value,this)" placeholder="Amount"
                                               required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Adress </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="address"></textarea>
                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="accountExValidat()"  >Register</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Loan_provider_ajax/'); ?>','<?php echo '/Admin/Loan_provider/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>

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