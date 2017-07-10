<?php
class Zend_View_Helper_GetNavDown extends Zend_View_Helper_Abstract{
    public function GetNavDown($isAll=null)
    { 
        $dbPages = new Application_Model_DbTable_Pages();
        return $dbPages->getDownMenu($isAll);
    }
    
}