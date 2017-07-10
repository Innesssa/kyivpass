<?	
 class Search{
 	 
    static function admSearch()
    {  
        define('MOD_NAME', 'Поиск');
        define('MOD_SEARCH_TABLE','search');

        $tpl_item=new AdmModTpl;
        
        foreach ($_REQUEST as $key=>$val)
        {
            $str="$".$key."=\$val;";
            eval($str);
        }

        $_SESSION['search_input'] = $_POST['search_input'];
        
        $search = Search::checkSearchRequest($_SESSION['search_input']);     
SYS::varDump($search,__FILE__,__LINE__,"Search input");

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
            $page["content"]=$tpl_item->get('search-error');
        }
//SYS::varDump($page,__FILE__,__LINE__); 
        return $page;
    }

    static function checkSearchRequest($val)
    {
        if(strlen($val) < 3 || strlen($val) > 50)
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
        $tpl_item=new AdmModTpl;    
        $db =new mysql;
/*        
        $from="`page`";
        $where = "`caption` LIKE '%{$rub}%' OR `content` LIKE '%{$rub}%'";
        $search_list_page = $db->origSelectSQL("", $from, $where, "");
SYS::varDump($search_list_page,__FILE__,__LINE__);
*/
/*
        $from="`art_article`";
        $where = "`caption` LIKE '%{$rub}%' OR `full_text` LIKE '%{$rub}%'";
        $search_list_article = $db->origSelectSQL("", $from, $where, "");
SYS::varDump($search_list_article,__FILE__,__LINE__);       
*/
/*
        $from="`items`";
        $where = "`caption` LIKE '%{$rub}%' OR `content` LIKE '%{$rub}%'";
        $search_list_items = $db->origSelectSQL("", $from, $where, "");
SYS::varDump($search_list_items,__FILE__,__LINE__);        
*/
/*
        $from=TABLE_ITEMS;
        $where = "`1Cid` LIKE '%{$rub}%' OR `caption_short` LIKE '%{$rub}%' OR `caption_full` LIKE '%{$rub}%' OR `description_short` LIKE '%{$rub}%' OR `description_full` LIKE '%{$rub}%' OR `note_1` LIKE '%{$rub}%' OR `note_2` LIKE '%{$rub}%' OR `note_3` LIKE '%{$rub}%'";
        $search_list_product = $db->origSelectSQL("", $from, $where, "");
*/
        $from="shop_item";
        $where = "`1Cid` LIKE '%{$rub}%' OR `caption` LIKE '%{$rub}%' OR `desc_full` LIKE '%{$rub}%' ";
        $search_list_product = $db->origSelectSQL("", $from, $where, "");
//SYS::varDump($search_list_article,__FILE__,__LINE__,"SearchResult shop_item");        
        
        if(//$search_list_page == NULL && 
           //$search_list_article == NULL &&  
           $search_list_product == NULL)
        {
            $page["meta"]["title"]="Поиск по сайту";
            $page["content"]=$tpl_item->get('search-error');
        }
        else
        {
//            $tpl_item->assign('list_page', $search_list_page);
//            $tpl_item->assign('list_article', $search_list_article);
//            $tpl_item->assign('list_items', $search_list_items);
            $tpl_item->assign('list_product', $search_list_product);            
            $page["content"]=$tpl_item->get('search-list');
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
    
    function viewSearchListAll()
    {
        $tpl_item = new AdmTpl;    

        $db =new mysql;
        $from="`mr_health`";
        $where="1=1";
        $row = $db->origSelectSQL("", $from, $where, "`caption`", "");
        
        $tpl_item->assign('list', $row);
        
        $page["content"]=$tpl_item->get('health_list_all');
        
        $page["meta"]["title"]=$row[0]["category_name"];
        $page["meta"]["description"]=$row[0]["category_name"];
        if($row !== 0)
            {
                $txt = new Text;
                foreach($row as $k=>$v)
                {
                     $page["meta"]["keywords"] .= $txt->cut($v['meta_t']).", ";
                }    
            }
        
        return $page; 
    }
    
    function viewSearch($rub)
    {
        //$_SESSION['ip'] = getenv("REMOTE_ADDR");
        $tpl_item = new AdmTpl;    

        $rub=urldecode($rub);
       // $rub = iconv('UTF-8', 'Windows-1251', $rub);
        
        $db =new mysql;
        $from="`mr_health`";
        $where="`seolink`='{$rub}'";
        $row = $db->origSelectSQL("", $from, $where, "", "1");
        
        $tpl_item->assign('list', $row[0]);
        
        $page["content"]=$tpl_item->get('health');
        
        $page["meta"]["title"]=$row[0]["meta_t"];
        $page["meta"]["keywords"]=$row[0]["meta_k"];
        $page["meta"]["description"]=$row[0]["meta_d"];

        
        return $page; 
    }


}
?>
