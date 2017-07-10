<?php

/**
 * Class Application_Model_DbTable_UserLoginData
 */
class Application_Model_DbTable_OrganizationList extends Application_Model_DbTable_Abstract
{
    protected $_name = 'organizationlist';
    protected $_lnkConveyorName = 'conveyorproperties';
    protected $_conveyorType = 1272;
    protected $_insurerType = 1271;
    protected $_primary = 'id'; // primary column name
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array("id","title","type","ipn","edrpou","mfo","accountnr","bank","legaladdress","realaddress","email","printedfield","code","vat");
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

    public function selectDataByType($tp){
        return $this->fetchAll("type=".(int)$tp,"title");
    }

    public function getOrgList($aFilter){

        //$select = $this->select();


        $select = $this->getAdapter()->select();
        $select->from(array($this->_name))
               ->joinleft('handbooks',$this->_name.'.type = handbooks.id',array('type_name'=>'handbooks.title'));
        $select->where($this->_name.".type<>".$this->_conveyorType);

        $page =(!empty($aFilter['page']) && $aFilter['page']>0 ) ? (int)$aFilter['page'] : 1;

        if(!empty($aFilter['search_type'])) $select->where('rate="'.(int)$aFilter['search_type'].'"');
        if(!empty($aFilter['search_title'])) $select->where('title like "'.addslashes(trim($aFilter['search_title'])).'%"');
        if(!empty($aFilter['search_ipn'])) $select->where('ipn like "'.addslashes(trim($aFilter['search_ipn'])).'%"');
        if(!empty($aFilter['search_edrpou'])) $select->where('edrpou like "'.addslashes(trim($aFilter['search_edrpou'])).'%"');
        if(!empty($aFilter['search_mfo'])) $select->where('mfo like "'.addslashes(trim($aFilter['search_mfo'])).'%"');
        if(!empty($aFilter['search_accountnr'])) $select->where('accountnr like "'.addslashes(trim($aFilter['search_accountnr'])).'%"');
        if(!empty($aFilter['search_bank'])) $select->where('bank like "'.addslashes(trim($aFilter['search_bank'])).'%"');
        if(!empty($aFilter['search_address'])) $select->where('legaladdress like "'.addslashes(trim($aFilter['search_address'])).'%" OR realaddress like "'.addslashes(trim($aFilter['search_address'])).'%"');
        if(!empty($aFilter['search_email'])) $select->where('email like "'.addslashes(trim($aFilter['search_email'])).'%"');
        if(!empty($aFilter['search_printedfield'])) $select->where('printedfield like "'.addslashes(trim($aFilter['search_printedfield'])).'%"');
        if(!empty($aFilter['search_code'])) $select->where('code like "'.addslashes(trim($aFilter['search_code'])).'%"');
        if(!empty($aFilter['search_vat'])) $select->where('vat like "'.addslashes(trim($aFilter['search_vat'])).'%"');

        $sort = 'id';
        $order = 'DESC';

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


    private function _setSQLforConveyors(){
        $select = $this->getAdapter()->select();
        $select->from($this->_name)
            ->joinLeft($this->_lnkConveyorName,$this->_lnkConveyorName.".conveyorid = ".$this->_name.".id",array('insurerid','benefits','insurerrate'))
            ->joinleft('handbooks',$this->_name.'.type = handbooks.id',array('type_name'=>'handbooks.title'));
        $select->where($this->_name.".type=".$this->_conveyorType);
        //echo $select;
        return $select;
    }

    public function getConveyor($id){
        //$select = $this->_setSQLforDrivers();
        $select = $this->_setSQLforConveyors();
        $select->joinLeft(array("insurer"=>$this->_name),"insurer.id = ".$this->_lnkConveyorName.".insurerid",array('insurer_name'=>'insurer.title','insurer_print_name'=>'printedfield'));
        $select->where($this->_name.".id=".(int)$id);
        return $this->getAdapter()->fetchRow($select);
    }

    public function getAllConveyors(){
        $select = $this->_setSQLforConveyors();
        $select->order($this->_name.".title");
        return $this->getAdapter()->fetchAll($select);
    }

    public function getConveyorsList($aFilter){

        $select = $this->_setSQLforConveyors();

        $page =(!empty($aFilter['page']) && $aFilter['page']>0 ) ? (int)$aFilter['page'] : 1;

        if(!empty($aFilter['search_type'])) $select->where('rate="'.(int)$aFilter['search_type'].'"');
        if(!empty($aFilter['search_title'])) $select->where('title like "'.addslashes(trim($aFilter['search_title'])).'%"');
        if(!empty($aFilter['search_ipn'])) $select->where('ipn like "'.addslashes(trim($aFilter['search_ipn'])).'%"');
        if(!empty($aFilter['search_edrpou'])) $select->where('edrpou like "'.addslashes(trim($aFilter['search_edrpou'])).'%"');
        if(!empty($aFilter['search_mfo'])) $select->where('mfo like "'.addslashes(trim($aFilter['search_mfo'])).'%"');
        if(!empty($aFilter['search_accountnr'])) $select->where('accountnr like "'.addslashes(trim($aFilter['search_accountnr'])).'%"');
        if(!empty($aFilter['search_bank'])) $select->where('bank like "'.addslashes(trim($aFilter['search_bank'])).'%"');
        if(!empty($aFilter['search_address'])) $select->where('legaladdress like "'.addslashes(trim($aFilter['search_address'])).'%" OR realaddress like "'.addslashes(trim($aFilter['search_address'])).'%"');
        if(!empty($aFilter['search_email'])) $select->where('email like "'.addslashes(trim($aFilter['search_email'])).'%"');
        if(!empty($aFilter['search_printedfield'])) $select->where('printedfield like "'.addslashes(trim($aFilter['search_printedfield'])).'%"');
        if(!empty($aFilter['search_code'])) $select->where('code like "'.addslashes(trim($aFilter['search_code'])).'%"');
        if(!empty($aFilter['search_vat'])) $select->where('vat like "'.addslashes(trim($aFilter['search_vat'])).'%"');

        $sort = 'id';
        $order = 'DESC';

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

        foreach($this->_fields as $field){
            if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }
        if(count($toSave)==0) return false;
        if((int)$data['id']>0){
            //TODO: need check change type, if this organisation has childs then change type is forrbidden
            if(!empty($toSave['type']) && $this->hasChild((int)$data['id']) ) unset($toSave['type']);
            $this->update($toSave,"id=".(int)$data['id']);

        }else {
            $data['id'] = $this->insert($toSave);
        }
        //trace($data,1);
        if(isset($data['insurerid']) && (int)$data['insurerid']>0 && $data['type']=='1272'){
            $data['benefits']= (!empty($data['benefits'])) ? json_encode( $data['benefits']) : '';
            $this->getAdapter()->query("delete from ".$this->_lnkConveyorName." where conveyorid=".$data['id']);
            $this->getAdapter()->query("insert into ".$this->_lnkConveyorName." (conveyorid,insurerid,benefits,insurerrate) values(".$data['id'].",".(int)$data['insurerid'].",'".$data['benefits']."','".$data['insurerrate']."')");
        }
        return  $data['id'];
    }


    public function hasChild($id){
        /*
        if(empty($id) || !is_numeric($id)) return null;
        $db = new Application_Model_DbTable_Race();
        $r01 = $db->fetchRow("driverid=".(int)$id);
        $db = new Application_Model_DbTable_Routes();
        $r02 = $db->fetchRow("insurerid=".(int)$id." OR conveyorid=".(int)$id);
        if($r02 || $r01) return true;
        */
        return false;


    }


    /*
    public function getConveyorList($aFilter){
        $select = $this->getAdapter()->select();
        $select->from('conveyorlist_view');
        $select->where("conv_id=".$aFilter);
        //$select->where($this->_name.".type=".$this->_conveyorType);
        $select->order("conv_title");
        return $this->getAdapter()->fetchRow($select);
    }
    */

}