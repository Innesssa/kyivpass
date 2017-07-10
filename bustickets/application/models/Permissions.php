<?php
/**
 * Created by PhpStorm.
 * User: v0id
 * Date: 20.12.2014
 * Time: 16:06
 */

class Application_Model_Permissions {

 public function getAllLinked(){
     $dbP = new Application_Model_DbTable_Permissions();
     $ad = $dbP->getDefaultAdapter();
     $sSql = "
       select p.id,c.title as ctitle,a.title as atitle
       from permissions as p, lnkaction2controller as l, controllers as c, actions as a
       where p.id_c2a=l.id AND l.id_controller=c.id AND l.id_action=a.id
       order by c.title ASC, a.title ASC";
     return $ad->fetchAll($sSql);
 }

} 