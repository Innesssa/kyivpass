<?php

class Tpl
{
    var $template_dir    =  'tpl';
    var $ct_data    =  array();
    
	function assign($d_name,$data)
	{
    $this->ct_data[$d_name]=$data;
	}
	
	function display($tpl){
	if($_REQUEST!=NULL){	
	foreach ($_REQUEST as $key=>$val){
    $str="$".$key."=\"".$val."\";";
    eval($str);}}
    if($_SESSION!=NULL){
	foreach ($_SESSION as $key=>$val){
    $str="$".$key."=\"".$val."\";";
    eval($str);}}  
	foreach($this->ct_data as $name=>$data){ 
    $str="$".$name." = \$data ;";
    eval($str);
    }
    require ($this->template_dir."/".$tpl.".tpl.htm");
	}
	
	function get($tpl)
	{
	if($_REQUEST!=NULL){	
	foreach ($_REQUEST as $key=>$val){
    $str="$".$key."=\"".$val."\";";
    eval($str);}}
    if($_SESSION!=NULL){
	foreach ($_SESSION as $key=>$val){
    $str="$".$key."=\"".$val."\";";
    eval($str);}}  
	foreach($this->ct_data as $name=>$data){ 
    $str="$".$name." = \$data ;";
    eval($str);
    }
    ob_start();
    require ($this->template_dir."/".$tpl.".tpl.htm");
    $b_text=ob_get_contents();
    ob_end_clean();
    return $b_text;
	}
		
}

 ?>