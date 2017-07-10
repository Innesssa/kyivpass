<?php
class Zend_View_Helper_GetNavUp extends Zend_View_Helper_Abstract{
    public function GetNavUp($isAll=null)
    {
        
        $dbPages = new Application_Model_DbTable_Pages();
        return $dbPages->getUpMenu($isAll);
    }
    
}