<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Bank balance update  <small>Bank balance update</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Bank balance update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-12">
                <?php echo $menu;?>
            </div>

            <div class="col-lg-12" style="margin-top: 15px;">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-9">
                                <h3 class="box-title">Bank balance update</h3>
                            </div>
                            <div class="col-lg-3">
                            </div>
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <div id="message"></div>
                                <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                        </div>


                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" >
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <form id="geniusform2" method="POST" action="<?php echo site_url('/Admin/Closing/bank_action');?>">
                                <div class="form-group">
                                    <label for="int">Bank</label>
                                    <select class="form-control select2 select2-hidden-accessible" style=" width: 100%;" tabindex="-1" aria-hidden="true" name="bank_id" >
                                        <option selected="selected"  value="">Please Select</option>
                                        <?php echo getTwoValueInOption('','bank_id','name','account_no','bank'); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="particulars">Particulars </label>
                                    <textarea class="form-control" rows="3" name="particulars" id="particulars" placeholder="Particulars" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="enum">Trangaction Type </label>
                                    <select class="form-control" name="trangaction_type" onchange="changepaymenttype(this.value)" id="trangaction_type">
                                        <option value="2">জমা (Dr.)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="varchar">Amount</label>
                                    <input type="number" step=any class="form-control" name="amount" id="amount" oninput="minusValueCheck(this.value,this)" placeholder="Amount" value="" required />
                                </div>

                                <button type="submit" class="btn btn-primary geniusSubmit-btn2">Update</button>

                            </form>
                        </div>
                        <div class="col-md-4"></div>
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
