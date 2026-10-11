
<?php
$curUrl = current_url(true);
$url = new \CodeIgniter\HTTP\URI($curUrl);


$uri = $url->getSegment(3);
$uri2 = $url->getSegment(4);
$uri3 = $url->getSegment(4);


$purBack = 'display: none';
$retPurBack = 'display: none';
$purchase = '';
$retPurchase = '';

if (($uri == 'Purchase') && ($uri3 == 'create')  || ($uri2 == 'create') ) { $purBack = ''; $purchase = 'display: none';}
if (($uri == 'Purchase_ajax') && ($uri3 == 'create')  || ($uri2 == 'create') ) { $purBack = ''; $purchase = 'display: none';}


if ($uri == 'Return_purchase') { $retPurchase = 'display: none';}
if (($uri == 'Return_purchase') && ($uri2 == 'purchase_search') || ($uri2 == 'return') ) {$retPurBack =''; $retPurchase = 'display: none'; }
if ($uri == 'Return_purchase_ajax') { $retPurchase = 'display: none';}
if (($uri == 'Return_purchase_ajax') && ($uri2 == 'purchase_search') || ($uri2 == 'return') ) {$retPurBack =''; $retPurchase = 'display: none'; }

?>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Return_purchase_ajax'); ?>','<?php echo '/Admin/Return_purchase';?>')" class="btn btn-default" style="<?php echo $retPurBack ;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax/'); ?>','<?php echo '/Admin/Purchase/'?>')"  class="btn btn-default" style="<?php echo $purchase;?>" >Purchase</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Purchase_ajax'); ?>','<?php echo '/Admin/Purchase'?>')" class="btn btn-default" style="<?php echo $purBack ;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Return_purchase_ajax'); ?>','<?php echo '/Admin/Return_purchase'?>')" class="btn btn-default" style="<?php echo $retPurchase;?>" >Return Purchase</a>
