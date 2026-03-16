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
                    <div class="col-lg-12">
                        <div id="message"></div>
                        <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                    </div>
                    <div class="col-lg-12">
                        <form action="<?php echo $action; ?>" method="post">
                            <div class="col-lg-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Product Add</h3>
                                        <p style="float: right;">Supplier: <?= $supplier->name;?>. Balance: <?= showWithCurrencySymbol($supplier->balance);?></p>
                                    </div>
                                    <div class="box-body ">
                                        <input type="hidden" class="form-control" name="purchase_id"
                                               value="<?php echo $purchaseId; ?>">
                                        <input type="hidden" class="form-control" name="supplier_id"
                                               value="<?php echo $supplierId; ?>">

                                        <div class="form-group col-xs-3">
                                            <label for="int">Category </label>
                                            <select class="form-control" onchange="showSubCategory(this.value)"
                                                    name="category" id="category">
                                                <option value="">Please Select</option>
                                                <?php echo getCatListInOption('prod_cat_id', 'prod_cat_id', 'product_category', 'product_category'); ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-3">
                                            <label for="int">Sub Category </label>
                                            <select class="form-control" name="sub_category" id="subCat">
                                                <option value="">Please Select</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-3">
                                            <label for="varchar">Product Name </label> <span id="nameValid"
                                                                                             style="color: red;"></span>
                                            <input type="text" class="form-control" name="name" id="name"
                                                   placeholder="Name"/>
                                        </div>


                                        <div class="form-group col-xs-3">
                                            <label for="int">Unit </label>
                                            <select class="form-control" name="unit">
                                                <?php echo selectOptions($selected = 1, unitArray()); ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-3">
                                            <label for="int">Purchase Price</label>
                                            <input type="number" class="form-control purchase_price"
                                                   oninput="minusValueCheck(this.value,this)" name="price"
                                                   id="price"
                                                   placeholder="Purchase Price"/>
                                        </div>
                                        <div class="form-group col-xs-3">
                                            <label for="int">Selling Price</label>
                                            <input type="number" class="form-control selling_price"
                                                   oninput="minusValueCheck(this.value,this)"
                                                   name="selling_price" id="selling_price"
                                                   placeholder="Selling Price"/>
                                        </div>
                                        <div class="form-group col-xs-3">
                                            <label for="int">Quantity </label>
                                            <input type="number" class="form-control quantity" name="qty"
                                                   placeholder="Quantity" value="1"/>
                                        </div>
                                        <div class="form-group col-xs-3 " style="margin-top: 30px;">
                                            <button onclick="addCart()" type="button"
                                                    class="form-control btn btn-info btn-xs">Add Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Product List</h3>
                                    </div>
                                    <div class="box-body ">
                                        <table class="table table-bordered table-striped" id="TFtable">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Unit</th>
                                                <th>Purchase Price</th>
                                                <th>Selling Price</th>
                                                <th>Quantity</th>
                                                <th>Sub Total</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = '';
                                            foreach (Cart()->contents() as $row) { ?>
                                                <tr>
                                                    <td width=""><input type="hidden" class="form-control"
                                                                        name="prod_cat_id[]"
                                                                        id="prod_cat_id[]"
                                                                        value="<?php echo $row['cat_id']; ?>"/><?php echo ++$i; ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control" name="name[]"
                                                               id="name"
                                                               value="<?php echo $row['name']; ?>"/><?php echo $row['name']; ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control" name="unit[]"
                                                               id="unit"
                                                               value="<?php echo $row['unit']; ?>"/><?php echo showUnitName($row['unit']); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control purchase_price"
                                                               name="purchase_price[]" id="purchase_price"
                                                               value="<?php echo $row['price']; ?>"/><?php echo showWithCurrencySymbol($row['price']); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control selling_price"
                                                               name="selling_price[]" id="selling_price"
                                                               value="<?php echo $row['salePrice']; ?>"/><?php echo showWithCurrencySymbol($row['salePrice']); ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control quantity"
                                                               name="quantity[]"
                                                               placeholder="Quantity"
                                                               value="<?php echo $row['qty']; ?>"/><?php echo $row['qty']; ?>
                                                    </td>
                                                    <td><input type="hidden" class="form-control"
                                                               name="total_price[]"
                                                               value="<?php echo $row['subtotal']; ?>"/><?php echo showWithCurrencySymbol($row['subtotal']); ?>
                                                    </td>
                                                    <td width="120px">
                                                        <button class="btn btn-xs btn-danger" type="button"
                                                                id="<?php echo $row['rowid'] ?>"
                                                                onclick="remove_cart(this)">
                                                            Cancel
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th colspan="8">
                                                    <button style="float: right; margin-right: 40px;"
                                                            onclick="clearCart()"
                                                            class="btn btn-info btn-xs" type="button">Clear All
                                                    </button>
                                                </th>
                                            </tr>
                                            </tfoot>

                                        </table>
                                        <!-- <div id="cart"></div> -->
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
                                                        <input type="number" class="form-control" name="totalPrice"
                                                               id="totalPrice" value="<?php echo Cart()->total() ?>"
                                                               readonly>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Cash</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" onchange="checkShopsBalance(this.value)"
                                                               class="cash form-control"
                                                               oninput="minusValueCheck(this.value,this)" name="cash"
                                                               id="cash"><b
                                                                id="Balance_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Bank</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <select class="form-control" name="bank_id" id="bank_id">
                                                            <option value="">Select Bank</option>
                                                            <?php echo getTwoValueInOption('bank_id', 'bank_id', 'name', 'account_no', 'bank'); ?>
                                                        </select><br>
                                                        <input type="number" onchange="checkBankBalance(this.value)"
                                                               class="bank form-control"
                                                               oninput="minusValueCheck(this.value,this)" name="bank"
                                                               id="bank">
                                                        <b id="Bank_valid"></b>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Due</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" class="form-control" name="due"
                                                               id="totaldue"
                                                               readonly value="<?php echo Cart()->total() ?>">
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col-xs-12">
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