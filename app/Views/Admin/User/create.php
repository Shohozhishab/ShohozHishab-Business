<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> User <small>User Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">User </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 15px;">
                <a href="#" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/';?>')"  class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

                <a href="#" onclick="showData('<?php echo site_url('/Admin/Role_ajax') ?>','/Admin/Role')" class="btn btn-default">User Role</a>
            </div>
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">User Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div id="message"></div>
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            <div class="col-lg-6" >
                                <form id="geniusform" action="<?php echo $action; ?>" method="post">

                                    <div class="form-group">
                                        <label for="varchar">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Email</label>
                                        <input type="text" class="form-control" name="email" id="email" placeholder="Email" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="varchar">Password </label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                        <div class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="longtext">Confirm Password </label>
                                        <input type="password" class="form-control" name="con_password" id="con_password" placeholder="Confirm Password" required>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="enum">Role </label>
                                        <select class="form-control" name="role_id" id="role_id" required>
                                            <option value="">Please Select</option>
                                            <?php echo getRoleIdListInOption('','role_id','role','roles'); ?>
                                        </select>
                                        <div class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="enum">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <?php print globalStatus(''); ?>
                                        </select>
                                        <div class="error"></div>
                                    </div>


                                    <button type="button" class="btn btn-primary" onclick="userValidat()" >Register</button>
                                    <a href="javascript:void(0)" onclick="showData('<?php echo site_url('/Admin/User_ajax/'); ?>','<?php echo '/Admin/User/'; ?>')" class="btn btn-default">Cancel</a>
                                </form>
                            </div>


                            <div class="col-lg-6" style="border-left: 1px solid #cecdcd;">

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