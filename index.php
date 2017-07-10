<?
//error_reporting(E_ALL^E_STRICT);
//ini_set('display_errors','On');
/*
session_start();
unset($_SESSION); 
session_destroy();
//header('Location: index.php')
*/

$MySQL_log=getenv("DOCUMENT_ROOT")."/magic/MySQL.log";
//unlink($filename);
$filewrite=fopen($MySQL_log,"w+");
$paper='';
fwrite($filewrite,$paper);
fclose($filewrite);

include_once ("kernel/cfg.php");		
include_once ("site/logic.php");	

?>