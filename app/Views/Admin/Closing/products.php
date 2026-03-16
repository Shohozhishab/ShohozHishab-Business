<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Products balance update  <small>Products balance update</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Products balance update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-12">
                <?php echo $menu;?>
            </div>
            <div class="col-lg-12" style="margin-top: 20px;">
                <div id="message"></div>
                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
            </div>

            <div class="col-lg-8" style="margin-top: 15px;">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Products balance update</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>

                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" >
                        <form action="<?php echo site_url('Admin/Closing/product_update') ?>" method="POST">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title"><i class="fa fa-clock-o"></i> Product update</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="col-xs-12" id="box_form">
                                                <div class="form-group col-xs-12">
                                                    <div class="form-group col-xs-6">
                                                        <label for="int">Suppliers </label>
                                                        <select class="form-control select2 select2-hidden-accessible"
                                                                style=" width: 100%;" tabindex="-1" aria-hidden="true"
                                                                name="supplier_id" required>
                                                            <option selected="selected" value="">Please Select</option>
                                                            <?php echo getAllListInOption('', 'supplier_id', 'name', 'suppliers'); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-xs-3">
                                                    <label for="int">Category </label>
                                                    <select class="form-control" onchange="showSubCategory(this.value)"
                                                            name="category" id="category">
                                                        <option value="">Please Select</option>
                                                        <?php echo getCatListInOption('', 'prod_cat_id', 'product_category', 'product_category'); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-3">
                                                    <label for="int">Sub Category </label>
                                                    <select class="form-control" name="sub_category" id="subCat">
                                                        <option value="">Please Select</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-3">
                                                    <label for="varchar">Name </label>
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
                                                    <label for="int">Purchase Price </label>
                                                    <input type="number" step=any class="form-control purchase_price"
                                                           oninput="minusValueCheck(this.value,this)" name="price" id="price"
                                                           placeholder="Purchase Price"/>
                                                </div>
                                                <div class="form-group col-xs-3">
                                                    <label for="int">Selling Price </label>
                                                    <input type="number" step=any class="form-control selling_price"
                                                           oninput="minusValueCheck(this.value,this)" name="selling_price"
                                                           id="selling_price" placeholder="Selling Price"/>
                                                </div>
                                                <div class="form-group col-xs-3">
                                                    <label for="int">Quantity </label>
                                                    <input type="number"  class="form-control quantity"
                                                           oninput="minusValueCheck(this.value,this)" name="qty"
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
                                </div>


                                <div class="col-xs-12">
                                    <div class="box" id="box_bac">
                                        <div class="box-header">
                                            <h3 class="box-title">Cart Product List</h3>
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
                                                        <td><input type="hidden" class="form-control" name="name[]" id="name"
                                                                   value="<?php echo $row['name']; ?>"/><?php echo $row['name']; ?>
                                                        </td>
                                                        <td><input type="hidden" class="form-control" name="unit[]" id="unit"
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
                                                        <td><input type="hidden" class="form-control quantity" name="quantity[]"
                                                                   placeholder="Quantity"
                                                                   value="<?php echo $row['qty']; ?>"/><?php echo $row['qty']; ?>
                                                        </td>
                                                        <td><input type="hidden" class="form-control" name="total_price[]"
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
                                                        <button style="float: right; margin-right: 40px;" onclick="clearCart()"
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
                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-primary geniusSubmit-btn" style="float: right;">Update
                                    </button>
                                </div>

                            </div>

                        </form>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-4" style="margin-top: 15px;">
                <div class="box">
                    <div class="box-body">
                        <form id="geniusform" action="<?php echo site_url('/Admin/Closing/pro_bulk_action'); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="int">Bulk upload</label>
                                <input type="file" accept=".csv" class="form-control" name="file" required>
                                <small>support type: .csv</small><br>
                                <small><a href="<?php echo base_url()?>/xlStracture/product.csv">Download sheets</a></small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary geniusSubmit-btn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
