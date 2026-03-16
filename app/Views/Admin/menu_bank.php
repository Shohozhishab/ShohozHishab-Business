
<?php
$curUrl = current_url(true);
//$curUrl = $current_url;
$url = new \CodeIgniter\HTTP\URI($curUrl);


$uri = $url->getSegment(3);
$uri2 = $url->getSegment(4);
$uri3 = $url->getSegment(4);



$bankBack ='display: none';
$bankAdd ='display: none';
$bank ='';

if ($uri == 'Bank') { $bank ='display: none'; $bankAdd = '';}
if (($uri == 'Bank') && ($uri2 == 'create') || ($uri == 'Bank') && ($uri3 == 'create') || ($uri == 'Bank') && ($uri2 == 'read') || ($uri == 'Bank') && ($uri3 == 'read') || ($uri == 'Bank') && ($uri2 == 'update') || ($uri == 'Bank') && ($uri3 == 'update')){ $bank ='display: none'; $bankAdd = 'display: none'; $bankBack ='';}

if ($uri == 'Bank_ajax') { $bank ='display: none'; $bankAdd = '';}
if (($uri == 'Bank_ajax') && ($uri2 == 'create') || ($uri == 'Bank_ajax') && ($uri3 == 'create') || ($uri == 'Bank_ajax') && ($uri2 == 'read') || ($uri == 'Bank_ajax') && ($uri3 == 'read') || ($uri == 'Bank_ajax') && ($uri2 == 'update') || ($uri == 'Bank_ajax') && ($uri3 == 'update')){ $bank ='display: none'; $bankAdd = 'display: none'; $bankBack ='';}




$depositBack ='display: none';
$depositAdd ='display: none';
$deposit ='';
if ($uri == 'Bank_deposit') { $deposit ='display: none'; $depositAdd = '';}
if (($uri == 'Bank_deposit') && ($uri2 == 'create') || ($uri == 'Bank_deposit') && ($uri3 == 'create') || ($uri == 'Bank_deposit') && ($uri2 == 'read') || ($uri == 'Bank_deposit') && ($uri3 == 'read') || ($uri == 'Bank_deposit') && ($uri2 == 'update') || ($uri == 'Bank_deposit') && ($uri3 == 'update')){ $deposit ='display: none'; $depositAdd = 'display: none'; $depositBack ='';}

if ($uri == 'Bank_deposit_ajax') { $deposit ='display: none'; $depositAdd = '';}
if (($uri == 'Bank_deposit_ajax') && ($uri2 == 'create') || ($uri == 'Bank_deposit_ajax') && ($uri3 == 'create') || ($uri == 'Bank_deposit_ajax') && ($uri2 == 'read') || ($uri == 'Bank_deposit_ajax') && ($uri3 == 'read') || ($uri == 'Bank_deposit_ajax') && ($uri2 == 'update') || ($uri == 'Bank_deposit_ajax') && ($uri3 == 'update')){ $deposit ='display: none'; $depositAdd = 'display: none'; $depositBack ='';}

$withdrawBack ='display: none';
$withdrawAdd ='display: none';
$withdraw ='';

if ($uri == 'bank_withdraw') { $withdraw ='display: none'; $withdrawAdd = '';}
if (($uri == 'Bank_withdraw') && ($uri2 == 'create') || ($uri == 'Bank_withdraw') && ($uri3 == 'create') || ($uri == 'Bank_withdraw') && ($uri2 == 'read') || ($uri == 'Bank_withdraw') && ($uri3 == 'read') || ($uri == 'Bank_withdraw') && ($uri2 == 'update') || ($uri == 'Bank_withdraw') && ($uri3 == 'update')){ $withdraw ='display: none'; $withdrawAdd = 'display: none'; $withdrawBack ='';}

if ($uri == 'Bank_withdraw_ajax') { $withdraw ='display: none'; $withdrawAdd = '';}
if (($uri == 'Bank_withdraw_ajax') && ($uri2 == 'create') || ($uri == 'Bank_withdraw_ajax') && ($uri3 == 'create') || ($uri == 'Bank_withdraw_ajax') && ($uri2 == 'read') || ($uri == 'Bank_withdraw_ajax') && ($uri3 == 'read') || ($uri == 'Bank_withdraw_ajax') && ($uri2 == 'update') || ($uri == 'Bank_withdraw_ajax') && ($uri3 == 'update')){ $withdraw ='display: none'; $withdrawAdd = 'display: none'; $withdrawBack ='';}

?>

<a href="#" onclick="showData('<?php echo site_url('Admin/Bank_ajax/create/'); ?>','<?php echo '/Admin/Bank/create/';?>')"  class="btn btn-default" style="<?php echo $bankAdd;?>">Add</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_ajax') ?>','/Admin/Bank/')" class="btn btn-default" style="<?php echo $bankBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>





<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_deposit_ajax') ?>','/Admin/Bank_deposit')" class="btn btn-default" style="<?php echo $depositBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_deposit_ajax/create') ?>','/Admin/Bank_deposit/create')"class="btn btn-default"  style="<?php echo $depositAdd;?>">Deposit</a>



<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_withdraw_ajax/create') ?>','/Admin/Bank_withdraw/create')"class="btn btn-default"  style="<?php echo $withdrawAdd;?>">Withdraw</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_withdraw_ajax') ?>','/Admin/Bank_withdraw')"class="btn btn-default"  style="<?php echo $withdrawBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>



<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_ajax') ?>','/Admin/Bank')" class="btn btn-default" style="<?php echo $bank;?>">Bank</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_deposit_ajax') ?>','/Admin/Bank_deposit')" class="btn btn-default" style="<?php echo $deposit;?>">Deposit</a>




<a href="#" onclick="showData('<?php echo site_url('/Admin/Bank_withdraw_ajax') ?>','/Admin/Bank_withdraw')" class="btn btn-default" style="<?php echo $withdraw;?>">Withdraw</a>



<a href="#" onclick="showData('<?php echo site_url('/Admin/Chaque_ajax') ?>','/Admin/Chaque')" class="btn btn-default">Chaque</a>