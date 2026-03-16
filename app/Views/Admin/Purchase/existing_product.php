<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase New Product <small>Purchase New Product</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Purchase New Product</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div id="message"></div>
                    <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                    <div class="col-lg-12">
                        <form action="<?php echo $action; ?>" method="post">
                            <div class="col-lg-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Product Add</h3>
                                    </div>
                                    <div class="box-body ">
                                        <table id="example1" class="table table-bordered table-striped dataTable" >
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Purchase Price</th>
                                                    <th>Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($product as $row) { ?>
                                                <tr class="odd">
                                                    <td ><input type="checkbox" name="returnchecked[]" class="datatables" id="checkedProd" value="<?php echo $row->prod_id ;?>" >
                                                        <input type="hidden" name="prod_id[]" value="<?php echo $row->prod_id?>"></td>
                                                    <td><?php echo $row->name?></td>
                                                    <td><input type="number" class="quantity form-control" id="quantity" name="quantity[]" placeholder="Quantity" value="1" ></td>
                                                    <td><input type="text" class="purchase_price form-control" id="searchColumn" name="purchase_price[]" value="<?php echo $row->purchase_price?>" readonly ></td>
                                                    <td><?php echo get_data_by_id('product_category','product_category', 'prod_cat_id',$row->prod_cat_id)?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>




                            <div class="col-lg-12">
                                <div class="box" id="box_bac">
                                    <div class="box-header">
                                        <h3 class="box-title">Payment</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-xs-12">
                                            <div class="col-xs-12" id="box_form">
                                                <div class="col-xs-6">
                                                    <p class="lead">Payment Type:</p>
                                                    <img src="<?php print base_url(); ?>/dist/img/credit/cash.jpeg"
                                                         alt="Cash">
                                                    <img src="<?php print base_url(); ?>/dist/img/credit/bank.png"
                                                         alt="Bank">

                                                    <p class="text-muted well well-sm no-shadow"
                                                       style="margin-top: 10px;">
                                                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                        weebly
                                                        ning heekya handango imeem plugg
                                                        dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
                                                    </p>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="col-xs-6">
                                                        <label for="int">Total Amount</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" class="form-control" name="totalPrice"  id="totalAmount" readonly >
                                                        <!-- <input type="hidden" class="form-control" name="totalPrice"  id="totalPrice" readonly > -->
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Cash</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="text" oninput="minusValueCheck(this.value,this)" onchange="checkShopsBalance(this.value)" class="cash form-control" name="cash"  id="cash" ><b id="Balance_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Bank</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <select  class="form-control" name="bank_id" id="bank_id" >
                                                            <option value="">Select Bank</option>
                                                            <?php echo getTwoValueInOption('','bank_id','name','account_no','bank'); ?>
                                                        </select><br>
                                                        <input type="text" oninput="minusValueCheck(this.value,this)" onchange="checkBankBalance(this.value)" class="bank form-control" name="bank" id="bank" >
                                                        <b id="Bank_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Due</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="text" class="form-control" name="due" id="totalDueAmount" readonly >
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col-xs-12" >
                                                <input type="hidden" name="purchase_id" value="<?php echo $purchaseId?>">
                                                <input type="hidden" name="supplier_id" value="<?php echo $supplierId?>">
                                                <div class="form-group form-check">
                                                    <input type="checkbox" class="form-check-input" name="sms" id="sms">
                                                    <label class="form-check-label" for="sms">Send SMS</label>
                                                </div>
                                                <button type="submit" class="btn btn-primary" id="createBtn">Create
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>


                        </form>
                    </div>


                </div>

            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>