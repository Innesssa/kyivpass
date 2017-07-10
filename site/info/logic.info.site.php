<?    
class Info
{
    function siteInfo()
    {   
        foreach ($_REQUEST as $key=>$val)
        {
            $str="$".$key."=\$val;";
            eval($str);
        }
SYS::varDump($_REQUEST,__FILE__,__LINE__,"INFO");      
        $seodata=explode("/",$seolink);
//$lang = $seodata[0];
        $mod  = $seodata[0];  //$mod." - \$mod<br />";
        $rub  = $seodata[1];  //echo $rub." - \$rub<br />";
        
        $iddata = explode("-",$seodata[1]);
        $id = $iddata[0];  //echo $id." - \$id<br />";
        
        $cont = Info::viewPage($rub);
//       
                           
        $tpl_item=new SiteModTpl;
        $tpl_item->assign('mod', $mod);
        $tpl_item->assign('rub', $rub);    
        $tpl_item->assign('page', $cont);
       
         
        $cont = $tpl_item->get('page');
//Sys::varDump($page);
        $page["content"]=$cont;
        return $page;
    }

    function viewPage($rub)
    {
//запрет на использование правой кнопки мыши 
$some_meta = "<script type='text/javascript'>
<!--
function click() {
if (event.button==2) {
alert(\"Правая кнопка мыши запрещена\");
}
}
document.onmousedown=click
// -->
</script>";
// /   
        $db =new mysql;
        $from="`info_item`";
        $where="`seolink`='{$rub}'";
        $row = $db->origSelectSQL("", $from, $where, "", "");

        $page["meta"]["title"]=$row[0]["meta_t_{$_SESSION['lang']}"];
        $page["meta"]["keywords"]=$row[0]["meta_k_{$_SESSION['lang']}"];
        $page["meta"]["description"]=$row[0]["meta_d_{$_SESSION['lang']}"];
        //if($rub == "Лицензии")$page["meta"]["some_meta"]=$some_meta;
        
        $page['content'] = $row[0]; 
//var_dump($page);                     
        return $page; 
    }
}
?>
