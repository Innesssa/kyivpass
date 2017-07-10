<?php
abstract class Application_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{

    /** @var  string Table name, should be set in table classes */
    protected $_name;
    /**
     * Should use $this->info(Zend_Db_Table_Abstract::COLS) instead
     * @deprecated */
    protected $_fields;
    protected $_logger;





    /**
     * @return Zend_Log
     * @throws Zend_Exception
     */
    protected function getLogger()
    {
        if (!$this->_logger) {
            $this->_logger=Zend_Registry::get('Zend_Log');
        }
        return $this->_logger;
    }
}