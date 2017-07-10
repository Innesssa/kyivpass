<?php
	$GLOBALS['VARDUMP_NUM']=1;
	$GLOBALS['VARSQL_NUM']=1;
	$GLOBALS['DEBUG']='';
	$GLOBALS['DEBUG_SQL']='';	
class SYS 
{
	
    function requestToString()
    {
        $str="";
        foreach ($_REQUEST as $key=>$val)
        {
            $str.='$'.$key.'="'.$val.'";';
        }
        return $str;
    }
    
    static function varDump($var,$filename,$line,$metka='')
    { 
        if($_SESSION['user_level'] == "9"){
          ob_start(); 
    $vd_num=$GLOBALS['VARDUMP_NUM'];
    	?>
    	<div id='sys<?=$vd_num?>' style='background-color:red;padding:5px;margin-top:7px;color:white;cursor:pointer;width:100px;'>&nbsp; <?=$metka!=''?$metka:'varDump '.$vd_num?> &nbsp;</div>
        <div id='system<?=$vd_num?>' style='display:none;padding:15px 25px;max-height:500px;overflow:auto;text-align:left;border:7px solid red;background:#fff;width:900px;'><div style='color:blue;font-size:12px;line-height:18px;'><?=$filename?> (<?=$line?>)</div>
<pre style="line-height:24px;font-size:14px;color:black"><?var_dump($var);?></pre>
        </div><script>$(document).ready(function(){$('#sys<?=$vd_num?>').click(function () {$('#system<?=$vd_num?>').toggle(200);});});</script><?
        $ob_text=ob_get_contents();
    ob_end_clean();
    $ob_text=str_replace('["','["<b>',$ob_text);
    $ob_text=str_replace('"]','</b>"]',$ob_text); 
    $ob_text=str_replace('   ',' ',$ob_text);       
    $GLOBALS['DEBUG'].=str_replace("=>\n "," =>",$ob_text); 
    $GLOBALS['VARDUMP_NUM']=$vd_num+1;
        }
    }
    
    static function varSQL($query,$filename,$line,$metka='')
    {   
        if($_SESSION['user_level'] == "9"){
        ob_start(); 
    $vd_num=$GLOBALS['VARSQL_NUM'];
    $qn=explode(" ",$query);
    	?>
    	<div id='varsql<?=$vd_num?>' style='background-color:blue;padding:5px;margin-top:7px;color:white;cursor:pointer;width:100px;'>&nbsp; <?=$metka!=''?$metka:$qn[0]." ".$vd_num?> &nbsp;</div>
        <div id='sql<?=$vd_num?>' style='display:none;padding:15px 25px;max-height:500px;overflow:auto;text-align:left;border:7px solid blue;background:#fff;width:900px;'>
        <div style='color:blue;font-size:12px;line-height:18px;'><?=$filename?> <?="(".$line.")"?></div><br>
        <div style='font-size:12px;line-height:18px;'><?=$query?></div>        	
         <?$raw = mysql_query ($query);
        while ($result = mysql_fetch_assoc ($raw)){
		$itog[] = $result;
		}?>
<pre style="line-height:24px;font-size:14px;color:black"><?var_dump($itog);?></pre>
        </div><script>$(document).ready(function(){$('#varsql<?=$vd_num?>').click(function () {$('#sql<?=$vd_num?>').toggle(200);});});</script><?
        $ob_text=ob_get_contents();
    ob_end_clean();
    $ob_text=str_replace('<br />','<br><div style="height:4px;"></div>',$ob_text);
    $ob_text=str_replace('SELECT','<b>SELECT</b>',$ob_text);    
    $ob_text=str_replace('FROM','<b>FROM</b>',$ob_text); 
    $ob_text=str_replace('WHERE','<b>WHERE</b>',$ob_text);   
    $ob_text=str_replace('ORDER BY','<b>ORDER BY</b>',$ob_text); 
    $ob_text=str_replace('GROUP BY','<b>GROUP BY</b>',$ob_text);      
    $ob_text=str_replace('UPDATE','<b>UPDATE</b>',$ob_text);  
    $ob_text=str_replace('SET','<b>SET</b>',$ob_text);   
    $ob_text=str_replace('INSERT','<b>INSERT</b>',$ob_text); 
    $ob_text=str_replace(' AND ',' <b style="color:#777">AND</b> ',$ob_text); 
    $ob_text=str_replace('["','["<b>',$ob_text);
    $ob_text=str_replace('"]','</b>"]',$ob_text); 
    $ob_text=str_replace('   ',' ',$ob_text);                        
    $GLOBALS['DEBUG_SQL'].=str_replace("=>\n "," =>",$ob_text); 
    $GLOBALS['VARSQL_NUM']=$vd_num+1;
        }
    }
    
    static function varSQLLog($metka='')
    {   
        if($_SESSION['user_level'] == "9"){
        ob_start(); 
    $vd_num=1;
    	?>
    	<div id='varsqllog<?=$vd_num?>' style='background-color:blue;padding:5px;margin-top:7px;color:white;cursor:pointer;width:100px;'>&nbsp; <?=$metka!=''?$metka:'varDump'.$vd_num?> &nbsp;</div>
        <div id='sqllog<?=$vd_num?>' style='display:none;padding:15px 25px;max-height:450px;overflow:auto;text-align:left;border:7px solid blue;background:#fff;width:900px;'><div style='color:blue;font-size:12px;line-height:18px;'><?//=$filename?> <?//="(".$line.")"?></div>
<pre style="margin:0px 0px 0px -195px;font-size:14px;line-height:19px;"><?
$mainFile = @file(getenv("DOCUMENT_ROOT")."/magic/MySQL.log");
$i = 0;$html='';
while (!empty ($mainFile[$i])){
$html.= stripslashes($mainFile[$i]);
$i = $i + 1; }
echo $html;
?></pre>
        </div><script>$(document).ready(function(){$('#varsqllog<?=$vd_num?>').click(function () {$('#sqllog<?=$vd_num?>').toggle(200);});});</script><?
        $ob_text=ob_get_contents();
    ob_end_clean();
    $ob_text=str_replace('<br />','<br><div style="height:4px;"></div>',$ob_text);
    $ob_text=str_replace('Query','     ',$ob_text);
    $ob_text=str_replace('SELECT','<b>SELECT</b>',$ob_text);    
    $ob_text=str_replace('FROM','<b>FROM</b>',$ob_text); 
    $ob_text=str_replace('WHERE','<b>WHERE</b>',$ob_text);   
    $ob_text=str_replace('ORDER BY','<b>ORDER BY</b>',$ob_text); 
    $ob_text=str_replace('GROUP BY','<b>GROUP BY</b>',$ob_text);      
    $ob_text=str_replace('UPDATE','<b>UPDATE</b>',$ob_text);  
    $ob_text=str_replace('SET','<b>SET</b>',$ob_text);   
    $ob_text=str_replace('INSERT','<b>INSERT</b>',$ob_text); 
    $ob_text=str_replace(' AND ',' <b style="color:#777">AND</b> ',$ob_text);                  
    $ob_text=str_replace('   ',' ',$ob_text);       
    $GLOBALS['DEBUG'].=str_replace("=>\n "," =>",$ob_text); 
     }
    }    
    
    static function deleteEmptyFiles($dir) 
    {
        $dirhandle = opendir($dir);
		  $filename = "";
		    
		  while(($filename = readdir($dirhandle)) !== false)
		  if (filesize($filename) == 0)
		      unlink($dir . $filename);
		    
		  closedir($dirhandle);
    }
 
     
}
?>
