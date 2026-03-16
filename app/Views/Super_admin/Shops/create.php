<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Shops <small>Shops Create</small> </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Shops</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                         <h3 class="box-title">Shop Create</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                            <?php if (session()->getFlashdata('message') !== NULL) : echo session()->getFlashdata('message'); endif; ?>
                            </div>
                            <div class="col-lg-6">
                                <form action="<?php echo $action; ?>" method="post">
                                    <div class="form-group">
                                        <label for="varchar">Name </label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="int">Email </label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="longtext">Password </label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="longtext">Confirm Password</label>
                                        <input type="password" class="form-control" name="con_password" id="password" placeholder="Confirm Password" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="enum">Status </label>
                                        <select class="form-control" name="status" id="status">
                                            <?php //print globalStatus($status); ?>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Create</button>
                                    <a href="<?php echo site_url('Super_admin/Shops') ?>" class="btn btn-default">Cancel</a>
                                </form>
                            </div>
                            <div class="col-lg-6"></div>
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