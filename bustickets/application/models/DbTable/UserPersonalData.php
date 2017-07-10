<?php

/**
 * Class Application_Model_DbTable_UserPersonalData
 */
class Application_Model_DbTable_UserPersonalData extends Application_Model_DbTable_Abstract
{
    protected $_name = 'userpersonaldata';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array("id","firstname","middlename","lastname");
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
        //trace($data);
        if(!isset($data['id'])) return false;
        foreach($this->_fields as $field){
            if(isset( $data[$field] ) )  $toSave[$field] = $data[$field];
        }
        //trace($toSave,1);
        if(count($toSave)==0) return false;
        $this->insert($toSave);
        return $data['id'];
    }

}