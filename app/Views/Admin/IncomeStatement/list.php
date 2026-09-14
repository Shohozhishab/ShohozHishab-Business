<div class="content-wrapper" id="viewpage">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Income Statement <small>Income Statement</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Income Statement</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if (isDefaultRole() == true){ ?>
            <div class="row" id="reloadRoleDiv">
                <div class="col-lg-12" >
                    <button class="btn btn-sm btn-info " style="float: right;" onclick="rollPermissionBtn()">Roll Permission</button>
                </div>
                <div class="col-lg-12" id="permissionDiv" style="display: none; margin-top: 20px">
                    <form id="roleUpdateform" action="<?= base_url('Admin/Role/modulePermissionAction')?>" method="post">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" onchange="rolePermission(this.value,'IncomeStatement')" name="role_id">
                                            <option value="">Please Select</option>
                                            <?php  foreach (userRole() as $val ){ ?>
                                                <option value="<?= $val->role_id;?>"><?= $val->role;?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="moduleName" value="IncomeStatement">
                                    </div>
                                    <div class="col-md-12" id="rolView"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="margin-top: 20px;">

            <?php if (isset($filter) && $filter == 1){ ?>
                <div class="col-xs-12" >
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter </h3>
                        </div>
                        <div class="box-body">
                                <div class="row">
                                    <form action="<?= base_url('Admin/Income_statement') ?>" method="get" >
                                        <div class="col-md-2">
                                            <label>Days</label>
                                            <select class="form-control" onchange="this.form.submit()" name="period">
                                                <option value="0">Please Select</option>
                                                <option value="7" <?= ($days == 7) ? 'selected' : '' ?>>Last 7 Days</option>
                                                <option value="30" <?= ($days == 30) ? 'selected' : '' ?>>Last 30 Days</option>
                                            </select>
                                        </div>
                                    </form>

                                    <form action="<?= base_url('Admin/Income_statement') ?>" method="get" >
                                        <div class="col-md-2">
                                            <label>Months</label>
                                            <select class="form-control" onchange="this.form.submit()" name="month">
                                                <option value="">Please Select</option>
                                                <option value="1"  <?= (isset($month) && $month == 1)  ? 'selected' : '' ?>>January</option>
                                                <option value="2"  <?= (isset($month) && $month == 2)  ? 'selected' : '' ?>>February</option>
                                                <option value="3"  <?= (isset($month) && $month == 3)  ? 'selected' : '' ?>>March</option>
                                                <option value="4"  <?= (isset($month) && $month == 4)  ? 'selected' : '' ?>>April</option>
                                                <option value="5"  <?= (isset($month) && $month == 5)  ? 'selected' : '' ?>>May</option>
                                                <option value="6"  <?= (isset($month) && $month == 6)  ? 'selected' : '' ?>>June</option>
                                                <option value="7"  <?= (isset($month) && $month == 7)  ? 'selected' : '' ?>>July</option>
                                                <option value="8"  <?= (isset($month) && $month == 8)  ? 'selected' : '' ?>>August</option>
                                                <option value="9"  <?= (isset($month) && $month == 9)  ? 'selected' : '' ?>>September</option>
                                                <option value="10" <?= (isset($month) && $month == 10) ? 'selected' : '' ?>>October</option>
                                                <option value="11" <?= (isset($month) && $month == 11) ? 'selected' : '' ?>>November</option>
                                                <option value="12" <?= (isset($month) && $month == 12) ? 'selected' : '' ?>>December</option>
                                            </select>
                                        </div>
                                    </form>

                                    <form action="<?= base_url('Admin/Income_statement') ?>" method="get">
                                        <div class="col-md-2">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="st_date"  id="st_date" value="<?= $st_date; ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="en_date" id="en_date" value="<?= $en_date; ?>" required>
                                        </div>

                                        <div class="col-md-2" style="margin-top: 25px;">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i>
                                                Filter
                                            </button>
                                        </div>
                                        <div class="col-md-2" style="margin-top: 25px;">
                                            <a href="<?= base_url('Admin/Income_statement') ?>" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</a>
                                        </div>
                                    </form>
                                </div>

                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="box-title">Income Statement</h3>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3>Report (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' 1 Month'; } elseif (!empty($days)) { echo 'Last ' .$days . ' Days'; } ?>)</h3>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Purchase</td>
                                            <td><?= showWithCurrencySymbol($purchaseAmount); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Sales</td>
                                            <td><?= showWithCurrencySymbol($sales); ?></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <h3>Report (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' 1 Month'; } elseif (!empty($days)) { echo 'Last ' .$days . ' Days'; } ?>)</h3>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Revenue
                                                <div class="tooltip-container" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 20 20" fill="none" style="vertical-align: middle;">
                                                        <path d="M10.0013 13.3334V10M10.0013 6.66669H10.0096M18.3346 10C18.3346 14.6024 14.6037 18.3334 10.0013 18.3334C5.39893 18.3334 1.66797 14.6024 1.66797 10C1.66797 5.39765 5.39893 1.66669 10.0013 1.66669C14.6037 1.66669 18.3346 5.39765 18.3346 10Z" stroke="#A7A7A7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    <span class="tooltip-text">Sales Profit + Service Charge + Others Income</span>
                                                </div>
                                            </td>
                                            <td><?= showWithCurrencySymbol($profit); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Expenses</td>
                                            <td><?= showWithCurrencySymbol($expenses); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Profit/Loss</td>
                                            <?php $profitLoss = $profit - $expenses;?>
                                            <td>
                                                <?php if ($profitLoss >= 0){ ?>
                                                    <span style="color: green;font-weight: bold;"><?= showWithCurrencySymbol($profitLoss)?></span>
                                                <?php }else{ ?>
                                                    <span style="color: red;font-weight: bold;"><?= showWithCurrencySymbol($profitLoss)?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12">
                                <?php if (isset($print) && $print == 1){ ?>
                                    <button onclick="printDiv('incomePrint')" class="print_line btn btn-primary pull-right"><i class="fa fa-print "></i> Print Now </button>
                                <?php } ?>
                                <?php if (isset($download_PDF) && $download_PDF == 1){ ?>
                                    <button type="button" class="btn btn-info pull-right" style="margin-right: 10px;" onclick="downloadPDF('incomePrint','cash')"><i class="fa fa-file-pdf-o "></i> Download PDF </button>
                                <?php } ?>
                                <?php if (isset($download_CSV) && $download_CSV == 1){ ?>
                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 10px;" onclick="downloadCSV('incomePrint','cash')"><i class="fa fa-file-excel-o "></i> Download CSV</button>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="col-md-12" id="incomePrint" style="display: none; text-transform: capitalize; " >
                        <div class="col-xs-12 " style="margin-bottom: 20px;   ">
                            <div class="col-xs-6">
                                <?php if(logo_image() == NULL){ ?>
                                    <img src="<?php echo base_url() ?>/uploads/schools/no_image.jpg" alt="User Image" >
                                <?php }else{ ?>
                                    <img src="<?php echo base_url(); ?>/uploads/schools/<?php echo logo_image(); ?>" class="" alt="User Image">
                                <?php } ?>
                            </div>
                            <div class="col-xs-6">
                                <?php print address(); ?>
                            </div>
                        </div>
                        <div class="col-md-12 " id="incomePrintCss">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="box-title">Income Statement</h3>
                                    <h5>Report Of (<?= $st_date; ?> To <?= $en_date;?>)</h5>
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Purchase</td>
                                                <td><?= showWithCurrencySymbol($purchaseAmount); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Sales</td>
                                                <td><?= showWithCurrencySymbol($sales); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-12" style="float: right;width: 50%;">
                                    <table class="table table-bordered table-striped" style="width: 200%;">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Revenue</td>
                                                <td><?= showWithCurrencySymbol($profit); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Expenses</td>
                                                <td><?= showWithCurrencySymbol($expenses); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Profit/Loss</td>
                                                <td><?= showWithCurrencySymbol($profitLoss); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box -->

            </div>

            <div class="col-xs-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Report Chart</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div style="max-width: 400px; margin: 20px auto;">
                            <canvas id="financeDoughnut"></canvas>
                        </div>

                        <script>
                            const doughnutCtx = document.getElementById('financeDoughnut').getContext('2d');

                            new Chart(doughnutCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Sales', 'Purchase', 'Profit', 'Expenses'],
                                    datasets: [{
                                        data: [
                                            <?= $sales ?? 0 ?>,
                                            <?= $purchaseAmount ?? 0 ?>,
                                            <?= $profitLoss ?? 0 ?>,
                                            <?= $expenses ?? 0 ?>
                                        ],
                                        backgroundColor: [
                                            '#36A2EB',
                                            '#FF6384',
                                            '#4BC0C0',
                                            '#FF9F40'
                                        ],
                                        borderWidth: 2,
                                        borderColor: '#fff',
                                        hoverOffset: 12
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                padding: 15,
                                                font: { size: 13 }
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Last <?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' (1 Month)'; } elseif (!empty($days)) { echo $days . ' Days'; } ?>  Overview',
                                            font: { size: 15 }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.label + ': ' + context.raw.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    cutout: '55%'
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Report Chart</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div style="max-width: 700px; margin: 20px auto;">
                            <canvas id="financeBarChart"></canvas>
                        </div>

                        <script>
                            const barCtx = document.getElementById('financeBarChart').getContext('2d');

                            new Chart(barCtx, {
                                type: 'bar',
                                data: {
                                    labels: ['Sales', 'Purchase', 'Profit', 'Expenses'],
                                    datasets: [{
                                        label: 'Amount',
                                        data: [
                                            <?= $sales ?? 0 ?>,
                                            <?= $purchaseAmount ?? 0 ?>,
                                            <?= $profitLoss ?? 0 ?>,
                                            <?= $expenses ?? 0 ?>
                                        ],
                                        backgroundColor: [
                                            'rgba(54, 162, 235, 0.8)',
                                            'rgba(255, 99, 132, 0.8)',
                                            'rgba(75, 192, 192, 0.8)',
                                            'rgba(255, 159, 64, 0.8)',
                                        ],
                                        borderColor: [
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(255, 159, 64, 1)',
                                            'rgba(153, 102, 255, 1)'
                                        ],
                                        borderWidth: 1,
                                        borderRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { display: false },
                                        title: {
                                            display: true,
                                            text: 'Financial Overview (<?php if (!empty($month)) { echo date('F', mktime(0, 0, 0, $month, 1)) . ' 1 Month'; } elseif (!empty($days)) { echo $days . ' Days'; } ?>)',
                                            font: { size: 15 },
                                            padding: { bottom: 15 }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.parsed.y.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString();
                                                }
                                            }
                                        },
                                        x: {
                                            grid: { display: false }
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>

