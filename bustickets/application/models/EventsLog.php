<?php
/**
 * Created by PhpStorm.
 * User: v0id
 * Date: 20.12.2014
 * Time: 16:06
 */

class Application_Model_EventsLog {

   static private $_db = null;
   static private $_required = array("uid","controller","action","group","request","answer");

   static private function _connect(){
       if(!self::$_db){ self::$_db = new Application_Model_DbTable_UserEventsSysLog(); }
       return self::$_db;
   }

   static public function addToLog($uid,$controller,$action,$group,$request,$answer){
       foreach(self::$_required as $fld){
           if(empty($fld)) {
               $error = 'Поле:'.$fld.' порожнє';
               throw new Exception($error);
           }
       }

       return self::_connect()->setData(array(
           "uid"        =>  $uid,
           "controller" =>  $controller,
           "action"     =>  $action,
           "permissions"=>  $group,
           "request"    =>  $request,
           "answer"     =>  $answer
       ));
   }







} 