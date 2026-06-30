<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Expenses <small>Expenses Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Expenses </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Expenses Create </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">
                                    <h4>New Expenses</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="enum">Assets Sub Type </label>
                                        <select class="form-control input" name="sub_type_id" id="sub_type" >
                                            <option value="">Please select</option>
                                            <?php foreach ($subType as $val){ ?>
                                            <option value="<?= $val->account_type_id?>"><?= $val->type_name?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-create">Create new</a>
                                    </div>
                                    <input type="hidden" name="account_type_id" value="<?= $assetsType;?>">
                                    <button type="submit" class="btn btn-primary" >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Expenses_ajax/'); ?>','<?php echo '/Admin/Expenses/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">
                                <form id="geniusform3" action="<?php echo $actionExisting; ?>" method="post">
                                    <h4>Existing Expenses</h4>
                                    <div class="form-group">
                                        <label for="varchar">Name </label>
                                        <input type="text" class="form-control" name="name" id="name_ex" placeholder="Name" required/>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="enum">Assets Sub Type </label>
                                        <select class="form-control input" name="sub_type_id" id="sub_type2" >
                                            <option value="">Please select</option>
                                            <?php foreach ($subType as $val){ ?>
                                                <option value="<?= $val->account_type_id?>"><?= $val->type_name?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#modal-create">Create new</a>
                                    </div>

                                    <div class="form-group " id="chaque">
                                        <label for="int">Amount </label>
                                        <input type="number" class="form-control input" name="amount" id="amount" oninput="minusValueCheck(this.value,this)" placeholder="Amount"
                                               required />
                                        <div class="error"></div>
                                    </div>
                                    <input type="hidden" name="account_type_id" value="<?= $assetsType;?>">
                                    <button type="submit" class="btn btn-primary" >Create</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Expenses_ajax/'); ?>','<?php echo '/Admin/Expenses/'; ?>')" class="btn btn-default">Cancel</a>
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

        <!--customer modal-->
        <div class="modal fade" id="modal-create">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">New Assets Type</h4>
                    </div>
                    <form id="typeform" action="<?= base_url('Admin/Assets/type_action');?>" method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="varchar">Assets Sub Type </label>
                                        <input type="text" class="form-control" name="sub_type" id="sub_type" placeholder="Type" required />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="account_type_id" value="<?= $assetsType;?>">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" >Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--customer modal-->

    </section>
    <!-- /.content -->
</div>