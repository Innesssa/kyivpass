<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_ServicesList extends Application_Model_DbTable_Abstract
{
    protected $_name = 'serviceslist';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array(
        "id",           //id запису
        "code",	        //Код послуги
        "title",	    //Назва послуги
        "title_short",	//Коротка назва послуги
        "title_print",	//текст для друку чеку
        "price",	    //Ціна без ПДВ
        "vat",	        //ПДВ
        "article",	    //артикул(для касового апарату)
        "group_vat",	//группа обліку (для касового аппарату)
        "description"   //Опис
    );


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

    public function getServicesList(){

        $select = $this->getAdapter()->select();
        $select->from(array("s"=>$this->_name))
            ->order("s.title ASC");

        return $this->getAdapter()->fetchAll($select);

    }



    public function setData(Array $data)
    {
        $toSave = array();
        if (!isset($data['id'])) $data['id'] = 0;

        foreach ($this->_fields as $field) {
            if ($field != "id" && isset($data[$field])) {
                $toSave[$field] = $data[$field];
            }
        }
        if (count($toSave) == 0) return false;
        if ($data['id'] > 0) {
            $this->update($toSave, "id=" . $data['id']);
            return $data['id'];
        }
        return $this->insert($toSave);

    }

    public function getServicesGroupByStation($DtS,$DtE,$statid){
        $sSQL = "select k.serviceid,SUM(k.price*k.num) as prices,SUM(k.vat*k.num) as vats,SUM(k.amount) as total,s.code,s.title,sum(k.num) as cnt from kassaoperation as k";
        $sSQL.= " Left Join ".$this->_name." as s ON s.id=k.serviceid ";
        $sSQL.= " where k.stationid='".(int)$statid."' AND k.operation='adService' AND ( k.dt BETWEEN '".$DtS."' AND '".$DtE."') ";
        $sSQL.= " Group By k.serviceid,s.code,s.title Order By s.title";
        return $this->getAdapter()->fetchAll($sSQL);
    }
    public function getServicesGroupByKassaUID($DtS,$DtE,$uid){
        $sSQL = "select k.kassauid,k.serviceid,SUM(k.amount) as total,SUM(k.price*k.num) as prices,SUM(k.vat*k.num) as vats,SUM(k.num) as nums, s.code,s.title from kassaoperation as k";
        $sSQL.= " Left Join ".$this->_name." as s ON s.id=k.serviceid ";
        $sSQL.= " where k.kassauid='".(int)$uid."' AND k.operation='adService' AND ( k.dt BETWEEN '".$DtS."' AND '".$DtE."') ";
        $sSQL.= " Group By k.kassauid,k.serviceid,s.code,s.title";
        //exit ($sSQL);
        return $this->getAdapter()->fetchAll($sSQL);
    }
    public function getAmountForOpenDay($dt,$uid){
        $a = explode(" ",$dt);
        //$sSQL = "select min(dt) as  d from kassaoperation where kassauid=".(int)$uid." AND operation='OpenDay' AND (dt BETWEEN '".$dt."' AND '".$a[0]." 23:59:59')";
        $sSQL = "select amount from kassaoperation where kassauid=".(int)$uid." AND operation='OpenDay' AND dt >= '".$dt."' Order By dt ASC Limit 1";
        $amount = 0;
        $dtl = "";
        $r = $this->getAdapter()->fetchRow($sSQL);
        return (empty($r['amount'])) ? 0 : $r['amount'];
        //echo $sSQL;
        //trace($r,1);
        if(empty($r['d'])) {
            $sSQL = "select max(dt) as  d from kassaoperation where kassauid=" . (int)$uid . " AND operation='OpenDay' AND dt <'" . $dt . "'";
            $r = $this->getAdapter()->fetchRow($sSQL);
            if (!empty($r['d'])) $dt = $r['d'];
        }else{
            $dt = $r['d'];
        }
        if(!empty($dt)){
            $r = $this->getAdapter()->fetchRow("select * from kassaoperation where kassauid=" . (int)$uid . " AND operation='OpenDay' AND dt='" . $dt . "' Order By id ASC");
            $amount = ( !empty($r['amount']) ) ? $r['amount'] : 0;

        }
        return $amount;
    }

    public function getLastOpenDay($DtS,$uid){
        $r = $this->getAdapter()->fetchRow("select amount from kassaoperation where kassauid=" . (int)$uid . " AND operation='OpenDay' AND dt>'" . $DtS . "' Order By id DESC");
        return ( !empty($r['amount']) ) ? $r['amount'] : 0;
    }

    public function getLastCloseDay($DtS,$uid)
    {
        $r = $this->getAdapter()->fetchRow("select symmcass_n6 from kassaoperation  where kassauid=" . (int)$uid . " AND operation='CloseDay'  Order By id DESC");
        return ( !empty($r['symmcass_n6']) ) ? ($r['symmcass_n6']/100.0) : 0;
    }



}