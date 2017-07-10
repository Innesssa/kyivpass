<?php

/**
 * Class Application_Model_DbTable_UserEventsSysLog
 */
class Application_Model_DbTable_UserEventsSysLog extends Application_Model_DbTable_Abstract
{
    protected $_name = 'usereventssyslog';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array( "id",//	integer id дії
                                "uid",//	integer	id користувача
                                "dt",//	timestamp	час дії
                                "controller",// блок
                                "action",// дії
                                "permissions",//	права користувача
                                "request",//	text	вхідні данні
                                "answer",//	text	вихідні данні
    );
    protected function _setupTableName()
    {
        self::$_fullName = $this->_name;
    }


    public function setData(Array $data){
        $toSave = array();
        foreach($this->_fields as $field){
            if(isset( $data[$field] ) && $field!="id" )  $toSave[$field] = $data[$field];
        }
        $toSave['dt']=date("Y-m-d H:i:s");
        if(count($toSave)==0) return false;
        return $this->insert($toSave);
    }

    public function getLog($data){

        $select = $this->getAdapter()->select(); //  select();
        $select->from($this->_name,array($this->_name.".*"));
        $select->joinLeft("userlogindata",$this->_name.".uid=userlogindata.id",array("kassaname"=>"userlogindata.login") );

        foreach($this->_fields as $field){

            if(isset( $data[$field] ) ) {
                switch ($field) {
                    case "id" :
                    case "uid":     $select->where($this->_name.".".$field."=".(int)$data[$field]);
                                    break;
                    case "controller" :
                    case "action":  $select->where($this->_name.".".$field."='".addslashes($data[$field])."'");
                                    break;
                    case "request" :
                    case "answer":  $select->where($this->_name.".".$field." like '".addslashes($data[$field])."'");
                                    break;

                }
            }


        }

        //$select->where($this->_name.".routeid=".(int)$routeID." AND ".$this->_name.".dt_time_begin='".$date."'");

        $select->order($this->_name.".id");
        //echo $select;
        //exit;
        return $this->getAdapter()->fetchAll($select);
    }

}