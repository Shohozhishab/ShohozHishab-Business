<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Sales <small>Sales Create</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>

            <div class="col-md-12" style="margin-top: 20px;" >
                <div id="message"></div>
                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
            </div>
            <div class="col-md-8">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Sales Create</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-pencil-square-o fa-lg" style="color: white;"></i>
                                    </span>
                                    <input type="text" class="form-control input-lg" onkeypress="findResult()"
                                           name="keyWord" id="keyWord" value="">

                                    <span class="input-group-btn">
                                      <button class="btn btn-primary btn-lg" type="submit">Search</button>
                                    </span>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-barcode fa-lg" style="color: white;"></i>
                                    </span>
                                    <input type="text" class="form-control input-lg" oninput="QrScan(this.value)"
                                           name="qrKey" id="qrKey" placeholder="Scan barcode">
                                </div>

                            </div>
                            <div class="input-group col-md-12">
                                <ul style="list-style-type:none;" id="result"></ul>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    <form action="<?php echo $action; ?>" method="post">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-fw fa-cart-plus"></i> Product cart list</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-xs-12" id="box_form">
                                <table class="table table-bordered table-striped" id="TFtable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <?php if (isset($discount) AND ($discount == 1)) { ?>
                                            <th>Disc %</th>
                                        <?php } ?>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0;
                                    $j = 0;
                                    $k = 0;
                                    $l = 0;
                                    $m = 0;
                                    $n = 0;
                                    foreach (Cart()->contents() as $row) { ?>
                                        <tr>
                                            <td><?php echo ++$i; ?></td>
                                            <td>
                                                <?php echo $row['name']; ?>
                                                <input type="hidden" class="form-control " name="productId[]"
                                                       value="<?php echo $row['id']; ?>">
                                            </td>
                                            <td>
                                                <?php echo $row['qty']; ?>
                                                <input type="hidden" class="form-control " name="qty[]"
                                                       value="<?php echo $row['qty']; ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control upprice" name="price[]"
                                                       value="<?php echo $row['price']; ?>">
                                            </td>
                                            <?php if (isset($discount) AND ($discount == 1)) { ?>
                                                <td><input type="number" step=any class="form-control disc" oninput="minusValueCheck(this.value,this),validationDiscount('disc_<?= $row['id']?>')" name="disc[]" id="disc_<?= $row['id']?>"></td>
                                            <?php } ?>
                                            <td>
                                                <input type="hidden" readonly class="form-control subtotal"
                                                       name="subtotal[]" id="subt_<?php print $m++; ?>"
                                                       value="<?php echo $row['subtotal'] ?>">
                                                <input type="hidden" name="suballtotal[]"
                                                       id="subtl2_<?php print $k++; ?>"
                                                       value="<?php echo $row['subtotal']; ?>">
                                                <span id="subtl_<?php print $l++; ?>">
                                                <span id="subtl_<?php print $j++; ?>">
                                                 <?php echo number_format($row['subtotal']); ?>
                                                </span>
                                            </span>

                                            </td>
                                            <td width="120px">
                                                <a href="<?php echo site_url('/Admin/Sales/remove_cart/' . $row['rowid']); ?>"
                                                   onclick="javasciprt: return confirm('Are You Sure ?')"
                                                   class="btn btn-danger btn-xs">Cancel</a>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>

                                </table>
                            </div>
                            <div class="col-xs-12"
                                 id="box_form">
                                <div></div>
                                <div class="col-xs-9">
                                </div>
                                <div class="col-xs-3">
                                    <a href="<?php echo site_url('/Admin/Sales/clearCart/'); ?>"
                                       onclick="javasciprt: return confirm('Are You Sure ?')"
                                       class="btn btn-info btn-xs">Clear Cart</a>
                                </div>


                            </div>
                        </div>
                    </div>

            </div>

            <div class="col-md-4" style="background-color: #e8b96f;padding: 10px;">

                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                    <label>Customer</label>
                    <div class="panel with-nav-tabs panel-default nav-tabs-custom"
                         style="background-color: #e8b96f; border-color: ;" >
                        <ul class="nav nav-tabs" >
                            <li class="active"><a href="#existing" data-toggle="tab">Existing Customer</a></li>
                            <li class=""><a href="#new" data-toggle="tab">New Customer</a></li>
                        </ul>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="existing">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <select class="form-control select2 select2-hidden-accessible"
                                                    onchange="createBtnShow(),customerBalanceShow(this.value)" style=" width: 100%;"
                                                    tabindex="-1" aria-hidden="true" name="customer_id"
                                                    id="cus">

                                                <option selected="selected" value="">Please Select</option>
                                                <?php echo getAllListInOptionWithStatus('customer_id', 'customer_id', 'customer_name', 'customers','customer_name'); ?>
                                            </select>
                                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-customer">Create new</a><br>
                                            <span id="balance"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="new">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="text" class="form-control " name="name" id="name"
                                                   placeholder="Name" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php if (isset($discount) AND ($discount == 1)) { ?>
                    <div class="col-xs-6"
                         style="border:1px dashed #D0D3D8 ;padding:5px;">

                        <label>Entire Sale Discount: %</label>

                        <input type="number" step=any class="form-control saleDisc" oninput="minusValueCheck(this.value,this)" name="saleDisc" id="saleDisc"
                               placeholder="Input Discount %">
                        <input type="hidden" class="form-control totalamount" name="total" id="totalamount"
                               readonly value="<?php echo Cart()->total() ?>">
                        <!--  </div> -->
                    </div>

                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px; ">
                        <label>Discount Amount</label>
                        <input type="text" class="form-control " name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" readonly>
                        <input type="hidden" name="granddiscountlast" id="granddiscountlast">
                    </div>
                <?php } ?>
                <?php if (isset($vat_option) AND ($vat_option == 1)) { ?>
                <div class="col-xs-12"
                     style="border:1px dashed #D0D3D8 ; padding:5px; ">
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                        <label>Vat: %</label>
                        <input type="number" step=any class="form-control vat" oninput="minusValueCheck(this.value,this)" name="vat" id="vat" placeholder="vat %">

                        <input type="hidden" class="form-control vatTotallast" name="vatTotallast"
                               id="vatTotallast" readonly value="<?php echo Cart()->total() ?>">
                    </div>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                        <label>Vat Amount</label>
                        <input type="text" onchange="checkBankId()" class="form-control vatAmount"
                               name="vatAmount" id="vatAmount" placeholder="Vat Amount" readonly>

                    </div>
                </div>
                <?php } ?>

                <div class="col-xs-12"
                     style="border:1px dashed #D0D3D8 ; padding:5px; ">

                    <div class="col-xs-12" style="padding:5px;">
                        <label>Grand Total</label>
                        <input type="hidden" class="form-control" name="grandtotal2" readonly id="grandtotal2"
                               value="<?php echo Cart()->total() ?>">

                        <input type="text" class="form-control" name="grandtotal" readonly id="grandtotal"
                               value="<?php echo Cart()->total() ?>">

                    </div>

                    <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                        <label>Payment</label>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <label>Cash</label>
                            <input type="number" step=any class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod"
                                   placeholder="Input Cash Amount">
                        </div>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Bank</label>
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    <?php echo getTwoValueInOption('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                </select>
                            </div>
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Bank Amount</label>
                                <input type="number" step=any onchange="checkBankId()" class="form-control bankAmount"
                                       name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this)" placeholder="input Bank Amount">
                                <b id="Bank_valid"></b>
                            </div>
                        </div>
                        <div class="col-xs-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Cheque No</label>
                                <input type="text" class="form-control" name="chequeNo" id="chequeNo"
                                       placeholder="Input Cheque No ">
                            </div>
                            <div class="col-xs-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                <label>Cheque Amount</label>
                                <input type="number" step=any onchange="cheque()" class="form-control chequeAmount"
                                       name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount ">
                                <b id="cheque_valid"></b>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8 ;padding:5px;">
                        <label>Total Amount </label>
                        <input type="text"  class="form-control " name="grandtotallast" readonly
                               id="grandtotallast" value="<?php echo number_format(Cart()->total()) ?>">

                    </div>
                    <div class="col-xs-6" style="border:1px dashed #D0D3D8; padding:5px;">
                        <label>Total Due</label>
                        <input type="number" step=any class="form-control" name="grandtotaldue" readonly id="grandtotaldue"
                               value="<?php echo Cart()->total() ?>">
                    </div>
                </div>
                <div class="col-xs-12" style="border:1px dashed #D0D3D8 ;padding-top: 10px;">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="sms" id="sms">
                        <label class="form-check-label" for="sms">Send SMS</label>
                    </div>
                </div>
                <div class="col-xs-12" style="padding:20px; ">

                    <button style="float: right;" id="btn" type="submit"
                            class="btn btn-primary">Sale</button>
                    <b id="mess"></b>
                </div>



            </div>

            </form>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

<!--customer modal-->
<div class="modal fade" id="modal-customer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Customer Add</h4>
            </div>

                <div class="modal-body">
                    <div id="cusmessage2"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <form id="customerform" action="<?= base_url('Admin/Customers/create_action_ajax');?>" method="post">
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
                                <button type="button" class="btn btn-primary" onclick="customerValidat()" >Save</button>
                            </form>
                        </div>
                        <div class="col-md-6" style="border-left: 1px solid #cecdcd;">
                            <form id="customerform2" action="<?= base_url('Admin/Customers/create_action_existing_ajax');?>" method="post">
                                <h4>Existing Customer</h4>
                                <div class="form-group">
                                    <label for="varchar">Customer Name </label>
                                    <input type="text" class="form-control" name="customer_name" id="customer_name_ex" placeholder="Customer Name" required />
                                    <div class="error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="int">Mobile</label>
                                    <input type="number" class="form-control" name="mobile" id="mobile_ex" placeholder="Mobile" required/>
                                    <div class="error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="int">Customer Type</label>

                                    <select class="form-control" name="cus_type_id" id="cus_type_id_ex" required>
                                        <option value="">Please Select</option>
                                        <?php echo getAllListInOption('','cus_type_id','type_name','customer_type'); ?>
                                    </select>
                                    <div class="error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="enum">Transaction Type</label>
                                    <select class="form-control input" name="transaction_type" id="transaction_type" required>
                                        <option value="">Please Select</option>
                                        <option value="1">খরচ (Cr.) /পাওনাদার</option>
                                        <option value="2">জমা (Dr.) /দেনাদার</option>
                                    </select>
                                    <div class="error"></div>
                                </div>

                                <div class="form-group databank" id="chaque">
                                    <label for="int">Amount</label>
                                    <input type="number" class="form-control input" name="amount" id="amount" placeholder="Amount" oninput="minusValueCheck(this.value,this)" required/>
                                    <div class="error"></div>
                                </div>

                                <button type="button" class="btn btn-primary" onclick="customerExisValidat()"  >Save</button>
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
<!--customer modal-->