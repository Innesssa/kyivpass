<?php

class Info {	
	
function admInfo() {	
	
define('MOD_NAME', 'Информация');

define('MOD_ITEM_TABLE',$_GET["mod"].'_item');
define('MOD_TAG_TABLE',$_GET["mod"].'_tag');
define('MOD_ITEM_IMG_PATH', SITE_PATH."/image/".$_GET["mod"]."/item");
define('MOD_TAG_IMG_PATH', SITE_PATH."/image/".$_GET["mod"]."/tag");

include_once("item.info.admin.php");
include_once("tag.info.admin.php");

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=InfoTag::admTag();	
}
if($type=="item"){
$c_cont=InfoItem::admItem();
}


return $c_cont;
}


}
?>
