<?php
	
include_once ("cfg.php");
include ("sys.util.php");
include ("ini.util.php");		
include ("time.util.php");
include ("text.util.php");
include ("gd.util.php");
include_once ("mysql.class.php");
include ("template.class.php");



class SiteTpl extends Tpl
{
	function SiteTpl ()
	{
		$this->template_dir = SITE_PATH.'/site/tpl';
	}
}

class AdmTpl extends Tpl
{
	function AdmTpl ()
	{
		$this->template_dir = SITE_PATH.'/cms/tpl';
	}
}

SYS::varDump($GLOBALS,__FILE__,__LINE__,"GLOBALS");

?>
