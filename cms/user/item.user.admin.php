<?php

class UserItem {	

    function admItem() {
        foreach ($_REQUEST as $key=>$val){
            $str="$".$key."=\$val;";
            eval($str);
        }

        $tpl_item=new AdmModTpl;


        if($com=="list"){	
            $_SESSION['refresh_page']=PAGE_URL;

            $item_list=UserItem::itemListIdData($tag);
            $tag_list=UserTag::tagListIdData();

            $tpl_item->assign('item_list',$item_list);
            $tpl_item->assign('tag_list',$tag_list);

            $c_cont=$tpl_item->get("item-list");
        }

        if($cmd=="update"||$cmd=="insert"){
	
            $connect_real = array_unique($connect);

            $arr_value['connect']=';';
            
            if($connect_real!=NULL){
                foreach($connect_real as $key=>$val){
                    $arr_value['connect'].=$val.';';	
                }
            }

            $arr_value['name'] = Text::cut($name);
            $arr_value['surname'] = Text::cut($surname);
            $arr_value['email'] = Text::cut($email);
// ------------ level --------------            
            $level = intval($level);
            if($level){
                $level = $level; // http://habrahabr.ru/post/143035/
            }
            $arr_value['level'] = ($level !=="") ? $level : "2";
// ------------ /level --------------             
            $arr_value['phone'] = Text::cut($phone);
            $arr_value['organization'] = Text::cut($organization);
            $arr_value['address'] = Text::cut($address);
            $arr_value['job'] = Text::cut($job);
            $arr_value['date']=$date!=""?$date:date("Y-m-d H:i:s");
            $arr_value['ban'] = $ban;
            
            $nowdata=getdate(time());
            $arr_value['pos']=$pos!=''&&$pos!=0?$pos:$nowdata[0];

            $table=MOD_ITEM_TABLE;
            SYS::varDump($arr_value,__FILE__,__LINE__,'arr_value');
            if($id!=""&&$id!="new"){
                $where['id']=$id;
            
                $db = new mysql;
                $res = $db->updateSQL ($table, $arr_value, $where);
            }
            else{
                $arr_value['login'] = $login;
                $arr_value['password'] = md5($password);
                $password_1 = Text::cut($password_1);
                
                $db = new mysql;
                $id = $db->insertSQL ($arr_value, $table);
                rename(MOD_ITEM_IMG_PATH."/new",MOD_ITEM_IMG_PATH."/".$id);
                $url="/cms/?".str_replace("com=add","com=edit", getenv("QUERY_STRING"));
                $url=str_replace("id=new","id=".$id, $url);
                header("Location: ".$url."&save=ok");
            }
            $_SESSION['dplay']="tab";
        }


if($com=="add"||$com=="edit"){
// Change password
    if(isset($password)){
        UserItem::changePassword($id,$password_old,$password,$password_1);
    }
// /Change password	
$tag_list=UserTag::tagListIdData();
$tpl_item->assign('tag_list',$tag_list);

$tag_list1=UserTag::tagListIdData(1);
$tpl_item->assign('tag_list1',$tag_list1);

$tag_list2=UserTag::tagListIdData(2);
$tpl_item->assign('tag_list2',$tag_list2);

if($com=="edit"){
$item_data=UserItem::itemData($id);
$tpl_item->assign('item_data',$item_data);	
	
}
if($com=="delete"){

	
}



$c_cont=$tpl_item->get("item-add-edit");	
}

// Change password
if(isset($field) && $field == "pass"){
    
    $c_cont=$tpl_item->get("item-edit-pass");	
}
// /Change password	


return $c_cont;
}

function itemListData($tag){
	
$select="";
$from=MOD_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby="ban ASC, pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row;
}

function itemListIdData($tag){
	
$select="";
$from=MOD_ITEM_TABLE;
$where="connect LIKE '%;".$tag.";%'";
$sortby="ban ASC, pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

if($row!=NULL){foreach($row as $key=>$val){
$arrcat[$val["id"]]=$val;
}}

return $arrcat;
}

function itemCountTag($tag){
	
$select="COUNT(id)";
$from=MOD_ITEM_TABLE;
$where="connect LIKE '%".$tag.";%'";
$sortby="pos DESC";

$db = new mysql;
$row = $db->origSelectSQL($select, $from, $where, $sortby,"0,70");

return $row[0]["COUNT(id)"];
}

function itemData($id){
	
$select="";
$from=MOD_ITEM_TABLE;
$where["id"]=$id;

$db = new mysql;
$row = $db->selectSQL($select, $from, $where, "", 1);

return $row[0];
}

static function getUserDo($uid){
    $query = "SELECT * FROM `user_action` WHERE `uid`='{$uid}' ORDER BY `id` DESC";
    $res = mysql_query($query);
    mysql::queryError($res,$query);
    
    while ($row = mysql_fetch_assoc($res))
    {
       $user_action[] = $row;
    }
//SYS::varDump($user_action,__FILE__,__LINE__,"user_action");
    return $user_action;
}

static function userDecodingQuery($query){
    $mess = Text::decodingQuery($query); 
	return $mess;	
}

static function userCanChangeUser($sess_id,$sess_level,$pacient_id,$pacient_level){
/* 
 * Правило поведения юзера в зависимости от уровня (пока для редактирования карточек юзеров)
 * 0,1,2   - Юзеры магазина (без прав в админке);
 * 3,4,5,6 - Редакторы магазина (ограниченные права);
 * 7,8     - Администрация магазина
 * 
 * $sess_id и $sess_level - id и level юзера в сессии (залогинивщегося)
 * $pacient_id и $pacient_level - id и level юзера, с карточкой которого пытается работать залогинившийся юзер
 */
 

	//if(defined(Web2Net)){return TRUE;} // вход под Web2Net - можно все. 
	if($sess_level == 9){return TRUE;} // вход под Web2Net - можно все. 
	else
    {
        if($sess_level < 2){return FALSE;} // безправные юзеры - клиенты магазина НИЗЯ
        else{
            if($sess_level < $pacient_level){return FALSE;}	 // уровень залогинившегося меньше чем пациента уровень НИЗЯ
            else{
                if($sess_level > $pacient_level){return TRUE;} // уровень залогинившегося больше чем пациента уровень МОНА
                //elseif($sess_level == $pacient_level){return FALSE;} // карточка равноправного юзера НИЗЯ
                elseif($sess_level == $pacient_level && $sess_id == $pacient_id){return TRUE;} // своя карточка МОНА
                else{return FALSE;}	
            }
        }
    }
 /*
    switch ($sess_level) {
    case $sess_level < 2 : return FALSE; break; // безправные юзеры - клиенты магазина НИЗЯ
    case $sess_level < $pacient_level : return FALSE; break; // уровень залогинившегося меньше чем пациента уровень НИЗЯ
    case $sess_level > $pacient_level : return TRUE; break; // уровень залогинившегося больше чем пациента уровень МОНА
    case $sess_level == $pacient_level && $sess_id == $pacient_idl : return TRUE; break; // своя карточка МОНА
    default: return FALSE; 
    }
*/

}

    static function userCanSee($user_level,$level_limit){
    /*  Скрыто-открыто для юзера
        $user_level - level юзера в сессии (залогинивщегося)
        $level_limit - минимальный level для доступа
    */  
        if($user_level >= $level_limit){return TRUE;}
        else{return FALSE;}
    }
    
    static function userSelf($sess_id,$user_id){
    /*  Проверка на то, что юзер правит свои данные
        $sess_id - id юзера в сессии (залогинивщегося)
        $user_id - id юзера
    */  
        if($sess_id == $user_id){return TRUE;}
        else{return FALSE;}
    }
    
    function checkOldPassword($password, $user_id){
        $query = "SELECT `password` FROM ".USERS_TABLE." WHERE `id`='{$user_id}' LIMIT 1";
        $res = mysql_query($query);
        mysql::queryError($res,$query);
        $pas = mysql_fetch_array($res,1);          

        foreach($pas as $k=>$v)
        {
            if(md5($password) == $v)
            {
                return true; 
            }
            else
            {
                return false;
            }
        }
    }
    
    function changePassword($user_id,$password_old,$password,$password_1){
        if($password == $password_1){
            if(get_magic_quotes_gpc()){ 
                $password = stripslashes($password);
            }
            $password = mysql_real_escape_string(trim($password));
        
            $arr_value['password'] = md5($password);
            
            $db = new mysql;
            $table = USERS_TABLE;

            if($user_id !== ""){
				$oldPass = UserItem::checkOldPassword($password_old, $user_id);
                if($oldPass){
                    $where['id'] = $_SESSION['user_id'];
                    $res = $db->updateSQL ($table, $arr_value, $where);
                }
            }
            else{
                // Надо что-то придумать..    
            }
        }
    }

/*

function Event($event,$table,$id){
$from=$table;
$where="id={$id}";
$db = new mysql;
$row = $db->origSelectSQL("id,pos", $from, $where,"pos ASC","0,1");

$pos_start=$row[0]["pos"];
$id_start=$row[0]["id"];

if($event=='moveDown'){
$from2=$table;	
$where2="pos<{$pos_start}";
$db = new mysql;
$row2 = $db->origSelectSQL("id,pos", $from2, $where2,"pos DESC","0,1");
$pos_end=$row2[0]["pos"];?><br><?
$id_end=$row2[0]["id"];
}

if($event=='moveUp'){
$from2=$table;	
$where2="pos>{$pos_start}";
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



if($com=="delete"){
	
$from="art_Fotogallery";	
$where['id']=$id;	
$db = new mysql;
$res = $db->deleteSQL ($from, $where);
$loc_url=str_replace("com=delete","com=view",PAGE_URL);
$loc_url=str_replace("&id=".$id."","",$loc_url);
header("Location: ".$loc_url."");
}


*/


}
?>
