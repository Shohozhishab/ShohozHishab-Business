
<?php
$curUrl = current_url(true);
//$curUrl = $current_url;
$url = new \CodeIgniter\HTTP\URI($curUrl);


$uri = $url->getSegment(3);
$uri2 = $url->getSegment(4);
$uri3 = $url->getSegment(4);



$saleBack = 'display: none';
$retSaleBack = 'display: none';
$sale = '';
$retSale = '';

if (($uri == 'Sales') && ($uri3 == 'create')  || ($uri2 == 'create') ) { $saleBack = ''; $sale = 'display: none';}
if (($uri == 'Sales_ajax') && ($uri3 == 'create')  || ($uri2 == 'create') ) { $saleBack = ''; $sale = 'display: none';}


if ($uri == 'Return_sale') { $retSale = 'display: none';}
if (($uri == 'Return_sale') && ($uri2 == 'invoice_search') || ($uri2 == 'return') ) {$retSaleBack =''; $retSale = 'display: none'; }
if ($uri == 'Return_sale_ajax') { $retSale = 'display: none';}
if (($uri == 'Return_sale_ajax') && ($uri2 == 'invoice_search') || ($uri2 == 'return') ) {$retSaleBack =''; $retSale = 'display: none'; }

?>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Return_sale_ajax'); ?>','<?php echo '/Admin/Return_sale';?>')" class="btn btn-default" style="<?php echo $retSaleBack ;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>


<a href="#" onclick="showData('<?php echo site_url('/Admin/Sales_ajax/create/'); ?>','<?php echo '/Admin/Sales/create/';?>')"  class="btn btn-default" style="<?php echo $sale;?>" >Sales</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Sales_ajax'); ?>','<?php echo '/Admin/Sales';?>')" class="btn btn-default" style="<?php echo $saleBack ;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>



<a href="#" onclick="showData('<?php echo site_url('/Admin/Return_sale_ajax'); ?>','<?php echo '/Admin/Return_sale';?>')" class="btn btn-default" style="<?php echo $retSale;?>" >Return Sale</a>
