<?php
	
class Time {
	
function formatMonth($data=''){
if($data=='')$data=date("Y-m-d H:i:s");
$gdate=getdate(strtotime($data));
//var_dump($gdate);
$months[1] = "января";
$months[2] = "февраля";
$months[3] = "марта";
$months[4] = "апреля";
$months[5] = "мая";
$months[6] = "июня";
$months[7] = "июля";
$months[8] = "августа";
$months[9] = "сентября";
$months[10] = "октября";
$months[11] = "ноября";
$months[12] = "декабря";
$ndata="".$gdate['mday']." ".$months[$gdate['mon']]." ".$gdate['year']."";
return $ndata;
}

function formatGetTime($data){
$gdate=strtotime($data);
//var_dump($gdate);
$ndata=date("H:i:s",$gdate);
return $ndata;
}


}

?>