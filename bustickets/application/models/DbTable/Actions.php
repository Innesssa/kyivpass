<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_Actions extends Application_Model_DbTable_Abstract
{
    protected $_name = 'actions';
    protected static $_instance;
    protected static $_fullName = '';


    protected function _setupTableName()
    {
        self::$_fullName = $this->_name;
    }

    public static function getTableName()
    {
        if (self::$_fullName != '') {
            return self::$_fullName;
        }
        // проверяем актуальность экземпляра
        if (null === self::$_instance) {
            // создаем новый экземпляр
            self::$_instance = new self();
        }
        // возвращаем значение созданного или существующего экземпляра
        return self::$_instance->getTableName();
    }

    public function getAll($type){
        return $this->fetchAll(null,array("title ASC"));
    }

    


}