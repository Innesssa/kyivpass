<?php

/**
 * Class Application_Model_DbTable_UserPersonalData
 */
class Application_Model_DbTable_LnkUser2System extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lnkuser2system';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array("id","idu","id_entity");
    protected $_elm = array();

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

    public function setData(Array $data){
        $toSave = array();
        if(!isset($data['idu'])) return false;
        foreach($this->_fields as $field){
                if(isset( $data[$field] ) )$toSave[$field] = $data[$field];
        }
        if(count($toSave)==0) return false;
        $this->insert($toSave);
        return $data['idu'];
    }

}