<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase <small>Purchase Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax'); ?>','<?php echo '/Admin/Purchase';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Purchase Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form  action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="int">Type </label>
                                        <select class="form-control" name="type_id" required >
                                            <option value="" >Please select</option>
                                            <option value="1">New Item</option>
                                            <option value="2">Existing Item</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Supplier</label>
                                        <select class="form-control" name="supplier_id" id="supData"  required>
                                            <option value="">Please select</option>
                                            <?php echo getAllListInOptionWithStatus('','supplier_id','name','suppliers','name'); ?>
                                        </select>
                                        <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-default">Create new</a>
                                    </div>


                                    <button type="submit" class="btn btn-primary geniusSubmit-btn">Next</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/'); ?>','<?php echo '/Admin/Purchase/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;"></div>
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

<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Suppliers Add</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <form id="supplierform" action="<?= base_url('Admin/Suppliers/create_action_ajax');?>" method="post">
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
                            <button type="button" class="btn btn-primary" onclick="suppliersValidat()" >Save</button>
                        </form>
                    </div>
                    <div class="col-md-6" style="border-left: 1px solid #cecdcd;">
                        <form id="supplierform2" action="<?= base_url('Admin/Suppliers/create_existing_action_ajax');?>" method="post">
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
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>