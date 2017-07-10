<?php

class BannerItem {	

function admItem() {

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

$tpl_item=new AdmModTpl;


if($com=="list"){	
$_SESSION['refresh_page']=PAGE_URL;

if($event!=""){
BannerItem::Event($event,BANNER_ITEM_TABLE,$id,$tag);
}

$item_list=BannerItem::itemListIdData($tag);

if(INI::Life('banner_tag_list')>100){
$tag_list=BannerTag::tagListIdData();
INI::SetXXL($tag_list,'banner_tag_list');
}
$tag_list=INI::Get('banner_tag_list');


$tpl_item->assign('item_list',$item_list);
$tpl_item->assign('tag_list',$tag_list);

$c_cont=$tpl_item->get("item-list");
}



if($cmd=="update"||$cmd=="insert"){
	
$connect_real = array_unique($connect);

$arr_value['connect']=';';
if($connect_real!=NULL){foreach($connect_real as $key=>$val){
$arr_value['connect'].=$val.';';	
}}

$arr_value['caption']=trim(str_replace('"','&quot;',$caption));
$arr_value['seolink']=$seolink!=""?$seolink:Text::cirilToLatin($arr_value['caption']);
$arr_value['desc_short']=trim(str_replace('"','&quot;',$desc_short));
$arr_value['desc_full']=trim($desc_full);
$arr_value['show']=$show;
$arr_value['hit']=$hit;

$arr_value['value1']=$value1;
$arr_value['value2']=$value2;

$arr_value['meta_t']=$meta_t!=''?str_replace('"','&quot;',$meta_t):$arr_value['caption'];
$arr_value['meta_d']=$meta_d!=''?str_replace('"','&quot;',$meta_d):$arr_value['desc_short'];
$arr_value['meta_k']=$meta_k;

$arr_value['date']=$date!=""?$date:date("Y-m-d H:i:s");

$nowdata=getdate(time());
$arr_value['pos']=$pos!=''&&$pos!=0?$pos:$nowdata[0];


$table=BANNER_ITEM_TABLE;
SYS::varDump($arr_value,__FILE__,__LINE__,'arr_value');
if($id!=""&&$id!="new"){
	$where['id']=$id;
	$db = new mysql;
	$res = $db->updateSQL ($table, $arr_value, $where);
}else{
	$db = new mysql;
	$id = $db->insertSQL ($arr_value, $table);
	rename(BANNER_ITEM_IMG_PATH."/new",BANNER_ITEM_IMG_PATH."/".$id);
	$url="/cms/?".str_replace("com=add","com=edit", getenv("QUERY_STRING"));
	$url=str_replace("id=new","id=".$id, $url);
	header("Location: ".$url."&save=ok");
}
$_SESSION['dplay']="tab";
}


if($com=="add"||$com=="edit"){	
$tag_list=BannerTag::tagListIdData();
$tpl_item->assign('tag_list',$tag_list);

$tag_list1=BannerTag::tagListIdData(1);
$tpl_item->assign('tag_list1',$tag_list1);

$tag_list2=BannerTag::tagListIdData(2);
$tpl_item->assign('tag_list2',$tag_list2);

if($com=="edit"){
$item_data=BannerItem::itemData($id);
$tpl_item->assign('item_data',$item_data);	
}	

$c_cont=$tpl_item->get("item-add-edit");	
}


return $c_cont;
}


function itemListData($tag){
	
$select="";
$from=BANNER_ITEM_TABLE;
$where="connect LIKE '%".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row;
}


function itemListIdData($tag){
	
$select="";
$from=BANNER_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemShowListIdData($tag){
	
$select="";
$from=BANNER_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%' AND `show`='1'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemCountTag($tag){
	
$select="COUNT(id)";
$from=BANNER_ITEM_TABLE;
$where="connect LIKE '%".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row[0]["COUNT(id)"];
}

function itemData($id){
	
$select="";
$from=BANNER_ITEM_TABLE;
$where["id"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}



function Event($event,$table,$id,$tag){
$from=$table;
$where="id={$id} AND connect LIKE '%;{$tag};%'";
$db = new mysql;
$row = $db->origSelectSQL("id,pos", $from, $where,"pos ASC","0,1");

$pos_start=$row[0]["pos"];
$id_start=$row[0]["id"];

if($event=='moveDown'){
$from2=$table;	
$where2="pos<{$pos_start} AND connect LIKE '%;{$tag};%'";
$db = new mysql;
$row2 = $db->origSelectSQL("id,pos", $from2, $where2,"pos DESC","0,1");
$pos_end=$row2[0]["pos"];
$id_end=$row2[0]["id"];
}

if($event=='moveUp'){
$from2=$table;	
$where2="pos>{$pos_start} AND connect LIKE '%;{$tag};%'";
$db = new mysql;
$row2 = $db->origSelectSQL("id,pos", $from2, $where2,"pos ASC","0,1");
$pos_end=$row2[0]["pos"];
$id_end=$row2[0]["id"];
}
	
if($pos_end!=""){
$arr_value1['pos']=$pos_end;
$arr_value2['pos']=$pos_start;
$whereu1['id']=$id_start;
$whereu2['id']=$id_end;
$db = new mysql;
$db->updateSQL ($from, $arr_value1, $whereu1);
$db->updateSQL ($from, $arr_value2, $whereu2);
}
//return $res;
}


}



/*
	


if($com=="delete"){
	
$from="art_Fotogallery";	
$where['id']=$id;	
$db = new mysql;
$res = $db->deleteSQL ($from, $where);
$loc_url=str_replace("com=delete","com=view",PAGE_URL);
$loc_url=str_replace("&id=".$id."","",$loc_url);
header("Location: ".$loc_url."");
}

}

 return $c_cont;

}


*/

?>