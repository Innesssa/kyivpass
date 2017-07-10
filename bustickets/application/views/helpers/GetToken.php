<?php
class Zend_View_Helper_GetToken extends Zend_View_Helper_Abstract{
    static private $frm=null;
    public function GetToken($isInit=false,$onlyNewToken=false){
        self::$frm = (!self::$frm) ?  new Application_Form_CSFRtoken() : self::$frm;
        if($isInit || $onlyNewToken){
            self::$frm->csrf_token->initCsrfToken();
        }
        if($onlyNewToken){
            return self::$frm->csrf_token->getSession()->hash;
        }
        return self::$frm;
    }
    
}