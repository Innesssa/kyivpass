<?php
include_once("item.shop.admin.php");
include_once("tag.shop.admin.php");

define('SHOP_ITEM_TABLE','shop_item');
define('SHOP_TAG_TABLE','shop_tag');
define('SHOP_ITEM_IMG_PATH', SITE_PATH."/image/shop/item");
define('SHOP_TAG_IMG_PATH', SITE_PATH."/image/shop/tag");

class Shop {	

function admShop() {

define('SHOP_NAME', 'Магазин');

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=ShopTag::admTag();	
}
if($type=="item"){
$c_cont=ShopItem::admItem();
}


return $c_cont;
}


}
?>
