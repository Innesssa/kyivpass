<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_Races extends Application_Model_DbTable_Abstract
{
    protected $_name = 'races';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array(
        "id",//id запису
        "routeid",//номер рейсу
        "tp",//тип рейсу прибуття відправлення
        "status",//статус прйняття відправлення рейсу
        "govnumber",//держ номер ТЗ
        "driver_name",//ПІБ водія
        "date_begin",//	date дата відправлення рейсу
        "tm_received",//час прибуття
        "time_begin",//	time 	час відправлення рейсу
        "vehicletitle",//тип траспортного засобу
        "description",//	коментар
        "fio",//	відправив
        "stationid",//станція
        "places",
        "places_busy",
        "total",
        "fail_type",
        'dispuid',
        'dt_received',//	дата коли прийнятий
        'tm_opened',  //	час коли відкритий
        'dt_opened',  //	дата коли відкритий
        'dt_sent',    //	дата коли відправлений
        'tm_sent',	 //	час коли відпрвлений
        'platform'   // платформа
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

    public function getRacesGroupByDays($DtS,$DtE,$ststionID){
        $sSQL = "select r.*,h.title as vehiclename,ro.code as routecode,ro.hour,ro.minute,ro.title as routetitle from ".$this->_name." as r";
        $sSQL.= " Left Join handbooks as h ON h.id=CAST(coalesce(r.vehicletitle, '0') AS integer)";
        $sSQL.= " Left Join routes as ro ON ro.id=r.routeid";
        $sSQL.= " where r.stationid=".$ststionID."	AND ( r.date_begin between '".$DtS."' AND '".$DtE."')";
        $sSQL.= " Order By r.date_begin,r.time_begin,r.routeid";
        return $this->getAdapter()->fetchAll($sSQL);
    }

    public function getAllRacesGroupByRate($DtS, $DtE, $ststionID){


        $sSQL = " select rc.routeid,r.title,r.code,count(rc.id) as num,r.stationrate ";
        $sSQL.= " ,d.title as route_title,o.title as conv_title ";
        $sSQL.= " from races as rc, routes as r  ";
        $sSQL.= " Left Join handbooks as d    ON r.stationrate=d.id ";
        $sSQL.= " Left Join organizationlist o ON r.conveyorid=o.id ";
        $sSQL.= " where rc.routeid=r.id AND rc.stationid='".$ststionID."'";
        $sSQL.= " AND ( rc.date_begin between '".$DtS."' AND '".$DtE."') ";
        $sSQL.= " Group By rc.routeid,r.title,r.code,r.stationrate,d.title,o.title ";
        $sSQL.= " Order By r.stationrate,r.code ";
        $rs = $this->getAdapter()->fetchAll($sSQL);
        if($rs) foreach($rs as $key => $r){
            $sSQL = " select \"status\",count(id) as nm  ";
            $sSQL.= " from races   ";
            $sSQL.= " where routeid=".$r["routeid"]." AND stationid='".$ststionID."'";
            $sSQL.= " AND ( date_begin between '".$DtS."' AND '".$DtE."') ";
            $sSQL.= " Group By \"status\" ";
            $rStatuses = $this->getAdapter()->fetchAll($sSQL);
            $rs[$key]["sent"]=0;
            $rs[$key]["fail"]=0;
            if($rStatuses) foreach($rStatuses as $s){
                if($s['status']=='sent') $rs[$key]["sent"]=$s['nm']*1;
                else $rs[$key]["fail"]+=$s['nm']*1;
            }
            $sSQL = " select SUM(stat_tariff_with_benefits_vat+conv_tariff_with_benefits_vat+stat_tariff_with_benefits+conv_tariff_with_benefits+insurer_tariff_with_benefits) as total, count(id) as pass  ";
            $sSQL.= " from tickets   ";
            $sSQL.= " where routeid=".$r["routeid"]." AND station_buy='".$ststionID."'";
            $sSQL.= " AND ( dt_time_begin between '".$DtS." 00:00:00' AND '".$DtE." 23:59:59') ";
            $sSQL.= " AND \"status\" in ('paid') ";
            $rStatuses = $this->getAdapter()->fetchRow($sSQL);
            $rs[$key]["total"]=0;
            $rs[$key]["pass"]=0;
            if($rStatuses){
                $rs[$key]["total"]=$rStatuses['total']*1.0;
                $rs[$key]["pass"]=$rStatuses['pass']*1;
            }


        }
        return $rs;


    }

    public function getFailRacesGroupByConv($DtS, $DtE, $ststionID){
        $sSQL = "select rc.routeid,rc.status,rc.fail_type ";
        $sSQL.= " ,r.code,o.title, r.conveyorid , count(rc.id) as num";
        $sSQL.= " from races as rc, routes r ";
        $sSQL.= " Left Join organizationlist o ON r.conveyorid=o.id ";
        $sSQL.= " where rc.stationid=".(int)$ststionID;
        $sSQL.= " AND ( rc.date_begin between '".$DtS."' AND '".$DtE."') ";
        $sSQL.= " AND rc.status in ('failed','forbidden','opened') ";
        $sSQL.= " AND rc.routeid=r.id ";
        $sSQL.= " Group By rc.routeid,rc.status,rc.fail_type  ,r.code,o.title, r.conveyorid ";
        $sSQL.= " Order By r.conveyorid,r.code ";
        return $this->getAdapter()->fetchAll($sSQL);


    }

    public function setData(Array $data){
        $toSave = array();

        if(!isset($data['id'])) $data['id']=0;

        if( isset($data['date_begin'])) {
            $aDT = explode(".", $data['date_begin']);
            if(count($aDT)==3) $aDT = $aDT[2]."-".$aDT[1]."-".$aDT[0];
            else  $aDT=$data['date_begin'];

            $dbTickets = new Application_Model_DbTable_Tickets();
            $rtickets = $dbTickets->getSumTicketsByStation(($aDT . " 00:00:00"),($aDT . " 23:59:59"),$data['stationid'],$data['routeid']);

            //trace($rtickets);
            $data['places_busy'] = (!empty($rtickets['places'])) ? (int)$rtickets['places'] : 0;
            $data['total'] = (!empty($rtickets['total'])) ? (float)$rtickets['total'] : 0;

        }
        if(isset($data['status']) && $data['status']!="failed" ){
            $data["fail_type"]="";
        }


        $isFillPlaces=false;
        if(isset($data["places"]) && (int)$data['places']>0 ){
            $data["places"]=(int)$data['places'];
            $isFillPlaces=true;
        }


        foreach($this->_fields as $field){
            //echo (isset($data[$field])) ? $field."=".$data[$field]."\n" : "";
            if($field == 'vehicletitle' && isset($data[$field])){
                $dbDic = new Application_Model_DbTable_Dictionary();
                $r = $dbDic->fetchRow("id=".(int)$data[$field]);
                if(!$isFillPlaces) {
                    $data["places"] = (!empty($r->description)) ? (int)$r->description : 0;
                }
                if($data['status']=='sent' && $data["places"]>0 ){
                    $dbRoute = new Application_Model_DbTable_Routes();
                    $oTmp = $dbRoute->fetchRow("id=".(int)$data['routeid']);
                    if($oTmp){
                        $reserv    = Application_Model_Ticket::parseReserv( json_decode($oTmp->reserv,true),0);
                        if(!empty($data['places'])) $data['places']=$data['places']-count($reserv);
                        else $data['places']=0;
                    }
                }
                $toSave[$field] = $data[$field];
            }else if($field == 'date_begin' && isset($data[$field])){
                $aDT = explode(".", $data['date_begin']);
                if(count($aDT)==3) $toSave[$field] = $aDT[2]."-".$aDT[1]."-".$aDT[0];
                else $toSave[$field]=$data['date_begin'];
            }else if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }

        if(count($toSave)==0) return false;

        if((int)$data['id']>0){
            //trace($data,1);
            $this->update($toSave,"id=".(int)$data['id']." ");//AND status!='sent'
            return $data['id'];
        }
        return $this->insert($toSave);


    }

    


}