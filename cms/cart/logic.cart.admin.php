<?php
include_once("item.cart.admin.php");
include_once("tag.cart.admin.php");

define('CART_ITEM_TABLE','cart_item');
define('CART_TAG_TABLE','cart_tag');
define('CART_ITEM_IMG_PATH', SITE_PATH."/image/cart/item");
define('CART_TAG_IMG_PATH', SITE_PATH."/image/cart/tag");

class Cart {	

function admCart() {

define('CART_NAME', 'Заказы');

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=CartTag::admTag();	
}
if($type=="item"){
$c_cont=CartItem::admItem();
}


return $c_cont;
}


}
?>
