<?php
	
define('SHOP_ITEM_TABLE','shop_item');
define('SHOP_TAG_TABLE','shop_tag');
define('SHOP_ITEM_IMG_PATH', "/image/shop/item");
define('SHOP_TAG_IMG_PATH', "/image/shop/tag");

define('SHOP_NAME', 'Магазин');

class Shop {	

function siteShop() {

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if(isset($_SERVER['REQUEST_URI'])){
$seodata = explode("/",$_SERVER['REQUEST_URI']);
$mod = $seodata[1];
$tag1 = $seodata[2];
$tag2 = $seodata[3];    
$param = $seodata[4];
}

$tpl=new SiteModTpl;

if(INI::Life('site_shop_tag_list')>100){
$all_tag_list=Shop::tagListShowLevel();
INI::SetXXL($all_tag_list,'site_shop_tag_list');
}
$all_tag_list=INI::Get('site_shop_tag_list');

$tpl->assign('all_tag_list',$all_tag_list);

if($tag1==""){
$tag1_list=Shop::tagListShowLevel(1);
$tpl->assign('tag1_list',$tag1_list);
$c_cont["content"]=$tpl->get("tag1-list");

}

else if($tag1!=""&&$tag2==""){
$tag1_data=Shop::tagDataSeolink($tag1);
$tag2_list=Shop::tagListShowConnect($tag1_data['id']);

$tpl->assign('tag1_data',$tag1_data);
$tpl->assign('tag2_list',$tag2_list);
$c_cont["content"]=$tpl->get("tag2-list");
}
else if($tag1!=""&&$tag2!=""&&$param==""){
$tag2_data=Shop::tagDataSeolink($tag2,$tag1);
$item_list=Shop::itemListShowFlagman($tag2_data['id']);

$tpl->assign('tag2_data',$tag2_data);
$tpl->assign('item_list',$item_list);
$c_cont["content"]=$tpl->get("item-list");
}
else if($param!=""){
$wa=explode("-",$param);
$item_id=$wa[0];

$item_data=Shop::itemDataId($item_id);
$tpl->assign('item_data',$item_data);

$c_cont["content"]=$tpl->get("item-look");

SYS::varDump($item_data,__FILE__,__LINE__,"item_data");
}
return $c_cont;
}

function siteShopMenu(){

$tpl=new SiteShopTpl;

$tag1_list=Shop::tagListShowLevel(1);
$tag2_list=Shop::tagListShowLevel(2);

$tpl->assign('tag1_list',$tag1_list);
$tpl->assign('tag2_list',$tag2_list);

$shop_menu=$tpl->get("tag-menu");

return $shop_menu;
}

function tagListShowLevel($level=''){

$select="";
$from=SHOP_TAG_TABLE;
$where["`show`"]=1;
if($level!=""){
$where["level"]=$level;
}else{
$where["1"]=1;
}
$sortby="pos DESC";

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, $sortby);

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;

}

function tagListShowConnect($tag){
	
$select="";
$from=SHOP_TAG_TABLE;
$where="connect LIKE '%;".$tag.";%' AND `show`='1'";
$sortby="pos DESC";


$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,100");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function tagDataSeolink($seolink,$parentseo=''){
	
$select="";
$from=SHOP_TAG_TABLE;
$where="seolink='{$seolink}'";
if($parentseo!=""){
$tag1_data=Shop::tagDataSeolink($parentseo);
$where.=" AND connect LIKE '%;{$tag1_data['id']};%'";
}

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, "", 1);

return $row[0];
}




function itemListShow($tag){
	
$select="";
$from=SHOP_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%' AND `show`='1'";
$sortby="pos DESC";
$groupby="article DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,100",$groupby);

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemListShowFlagman($tag){
	
$select="";
$from=SHOP_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%' AND `show`='1' AND `flagman`='1'";
$sortby="pos DESC";
//$groupby="article DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,100",$groupby);

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}


function itemListSpecial($spec_where){
	
$select="";
$from=SHOP_ITEM_TABLE;
//$where="connect LIKE '%;".$tag.";%' AND `show`='1'";
$where=$spec_where;
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,100");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemDataId($id){
	
$select="";
$from=SHOP_ITEM_TABLE;
$where["id"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}


function paramItemData($param_item_id){
	
$select="";
$from="param_item";
$where["id"]=$param_item_id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}

function paramTagData($param_tag_id){
	
$select="";
$from="param_tag";
$where["id"]=$param_tag_id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}


function paramItemList($tag){
	
$select="";
$from="param_item";
$where="connect LIKE '%;".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}


}

class SiteShopTpl extends Tpl{
function SiteShopTpl (){
$this->template_dir = SITE_PATH.'/site/shop/tpl';
}}

?>
