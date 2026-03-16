<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Service <small>Service Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Service </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <?php echo $menu;?>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Service Create</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>

                                <div class="form-group col-md-4">
                                    <label for="varchar">Name</label>
                                    <input type="text" class="form-control" name="service_name" id="service_name" placeholder="Name"  required>
                                    <div class="error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="varchar">Price</label>
                                    <input type="number" class="form-control" name="price" id="price" placeholder="Price"  required>
                                    <div class="error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <button class="btn btn-primary" style="margin-top: 25px;" onclick="addtocartService()" >Add</button>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    </div>
                    <div class="col-md-12">

                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Service List</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-responsive">
                                            <thead>
                                                <tr>
                                                    <th>Sl</th>
                                                    <th>Name</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 1; foreach (Cart()->contents() as $row) { ?>
                                                <tr>
                                                    <td><?php echo $i++;?></td>
                                                    <td>
                                                        <?php echo $row['name']; ?>
                                                        <input type="hidden" class="form-control " name="productId[]" value="<?php echo $row['id']; ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo $row['price']; ?>
                                                        <input type="hidden" class="form-control " name="qty[]" value="<?php echo $row['qty']; ?>">
                                                        <input type="hidden" class="form-control" name="price[]" value="<?php echo $row['price']; ?>">
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo site_url('/Admin/Service/remove_cart/' . $row['rowid']); ?>" onclick="javasciprt: return confirm('Are You Sure ?')" class="btn btn-danger btn-xs">Remove</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>

            <div class="col-md-4">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Service Data</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form action="<?php echo base_url($action);?>" method="post" >
                            <div class="row">
                                <div id="message"></div>
                                <div class="form-group col-md-12">
                                    <label for="varchar">Service Name</label>
                                    <input type="text" class="form-control" name="title" oninput="generateBtnShow()" id="title" placeholder="Service name"  required>
                                    <div class="error"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="varchar">Service Type</label>
                                    <select name="service_type" class="form-control" id="service_type" onchange="generateBtnShow()" required>
                                        <option value="">Please select</option>
                                        <?php echo selectOptions('',serviceArray())?>
                                    </select>
                                    <div class="error"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Customer</label>
                                    <div class="panel with-nav-tabs panel-default "  >
                                        <ul class="nav nav-tabs" >
                                            <li class="active"><a href="#existing" data-toggle="tab">Existing Customer</a></li>
                                            <li class=""><a href="#new" data-toggle="tab">New Customer</a></li>
                                        </ul>
                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade active in" id="existing">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <select class="form-control select2" onchange="generateBtnShow()" style="width: 100%;" name="customer_id" id="cus" >

                                                                <option selected="selected" value="">Please Select</option>
                                                                <?php echo getAllListInOptionWithStatus('customer_id', 'customer_id', 'customer_name', 'customers','customer_name'); ?>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade in" id="new">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <input type="text" class="form-control " oninput="generateBtnShow()" name="name" id="nameId" placeholder="Name" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Entire Sale Discount: %</label>

                                    <input type="number" step=any class="form-control saleDisc" oninput="minusValueCheck(this.value,this)" name="saleDisc" id="saleDisc" placeholder="Input Discount %">
                                    <input type="hidden" class="form-control totalamount" name="total" id="totalamount" readonly value="<?php echo Cart()->total() ?>">
                                </div>

                                <div class="col-md-6" >
                                    <label>Discount Amount</label>
                                    <input type="text" class="form-control" name="saleDiscshow" id="saleDiscshow" placeholder="Discount Amount" readonly>
                                    <input type="hidden" name="granddiscountlast" id="granddiscountlast">
                                </div>

                                <div class="col-md-12" >
                                    <label>Grand Total</label>
                                    <input type="hidden" class="form-control" name="grandtotal2" readonly id="grandtotal2" value="<?php echo Cart()->total() ?>">
                                    <input type="text" class="form-control" name="grandtotal" readonly id="grandtotal" value="<?php echo Cart()->total() ?>">

                                </div>

                                <div class="col-md-12" >
                                    <label>Payment</label>
                                    <div class="col-md-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <label>Cash</label>
                                        <input type="number" step=any class="form-control nagod" oninput="minusValueCheck(this.value,this)" name="nagod" id="nagod" placeholder="Input Cash Amount">
                                    </div>
                                    <div class="col-md-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <div class="col-md-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Bank</label>
                                            <select class="form-control" name="bank_id" id="bank_id">
                                                <option value="">Select Bank</option>
                                                <?php echo getTwoValueInOption('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Bank Amount</label>
                                            <input type="number" step=any onchange="checkBankId()" class="form-control bankAmount" name="bankAmount" id="bankAmount" oninput="minusValueCheck(this.value,this)" placeholder="input Bank Amount">
                                            <b id="Bank_valid"></b>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                        <div class="col-md-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Cheque No</label>
                                            <input type="text" class="form-control" name="chequeNo" id="chequeNo" placeholder="Input Cheque No ">
                                        </div>
                                        <div class="col-md-6" style="border:1px dashed #D0D3D8 ; padding:5px;">
                                            <label>Cheque Amount</label>
                                            <input type="number" step=any onchange="cheque()" class="form-control chequeAmount" name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount ">
                                            <b id="cheque_valid"></b>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" >
                                    <label>Total Amount </label>
                                    <input type="text"  class="form-control " name="grandtotallast" readonly id="grandtotallast" value="<?php echo Cart()->total() ?>">

                                </div>
                                <div class="col-md-6" >
                                    <label>Total Due</label>
                                    <input type="number" step=any class="form-control" name="grandtotaldue" readonly id="grandtotaldue" value="<?php echo Cart()->total() ?>">
                                </div>
                                <div class="col-md-12" style="margin-top: 20px" >
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="sms" id="sms">
                                        <label class="form-check-label" for="sms">Send SMS</label>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding:20px; ">

                                    <button style="float: right;" id="btn" type="submit" class="btn btn-primary">Generate</button>
                                    <div id="mess"></div>
                                </div>
                            </div>
                        </form>
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