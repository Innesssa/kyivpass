<?

class mandatoryShopSetting
{
    static function mandatoryElements()
    {
        define('CURRENCY','грн');

/*        define('PROCENT_NADBAVKI_0_50','25');
        define('PROCENT_NADBAVKI_50_100','20');
        define('PROCENT_NADBAVKI_100_200','10');
        define('PROCENT_NADBAVKI_200_500','10');
        define('PROCENT_NADBAVKI_500_1000','5');
        define('PROCENT_NADBAVKI_1000_','3');
*/        
        define('SITE_NAME', 'Имя Сайта');
    
        define('EMAIL_BOSS',      'you@mkr.com.ua');
        define('EMAIL_ADMIN',     'you@mkr.com.ua');
        define('EMAIL_FEEDBACK',  'you@mkr.com.ua');
        define('EMAIL_WEBMASTER', 'you@mkr.com.ua');
        define('EMAIL_OFFICE',    'you@mkr.com.ua');
        define('EMAIL_SHOP',    'you@mkr.com.ua');
        define('EMAIL_UNSUBCRIBE','you@mkr.com.ua');
        define('CONTACT_TEL_MOB', '+38 (050) 445 15 75');
        define('CONTACT_TEL_OFFICE', '+38 (050) 445 15 75');
        define('CONTACT_ICQ', 'larron');
        define('CONTACT_SKYPE', 'Yurk_in');
        define('CONTACT_ADRESS', 'г. Киев');
     
if($_SESSION['lang']=="en"){
    define('FEEDBACK_TITLE', 'Feedback');
    define('FEEDBACK_OK_TITLE', 'Thank you');
    define('FEEDBACK_NAME', 'Your name');
    define('FEEDBACK_PHONE', 'Phone');
    define('FEEDBACK_MESS', 'Your message');
    define('FEEDBACK_MESS_SEND', 'Your message has been sent');
    define('FEEDBACK_ONLINE', 'Send');
}else{
    define('FEEDBACK_TITLE', "Зворотній зв'язок");
    define('FEEDBACK_OK_TITLE', 'Дякуємо');
    define('FEEDBACK_NAME', "Ваше ім'я");
    define('FEEDBACK_PHONE', 'Телефон');
    define('FEEDBACK_MESS', 'Ваше повідомлення');
    define('FEEDBACK_MESS_SEND', 'Ваше повідомлення відправлено');
    define('FEEDBACK_ONLINE', 'Відправити');    
}


        mandatoryShopSetting::getShopSetting("shop_setting"); // Устанавливаем курс доллара и минимальную сумму бесплатной доставки
  

    }
    
    static function setSetting()
    {
	$query = "SELECT * FROM `shop_setting`";
        $res = mysql_query($query);
		mysql::queryError($res,$query);

        while ($row = mysql_fetch_assoc($res))
        {
        	   $setting[] = $row;
        }

        return $setting;
    }
/*
    static function getSetting($val)
    {
	    $db = new mysql;
        
        
        $from="`shop_setting`";
        $where="`description` = '{$val}'";
        $row = $db->origSelectSQL("", $from, $where, "", "");
        
        if($val == "kurs_usd"){define('KURS_USD',$row[0]["value"]);}
        if($val == "min_summa_dostavki"){define('MIN_SUMMA_DOSTAVKI',$row[0]['value']);}
         

        return $res = $row;
    }
*/    
    static function getShopSetting($file_name)
    {
        $GLOBALS["shop_setting"] = INI::Get($file_name);
        $shop_setting = $GLOBALS["shop_setting"];

        define('KURS_USD',$shop_setting[0]["value"]);  
        define('MIN_SUMMA_DOSTAVKI',$shop_setting[1]['value']);
    }
//include_once (SITE_PATH.PATH_MAGIC."/ini/shop_setting.ini");
//define('KURS_USD',$shop_setting[0]["value"]);    
 /*   
    static function getMinSummaDostavki()
    {
	    $db = new mysql;
        
        $from="`shop_setting`";
        $where="`description` = 'min_summa_dostavki'";
        $row = $db->origSelectSQL("", $from, $where, "", "");

        define('MIN_SUMMA_DOSTAVKI',$row[0]['value']);
        return $min_summa_dostavki = $row;
    }
 */   
    
/*
    function setSetting($function_name)
    {
        $db = new mysql;
        
        $from="`cfg_global`";
        $where="`function_name` = '{$function_name}' AND `enable`='1'";
        $row = $db->origSelectSQL("", $from, $where, "", "");
        if($row)
        {
            return TRUE;
        }       
        else
        {
            return FALSE;
        } 
    }
 
    
    function getBirthday()
    {
        $db = new mysql;
        $select = "";
        $from="`contacts`";
        $where="`birthday_day`='".date("d")."' AND `birthday_month`='".date("m")."'";
        $birthday = $db->origSelectSQL($select, $from, $where, "", "");
        $birthday = $birthday[0];
        if($birthday !== NULL)
        {
            foreach($birthday as $key=>$val)
            {
               $_SESSION['birthday'][$key] = $val."<br />";
            }
        }
        //mail("psydema@ukr.net","Днюха", "tetx");
    }
    
    function getNavigate($val)
    {
        unset($_SESSION['navigate'][$val]);
        
        $db =new mysql;
        $select = "";
        $from="`nav`";
        $where="`location`='{$val}' AND `showing_{$_SESSION['lang']}`='Y'";
        $_SESSION['navigate'][$val] = $db->origSelectSQL($select, $from, $where, "`order_show`", "");
        
    }
    
    function getNavigateItems()
    {
        unset($_SESSION['navigate']['items']);
        
        $db =new mysql;
        $select = "";
        $from=TABLE_ITEMS_CATEGORY;
        $where="`enabled`='1'";
        $_SESSION['navigate']['items'] = $db->origSelectSQL($select, $from, $where, "`order_show`", "");
//SYS::varDump($_SESSION['navigate']['items'],__FILE__,__LINE__);
    }
    
    function getImagesMain()
    {
        $imagecode=array(1,2,3,4,5);
        shuffle($imagecode);
        $_SESSION["images_main"] = $imagecode;
    }

    
    function getVideogid()
    {
        unset($_SESSION['videogid']);
        
        $db =new mysql;
        $select = "id, seo, image_small, caption"; 
        $from="`art_article`";
        $where="`type`='videogid' AND `enabled`='1'";
        $_SESSION['videogid'] = $db->origSelectSQL($select, $from, $where, "`date` DESC", "1"); 
    }
    
*/
    
    
}
?>
