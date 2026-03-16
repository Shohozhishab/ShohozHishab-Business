
<?php
$curUrl = current_url(true);
//$curUrl = $current_url;
$url = new \CodeIgniter\HTTP\URI($curUrl);


$uri = $url->getSegment(3);
$uri2 = $url->getSegment(4);
$uri3 = $url->getSegment(4);



$serviceBack ='display: none';
$serviceAdd ='display: none';
$service ='';

if ($uri == 'Service') { $service ='display: none'; $serviceAdd = '';}
if (($uri == 'Service') && ($uri2 == 'create') || ($uri == 'Service') && ($uri3 == 'create') || ($uri == 'Service') && ($uri2 == 'read') || ($uri == 'Service') && ($uri3 == 'read') || ($uri == 'Service') && ($uri2 == 'update') || ($uri == 'Service') && ($uri3 == 'update')){ $service ='display: none'; $serviceAdd = 'display: none'; $serviceBack ='';}

if ($uri == 'Service_ajax') { $service ='display: none'; $serviceAdd = '';}
if (($uri == 'Service_ajax') && ($uri2 == 'create') || ($uri == 'Service_ajax') && ($uri3 == 'create') || ($uri == 'Service_ajax') && ($uri2 == 'read') || ($uri == 'Service_ajax') && ($uri3 == 'read') || ($uri == 'Service_ajax') && ($uri2 == 'update') || ($uri == 'Service_ajax') && ($uri3 == 'update')){ $service ='display: none'; $serviceAdd = 'display: none'; $serviceBack ='';}




$serviceInvoiceBack ='display: none';
$serviceInvoiceAdd ='display: none';
$serviceInvoice ='';
if ($uri == 'Service_invoice') { $serviceInvoice ='display: none'; $serviceInvoiceAdd = '';}
if (($uri == 'Service_invoice') && ($uri2 == 'create') || ($uri == 'Service_invoice') && ($uri3 == 'create') || ($uri == 'Service_invoice') && ($uri2 == 'read') || ($uri == 'Service_invoice') && ($uri3 == 'read') || ($uri == 'Service_invoice') && ($uri2 == 'update') || ($uri == 'Service_invoice') && ($uri3 == 'update')){ $serviceInvoice ='display: none'; $serviceInvoiceAdd = 'display: none'; $serviceInvoiceBack ='';}

if ($uri == 'Service_invoice_ajax') { $serviceInvoice ='display: none'; $serviceInvoiceAdd = '';}
if (($uri == 'Service_invoice_ajax') && ($uri2 == 'create') || ($uri == 'Service_invoice_ajax') && ($uri3 == 'create') || ($uri == 'Service_invoice_ajax') && ($uri2 == 'read') || ($uri == 'Service_invoice_ajax') && ($uri3 == 'read') || ($uri == 'Service_invoice_ajax') && ($uri2 == 'update') || ($uri == 'Service_invoice_ajax') && ($uri3 == 'update')){ $serviceInvoice ='display: none'; $serviceInvoiceAdd = 'display: none'; $serviceInvoiceBack ='';}

?>

<a href="#" onclick="showData('<?php echo site_url('Admin/Service_ajax/create/'); ?>','<?php echo '/Admin/Service/create/';?>')"  class="btn btn-default" style="<?php echo $serviceAdd;?>">Add</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Service_ajax') ?>','/Admin/Service/')" class="btn btn-default" style="<?php echo $serviceBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>





<a href="#" onclick="showData('<?php echo site_url('/Admin/Service_invoice_ajax') ?>','/Admin/Service_invoice')" class="btn btn-default" style="<?php echo $serviceInvoiceBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>





<a href="#" onclick="showData('<?php echo site_url('/Admin/Service_ajax') ?>','/Admin/Service')" class="btn btn-default" style="<?php echo $service;?>">Service</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Service_invoice_ajax') ?>','/Admin/Service_invoice')" class="btn btn-default" style="<?php echo $serviceInvoice;?>">Service Invoice</a>




