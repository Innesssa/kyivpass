<?php


class INI {

function Set($arr,$name){
$str = "<?php "."\r\n";
$filewrite=fopen(getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini","w+");
if($arr!=NULL){foreach($arr as $key=>$val){
$str=$str.'$'.$name.'["'.$key.'"]="'.addslashes(str_replace('"]="',"2rr2",str_replace('";',"2tz2",$val))).'";';
$str = $str."\r\n";	
}}
$str = $str."?>";
SYS::varDump($arr,__FILE__,__LINE__,'INI::Set');
fwrite($filewrite,$str);
fclose($filewrite);
   
}

function SetXXL($arr,$name){
$i = 0;$str = "<?php "."\r\n";
$filewrite=fopen(getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini","w+");
if($arr!=NULL){foreach($arr as $keya=>$vala){
	if($vala!=NULL){foreach($vala as $key=>$val){
    $str=$str.'$'.$name.'['.$keya.']["'.$key.'"]="'.addslashes(str_replace('"]="',"2rr2",str_replace('";',"2tz2",$val))).'";';
	}}
$str = $str."\r\n";
$i = $i+1;}}
$str = $str."?>";
SYS::varDump($arr,__FILE__,__LINE__,'INI::SetXXL');
fwrite($filewrite,$str);
fclose($filewrite);
   
}

function Get($name){	
	$filename=getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini";
    include($filename);
    $fmame=$$name;
    SYS::varDump($fmame,$filename,$name,'INI::Get');
    return $fmame;
    }   
    
    
function GetXXL($name){
	
$mainFile = @file(getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini");

$i = 1;$q=0;$arr = array();

while (!empty ($mainFile[$i])){
$pere=explode('";',$mainFile[$i]);
$pusto = array_pop($pere);
	if($pere!=NULL){foreach($pere as $key=>$val){
	$per=explode('"]="',$val);
	$arr[$i][str_replace('$'.$name.'['.$q.']["',"",$per[0])]=stripslashes(str_replace("2rr2",'"]="',str_replace("2tz2",'";',$per[1])));
	}}
    $i = $i+1;$q = $q+1;}
    SYS::varDump($arr,$mainFile,$name,'INI::GetXXL');
return $arr;
}
      
function Life($name){	
	$filename=getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini";
	$filetime=filemtime($filename);
	$now=time();
    $life=$now-$filetime;
	SYS::varDump($life,$filename,$name,'INI::Life');
	return $life;
    }
    
    
function Delete($name){	
	$filename=getenv("DOCUMENT_ROOT")."/magic/ini/".$name.".ini";
	SYS::varDump($filename,$filename,$name,'INI::Delete');
    unlink($filename); 
    }   
    

}  
