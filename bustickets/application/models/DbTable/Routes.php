<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_Routes extends Application_Model_DbTable_Abstract
{
    protected $_name = 'routes';
    protected $_lnkConveyorName = 'conveyorproperties';
    protected $_primary = 'id'; // primary column name
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array(
        "id",
        "code",//номер маршруту
        "title",//назва
        "conveyorid",//юридична особа-власник маршруту
        "vehicletypeid",//тип траспортного засобу
        "countplaces",//кількість мість
        "description",//коментар
        "paritet",//Рейс в паритеті
        "year",//рік
        "month",//місяц
        "day",//день
        "hour",//година
        "minute",//хвилина
        "date_begin",//Дата запуску рейсу
        "date_end",//Дата припинення рейсу
        "reserv",//массив заброньованих місць
        "stationrate",//тип рейсу

        "luggagetype",//	character varying(15)
        "luggage",//Розрахунок вартості багажа - процент або зональна тарифікація з трьох колонок

        "pricetype",//тип очислення ціни квитка
        "priceperkm",//ціна за км
        "ticket",//	text

        "back",//	text
        "platform",//	номер платформи

    );
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
    public function getRoute($id){
        $select = $this->getAdapter()->select();
        $select->from(array("r"=>$this->_name))
            ->joinleft(array('o'=>'organizationlist')  ,'o.id = r.conveyorid'      ,array('conveyor_name'=>'title'))
            ->joinleft(array('l'=>$this->_lnkConveyorName),'l.conveyorid = r.conveyorid'       ,array('insurerid_for_conveyor'=>'insurerid'))
            ->joinleft(array('i'=>'organizationlist'),'i.id = l.insurerid'       ,array('insurer_name'=>'title'))
            ->joinleft(array("v"=>'handbooks'),'r.vehicletypeid = v.id AND v.type=\''.Application_Model_Dictionary::VECHICLETYPE.'\'',array('vehicle_name'=>'title'))
            ->joinleft(array("c"=>'handbooks'),'r.stationrate = c.id AND c.type=\'stationrate\'',array('station_rate'=>'title'))
            //->joinleft(array("c"=>'handbooks'),'r.countplaces = c.id AND c.type=\'sitnumber\'',array('count_places'=>'title'))
        ;
        $select->where("r.id=".(int)$id);
        $select->order(array('hour ASC','minute ASC'));
        //echo $select;
        //exit;
        return $this->getAdapter()->fetchRow($select);
    }
    public function getRoutesList($aFilter){
        $select = $this->getAdapter()->select();
        $select->from(array("r"=>$this->_name))
              ->joinleft(array('o'=>'organizationlist')  ,'o.id = r.conveyorid'      ,array('conveyor_name'=>'title'))
              ->joinleft(array('l'=>$this->_lnkConveyorName),'l.conveyorid = r.conveyorid'       ,array('insurerid_for_conveyor'=>'insurerid'))
              ->joinleft(array('i'=>'organizationlist'),'i.id = l.insurerid'       ,array('insurer_name'=>'title'))
              ->joinleft(array("v"=>'handbooks'),'r.vehicletypeid = v.id AND v.type=\''.Application_Model_Dictionary::VECHICLETYPE.'\'',array('vehicle_name'=>'title'))
              ->joinleft(array("c"=>'handbooks'),'r.stationrate = c.id AND c.type=\'stationrate\'',array('station_rate'=>'title'))
              //->joinleft(array("c"=>'handbooks'),'r.countplaces = c.id AND c.type=\'sitnumber\'',array('count_places'=>'title'))
        ;
        $page =(!empty($aFilter['page']) && $aFilter['page']>0 ) ? (int)$aFilter['page'] : 1;
        if(!empty($aFilter['search_name']))  $select->where('title like "'.addslashes(trim($aFilter['search_title'])).'%"');
        $sort = 'r.code';
        $order = 'ASC';
        if(isset($aFilter['sort'])){
            if(in_array($aFilter['sort'],$this->_fields)){
                $sort = $aFilter['sort'];
                $order = $aFilter['order'] && $aFilter['order']=='desc'? 'DESC' : 'ASC';
            }
        }
        $select->order($sort." ".$order);
        //echo $select;
        //trace($select,1);
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
            //echo (isset($data[$field])) ? $field."=".$data[$field]."\n" : "";
            if($field == 'date_begin' && isset($data[$field])){
                 //dd.mm.yy
                 $aDT = explode(".",$data['date_begin']);
                 //trace($aDT);
                 if(count($aDT)==3) $toSave[$field] = $aDT[2]."-".$aDT[1]."-".$aDT[0]." 00:00:00";

            }else  if($field == 'date_end' && isset($data[$field])){
                //dd.mm.yy
                $aDT = explode(".",$data['date_end']);
                //trace($aDT);
                if(count($aDT)==3) $toSave[$field] = $aDT[2]."-".$aDT[1]."-".$aDT[0]." 23:59:59";

            }else  if($field=="paritet" ){
                $toSave["paritet"] = (empty($data[$field])) ? 'false' :'true';
            }else if($field=="priceperkm" ){
                if(isset($data[$field]) && is_numeric($data[$field])) $toSave["priceperkm"] = sprintf("%8.2f",$data["priceperkm"]);
            }else if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }

        if(isset($data['tariffs']) && !empty($data['routeid'])) {
            $data['id']=$data['routeid'];
            $aTariff= array();
            $aTariff['tariffsFixed'] = $data['tariffs'];
            $aTariff['tariffsCalc']=array();
            if(isset($data['calculatedticketsS'])) foreach($data['calculatedticketsS'] as $i=>$val){
                $aTariff['tariffsCalc'][$i]=array("s"=>$data['calculatedticketsS'][$i],"e"=>$data['calculatedticketsE'][$i],"p"=>$data['calculatedprice'][$i]);
            }
            $toSave['ticket']=json_encode( $aTariff );
        }

        if(isset($data['luggagepercent']) && !empty($data['routeid'])) {
            $aTariff= array();
            $aTariff['luggageFixed'] = $data['luggagepercent'];
            $aTariff['luggageCalc']=array();
            if(isset($data['luggageticketsS'])) foreach($data['luggageticketsS'] as $i=>$val){
                $aTariff['luggageCalc'][$i]=array("s"=>$data['luggageticketsS'][$i],"e"=>$data['luggageticketsE'][$i],"p"=>$data['luggageprice'][$i]);
            }
            $toSave['luggage']=json_encode( $aTariff );
        }

        if(isset($data['backticketstimeS']) && !empty($data['routeid'])) {
            $aTariff= array();
            $aTariff['backCalc']=array();

            if(isset($data['backticketstimeS'])) foreach($data['backticketstimeS'] as $i=>$val){
                $aTariff['backCalc'][$i]=array("s"=>$data['backticketstimeS'][$i],"e"=>$data['backticketstimeE'][$i],"p"=>$data['backticketsprice'][$i]);
            }
            $toSave['back']=json_encode( $aTariff );
        }
        //trace($toSave,1);
        if(count($toSave)==0) return false;
        //trace($toSave,1);
        if((int)$data['id']>0){
            // TODO: need check change type, if this route has childs then change code is forrbidden
            // if(!empty($toSave['code']) && $this->hasChild((int)$data['id']) ) unset($toSave['type']);
            $this->update($toSave,"id=".(int)$data['id']);
            return $data['id'];
        }
        return $this->insert($toSave);


    }


    public function hasChild($id){
        /* TODO: repaire!!
         if(empty($id) || !is_numeric($id)) return null;
        $db = new Application_Model_DbTable_Race();
        $r01 = $db->fetchRow("driverid=".(int)$id);
        $db = new Application_Model_DbTable_Routes();
        $r02 = $db->fetchRow("insurerid=".(int)$id." OR conveyorid=".(int)$id);
        if($r02 || $r01) return true;
        */
        return false;
    }





    public function getListRoutes($stationstartid,$stationendid,$aDate,$sTime="00:00"){
            $select = $this->_buildWhereForRaces($stationstartid,$stationendid,$aDate,$sTime);
            $select->order("r.minute");
            //echo("-------------------\n");
            //echo "Minute=". $minTemp . ',\n '.$select;
            //exit;

        return $this->getAdapter()->fetchAll($select);
    }


    public function getRace($id,$stationstartid,$stationendid,$aDate){
        $select = $this->_buildWhereForRaces($stationstartid,$stationendid,$aDate);
        $select->where("r.id=".(int)$id);
        return $this->getAdapter()->fetchRow($select);
    }

    public function getIORace($routeid,$stationid){
        return $this->_buildWhereInOutRace($routeid,"",$stationid);
    }
    public function getOutputRace($routeid){
        return $this->_buildWhereInOutRace($routeid,"min");
    }
    public function getInputRace($routeid){
        return $this->_buildWhereInOutRace($routeid,"max");
    }

    public function getRaces($stationID,$dt){
        return $this->_buildWhereInOutRaces($stationID,$dt,"");
    }

    public function getOutputRaces($stationID,$dt){
        return $this->_buildWhereInOutRaces($stationID,$dt,"min");
    }

    public function getInputRaces($stationID,$dt){
        return $this->_buildWhereInOutRaces($stationID,$dt,"max");
    }

    private function _buildWhereInOutRace($routeid,$fn,$stationID=0){
        $sSQL = " SELECT r.*,l1.stationid,l1.pos,l1.timeperiod,rc.id as rcid,rc.status as rcstatus from lnkstation2route l1,".$this->_name." r";
        $sSQL.= " LEFT JOIN races rc ON rc.routeid=r.id AND rc.date_begin='".date("Y-m-d")."' AND rc.stationid=".(int)$stationID;
        if($fn) $sSQL.= " AND rc.tp='" . ( ($fn=='min') ? 'out' : 'in' ) . "'";
        $sSQL.= " WHERE l1.routeid = r.id AND l1.routeid=".(int)$routeid;
        if((int)$stationID>0) $sSQL.= " AND l1.stationid=".(int)$stationID;
        $sSQL.= " AND l1.pos in ( select ".$fn."(l2.pos) from lnkstation2route l2  where  l1.routeid=l2.routeid )";//l1.stationid=l2.stationid and
        return $this->getAdapter()->fetchRow($sSQL);
    }

    private function _buildWhereInOutRaces($stationID,$dt,$fn){
        $aDT = explode(".",$dt);
        if(count($aDT)!=3) return null;
        $sSQL = " SELECT r.*,l1.stationid,l1.pos,l1.timeperiod,rc.id as rcid,rc.status as rcstatus,rc.places,rc.govnumber";
        $sSQL.= " ,rc.tm_received,rc.dt_received,rc.tm_opened,rc.dt_opened,rc.dt_sent,rc.tm_sent";
        //платформа
        $sSQL.= " ,rc.platform as rcplatform,rc.date_begin as rcdate,rc.time_begin as rctime";
        $sSQL.= " ,vp.title as vcplantitle,vp.description as vcplanplaces";
        $sSQL.= " ,vf.title as vcfackttitle";
        $sSQL.= " from lnkstation2route l1,".$this->_name." r";
        $sSQL.= " LEFT JOIN races rc ON rc.routeid=r.id AND rc.date_begin='".$aDT[2]."-".$aDT[1]."-".$aDT[0]."' AND rc.stationid=".(int)$stationID;
        $sSQL.= " LEFT JOIN handbooks vp ON vp.id=r.vehicletypeid ";
        $sSQL.= " LEFT JOIN handbooks vf ON vf.id=rc.vehicletitle::Integer ";
        // "AND rc.tp='" . ( ($fn=='min') ? 'out' : 'in' ) . "'";
        $sSQL.= " WHERE l1.routeid = r.id AND l1.stationid=".(int)$stationID;
        $sSQL.= " AND l1.pos in ( select ".$fn."(l2.pos) from lnkstation2route l2  where  l1.routeid=l2.routeid )";//l1.stationid=l2.stationid and
        $sSQL.= " AND ( '".$aDT[2]."-".$aDT[1]."-".$aDT[0]." 00:00:00' BETWEEN r.date_begin AND r.date_end )" ;
        $sSQL.= " AND ( r.year='*' OR  r.year='".$aDT[2]."' )";
        $sSQL.= " AND ( r.month='*' OR  r.month='".(int)$aDT[1]."' )";
        $day = ((int)$aDT[0])%2 ==0 ? '*/2' : '*/1';
//        $sSQL.= " AND ( r.day='*' OR  r.day='".(int)$aDT[0]."' OR r.day='".$day."' )";
        $sSQL.= " AND ( r.day='*' OR  r.day='".date("N",strtotime($aDT[2]."-".$aDT[1]."-".$aDT[0]))."' OR r.day='".$day."' )";
        $sSQL.= " Order By (r.hour::Integer) ASC,(r.minute::Integer) ASC";
        //trace($sSQL);
        return $this->getAdapter()->fetchAll($sSQL);
    }

    private function _buildWhereForRaces($stationstartid,$stationendid,$aDate,$sTime="00:00"){
            $select = $this->getAdapter()->select();
            //echo ($sTime." | ");
            if(date("Ymd")==$aDate[2].$aDate[1].$aDate[0]){
                if( (int)date("Hi") > (int)(str_replace(":","",$sTime)) ){
                    $sTime = date("H:i");
                }
            }
            //echo ($sTime." | ");
            $aTime = explode(":",$sTime);
            if(count($aTime)!=2){
                $sTime = (date("Ymd")==$aDate[2].$aDate[1].$aDate[0]) ?  date("H:i") : "00:00";
            }
            //echo ($sTime." | ");
            $aTime = explode(":",$sTime);
            $sMinutes = " ".$sTime.":00";
            $select->from(array("r"=>'getlistroutes_view'))
            ->joinLeft(array("rc"=>"races"),"r.id = rc.routeid AND rc.date_begin='".$aDate[2]."-".$aDate[1]."-".$aDate[0]."'",array("rcstatus"=>"status","rcplaces"=>"places","rcplatform"=>"platform"))
            ->where('r.stationstartid=' . (int)$stationstartid .' and r.stationendid='. (int)$stationendid)
            ->where( "'".$aDate[2]."-".$aDate[1]."-".$aDate[0].$sMinutes. "' BETWEEN r.date_begin AND r.date_end " );
            // if($sMinutes!=" 00:00:00")  $select->where( " cast(minute as int) >= ".((int)$aTime[1] + 5 + (int)$aTime[0] * 60) . "");
            $select->where( "r.year='*' OR  r.year='".date("Y")."'" );
            $select->where( "r.month='*' OR  r.month='".date("n")."'" );
            $day = $aDate[0]%2 ==0 ? '*/2' : '*/1';
            //$select->where( "day='*' OR  day='".date("N")."' OR day='".$day."'" );
            $select->where( "r.day='*' OR  r.day='".date("N",strtotime($aDate[2]."-".$aDate[1]."-".$aDate[0]))."' OR r.day='".$day."'" );
            $select->where( "rc.status ISNULL OR rc.status='opened' OR rc.status='received'");
            $select->order( array("r.hour ASC","r.minute ASC") );
            //echo $select;
            //exit;
            return $select;
    }




}