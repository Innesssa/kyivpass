<?php
	
class ArtTag {	
	
function admTag() {
	
foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

$tpl_tag=new AdmModTpl;

if($com=="list"){
$_SESSION['refresh_page']=PAGE_URL;

if($event!=""){
$tag_data=ArtTag::tagDataId($id);

if($tag_data["level"]==1){
ArtTag::Event1($event,ART_TAG_TABLE,$id);
}
ArtTag::Event2($event,ART_TAG_TABLE,$id);
}
	
$tag1_list=ArtTag::tagListShowLevel($level=1);
$tag2_list=ArtTag::tagListShowLevel($level=2);

$tpl_tag->assign('tag1_list',$tag1_list);
$tpl_tag->assign('tag2_list',$tag2_list);

$c_cont=$tpl_tag->get("tag-list");	
}

if($cmd=="update"||$cmd=="insert"){
	
if($level==2){
$connect_real = array_unique($connect);
$arr_value['connect']=';';
if($connect_real!=NULL){foreach($connect_real as $key=>$val){
$arr_value['connect'].=$val.';';	
}}
}else{
$arr_value['connect']=str_replace(';;',';',$connect);
}
if($connect==""){$arr_value['connect']=";";}
$arr_value['seolink']=$seolink!=""?$seolink:Text::cirilToLatin($caption);

$arr_value2['caption']=addslashes(trim(str_replace('"','&quot;',$caption)));
$arr_value2['desc_short']=addslashes(trim(str_replace('"','&quot;',$desc_short)));
$arr_value2['desc_full']=addslashes(trim($desc_full));
$arr_value['show']=$show;
$arr_value['level']=$level;
$arr_value['hit']=$hit;

$arr_value2['meta_t']=$meta_t!=''?addslashes(str_replace('"','&quot;',$meta_t)):addslashes($arr_value['caption']);
$arr_value2['meta_d']=$meta_d!=''?addslashes(str_replace('"','&quot;',$meta_d)):addslashes($arr_value['desc_short']);
$arr_value2['meta_k']=addslashes($meta_k);

$arr_value['date']=$date!=""?$date:date("Y-m-d H:i:s");

$nowdata=getdate(time());
$arr_value['pos']=$pos!=''&&$pos!=0?$pos:$nowdata[0];


$table=ART_TAG_TABLE;
$table2=ART_TAG_TABLE_LANG;
if($id!=""&&$id!="new"){
	$where['id']=$id;
	$db = new mysql;
	$res = $db->updateSQL ($table, $arr_value, $where);	
	
	$arr_value2['pid']=$id;
	$where2['pid']=$id;
	$res2 = $db->updateSQL ($table2, $arr_value2, $where2);	
	
if($arr_value['level']==2){
	$tag_connect_list=ArtTag::tagListConnectData($id);
	if($tag_connect_list){foreach($tag_connect_list as $key=>$val){
	$val["connect"]=str_replace(";".$id.";",";",$val["connect"]);
	$where_p['id']=$val['id'];
	$res_p = $db->updateSQL ($table, $val, $where_p);
    }}
    
    $pc=explode(";",substr($arr_value["connect"],1,-1));
    if($pc!=""){foreach($pc as $key=>$val){
    $c_tag_data=ArtTag::tagData($val);
    $c_tag_data["connect"]=$c_tag_data["connect"].$id.";";
	$where_pc['id']=$c_tag_data['id'];
	$res_p = $db->updateSQL ($table, $c_tag_data, $where_pc);
    }}  
}		
}else{
	
	$db = new mysql;
	$id = $db->insertSQL ($arr_value, $table);
	
//*********** Создание языковых записей *********//	
$site_langs = Lang::getSiteLangs();  // Получаем доступные языки   
if($site_langs !== NULL){   
foreach($site_langs as $key=>$val){   
	if($val['lang']==$_SESSION['lang']){
		$arr_value2['pid']=$id;
	$id2 = $db->insertSQL ($arr_value2, $table2);	
	}else{
	$table_lang="art_tag_".$val['lang'];
	$arr_value3['pid']=$id;
	$id3 = $db->insertSQL ($arr_value3, $table_lang);
		}
}}
	
if($arr_value['level']==2){	
	$pc=explode(";",substr($arr_value["connect"],1,-1));
    if($pc!=""){foreach($pc as $key=>$val){
    $c_tag_data=ArtTag::tagData($val);
    $c_tag_data["connect"]=$c_tag_data["connect"].$id.";";
	$where_pc['id']=$c_tag_data['id'];
	$res_p = $db->updateSQL ($table, $c_tag_data, $where_pc);
    }}
}
	rename(ART_ITEM_IMG_PATH."/new",ART_ITEM_IMG_PATH."/".$id);
	$url="/cms/?".str_replace("com=add","com=edit", getenv("QUERY_STRING"));
	$url=str_replace("id=new","id=".$id, $url);
	header("Location: ".$url."&save=ok");
}
$_SESSION['dplay']="tab";
}


if($com=="add"||$com=="edit"){

SYS::varDump($_POST,__FILE__,__LINE__,'POST'); 

$tag1_list=ArtTag::tagListShowLevel($level=1);
$tag2_list=ArtTag::tagListShowLevel($level=2);

$tpl_tag->assign('tag1_list',$tag1_list);
$tpl_tag->assign('tag2_list',$tag2_list);

if($com=="edit"){
$tag_data=ArtTag::tagDataId($id);
$tpl_tag->assign('tag_data',$tag_data);	
}
$c_cont=$tpl_tag->get("tag-add-edit");	
}





//if($display=="tab"){$_SESSION['dplay']="tab";}

return $c_cont;
}

function tagListShowLevel($level=''){

     if($level!=""){$levelz="`level`='".$level."'";}else{$levelz="1=1";}

    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE ".$levelz." ORDER BY `pos` DESC";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    //$row = mysql_fetch_assoc($res);
    
		if (mysql_num_rows($res) > 0)
		{
			while ($result = mysql_fetch_assoc ($res))
			{   
				$result["caption"]=stripslashes($result["caption"]);
				$result["desc_short"]=stripslashes($result["desc_short"]);
				$result["desc_full"]=stripslashes($result["desc_full"]);
				
				$result["meta_t"]=stripslashes($result["meta_t"]);
				$result["meta_d"]=stripslashes($result["meta_d"]);
				$result["meta_k"]=stripslashes($result["meta_k"]);

				$itog[] = $result;
				//SYS::varDump($result);
				
			}
		}

		
    if($itog!=NULL){
        foreach($itog as $key=>$val){
            $arrcat[$val["id"]]=$val;
        }
    }
SYS::varDump($arrcat,__FILE__,__LINE__,"ARRCAT");
    return $arrcat;
} 


function tagListShowConnect($tag){
	
    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `connect` LIKE '%;".$tag.";%' ORDER BY `pos` DESC";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
   //$row = mysql_fetch_assoc($res);
    
		if (mysql_num_rows($res) > 0)
		{
			while ($result = mysql_fetch_assoc ($res))
			{   
				$result["caption"]=stripslashes($result["caption"]);
				$result["desc_short"]=stripslashes($result["desc_short"]);
				$result["desc_full"]=stripslashes($result["desc_full"]);
				
				$result["meta_t"]=stripslashes($result["meta_t"]);
				$result["meta_d"]=stripslashes($result["meta_d"]);
				$result["meta_k"]=stripslashes($result["meta_k"]);

				$itog[] = $result;
				//SYS::varDump($result);
				
			}
		}

		
    if($itog!=NULL){
        foreach($itog as $key=>$val){
            $arrcat[$val["id"]]=$val;
        }
    }
SYS::varDump($arrcat,__FILE__,__LINE__,"ARRCAT");
    return $arrcat;

}

function tagDataSeolink($seolink){

    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `seolink`='".$seolink."' LIMIT 1";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $result = mysql_fetch_assoc($res); 
    
    $result["caption"]=stripslashes($result["caption"]);
	$result["desc_short"]=stripslashes($result["desc_short"]);
	$result["desc_full"]=stripslashes($result["desc_full"]);
	$result["meta_t"]=stripslashes($result["meta_t"]);
	$result["meta_d"]=stripslashes($result["meta_d"]);
	$result["meta_k"]=stripslashes($result["meta_k"]);
	
    return $result;

}

function tagDataId($id){

    $query = "SELECT * FROM ".ART_TAG_TABLE." 
              LEFT JOIN ".ART_TAG_TABLE_LANG." ON ".ART_TAG_TABLE.".id=".ART_TAG_TABLE_LANG.".pid 
              WHERE `pid`='".$id."' LIMIT 1";
    $res = mysql_query($query);
    Mysql::queryError($res,$query);
    $result = mysql_fetch_assoc($res); 
    
    $result["caption"]=stripslashes($result["caption"]);
	$result["desc_short"]=stripslashes($result["desc_short"]);
	$result["desc_full"]=stripslashes($result["desc_full"]);
	$result["meta_t"]=stripslashes($result["meta_t"]);
	$result["meta_d"]=stripslashes($result["meta_d"]);
	$result["meta_k"]=stripslashes($result["meta_k"]);

    return $result;

}


function tagListData($level=""){
	
$select="";
$from=ART_TAG_TABLE;
if($level!=""){
$where["level"]=$level;
}else{
$where["1"]=1;
}
$sortby="pos DESC";

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, $sortby);
SYS::varDump($row,__FILE__,__LINE__,"ARRCAT");
return $row;
}

function tagListIdData($level=""){
	
$select="";
$from=ART_TAG_TABLE;
if($level!=""){
$where["level"]=$level;
}else{
$where["1"]=1;
}
$sortby="pos DESC";

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, $sortby);

if($row!=NULL){foreach($row as $key=>$result){
	
$arrcat[$val["id"]]=$result;

}}

return $arrcat;
}

function tagData($id){
	
$select="";
$from=ART_TAG_TABLE;
$where["id"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

$result=$row[0];
	
return $result;

}


function tagListConnectData($tag){
	
$select="";
$from=ART_TAG_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$result = $db->origSelectSQL($select, $from, $where, $sortby,"0,100");
	
return $result;
}


function Event2($event,$table,$id){
$from=$table;
$where="id={$id}";
$db = new mysql;
$row = $db->origSelectSQL("id,pos,level,connect", $from, $where,"pos ASC","0,1");

$pos_start=$row[0]["pos"];
$id_start=$row[0]["id"];
$id_level=$row[0]["level"];
$id_connect=$row[0]["connect"];

$parent_id=str_replace(";","",$id_connect);
$from2=$table;	
$where2['id']=$parent_id;

$db = new mysql;
$row2 = $db->selectSQL("id,connect", $from2, $where2,"pos DESC","0,1");

if($event=='moveDown'){
$parent_connect=explode(";",substr($row2[0]["connect"],1,-1));
if($parent_connect){foreach($parent_connect as $key=>$val){
if($val==$id&&$parent_connect[$key+1]!=NULL){
list($parent_connect[$key],$parent_connect[$key+1])=array($parent_connect[$key+1],$parent_connect[$key]);

$where3['id']=$parent_connect[$key];
$db = new mysql;
$row3 = $db->selectSQL("id,connect,pos", $from2, $where3,"pos DESC","0,1");

$where4['id']=$parent_connect[$key+1];
$db = new mysql;
$row4 = $db->selectSQL("id,connect,pos", $from2, $where4,"pos DESC","0,1");

if($row3[0]["pos"]>$row4[0]["pos"]){
$maxi=$row3[0]["pos"];$mini=$row4[0]["pos"];
}else{
$maxi=$row4[0]["pos"];$mini=$row3[0]["pos"];
}

$arr_value3['pos']=$maxi;
$db = new mysql;
$db->updateSQL ($from, $arr_value3, $where3);

$arr_value4['pos']=$mini;
$db = new mysql;
$db->updateSQL ($from, $arr_value4, $where4);

//SYS::varDump($md,__FILE__,__LINE__,'moveDown');
}

}}

$p_connect=";".implode(";",$parent_connect).";";
}

if($event=='moveUp'){
$parent_connect=explode(";",substr($row2[0]["connect"],1,-1));
if($parent_connect){foreach($parent_connect as $key=>$val){
if($val==$id&&$parent_connect[$key-1]!=NULL){
list($parent_connect[$key],$parent_connect[$key-1])=array($parent_connect[$key-1],$parent_connect[$key]);

$where3['id']=$parent_connect[$key];
$db = new mysql;
$row3 = $db->selectSQL("id,connect,pos", $from2, $where3,"pos DESC","0,1");

$where4['id']=$parent_connect[$key-1];
$db = new mysql;
$row4 = $db->selectSQL("id,connect,pos", $from2, $where4,"pos DESC","0,1");

if($row3[0]["pos"]>$row4[0]["pos"]){
$maxi=$row3[0]["pos"];$mini=$row4[0]["pos"];
}else{
$maxi=$row4[0]["pos"];$mini=$row3[0]["pos"];
}

$arr_value3['pos']=$mini;
$db = new mysql;
$db->updateSQL ($from, $arr_value3, $where3);

$arr_value4['pos']=$maxi;
$db = new mysql;
$db->updateSQL ($from, $arr_value4, $where4);

}
}}

$p_connect=";".implode(";",$parent_connect).";";
}

if($p_connect!=""){
$arr_value['connect']=$p_connect;
$whereu['id']=$parent_id;

$db = new mysql;
$db->updateSQL ($from, $arr_value, $whereu);

}
//return $res;
}

function Event1($event,$table,$id){
$from=$table;
$where="id={$id}";
$db = new mysql;
$row = $db->origSelectSQL("id,pos,level,connect", $from, $where,"pos ASC","0,1");

$pos_start=$row[0]["pos"];
$id_start=$row[0]["id"];
$id_level=$row[0]["level"];
$id_connect=$row[0]["connect"];

if($event=='moveDown'){
$from2=$table;	
$where2="pos<{$pos_start}";
$where2.=" AND level={$id_level}";

$db = new mysql;
$row2 = $db->origSelectSQL("id,pos", $from2, $where2,"pos DESC","0,1");
$pos_end=$row2[0]["pos"];
$id_end=$row2[0]["id"];
}

if($event=='moveUp'){
$from2=$table;	
$where2="pos>{$pos_start}";
$where2.=" AND level={$id_level}";
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


//-----------------------------  Not Use  ---------------------------------
function Navigate(){
	
$row=ArtTag::catListData();

$tpl=new AdmTpl;
$tpl->assign('category',$row);
$art_navigate=$tpl->get('menu-navigate');

return $art_navigate;
}



//-----------------------------  Not Use  ---------------------------------




}
/*
if($event!=""){FotoArt::Event($event,'items_branch',$id);}
if($com=="add"){

$c_cont=$tpl->get("category-add-edit");	

}

if($com=="edit"){
	
$cat_data=FotoArt::catData($id);
$tpl->assign('cat_data',$cat_data);
$c_cont=$tpl->get("category-add-edit");	

}

if($com=="update"){
	
$arr_value['parent_id']=isset($parent_id)?$parent_id:0;
$arr_value['type']=$type;
$arr_value['caption']=$caption;
$arr_value['seo']=$seo!=""?$seo:Text::cirilToLatin($arr_value['caption']);
$arr_value['date']=isset($date) && $date!=""?$date:date("Y-m-d H:i:s");
$arr_value['meta_title']=$meta_title;
$arr_value['meta_description']=$meta_description;
$arr_value['meta_keywords']=$meta_keywords;
$arr_value['enabled']=$enabled;
$nowdata=getdate(time());


$table="items_branch";

if($id!=""){
	
$where['id']=$id;
$db = new mysql;
$res = $db->updateSQL ($table, $arr_value, $where);

}else{
$arr_value['pos']=isset($pos)?$pos:$nowdata[0];
$db = new mysql;
$id = $db->insertSQL ($arr_value, $table);	
	
}
$loc_url=str_replace("com=update","com=edit",PAGE_URL);
$loc_url=str_replace("&id=".$id."","",$loc_url);
$loc_url=$loc_url."&id=".$id."&upd=ok";

header("Location: ".$loc_url."");

}

if($com=="delete"){
	
$from="items_branch";	
$where['id']=$id;	
$db = new mysql;
$res = $db->deleteSQL ($from, $where);
$loc_url=str_replace("com=delete","com=view",PAGE_URL);
$loc_url=str_replace("&id=".$id."","",$loc_url);
header("Location: ".$loc_url."");
}

}


*/




?>