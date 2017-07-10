<?php

/**
 * Class Application_Model_DbTable_UserPersonalData
 */
class Application_Model_DbTable_LnkStation2Route extends Application_Model_DbTable_Abstract
{
    protected $_name = 'lnkstation2route';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array ("id","routeid","stationid","pos","timeperiod","holdtime","distantion","description");
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

    public function getRoutesTime($routeid){
        $sSQL = "select timeperiod as tm from ".$this->_name." where routeid=".(int)$routeid." Order By pos DESC Limit 1";
        $r = $this->getAdapter()->fetchRow($sSQL);
        return isset($r['tm']) ? $r['tm'] : 0;
    }

    public function getStation($routeid,$id){
        $result = null;
        $r = $this->fetchRow("routeid=".(int)$routeid." AND id=".(int)$id);
        if(!$r) return $result;
        $db = new Application_Model_DbTable_Dictionary();
        // TODO Разобраться - почему не хочет работать getFullPath
        //$station = $db->getFullPath($r->stationid,Application_Model_Dictionary::VILLAGES);
        $station = $db->getItemTitle($r->stationid);
        //trace($r,0);
        $result = $r->toArray();
        //$result['station']=isset($station['name']) ? $station['name'] : '';
        $result['station']=isset($station['title']) ? $station['title'] : '';
        //trace($result,0);
        return  $result;

    }


    public function getStationList($routeId){
        $select = $this->getAdapter()->select();
        $select->from(array("l"=>"lnkstation2route"))
            /*
                id	integer
                routeid	integer
                stationid	integer
                pos	integer
                timeperiod	integer
                holdtime	integer
                distantion	numeric(10,2)
                price	numeric(10,2)
                priceinzone	numeric(10,2)
                description
            */
            //->join(array("s1"=>'handbooks'),'l.stationid = s1.id AND s1.type=\''.Application_Model_Dictionary::STATION.'\'',array('station_name'=>'title'));
            ->join(array("s2"=>'handbooks'),'l.stationid = s2.id AND s2.type IN (\''. Application_Model_Dictionary::VILLAGES .'\',\''. Application_Model_Dictionary::STATION .'\')',array('station_name'=>'title'));
        $select->where("l.routeid=".(int)$routeId);
        $select->order(array("pos ASC","id ASC"));
        return $this->getAdapter()->fetchAll($select);
    }


    public function setStation2Route($data){
       if(!isset($data['routeid']) || (int)$data['routeid']==0) return false;
        $toSave = array();
        if(!isset($data['id'])) $data['id']=0;

        foreach($this->_fields as $field){
            if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }
        //Zend_Debug::dump($data,$label='Data');
        //Zend_Debug::dump($toSave,$label='ToSave');
        if(count($toSave)==0) return false;
        if((int)$data['id']>0){
            // TODO: need check change type, if this route has childs then change code is forrbidden
            // if(!empty($toSave['code']) && $this->hasChild((int)$data['id']) ) unset($toSave['type']);
            $this->update($toSave,"id=".(int)$data['id']);
            return $data['id'];
        }
        return $this->insert($toSave);
    }
}