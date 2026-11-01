<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Settings <small>Settings Update</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Settings Update</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <!-- Nav tabs -->
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active "><a href="#home" aria-controls="home"
                                                                               role="tab" data-toggle="tab">General</a></li>
                                    <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab"
                                                                data-toggle="tab">Photo</a></li>
                                    <li role="presentation" ><a href="#messages" aria-controls="messages" role="tab"
                                                                data-toggle="tab">General Sattings</a></li>
                                    <li role="presentation" ><a href="#register" aria-controls="register" role="tab"
                                                                data-toggle="tab">Vat Register</a></li>
                                    <li role="presentation" ><a href="#invoice-settings" aria-controls="invoice-settings" role="tab"
                                                                data-toggle="tab">Invoice Settings</a></li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content " style="margin-top: 60px;">
                                <?php
                                // label => value map of the current shop's gen_settings rows
                                $invoiceSetting = array();
                                foreach ($gen_setting as $geninfo) {
                                    $invoiceSetting[$geninfo->label] = $geninfo->value;
                                }
                                $invoiceGmailRows = !empty($invoiceSetting['invoice_gmail']) ? explode(',', $invoiceSetting['invoice_gmail']) : array();
                                $invoiceNamePhoneRows = !empty($invoiceSetting['invoice_name_phone']) ? explode(',', $invoiceSetting['invoice_name_phone']) : array();
                                ?>
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    <div class="col-lg-6 ">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Settings/general_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="varchar">Name</label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $stores->name; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="longtext">Email </label>
                                                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $stores->email; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address </label>
                                                <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"><?php echo $stores->address; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="longtext">Mobile</label>
                                                <input type="number" class="form-control" name="mobile" id="mobile" placeholder="Mobile" value="<?php echo $stores->mobile; ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="comment">Comment </label>
                                                <textarea class="form-control" rows="3" name="comment" id="comment" placeholder="Comment"><?php echo $stores->comment; ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="enum">Invoice Template</label>
                                                <select name="invoice" id="invoice" class="form-control">
                                                    <?php print available_invoice(get_gen_settings_by_lavel('invoice')); ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="enum">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <?php print globalStatus($stores->status); ?>
                                                </select>
                                            </div>
                                            <input type="hidden" name="sch_id" value="<?php echo $stores->sch_id; ?>" />

                                            <button type="submit" class="btn btn-primary geniusSubmit-btn"> Update </button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>

                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="profile">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Settings/photo_update'); ?>" method="post">

                                            <div class="form-group">
                                                <label for="longtext">Logo</label>
                                                <input type="file" class="form-control" name="logo" id="logo" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=350px) x (height=100px)</b></span>
                                            </div>

                                            <div class="form-group">
                                                <label for="longtext">Profile Image</label>
                                                <input type="file" class="form-control" name="profile_image" id="profile_image" />
                                                <span class="help-block"><b>Max. file size 1024KB and (width=160px) x (height=160px)</b></span>
                                            </div>

                                            <input type="hidden" name="sch_id" value="<?php echo $stores->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="messages">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Settings/general_settings_update'); ?>" method="post" enctype="multipart/form-data" >
                                            <?php foreach ($gen_setting as $geninfo) { ?>
                                                <div class="form-group">
                                                    <label for="longtext"><?php print $geninfo->label; ?></label>
                                                    <input type="hidden" name="id[]" value="<?php print $geninfo->settings_id; ?>">
                                                    <input type="text" class="form-control" name="value[]" id="" value="<?php print $geninfo->value; ?>" >
                                                </div>
                                                <?php } ?>

                                            <input type="hidden" name="sch_id" value="<?php echo $stores->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="register">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Settings/vat_update'); ?>" method="post" enctype="multipart/form-data" >

                                            <div class="form-group">
                                                <label for="varchar">Name </label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $vat_register->vat_register_no; ?>" required >
                                            </div>
                                            <div class="form-group">
                                                <label for="varchar">Vat Register No </label>
                                                <input type="text" class="form-control" name="vat_register_no" id="vat_register_no" placeholder="Vat Register No" value="<?php echo $vat_register->vat_register_no; ?>" required >
                                            </div>

                                            <input type="hidden" name="vat_id" value="<?php echo $vat_register->vat_id; ?>" >

                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>

                                <div role="tabpanel" class="tab-pane " id="invoice-settings">
                                    <div class="col-lg-6">
                                        <form id="geniusformUpdate" action="<?php echo base_url('Admin/Settings/invoice_settings_update'); ?>" method="post" enctype="multipart/form-data" >

                                            <div class="form-group">
                                                <label for="invoice_title">Invoice Title</label>
                                                <input type="text" class="form-control" name="invoice_title" id="invoice_title" placeholder="Invoice Title" value="<?php echo esc($invoiceSetting['invoice_title'] ?? ''); ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="invoice_sub_title">Invoice Sub Title</label>
                                                <input type="text" class="form-control" name="invoice_sub_title" id="invoice_sub_title" placeholder="Invoice Sub Title" value="<?php echo esc($invoiceSetting['invoice_sub_title'] ?? ''); ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="invoice_bin_no">Bin No</label>
                                                <input type="text" class="form-control" name="invoice_bin_no" id="invoice_bin_no" placeholder="Bin No" value="<?php echo esc($invoiceSetting['invoice_bin_no'] ?? ''); ?>" />
                                            </div>
                                            <div class="form-group">
                                                <label for="invoice_address">Address</label>
                                                <textarea class="form-control" rows="3" name="invoice_address" id="invoice_address" placeholder="Address"><?php echo esc($invoiceSetting['invoice_address'] ?? ''); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="invoice_gmail">Invoice Email (multiple)</label>
                                                <div id="invoice_gmail_rows">
                                                    <?php foreach ((count($invoiceGmailRows) ? $invoiceGmailRows : array('')) as $gmailVal) { ?>
                                                        <div class="input-group" style="margin-bottom: 5px;">
                                                            <input type="text" class="form-control" name="invoice_gmail[]" placeholder="Email" value="<?php echo esc(trim($gmailVal)); ?>" />
                                                            <span class="input-group-btn"><button type="button" class="btn btn-danger" onclick="invoiceMultiRemove(this)"><i class="fa fa-minus"></i></button></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <button type="button" class="btn btn-default btn-sm" onclick="invoiceMultiAdd(this)"><i class="fa fa-plus"></i> Add Email</button>
                                            </div>

                                            <div class="form-group">
                                                <label for="invoice_name_phone">Invoice Name &amp; Phone (multiple)</label>
                                                <div id="invoice_name_phone_rows">
                                                    <?php foreach ((count($invoiceNamePhoneRows) ? $invoiceNamePhoneRows : array('')) as $namePhoneVal) { ?>
                                                        <div class="input-group" style="margin-bottom: 5px;">
                                                            <input type="text" class="form-control" name="invoice_name_phone[]" placeholder="Name : Phone" value="<?php echo esc(trim($namePhoneVal)); ?>" />
                                                            <span class="input-group-btn"><button type="button" class="btn btn-danger" onclick="invoiceMultiRemove(this)"><i class="fa fa-minus"></i></button></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <button type="button" class="btn btn-default btn-sm" onclick="invoiceMultiAdd(this)"><i class="fa fa-plus"></i> Add Name &amp; Phone</button>
                                            </div>

                                            <div class="form-group">
                                                <label for="invoice_footer_text">Footer Text</label>
                                                <textarea class="form-control" rows="3" name="invoice_footer_text" id="invoice_footer_text" placeholder="Footer Text"><?php echo esc($invoiceSetting['invoice_footer_text'] ?? ''); ?></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="invoice_logo">Invoice Logo</label>
                                                <input type="file" class="form-control" name="invoice_logo" id="invoice_logo" accept="image/*" />
                                                <span class="help-block"><b>Max. file size 1024KB</b></span>
                                                <img src="<?php echo no_image_view('/uploads/schools/' . ($invoiceSetting['invoice_logo'] ?? ''), '/uploads/schools/no_image_logo.jpg', $invoiceSetting['invoice_logo'] ?? ''); ?>" alt="Invoice Logo" style="max-width: 200px; margin-top: 10px;" />
                                            </div>

                                            <div class="form-group">
                                                <label for="invoice_background_image">Invoice Background Image</label>
                                                <input type="file" class="form-control" name="invoice_background_image" id="invoice_background_image" accept="image/*" />
                                                <span class="help-block"><b>Max. file size 1024KB</b></span>
                                                <img src="<?php echo no_image_view('/uploads/schools/' . ($invoiceSetting['invoice_background_image'] ?? ''), '/uploads/schools/no_image_logo.jpg', $invoiceSetting['invoice_background_image'] ?? ''); ?>" alt="Invoice Background Image" style="max-width: 200px; margin-top: 10px;" />
                                            </div>

                                            <input type="hidden" name="sch_id" value="<?php echo $stores->sch_id; ?>" />
                                            <button type="submit" class="btn btn-primary geniusSubmit-btn">Update</button>
                                            <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/Settings_ajax/'); ?>','<?php echo '/Admin/Settings/'; ?>')" class="btn btn-default">Cancel</a>
                                        </form>
                                    </div>
                                    <div class="col-lg-6"></div>
                                </div>
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