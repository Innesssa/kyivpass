<?

class Setting
{
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
    
    
    
}
?>