<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Suppliers <small>Suppliers Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Suppliers </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Suppliers Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <h4>New Suppliers</h4>
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
                                    <div class="form-group">
                                        <label for="address">Address </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"></textarea>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="suppliersValidat()"  >Register</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/'); ?>','<?php echo '/Admin/Suppliers/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <form id="geniusform3" action="<?php echo $action2; ?>" method="post">
                                    <h4>Existing Suppliers</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name </label>
                                        <input type="text" class="form-control" name="name" id="name_ex" placeholder="Name" required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Phone </label>
                                        <input type="number" class="form-control" name="phone" id="phone_ex" placeholder="Phone" required />
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address </label>
                                        <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"></textarea>
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
                                        <label for="int">Amount</label>
                                        <input type="number" class="form-control input"
                                               name="amount" id="amount" placeholder="Amount" oninput="minusValueCheck(this.value,this)"
                                               required/>
                                        <div class="error"></div>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="suppliersExValidat()"  >Register</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Suppliers_ajax/'); ?>','<?php echo '/Admin/Suppliers/'; ?>')" class="btn btn-default">Cancel</a>
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