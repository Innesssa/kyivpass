<?php

//if (!defined('SID')) { 
session_start();
//}

include_once (SITE_PATH."/kernel/kernel.php");
include_once (SITE_PATH."/kernel/auth.class.php");

include_once ("lang/lang.class.php");

include_once ("setting/setting.class.php");
include_once ("main/logic.main.site.php");
include_once ("art/logic.art.site.php");
include_once ("shop/logic.shop.site.php");
include_once ("gallery/logic.gallery.site.php");
include_once ("message/logic.message.site.php");
include_once ("banner/logic.banner.site.php");
//include_once ("cart/logic.cart.site.php");
include_once ("search/search.mod.site.php");



//include_once ("menu.mod.site.php");
//include_once ("block.mod.site.php");
//include_once ("page.mod.site.php");
//include_once ("items.mod.site.php");
//include_once ("article.mod.site.php");
//include_once ("catalog.mod.site.php");
//include_once ("shopping.mod.site.php");

//include_once ("feedback.mod.site.php");	

//include_once (SITE_PATH."/cms/price.mod.admin.php");
//include_once (SITE_PATH."/cms/price_erc.php");


include_once (SITE_PATH."/mandatory/shopSetting.mandatory.mod.php");






// Установки магазина
mandatoryShopSetting::mandatoryElements(); // Обязательные настройки магазина. Курс валют, мин сумма доставки, название сайта, адреса, телефоны и прочая инфа
// /Установки магазина

foreach ($_REQUEST as $key=>$val)
{
    $str="$".$key."=\$val;";
    eval($str);
}

$ext=$_REQUEST["ext"];

$seolink=$_SERVER['REQUEST_URI'];

if(isset($seolink))
{
    $seodata = explode("/",$seolink);
    $mod = $GLOBALS["mod"] = $seodata[1];
    $tag1 = $GLOBALS["tag1"] = $seodata[2];
    $tag2  = $GLOBALS["tag2"]= $seodata[3];    
    $param = $GLOBALS["param"] = $seodata[4];
}

class SiteModTpl extends Tpl{
function SiteModTpl (){
$this->template_dir = SITE_PATH.'/site/'.$GLOBALS["mod"].'/tpl';
}}

SYS::varDump($seodata,__FILE__,__LINE__,'seodata');



if($mod==""){$mod="main";}


if(isset($mod) && $mod=='main'){
	$c_cont=Main::siteMain();
	}
if(isset($mod) && $mod=='shop'){
	$c_cont=Shop::siteShop();
	}
if(isset($mod) && $mod=='art'){
	$c_cont=Art::siteArt();
	}
if(isset($mod) && $mod=='cart'){
	$c_cont=Cart::siteCart();
	}
if(isset($mod) && $mod=='gallery'){
	$c_cont=Gallery::siteGallery();
	}
if(isset($mod) && $mod=='message'){
	$c_cont=Message::siteMessage();
	}


if(isset($ext) && $ext == 'ajax')
{
	echo $c_cont;
}
else
{

//$main_menu=Menu::viewMenu();
//$tpl = new SiteTpl;

    //$main_menu=Menu::viewMenu();
    
/*  ------------- Потуги -------------- */    
   //$main_menu=Shop::siteShop(); // Через БД
//    $menu=INI::Get('site_main_menu'); // Через файл   
//    $main_menu=$menu["main_menu"];
   
//SYS::varDump($main_menu,__FILE__,__LINE__,'main_menu');
/*  -------------- /Потуги ------------- */  

    $tpl = new SiteTpl;


$tpl->assign('c_cont', $c_cont["content"]);
$tpl->assign('meta', $c_cont["meta"]);
    
    
SYS::varDump($_REQUEST,__FILE__,__LINE__,'REQUEST'); 
SYS::varDump($_SESSION,__FILE__,__LINE__,'SESSION'); 

SYS::varSQLLog('MySQL log'); 

    
if(isset($print) && $print == "print")
{
    $tpl->display('print');
}
else
{
	if($_GET["display"]!="ajax"){
    $tpl->display('index');		
	}else{
    echo $c_cont["content"];
    }
}
    unset($main_menu);
    unset($tpl);
}
?>
