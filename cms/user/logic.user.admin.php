<?php

class User {	
	
function admUser() {	
	
define('MOD_NAME', 'Персоны');

define('MOD_ITEM_TABLE',$_GET["mod"].'_item');
define('MOD_TAG_TABLE',$_GET["mod"].'_tag');
define('MOD_ITEM_IMG_PATH', SITE_PATH."/image/".$_GET["mod"]."/item");
define('MOD_TAG_IMG_PATH', SITE_PATH."/image/".$_GET["mod"]."/tag");

include_once("item.user.admin.php");
include_once("tag.user.admin.php");

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=UserTag::admTag();	
}
if($type=="item"){
$c_cont=UserItem::admItem();
}


return $c_cont;
}


}
?>
