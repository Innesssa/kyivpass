<?php
include_once("item.banner.admin.php");
include_once("tag.banner.admin.php");

define('BANNER_ITEM_TABLE','banner_item');
define('BANNER_TAG_TABLE','banner_tag');
define('BANNER_ITEM_IMG_PATH', SITE_PATH."/image/banner/item");
define('BANNER_TAG_IMG_PATH', SITE_PATH."/image/banner/tag");

class Banner {	

function admBanner() {
	


define('MOD_NAME', 'Баннеры');

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=BannerTag::admTag();	
}
if($type=="item"){
$c_cont=BannerItem::admItem();
}


return $c_cont;
}


}
?>
