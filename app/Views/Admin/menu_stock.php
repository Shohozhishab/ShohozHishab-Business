
<?php
$curUrl = current_url(true);
//$curUrl = $current_url;
$url = new \CodeIgniter\HTTP\URI($curUrl);


$uri = $url->getSegment(3);
$uri2 = $url->getSegment(4);
$uri3 = $url->getSegment(4);



$stoBack ='display: none';
$stoAdd ='display: none';
$store ='';

if ($uri == 'Stores') {$store ='display: none';$stoAdd ='';}
if (($uri == 'Stores') && ($uri2 == 'create') || ($uri == 'Stores') && ($uri3 == 'read') || ($uri == 'Stores') && ($uri2 == 'read') || ($uri == 'Stores') && ($uri3 == 'create') || ($uri == 'Stores') && ($uri3 == 'update') || ($uri == 'Stores') && ($uri2 == 'update') ) {$store ='display: none';$stoAdd ='display: none';$stoBack ='';}
if ($uri == 'Stores_ajax') {$store ='display: none';$stoAdd ='';}
if (($uri == 'Stores_ajax') && ($uri2 == 'create') || ($uri == 'Stores_ajax') && ($uri3 == 'read') || ($uri == 'Stores_ajax') && ($uri2 == 'read') || ($uri == 'Stores_ajax') && ($uri3 == 'create') || ($uri == 'Stores_ajax') && ($uri3 == 'update') || ($uri == 'Stores_ajax') && ($uri2 == 'update') ) {$store ='display: none';$stoAdd ='display: none';$stoBack ='';}




$proCatBack ='display: none';
$proCatAdd ='display: none';
$proCat ='';

if ($uri == 'Product_category') {$proCat ='display: none';$proCatAdd ='';}
if (($uri == 'Product_category') && ($uri2 == 'create') || ($uri == 'Product_category') && ($uri3 == 'create') || ($uri == 'Product_category') && ($uri2 == 'read') || ($uri == 'Product_category') && ($uri3 == 'read') || ($uri == 'Product_category') && ($uri2 == 'update') || ($uri == 'Product_category') && ($uri3 == 'update') ) {$proCat ='display: none';$proCatAdd ='display: none';$proCatBack = '';}

if ($uri == 'Product_category_ajax') {$proCat ='display: none';$proCatAdd ='';}
if (($uri == 'Product_category_ajax') && ($uri2 == 'create') || ($uri == 'Product_category_ajax') && ($uri3 == 'create') || ($uri == 'Product_category_ajax') && ($uri2 == 'read') || ($uri == 'Product_category_ajax') && ($uri3 == 'read') || ($uri == 'Product_category_ajax') && ($uri2 == 'update') || ($uri == 'Product_category_ajax') && ($uri3 == 'update') ) {$proCat ='display: none';$proCatAdd ='display: none';$proCatBack = '';}






$brandBack ='display: none';
$brandAdd ='display: none';
$brand ='';

if ($uri == 'Brand') {$brand ='display: none';$brandAdd ='';}
if (($uri == 'Brand') && ($uri2 == 'create') || ($uri == 'Brand') && ($uri3 == 'create') || ($uri == 'Brand') && ($uri2 == 'read') || ($uri == 'Brand') && ($uri3 == 'read') || ($uri == 'Brand') && ($uri2 == 'update') || ($uri == 'Brand') && ($uri3 == 'update') ){$brand ='display: none';$brandAdd ='display: none'; $brandBack ='';}
if ($uri == 'Brand_ajax') {$brand ='display: none';$brandAdd ='';}
if (($uri == 'Brand_ajax') && ($uri2 == 'create') || ($uri == 'Brand_ajax') && ($uri3 == 'create') || ($uri == 'Brand_ajax') && ($uri2 == 'read') || ($uri == 'Brand_ajax') && ($uri3 == 'read') || ($uri == 'Brand_ajax') && ($uri2 == 'update') || ($uri == 'Brand_ajax') && ($uri3 == 'update') ){$brand ='display: none';$brandAdd ='display: none'; $brandBack ='';}



$productBack = 'display: none';
$product = '';
if ($uri == 'Products') {$product ='display: none';}
if (($uri == 'Products') && ($uri2 == 'read') || ($uri == 'Products') && ($uri3 == 'read') || ($uri == 'Products') && ($uri2 == 'update') || ($uri == 'Products') && ($uri3 == 'update') ) {$productBack ='';}
if ($uri == 'Products_ajax') {$product ='display: none';}
if (($uri == 'Products_ajax') && ($uri2 == 'read') || ($uri == 'Products_ajax') && ($uri3 == 'read') || ($uri == 'Products_ajax') && ($uri2 == 'update') || ($uri == 'Products_ajax') && ($uri3 == 'update') ) {$productBack ='';}
?>




<a href="#" onclick="showData('<?php echo site_url('/Admin/Stores_ajax'); ?>','<?php echo '/Admin/Stores';?>')"  class="btn btn-default" style="<?php echo $stoBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Stores_ajax/create/'); ?>','<?php echo '/Admin/Stores/create/';?>')"  class="btn btn-default" style="<?php echo $stoAdd;?>">Add</a>


<a href="#" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax'); ?>','<?php echo '/Admin/Product_category';?>')"  class="btn btn-default" style="<?php echo $proCatBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax/create/'); ?>','<?php echo '/Admin/Product_category/create/';?>')"  class="btn btn-default" style="<?php echo $proCatAdd;?>">Add</a>


<a href="#" onclick="showData('<?php echo site_url('/Admin/Brand_ajax/create/'); ?>','<?php echo '/Admin/Brand/create/';?>')"  class="btn btn-default" style="<?php echo $brandAdd;?>">Add</a>
<a href="#" onclick="showData('<?php echo site_url('/Admin/Brand_ajax'); ?>','<?php echo '/Admin/Brand';?>')"  class="btn btn-default" style="<?php echo $brandBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Products_ajax'); ?>','<?php echo '/Admin/Products';?>')"  class="btn btn-default" style="<?php echo $productBack;?>"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back to list</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Stores_ajax'); ?>','<?php echo '/Admin/Stores';?>')"  class="btn btn-default" style="<?php echo $store;?>">Stores</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Products_ajax'); ?>','<?php echo '/Admin/Products';?>')"  class="btn btn-default" style="<?php echo $product;?>">Products</a>

<a href="#" onclick="showData('<?php echo site_url('/Admin/Product_category_ajax'); ?>','<?php echo '/Admin/Product_category';?>')"  class="btn btn-default" style="<?php echo $proCat;?>">Products Category</a>


<a href="#" onclick="showData('<?php echo site_url('/Admin/Brand_ajax'); ?>','<?php echo '/Admin/Brand';?>')"  class="btn btn-default" style="<?php echo $brand;?>">Brand</a>