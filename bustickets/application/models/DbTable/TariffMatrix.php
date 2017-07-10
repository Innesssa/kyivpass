<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_TariffMatrix extends Application_Model_DbTable_Abstract
{
    protected $_name = 'tariffmatrix';
    protected $_primary = 'id'; // primary column name
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array("id","title_short","title_full","title_print","benefit_perc","recoup","document","description");


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

    public function getAll(){
        $select = $this->getAdapter()->select();
        $select->from(array("b"=>$this->_name))
            ->joinleft(array("o"=>'organizationlist'),'b.recoupid = o.id',array('recoup_name'=>'o.title'))
            ->order('title_short ASC');
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
        //return $this->fetchAll(null,array("title_short ASC"));
    }

    public function setData(Array $data){
        $toSave = array();
        if(!isset($data['id'])) $data['id']=0;


        foreach($this->_fields as $field){
            if($field!="id" && isset($data[$field])){
                if($field=="document"){
                    $data[$field]=(!empty($data[$field])) ? "TRUE" : "FALSE";
                }else {
                    $toSave[$field] = $data[$field];
                }
            }
        }
        if(count($toSave)==0) return false;
        if($data['id']>0){
            $this->update($toSave,"id=".$data['id']." AND par_id=".$data['par_id']);
            return $data['id'];
        }
        return $this->insert($toSave);

    }
    public function selectChildsByID($id){
        //TODO need implement
        return true;
    }

    


}