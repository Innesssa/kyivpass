<?php
	
define('ART_ITEM_TABLE','art_item');
define('ART_ITEM_TABLE_LANG','art_item_'.$_SESSION['lang']);
define('ART_TAG_TABLE','art_tag');
define('ART_TAG_TABLE_LANG','art_tag_'.$_SESSION['lang']);
define('ART_ITEM_IMG_PATH', "/image/art/item");
define('ART_TAG_IMG_PATH', "/image/art/tag");

define('ART_NAME', 'Статьи');

class Art {	

function siteArt() {

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

if(INI::Life('site_art_tag_list')>1){
$all_tag_list=Art::tagListShowLevel(); 
//SYS::varDump($all_tag_list,__FILE__,__LINE__,"AllTagList");
INI::SetXXL($all_tag_list,'site_art_tag_list');
}
$all_tag_list=INI::Get('site_art_tag_list');

$tpl->assign('all_tag_list',$all_tag_list);

if($tag1==""){
$tag1_list=Art::tagListShowLevel(1);
$tpl->assign('tag1_list',$tag1_list);
$c_cont["content"]=$tpl->get("tag1-list");

}

else if($tag1!=""&&$tag2==""){
$tag1_data=Art::tagDataSeolink($tag1); 
$tag2_list=Art::tagListShowConnect($tag1_data['id']);

$tpl->assign('tag1_data',$tag1_data);
$tpl->assign('tag2_list',$tag2_list);
$c_cont["content"]=$tpl->get("tag2-list");
}
else if($tag1!=""&&$tag2!=""&&$param==""){
$tag2_data=Art::tagDataSeolink($tag2);
$item_list=Art::itemListShow($tag2_data['id']);

$tpl->assign('tag2_data',$tag2_data);
$tpl->assign('item_list',$item_list);
$c_cont["content"]=$tpl->get("item-list");
}
else if($param!=""){
$wa=explode("-",$param);
$item_id=$wa[0];

$item_data=Art::itemDataId($item_id);
$tpl->assign('item_data',$item_data);

$c_cont["content"]=$tpl->get("item-look");

}

return $c_cont;
}

function siteArtMenu(){
$tpl=new SiteModTpl;

$tag1_list=Art::tagListShowLevel(1);
$tag2_list=Art::tagListShowLevel(2);

$tpl->assign('tag1_list',$tag1_list);
$tpl->assign('tag2_list',$tag2_list);

$art_menu=$tpl->get("tag-menu");

return $art_menu;
}

function tagListShowLevel($level=''){

    if($level!=""){
        $level=$level;
    }
    else{
        $level=1;
    }

    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `show`='1' AND `level`='".$level."'";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    while ($row = mysql_fetch_array($res)){

    if($row!=NULL){
        foreach($row as $key=>$val){
            $arrcat[$val["id"]]=$val;
        }
    }
    }
//SYS::varDump($arrcat,__FILE__,__LINE__,"ARRCAT");
    return $arrcat;
} 


function tagListShowConnect($tag){
	
    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `connect` LIKE '%;".$tag.";%' AND `show`='1'";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $row = mysql_fetch_array($res);

    if($row!=NULL){
        foreach($row as $key=>$val){
            $arrcat[$val["id"]]=$val;
        }
    }
//SYS::varDump($arrcat,__FILE__,__LINE__,"ARRCAT");
    return $arrcat;

}

function tagDataSeolink($seolink){

    $query = "SELECT * FROM ".ART_ITEM_TABLE." 
              LEFT JOIN ".ART_ITEM_TABLE_LANG." ON ".ART_ITEM_TABLE.".id=".ART_ITEM_TABLE_LANG.".pid 
              WHERE `seolink`='".$seolink."' LIMIT 1";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $row = mysql_fetch_array($res); 

    return $row;

}


function itemListShow($tag){

    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `connect` LIKE '%;".$tag.";%' AND `show`='1' SORT BY `pos` DESC";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $row = mysql_fetch_array($res);

    if($row!=NULL){
        foreach($row as $key=>$val){
            $arrcat[$val["id"]]=$val;
        }
    }
//SYS::varDump($arrcat,__FILE__,__LINE__,"ARRCAT");
    return $arrcat;


}


function itemDataId($id)
{
    $query = "SELECT * FROM ".ART_ITEM_TABLE." 
              LEFT JOIN ".ART_ITEM_TABLE_LANG." ON ".ART_ITEM_TABLE.".id=".ART_ITEM_TABLE_LANG.".pid 
              WHERE `pid`='".$id."' LIMIT 1";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $row = mysql_fetch_array($res); 
//SYS::varDump($row,__FILE__,__LINE__,"ROW");
    return $row;
}
/*
function itemDataId($id){
	
$select="";
$from=ART_ITEM_TABLE_LANG;
$where["pid"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);


return $row[0];
}
*/



}
?>
