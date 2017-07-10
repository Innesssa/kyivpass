<?php

define('BANNER_ITEM_TABLE','banner_item');
define('BANNER_TAG_TABLE','banner_tag');
define('BANNER_ITEM_IMG_PATH', "/image/banner/item");
define('BANNER_TAG_IMG_PATH', "/image/banner/tag");

class Banner {	

function BigBan() {
		
$tpl=new SiteBanTpl;

$item_list=Banner::itemListShow(2);
$tpl->assign('item_list',$item_list);
$c_cont=$tpl->get("banner-center");

return $c_cont;
}

function LeftTiz() {
		
$tpl=new SiteBanTpl;

$item_list=Banner::itemListShow(4);
$tpl->assign('item_list',$item_list);
$c_cont=$tpl->get("tizer-left");

return $c_cont;
}

function itemListShow($tag){
	
$select="";
$from=BANNER_ITEM_TABLE;
if($tag=="all"){
$where="`show`='1'";
}else{
$where="connect LIKE '%;".$tag.";%' AND `show`='1'";
}
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,100");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}


}


class SiteBanTpl extends Tpl{
function SiteBanTpl (){
$this->template_dir = SITE_PATH.'/site/banner/tpl';
}}

?>
