<?php

/**
 * Class Application_Model_DbTable_UserLoginData
 */
class Application_Model_DbTable_UserLoginData extends Application_Model_DbTable_Abstract
{
    protected $_name = 'userlogindata';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array("id","login","password");
    protected $_elm = array();

    public static function convertPassword($pwd){
        $sold = "201501192131v0id";
        return md5("bustickets".trim($pwd).$sold);
    }
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

    public function falselLogin($username){
        $this->update(array('quantity_false' => new Zend_Db_Expr('quantity_false + 1')),"login='".addslashes($username)."'");
    }
    public function clearFalselLogin($username){
        $this->update(array('quantity_false' => 0),"login='".addslashes($username)."'");
    }
    public function getUserByLogin($login,$password){
        $select = $this->getAdapter()->select();
        $select->from(array("l"=>$this->_name))
            ->join(    array('p'=>'userpersonaldata'),'l.id = p.id'       ,array('lastname','middlename','firstname'))
            ->join(    array('s'=>'userservicedata'), 'l.id = s.id'       ,array('persnum','workstart','workend','defaultstation'))
            ->join(    array('r'=>'lnkuser2system')  ,'l.id = r.idu'      ,array('id_entity'))
            ->joinLeft(array('d'=>'permissionroles') ,'r.id_entity = d.id',array('perm_title'=>'title','perm_description'=>'description'))
        ->where("l.login='".addslashes($login)."' and password='".self::convertPassword($password)."'");
        return $this->getAdapter()->fetchRow($select);
    }

    public function getUserByID($id){
        $select = $this->getAdapter()->select();
        $select->from(array("l"=>$this->_name))
            ->join(    array('p'=>'userpersonaldata'),'l.id = p.id'       ,array('lastname','middlename','firstname'))
            ->join(    array('s'=>'userservicedata'), 'l.id = s.id'       ,array('persnum','workstart','workend','defaultstation'))
            ->join(    array('r'=>'lnkuser2system')  ,'l.id = r.idu'      ,array('id_entity'))
            ->joinLeft(array('d'=>'permissionroles') ,'r.id_entity = d.id',array('perm_title'=>'title','perm_description'=>'description'))
            ->where("l.id='".(int)$id."'");
        return $this->getAdapter()->fetchRow($select);
    }

    public function getUsersList($aFilter){
        $select = $this->getAdapter()->select();
        $select->from(array("l"=>$this->_name))
            ->join(    array('p'=>'userpersonaldata'),'l.id = p.id'       ,array('lastname','middlename','firstname'))
            ->join(    array('s'=>'userservicedata'), 'l.id = s.id'       ,array('persnum','workstart','workend','defaultstation'))
            ->join(    array('r'=>'lnkuser2system')  ,'l.id = r.idu'      ,array('id_entity'))
            ->joinLeft(array('d'=>'permissionroles') ,'r.id_entity = d.id',array('perm_title'=>'title','perm_description'=>'description'))
        ;
        $page =(!empty($aFilter['page']) && $aFilter['page']>0 ) ? (int)$aFilter['page'] : 1;
        if(!empty($aFilter['search_name']))  $select->where('title like "'.addslashes(trim($aFilter['search_title'])).'%"');
        $sort = 'l.login';
        $order = 'ASC';
        if(isset($aFilter['sort'])){
            if(in_array($aFilter['sort'],$this->_fields)){
                $sort = $aFilter['sort'];
                $order = $aFilter['order'] && $aFilter['order']=='desc'? 'DESC' : 'ASC';
            }
        }
        $select->order($sort." ".$order);
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(25);
        $paginator->setPageRange(10);
        return $paginator;
    }


    public function setData(Array $data){
        $toSave = array();
        if(!isset($data['id'])) $data['id']=0;
        //trace($data);
        foreach($this->_fields as $field){
            if($field=="password" && isset($data[$field]) ){
                $toSave[$field] = self::convertPassword( $data[$field] );
            }else if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }
        //trace($toSave);
        if(count($toSave)==0) return false;
        $this->getAdapter()->beginTransaction();
        try {
            if ((int)$data['id'] > 0) {
                if (!empty($toSave['type']) && $this->hasChild((int)$data['id'])) unset($toSave['type']);
                $this->update($toSave, "id=" . (int)$data['id']);
                $id = $data['id'];
            } else {
                $id = $this->insert($toSave);
            }
            //trace($id);
            if ($id) {
                $dbPersonal = new Application_Model_DbTable_UserPersonalData();
                $dbPersonal->delete("id=" . (int)$id);
                $data['id']=$id;
                $a = $dbPersonal->setData($data);

                $dbPersonal = new Application_Model_DbTable_LnkUser2System();
                $dbPersonal->delete("idu=" . (int)$id);
                $aStore = array("idu" => $id, "id_entity" => $data['id_entity']);
                $b = $dbPersonal->setData($aStore);

                $dbPersonal = new Application_Model_DbTable_UserServiceData();
                $dbPersonal->delete("id=" . (int)$id);
                $aStore = array("id" => $id, "persnum" => $data['persnum'], "workstart" => $data['workstart'],"workend" => $data['workend'],"defaultstation" => $data['defaultstation']);

                $b = $dbPersonal->setData($aStore);

                if( $a==$id && $a==$b) {
                    $this->getAdapter()->commit();
                    return $id;
                }else{
                    $this->getAdapter()->rollBack();
                    return false;
                }


            }
        }catch(Exception $e){
            $this->getAdapter()->rollBack();
            throw new Exception($e->getCode().":".$e->getMessage());
        }
        return false;
    }


    public function hasChild($id){
        if(empty($id) || !is_numeric($id)) return null;
        $db = new Application_Model_DbTable_Race();
        $r01 = $db->fetchRow("conveyorid=".(int)$id);
        $db = new Application_Model_DbTable_Routes();
        $r02 = $db->fetchRow("insurerid=".(int)$id." OR conveyorid=".(int)$id);
        if($r02 || $r01) return true;
        return false;
    }


}