<?
if (!defined('SID')) { 
session_start();
}


include_once("../kernel/kernel.php");
include_once ("../mandatory/shopSetting.mandatory.mod.php");  //уже не в ядре)

include_once ("shop/logic.shop.admin.php");
include_once ("art/logic.art.admin.php");
include_once ("cart/logic.cart.admin.php");
include_once ("message/logic.message.admin.php");
include_once ("banner/logic.banner.admin.php");
include_once ("gallery/logic.gallery.admin.php");
include_once ("user/logic.user.admin.php");
include_once ("param/logic.param.admin.php");
include_once ("search/logic.search.admin.php");
include_once ("lang/lang.class.php");
include_once ("setting/setting.class.php");


foreach ($_REQUEST as $key=>$val)
{
    $str="$".$key."=\"".$val."\";";
    eval($str);
}

class AdmModTpl extends Tpl{
function AdmModTpl (){
$this->template_dir = SITE_PATH.'/cms/'.$_GET["mod"].'/tpl';
}}

// Установки магазина
mandatoryShopSetting::mandatoryElements(); // Обязательные настройки магазина. Курс валют, мин сумма доставки, название сайта, адреса, телефоны и прочая инфа
// /Установки магазина

if(isset($mod) && $mod=='gallery'){
	$c_cont=Gallery::admGallery();
	} 
	
if(isset($mod) && $mod=='shop'){
	$c_cont=Shop::admShop();
	}
if(isset($mod) && $mod=='art'){
        $c_cont=Art::admArt();
    }   
if(isset($mod) && $mod=='cart'){
        $c_cont=Cart::admCart();
    }        
if(isset($mod) && $mod=='user'){
	$c_cont=User::admUser();
	}
if(isset($mod) && $mod=='param'){
	$c_cont=Param::admParam();
	}
if(isset($mod) && $mod=='search'){
        $c_cont=Search::admSearch();
    }
if(isset($mod) && $mod=='message'){
        $c_cont=Message::admMessage();
    }
if(isset($mod) && $mod=='banner'){
        $c_cont=Banner::admBanner();
    }

//*********** Языки и еже с ними *********//
define(LANG_DEFAULT, Lang::getDefaultSiteLang()); //язык сайта по умолчанию;
if(Setting::setSetting("multi_lang")) // Если на сайте разрешена мультиязычность
{
     $site_langs = Lang::getSiteLangs();  // Получаем доступные языки   
     if($site_langs !== NULL)
     {   
         foreach($site_langs as $key=>$val)
         {   
             if(!$_SESSION['lang']) // Если языки еще не переключались, устанавливаем язык по умолчанию
             {
                 $_SESSION['lang'] = LANG_DEFAULT;  // Загоняем дефаултный язык в сессию 
             }
             else
             {
                  if($mod == $val['lang'])  // Если юзер переключает язык
                 {   
                     Lang::swichLang($val['lang']);  // Загоняем нужный язык в сессию
                 } 
             }
         }
     } 
}  
//*********** /Языки и еже с ними *********//


    $tpl = new AdmTpl;

        $tpl->assign('main_menu', $main_menu);
if(isset($c_cont)){
        $tpl->assign('c_cont', $c_cont);
}        
SYS::varDump($_REQUEST,__FILE__,__LINE__,'REQUEST'); 
SYS::varDump($_SESSION,__FILE__,__LINE__,'SESSION'); 
SYS::varDump(array_reverse(get_defined_constants()),__FILE__,__LINE__,'DEFINE');
SYS::varSQLLog('MySQL log'); 

//$shop_setting["kurs_dollara"]="15.90";
//$shop_setting["min_summa_dostavki"]="1500";

//INI::Set($shop_setting,'shop_setting');

//$shop_setting=INI::Get('shop_setting');

//SYS::varDump($shop_setting,__FILE__,__LINE__,'shop_settin'); 

/*
$in=$_SESSION;
INI::Set($in,'metka');

$ini=INI::Get('metka');

$tm=INI::Life('metka');

if($tm>10){
INI::Delete('metka');
}

*/





    if(isset($com) && $com=="product")
    {
        $tpl->display('tab');
    }
    else if(isset($display) && $display=="tab")
    {
        $tpl->display('tab');
    } 
    else if(isset($display) && $display=="companion")
    {
        $tpl->display('tab2');
    }     
    else if(isset($display) && $display=="ajax")
    {
        $tpl->display('ajax');
    }        
    else
    {
    	unset($_SESSION['dplay']);
        $tpl->display('index');
    }

?>
