<?php

class ShopItem {	

function admItem() {

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

$tpl_item=new AdmModTpl;


if($com=="list"){	
$_SESSION['refresh_page']=str_replace("display=","",PAGE_URL);

if($event!=""){
ShopItem::Event($event,SHOP_ITEM_TABLE,$id,$tag);
}

$item_list=ShopItem::itemListIdData($tag);

if(INI::Life('shop_tag_list')>30){
$tag_list=ShopTag::tagListIdData();
INI::SetXXL($tag_list,'shop_tag_list');
}
$tag_list=INI::Get('shop_tag_list');

$tpl_item->assign('item_list',$item_list);
$tpl_item->assign('tag_list',$tag_list);

$param_tag_list=ParamTag::tagShowListConnectData('1');
$tpl_item->assign('param_tag_list',$param_tag_list);

if($display=="companion"){
$c_cont=$tpl_item->get("companion_item-list");	
}else{
$c_cont=$tpl_item->get("item-list");}	


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
$arr_value['flagman']=$flagman;
$arr_value['hit']=$hit;
$arr_value['good_price']=$good_price;
$arr_value['sell_out']=$sell_out;
$arr_value['on_main']=$on_main;

$arr_value['price_diler']=$price_diler;
$arr_value['package']=$package;

$p1=$_POST["p1"];
if($p1!=NULL){
$param_c=";";
$pparam_c=";";
foreach ($p1 as $key=>$valp){
$param_c.=$valp.";";

$pdata=ParamItem::ItemData($valp);
$ppdata=ParamTag::TagData(substr($pdata["connect"],1,-1));
$pparam_c.=str_replace(";","",$ppdata["id"]).";";
}}

$arr_value['param_connect']=$param_c;
$arr_value['p1']=$pparam_c;



$branch=$_POST["branch"];
if($branch!=NULL){
$branch_c=";";
foreach ($branch as $key=>$valb){$branch_c.=$valb.";";}}
$arr_value['branch_connect']=$branch_c;

$companion=$_POST["companion"];
if($companion!=NULL){
$companion_c=";";
foreach ($companion as $key=>$valb){$companion_c.=$valb.";";}}
$arr_value['companion_connect']=$companion_c;

$gallery=$_POST["gallery"];
if($gallery!=NULL){
$gallery_c=";";
foreach ($gallery as $key=>$valg){$gallery_c.=$valg.";";}}
$arr_value['gallery_connect']=$gallery_c;

////////////////////////////////////////////////////////////
//$param_list = ShopItem::getParam($param_c,";");
//$arr_value['article'] = $param_list[0]['value1']."-".$_POST['article']."-".$param_list[1]['value1'];
$arr_value['article'] = $article;
//SYS::varDump($param_list,__FILE__,__LINE__,'PARARAM');
///////////////////////////////////////////////////////////

$arr_value['meta_t']=$meta_t!=''?str_replace('"','&quot;',$meta_t):$arr_value['caption'];
$arr_value['meta_d']=$meta_d!=''?str_replace('"','&quot;',$meta_d):$arr_value['desc_short'];
$arr_value['meta_k']=$meta_k;

$arr_value['date']=$date!=""?$date:date("Y-m-d H:i:s");

$nowdata=getdate(time());
$arr_value['pos']=$pos!=''&&$pos!=0?$pos:$nowdata[0];


$table=SHOP_ITEM_TABLE;
SYS::varDump($arr_value,__FILE__,__LINE__,'arr_value');
if($id!=""&&$id!="new"){
	$where['id']=$id;
	$db = new mysql;
SYS::varDump($arr_value['p1'],__FILE__,__LINE__,'PARARAM');
	$res = $db->updateSQL ($table, $arr_value, $where);
}else{
	$db = new mysql;
	$id = $db->insertSQL ($arr_value, $table);
	rename(SHOP_ITEM_IMG_PATH."/new",SHOP_ITEM_IMG_PATH."/".$id);
	$url="/cms/?".str_replace("com=add","com=edit", getenv("QUERY_STRING"));
	$url=str_replace("id=new","id=".$id, $url);
	header("Location: ".$url."&save=ok");
}
$_SESSION['dplay']="tab";
}


if($com=="add"||$com=="edit"){	
$tag_list=ShopTag::tagListIdData();
$tpl_item->assign('tag_list',$tag_list);

$tag1_list=ShopTag::tagListIdData(1);
$tpl_item->assign('tag1_list',$tag1_list);

$tag2_list=ShopTag::tagListIdData(2);
$tpl_item->assign('tag2_list',$tag2_list);

$param_tag_list=ParamTag::tagShowListConnectData('1');
$tpl_item->assign('param_tag_list',$param_tag_list);

if($param_tag_list){foreach($param_tag_list as $key=>$val){

//$param_item_list[$val["id"]]=ParamItem::itemListIdData($val["id"]);
//SYS::varDump($param_item_list,__FILE__,__LINE__,'param_item');
}}




if($com=="edit"){
$item_data=ShopItem::itemData($id);
$tpl_item->assign('item_data',$item_data);	
}	

$c_cont=$tpl_item->get("item-add-edit");	
}

if($com=="look"){
$item_data=ShopItem::itemData($id);
$tpl_item->assign('item_data',$item_data);	
$c_cont=$tpl_item->get("companion_item-look");
}

return $c_cont;
}


function itemListData($tag){
	
$select="";
$from=SHOP_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row;
}


function itemListIdData($tag){
	
$select="";
$from=SHOP_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby=" article ASC,flagman DESC, pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemCountTag($tag){
	
$select="COUNT(id)";
$from=SHOP_ITEM_TABLE;
$where="connect LIKE '%".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row[0]["COUNT(id)"];
}


function itemData($id){
	
$select="";
$from=SHOP_ITEM_TABLE;
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



function getParam($param_list_id,$razdelitel){
    $pid = explode($razdelitel,substr($param_list_id,1,-1));
    $count_pid = count($pid);
    
    for($i = 0; $i < $count_pid; $i++){
        $query = "SELECT * FROM param_item WHERE `id`='".$pid[$i]."'";
        $res = mysql_query($query);
		
		mysql::queryError($res,$query);
        while ($row = mysql_fetch_assoc($res))
        {
        	   $param_list[] = $row;
        }
    }
    
    return $param_list;
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
}
?>
