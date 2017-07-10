<?php
	
define('GALLERY_ITEM_TABLE','gallery_item');
define('GALLERY_TAG_TABLE','gallery_tag');
define('GALLERY_ITEM_IMG_PATH', "/image/gallery/item");
define('GALLERY_TAG_IMG_PATH', "/image/gallery/tag");

define('GALLERY_NAME', 'Галлерея');

class Gallery {	

function siteGallery() {

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if(isset($_SERVER['REQUEST_URI'])){
$seodata = explode("/",$_SERVER['REQUEST_URI']);
$mod = $seodata[1];
$tag1 = $seodata[2];
$tag2 = $seodata[3];    
$param = $seodata[4];
$GLOBALS["mod"]=$mod;
$GLOBALS["tag1"]=$tag1;
$GLOBALS["tag2"]=$tag2;
$GLOBALS["param"]=$param;
}

$tpl=new SiteModTpl;

if(INI::Life('site_gallery_tag_list')>100){
$all_tag_list=Gallery::tagListShowLevel();
INI::SetXXL($all_tag_list,'site_gallery_tag_list');
}
$all_tag_list=INI::Get('site_gallery_tag_list');

$tpl->assign('all_tag_list',$all_tag_list);

if($tag1==""){
$tag1_list=Gallery::tagListShowLevel(1);
$tpl->assign('tag1_list',$tag1_list);
$c_cont["content"]=$tpl->get("tag1-list");

}

else if($tag1!=""&&$tag2==""){
$tag1_data=Gallery::tagDataSeolink($tag1);
$tag2_list=Gallery::tagListShowConnect($tag1_data['id']);

$tpl->assign('tag1_data',$tag1_data);
$tpl->assign('tag2_list',$tag2_list);
$c_cont["content"]=$tpl->get("tag2-list");
}
else if($tag1!=""&&$tag2!=""&&$param==""){
if($tag2!="all"){
$tag2_data=Gallery::tagDataSeolink($tag2,$tag1);
$item_list=Gallery::itemListShow($tag2_data['id']);
}else{
$tag2_data['caption']="Фотографии применения POS материалов";
$tag2_data['connect']=";1;";
$item_list=Gallery::itemListShow("all");
}
$tpl->assign('tag2_data',$tag2_data);
$tpl->assign('item_list',$item_list);
$c_cont["content"]=$tpl->get("item-list");
}
else if($param!=""){
$wa=explode("-",$param);
$item_id=$wa[0];

$item_data=Gallery::itemDataId($item_id);
$tpl->assign('item_data',$item_data);

$c_cont["content"]=$tpl->get("item-look");

SYS::varDump($item_data,__FILE__,__LINE__,"item_data");
}
return $c_cont;
}

function siteGalleryMenu(){

$tpl=new SiteGalleryTpl;

$tag1_list=Gallery::tagListShowLevel(1);
$tag2_list=Gallery::tagListShowLevel(2);

$tpl->assign('tag1_list',$tag1_list);
$tpl->assign('tag2_list',$tag2_list);

$Gallery_menu=$tpl->get("tag-menu");

return $Gallery_menu;
}

function tagListShowLevel($level=''){

$select="";
$from=GALLERY_TAG_TABLE;
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
$from=GALLERY_TAG_TABLE;
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
$from=GALLERY_TAG_TABLE;
$where="seolink='{$seolink}'";
if($parentseo!=""){
$tag1_data=Gallery::tagDataSeolink($parentseo);
$where.=" AND connect LIKE '%;{$tag1_data['id']};%'";
}

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, "", 1);

return $row[0];
}




function itemListShow($tag){
	
$select="";
$from=GALLERY_ITEM_TABLE;
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

function itemListSpecial($spec_where){
	
$select="";
$from=GALLERY_ITEM_TABLE;
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
$from=GALLERY_ITEM_TABLE;
$where["id"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}




}

class SiteGalleryTpl extends Tpl{
function SiteGalleryTpl (){
$this->template_dir = SITE_PATH.'/site/gallery/tpl';
}}

?>
