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

                            <div class="col-lg-12">
                                <div class="box" style="padding: 20px;">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                    <span class="input-group-addon " style="background-color:#367FA9; ">
                                        <i class="fa fa-pencil-square-o fa-lg" style="color: white;"></i>
                                    </span>
                                            <input type="text" class="form-control input-lg" onkeypress="findResultPurchase()"
                                                   name="keyWord" id="keyWord" value="">

                                            <span class="input-group-btn">
                                      <button class="btn btn-primary btn-lg" type="submit">Search</button>
                                    </span>
                                        </div>

                                    </div>
                                    <div class="input-group col-md-12">
                                        <ul style="list-style-type:none;" id="result"></ul>
                                    </div>
                                </div>
                            </div>

                        <form action="<?php echo $action; ?>" method="post">

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
                                            <th>Quantity</th>
                                            <th>Purchase Price</th>
                                            <th>Sale Price</th>
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
                                        foreach (Cart()->contents() as $row) { $unitId = productIdByDefaultStoreUnit($row['id']); ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td>
                                                    <?php echo $row['name']; ?>
                                                    <input type="hidden" class="form-control " name="productId[]"
                                                           value="<?php echo $row['id']; ?>">
                                                </td>
                                                <td>
                                                    <?php echo unitOrQtyByUnitQty($unitId,$row['qty']); ?>/<?php echo showUnitName($unitId) ?>
                                                    <input type="hidden" class="form-control " name="qty[]" value="<?php echo $row['qty']; ?>">
                                                    <input type="hidden" class="form-control " name="unit[]" value="<?php echo $unitId; ?>">
                                                </td>
                                                <td>
                                                    <input type="hidden" class="form-control upprice" id="qtyUp_<?= $row['id']; ?>" name="price[]" value="<?php echo $row['price']; ?>">
                                                    <?php
                                                    $uPrice = unitOrBasePriceByUnitPrice($unitId,$row['price']);
                                                    $conversion_factor = get_data_by_id('conversion_factor', 'units', 'units_id', $unitId);
                                                    ?>
                                                    <input type="hidden"  name="conversion_factore[]" value="<?= $conversion_factor; ?>">
                                                    <input type="text" class="form-control" name="unitPrice[]" oninput="priceMakeBasePurchase(this.value,'<?= $conversion_factor;?>','<?= $row['id'];?>' )" value="<?= $uPrice ?>">

                                                </td>
                                                <td>
                                                    <?php
                                                        $uSalePrice = unitOrBasePriceByUnitPrice($unitId,$row['sale_price']);
                                                    ?>
                                                    <input type="hidden" class="form-control" id="salePrice_<?= $row['id']; ?>" name="salePrice[]" value="<?php echo $row['sale_price']; ?>">
                                                    <input type="text" class="form-control" name="salePrice[]" oninput="priceMakeBaseSalePrice(this.value,'<?= $conversion_factor;?>','<?= $row['id'];?>' )" value="<?= $uSalePrice ?>">
                                                </td>
                                                <td>
                                                    <input type="hidden" readonly class="form-control subtotal" name="subtotal[]" id="subt_<?php print $m++; ?>" value="<?php echo $row['subtotal'] ?>">
                                                    <input type="hidden" name="suballtotal[]" id="subtl2_<?php print $k++; ?>" value="<?php echo $row['subtotal']; ?>">
                                                    <span id="subtl_<?php print $l++; ?>">
                                                    <span id="subtl_<?php print $j++; ?>">
                                                     <?php echo number_format($row['subtotal']); ?>
                                                    </span>
                                                </span>

                                                </td>
                                                <td width="120px">
                                                    <a href="<?php echo site_url('/Admin/Purchase/removeCart/' . $row['rowid']); ?>"
                                                       onclick="javasciprt: return confirm('Are You Sure ?')"
                                                       class="btn btn-danger btn-xs">Cancel</a>
                                                </td>
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
                                                        <label for="int">Purchase Date</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <label for="int">Supplier</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <select class="form-control" name="supplier_id" id="supData" required >
                                                            <option value="">Please select</option>
                                                            <?php echo getAllListInOptionWithStatus('','supplier_id','name','suppliers','name'); ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Total Amount</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" class="form-control" name="totalPrice"
                                                               id="totalPrice" value="<?php echo round(Cart()->total(),2) ?>"
                                                               readonly>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label for="int">Cash</label>
                                                    </div>
                                                    <div class="form-group col-xs-6">
                                                        <input type="number" onchange="checkShopsBalance(this.value)" class="cash form-control" oninput="minusValueCheck(this.value,this)" name="cash" id="cash">
                                                        <b id="Balance_valid"></b>
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
                                                               readonly value="<?php echo round(Cart()->total(),2) ?>">
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col-xs-12">
                                                <div class="form-group form-check">
                                                    <input type="checkbox" class="form-check-input" name="sms" id="sms">
                                                    <label class="form-check-label" for="sms">Send SMS</label>
                                                </div>

                                                <button type="submit" class="btn btn-primary" id="createBtn">Purchase </button>
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