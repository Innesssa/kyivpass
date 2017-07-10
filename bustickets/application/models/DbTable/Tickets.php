<?php

/**
 * Class Application_Model_DbTable_Controllers
 */
class Application_Model_DbTable_Tickets extends Application_Model_DbTable_Abstract
{
    protected $_name = 'tickets';
    protected $_primary = 'id'; // primary column name
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array(
        "id",
        "code",//номер маршруту
        "routeid",//назва
        "place",//місце
        "station_buy", // id станція продажу
        "kassauid",//uid користувача що продав квиток
        //"dt_action",//дата та час продажу
        //"dt_start",// дата выдправлення рейсу
        "status", //locked - блокирован, paid - куплен, inproccess - продается, refunded - возвращен
        "from_id",//станція посадки пасажира
        "to_id", //станція висадки
        "dt_time_begin",//дата выдправлення від станції посадки пасажира
        "dt_time_finish",//дата прибуття до станції висадки пасажира
        "conv_id",//перевізник
        "conveyor_vat",//платник ПДВ true, Ні - false
        "insurer_id",//cтраховик
        "insurer_percent",//відсоток страхового збору
        "insurer_tariff",//сума страхового збору
        "insurer_tariff_with_benefits",//сума страхового збору із знижкою
        "vehicle_id",//тип ТЗ
        "benefits_id",	//пільга
        "benefits_docnum",//номер документу для пільги
        "benefits_percent",//відсоток пільги
        "benefits_name",//ПІБ пільговика
        "price_tariff",//базова ставка
        "price_tariff_with_benefits",//базова ставка з урахуванням пільги
        "conv_tariff",//90 процентов от тарифа(пер1) - тариф перевозчика
        "conv_tariff_with_benefits",//90 процентов от тарифа(пер1) - тариф перевозчика із знижкою
        "stat_tariff",//10 процентов от тарифа(пер1) - тариф организации
        "stat_tariff_with_benefits",//10 процентов от тарифа(пер1) - тариф организации  із знижкою
        "gov_vat", //поточний выдсоток ПДВ
        "conv_tariff_vat",//ПДВ от тарифа перевізника
        "conv_tariff_with_benefits_vat",//ПДВ от тарифа перевізника із знижкою
        "stat_tariff_vat",//ПДВ от тарифа организации
        "stat_tariff_with_benefits_vat",//ПДВ от тарифа организации із знижкою
        "station_tax", //відсоток станційного збору
        "station_tax_tariff",//cума станційного збору
        "station_tax_tariff_with_benefits",//cума станційного збору із знижкою
        "station_tax_tariff_vat",//ПДВ на станційній збір
        "station_tax_tariff_with_benefits_vat",//ПДВ на станційній збір із знижкою
        "luggage_price",// ціна за один багажу
        "luggage_count",//кількість багажу
        "luggage_total",//всього за багаж
        "full_price",//вартість білету без ПДВ (полный тариф+ страховой збор + станционный збор )
        "full_price_with_benefits",//вартість білету без ПДВ (полный тариф+ страховой збор + станционный збор )  із знижкою
        "full_price_vat",//повний ПДВ вартісті білету
        "full_price_with_benefits_vat",// ПДВ вартісті білету із знижкою
        "total_price",//повна вартість білету з ПДВ
        "total_price_with_benefits",//повна вартість білету з ПДВ із знижкою
        "checknumber", //номер чеку з касового апарату
        "ppo",//Номер касового апарату
        "lastchange",// мітка останьої зміни запису
        "prepaid_vat",//пдв предвар продажи
        "prepaid",//предвар продажa
        "paidfromother_vat",//пдв продажи с другой станции
        "paidfromother",//продажи с другой станции
        "conv_luggage_tariff",//багаж перевізнику
        "stat_luggage_tariff",//станційна частка багажу
        "conv_luggage_tariff_vat",//пдв багаж перевізнику
        "stat_luggage_tariff_vat",//пдв багаж cтанції

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
    public function getRowByFields($data){
        $iWhere=0;
        $select = $this->select();
        foreach($this->_fields as $field){
            if(isset($data[$field])){ $iWhere++; $select->where($field."='".addslashes($data[$field])."'");}
        }
        if(!empty($data['exclude'])) $select->where('"status" NOT IN ('.$data['exclude'].')');
        //echo $select;
        //exit;
        if($iWhere) return $this->fetchRow($select);
        return false;
    }

    private function _insertToKassa($data){
        $dbKSSA = new Application_Model_DbTable_Kassa();
        try{
                //trace($data,1);
                $oSession = Zend_Auth::getInstance()->getStorage()->read();
                $data['kassauid'] = $oSession->id;
                if (!empty($data['user'])) $data['usr'] = $data['user'];
                if (!empty($data['date'])) $data['dt']  = $data['date'];

                if (!empty($data['id'])) {
                    unset($data['id']);
                }
                if (!empty($data['station_buy'])) {
                    $data['stationid'] = $data['station_buy'];
                }
                $data['num'] = 1;
                $data['price'] = 0;
                if (empty($data['amount'])) $data['amount'] = 1;

                $stat = $dbKSSA->setData($data);
                if(isset($stat["success"])){
                    return true;
                }
                return $stat;

        } catch(Exception $e){
            return array("error"=>$e->getMessage(),"message"=> $e->getMessage());
        }

    }

    public function insertData($data){
        $toSave = array();
        foreach($this->_fields as $field){
            if(isset($data[$field])) $toSave[$field]=$data[$field];
        }
        if(count($toSave)>0) {
            $toSave['lastchange']=time();
                $id = $this->insert($toSave);
                if ($id && $data['status']=='deduction' || $data['status']=='refund') {
                    $stat = $this->_insertToKassa($data);
                    if($stat === true) return $id;
                    return $stat;
                }
                return $id;
        }
        return array("error"=>"Data is empty","data"=>$data);

    }
    public function updateData(Array $data){
        $toSave = array();
        if(isset($data['id'])) {
            //trace($data);
            foreach ($this->_fields as $field) {
                if ($field == "conveyor_vat") {
                    if (isset($data[$field]) && $data[$field]!='' && $data[$field]!='false' ) $toSave[$field] = $data[$field] ? "TRUE" : "FALSE";
                } else if ($field != "id" && isset($data[$field])) {
                    $toSave[$field] = $data[$field];
                }
            }
           if( $data['status']!='bpaid' )  $toSave['lastchange'] = time();
           $stat = $this->update($toSave, "id=" . (int)$data['id']);

           if ( $stat && $data['status']=='paid' ) {
               $stat = $this->_insertToKassa($data);
               if($stat === true) return $data['id'];
               return $stat;
           }

           return  $stat  ? $data['id'] : array("error"=>"ticket not found","data"=>$data);

        }
        return array("error"=>"ticketID is required","data"=>$data);
    }
    public function getAllTicketsByRoute($routeID,$date="2015-03-11 00:00:00",$status=""){
        $select = $this->getAdapter()->select(); //  select();
        $select->from($this->_name,array($this->_name.".*"));
        $select->joinLeft("userlogindata",$this->_name.".kassauid=userlogindata.id",array("kassaname"=>"userlogindata.login") );
        $select->joinLeft("benefitslist",$this->_name.".benefits_id=benefitslist.id",array("benefits_title"=>"benefitslist.title_print") );
        $select->where($this->_name.".routeid=".(int)$routeID." AND ".$this->_name.".dt_time_begin='".$date."'");
        if($status) $select->where($this->_name.".status='".addslashes($status)."'");
        $select->order($this->_name.".place");
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }
    public function getLockedPlaces($routeid,$from_id ,$dt_time_begin,$kassauid){
        $this->delete(" ( \"status\" in ('locked')   AND ".time()."-lastchange>(1*60) )");
        $this->update( array ( "status" => "failed" ) ," ( \"status\" in ('inprocess')  AND ".time()."-lastchange>(4*60) ) " );

       
        $aplaces = array();
        $select = $this->select();
        $select->from($this,array("place"));

        $select->where("routeid=".(int)$routeid." AND from_id=".(int)$from_id." AND dt_time_begin='".addslashes($dt_time_begin)."'");
        $select->where(" \"status\"='locked' AND kassauid<>".(int)$kassauid."  " );

        $rs = $this->fetchAll($select);
        if($rs) foreach($rs as $r){
            $aplaces[]=$r->place;
        }
        return $aplaces;
    }
    public function getBuyPlaces($routeid,$from_id ,$dt_time_begin,$kassauid){
        $aplaces = array();
        $select = $this->select();
        $select->from($this,array("place"));

        $select->where("routeid=".(int)$routeid." AND from_id=".(int)$from_id." AND dt_time_begin='".addslashes($dt_time_begin)."'");
        $select->where("  \"status\"='paid' OR  \"status\"='inprocess'   AND  kassauid=".(int)$kassauid."  AND ".time()."-lastchange>60  OR   \"status\"='inprocess'   AND  kassauid<>".(int)$kassauid."  " );

        $rs = $this->fetchAll($select);
        if($rs) foreach($rs as $r){
            $aplaces[]=$r->place;
        }
        return $aplaces;
    }
    public function get1cExport($station_buy ,$aDtS,$aDtF){
        $select = $this->getAdapter()->select(); //  select();
        $select->from(array("r"=>'one_c_export_view'));
        //,array($this->_name.".*")
        //$select->where("\"as\"=".(int)$from_id." AND den>='".$aDtS[0]."' AND mes>='".$aDtS[1]."' AND god>='".$aDtS[2]."' AND den<='".$aDtF[0]."' AND mes<='".$aDtF[1]."' AND god<='".$aDtF[2]."'" );
        //$select->where("\"as\"='".$station_buy."' AND den>='".$aDtS[0]."' AND mes>='".$aDtS[1]."' AND god>='".$aDtS[2]."' AND den<='".$aDtF[0]."' AND mes<='".$aDtF[1]."' AND god<='".$aDtF[2]."'" );
        $select->where("\"as\"='".$station_buy."' AND (god||mes||den) >= '".$aDtS[2].$aDtS[1].$aDtS[0]."' AND (god||mes||den)<='".$aDtF[2].$aDtF[1].$aDtF[0]."'" );
        return $this->getAdapter()->fetchAll($select);
    }

    public function getPlacesStatus($ststionID,$routeId,$DT){
        $sSQL = " select \"status\",count(id) as num from ".$this->_name;
        $sSQL.= " where routeid=".(int)$routeId." AND station_buy='".$ststionID."'";
        $sSQL.= " AND ( dt_time_begin between '".$DT." 00:00:00' AND '".$DT." 23:59:59') ";
        $sSQL.= " Group By \"status\" ";
        return $this->getAdapter()->fetchAll($sSQL);
    }

    public function getSumTicketsByStation($DtS,$DtE,$ststionID,$routeId){

        $sSQL = "select COUNT(place) as places ,SUM(conv_tariff_with_benefits+stat_tariff_with_benefits+conv_tariff_with_benefits_vat+stat_tariff_with_benefits_vat) as total";
        $sSQL.= " from ".$this->_name."  ";
        $sSQL.= " where routeid=".(int)$routeId." AND station_buy='".$ststionID."'";
        $sSQL.= " AND ( dt_time_begin between '".$DtS."' AND '".$DtE."') ";
        $sSQL.= " AND \"status\"='paid'";
        //echo $sSQL;
        //exit;
        return $this->getAdapter()->fetchRow($sSQL);
    }
    private function _getCashe($DtS,$DtE,$ststionID,$Fbase,$Fvat,$isDeduc=null){
        $sSQL = "select SUM (t.".$Fbase.") as col1,";
        $sSQL.= ((!$Fvat) ? " 0" : " SUM( t.".$Fvat.")")." as col2,";
        $sSQL.= " count(t.*) as num";
        $sSQL.= " from ".$this->_name." as t ";
        $sSQL.= " where ( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND t.status in ('paid','bpaid','refund'";
        if($isDeduc===true){
            $sSQL.= ",'deduction'";
        }
        $sSQL.= " )";/*,'deduction'*/
        //echo $DtS.",".$DtE."<br/>\n";
        //echo $sSQL;
        //exit;

        return $this->getAdapter()->fetchRow($sSQL);
    }
    public function getPrepaidCash($DtS,$DtE,$ststionID){
        return $this->_getCashe($DtS,$DtE,$ststionID,"prepaid","prepaid_vat",true);
    }
    public function getStatCash($DtS,$DtE,$ststionID){
        return $this->_getCashe($DtS,$DtE,$ststionID,"station_tax_tariff_with_benefits","station_tax_tariff_with_benefits_vat",true);

    }
    public function getInsureCash($DtS,$DtE,$ststionID){
        return $this->_getCashe($DtS,$DtE,$ststionID,"insurer_tariff_with_benefits",null);
    }
    public function getOrgCash($DtS,$DtE,$ststionID){
        return $this->_getCashe($DtS,$DtE,$ststionID,"stat_tariff_with_benefits","stat_tariff_with_benefits_vat");
    }
    public function getConvCash($DtS,$DtE,$ststionID){
        return $this->_getCashe($DtS,$DtE,$ststionID,"conv_tariff_with_benefits","conv_tariff_with_benefits_vat");
    }

    public function getTicketsNextPeriod($DtS, $DtE, $ststionID){
        /*Проїзд*/
        $sSQL = "select t.dt_time_begin,SUM (t.conv_tariff_with_benefits*1.0+t.stat_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0+t.stat_tariff_with_benefits_vat) as col1,";
        /*Тар. пер*/
        $sSQL.= " SUM(t.conv_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0) as col2,";
        /*Тариф орг*/
        $sSQL.= " SUM(t.stat_tariff_with_benefits*1.0+t.stat_tariff_with_benefits_vat*1.0) as col3,";
        /*Страх. сбiр.*/
        $sSQL.= " SUM(t.insurer_tariff_with_benefits) as col4,";
        /*Cтан. Сбір.*/
        $sSQL.= " SUM(t.station_tax_tariff) as col5,";
        /*Ком. всього*/
        $sSQL.= " SUM(t.prepaid+t.prepaid_vat) as col6,";
        /* ПДФ */
        $sSQL.= " SUM(t.stat_tariff_with_benefits_vat) as col7,";

        $sSQL.= " SUM(t.luggage_count) as luggage_num, ";
        $sSQL.= " count(t.place) as places , t.conv_id,t.code,o.title as conv_title,o.code as conv_code ";
        $sSQL.= " from ".$this->_name." as t ";
        $sSQL.= " Left Join organizationlist as o ON t.conv_id=o.id";
        $dt = date("Y-m-01 00:00:00",strtotime(date("Y-m-01", strtotime(date($DtE))) . " +1 month"));
        $dtNext = date("Y-m-d 23:59:59",strtotime(date("Y-m-01", strtotime(date($DtE))) . " +2 month")-24*3600);
        $sSQL.= " where ( t.dt_time_begin between '".$dt."' AND '".$dtNext."' ) AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND ( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') ";
        $sSQL.= " AND t.status in ('paid','bpaid','refund','deduction' ) ";
        $sSQL.= " Group By t.conv_id,t.code,o.title,o.code,t.dt_time_begin";
        $sSQL.= " Order By t.conv_id,t.dt_time_begin,t.code";
        //echo $sSQL;
        //echo "\ndt=".$dt."|DtE:".$DtE."|dtNext:".$dtNext;
        //exit;
        return $this->getAdapter()->fetchAll($sSQL);

    }

    public function getTicketsPrevPeriod($DtS, $DtE, $ststionID){
        /*Проїзд*/
        $sSQL = "select t.dt_time_begin,SUM (t.conv_tariff_with_benefits*1.0+t.stat_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0+t.stat_tariff_with_benefits_vat) as col1,";
        /*Тар. пер*/
        $sSQL.= " SUM(t.conv_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0) as col2,";
        /*Тариф орг*/
        $sSQL.= " SUM(t.stat_tariff_with_benefits*1.0+t.stat_tariff_with_benefits_vat*1.0) as col3,";
        /*Страх. сбiр.*/
        $sSQL.= " SUM(t.insurer_tariff_with_benefits) as col4,";
        /*Cтан. Сбір.*/
        $sSQL.= " SUM(t.station_tax_tariff) as col5,";
        /*Ком. всього*/
        $sSQL.= " SUM(t.prepaid+t.prepaid_vat) as col6,";
        /* ПДФ */
        $sSQL.= " SUM(t.stat_tariff_with_benefits_vat) as col7,";

        $sSQL.= " SUM(t.luggage_count) as luggage_num, ";
        $sSQL.= " count(t.place) as places , t.conv_id,t.code,o.title as conv_title,o.code as conv_code ";
        $sSQL.= " from ".$this->_name." as t ";
        $sSQL.= " Left Join organizationlist as o ON t.conv_id=o.id";
        $dt = date("Y-m-01 00:00:00",strtotime(date("Y-m-01", strtotime(date($DtE))) . " +1 month"));
        //$dtNext = date("Y-m-d 23:59:59",strtotime(date("Y-m-01", strtotime(date($DtE))) . " +2 month")-24*3600);
        $sSQL.= " where ( t.dt_time_begin between '".$DtS."' AND '".$DtE."' ) AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND  t.lastchange < '".strtotime($DtS)."' ";
        $sSQL.= " AND t.status in ('paid','bpaid','refund','deduction' ) ";
        $sSQL.= " Group By t.conv_id,t.code,o.title,o.code,t.dt_time_begin";
        $sSQL.= " Order By t.conv_id,t.dt_time_begin,t.code";
        //echo $sSQL;
        //echo "\ndt=".$dt."|DtE:".$DtE."|dtNext:".$dtNext;
        //exit;
        return $this->getAdapter()->fetchAll($sSQL);

    }


    public function getTicketsGroupByDriverUID($DtS,$DtE,$ststionID){
        /*Проїзд*/
        $sSQL = "select SUM (t.conv_tariff_with_benefits*1.0+t.stat_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0+t.stat_tariff_with_benefits_vat+t.insurer_tariff_with_benefits) as col1,";
        /*Тариф*/
        $sSQL.= " SUM(t.conv_tariff_with_benefits*1.0+t.stat_tariff_with_benefits*1.0+t.conv_tariff_with_benefits_vat*1.0+t.stat_tariff_with_benefits_vat*1.0) as col2,";
        /* Тар. Орг. */
        $sSQL.= " SUM(t.stat_tariff_with_benefits*1.0+stat_tariff_with_benefits_vat*1.0) as col3,";
        /*Страх. сбiр.*/
        $sSQL.= " SUM(insurer_tariff_with_benefits) as col4,";
        /*К расч.*/
        $sSQL.= " SUM(conv_tariff_with_benefits*1.0+conv_tariff_with_benefits_vat*1.0) as col5,";

        $sSQL.= " SUM(t.luggage_count) as luggage_num, ";
        $sSQL.= " count(t.place) as places , t.conv_id,t.code,o.title as conv_title,o.code as conv_code ";
        $sSQL.= " from ".$this->_name." as t ";
        $sSQL.= " Left Join organizationlist as o ON t.conv_id=o.id";

        $sSQL.= " where ( t.dt_time_begin between '".$DtS."' AND '".$DtE."') AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND  t.status in ('paid','bpaid','refund' ) ";//,'deduction'
        $sSQL.= " Group By t.conv_id,t.code,o.title,o.code";
        return $this->getAdapter()->fetchAll($sSQL);
    }

    public function getBenefitsTickets($DtS,$DtE,$ststionID){
        $sSQL = "select t.*,b.title_short,u.persnum as kassa_tab ";
        $sSQL.= " from ".$this->_name." as t ";
        $sSQL.= " Left Join benefitslist b ON t.benefits_id=b.id ";
        $sSQL.= " Left Join userservicedata u ON t.kassauid=u.id ";
        $sSQL.= " where  t.benefits_id<>0 ";
        $sSQL.= " AND ( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') ";
        $sSQL.= " AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND  t.status in ('paid' ) ";
        $sSQL.= " Order By  t.benefits_id ";
        return $this->getAdapter()->fetchAll($sSQL);
    }

    public function getMinusBenefitsTickets($DtS,$DtE,$ststionID){



        $sSQL = " select count(t.id) as num, ";
        $sSQL.= " SUM ( stat_tariff_with_benefits+stat_tariff_with_benefits_vat ) as sm_stat_tariff_with_benefits, ";
        $sSQL.= " SUM ( stat_tariff+stat_tariff_vat  ) as sm_stat_tariff, ";
        $sSQL.= " SUM ( conv_tariff_with_benefits + conv_tariff_with_benefits_vat ) as sm_conv_tariff_with_benefits, ";
        $sSQL.= " SUM ( conv_tariff+ conv_tariff_vat ) as sm_conv_tariff, ";
        $sSQL.= " SUM ( insurer_tariff_with_benefits ) as sm_insurer_tariff_with_benefits, ";
        $sSQL.= " SUM ( insurer_tariff ) as sm_insurer_tariff, ";
        $sSQL.= " SUM ( station_tax_tariff_with_benefits_vat+station_tax_tariff_with_benefits ) as sm_station_tax_tariff_with_benefits , ";
        $sSQL.= " SUM ( station_tax_tariff_vat+station_tax_tariff ) as sm_station_tax_tariff , ";
        $sSQL.= " SUM ( full_price_with_benefits_vat+full_price_with_benefits )  as sm_full_price_with_benefits, ";
        $sSQL.= " SUM ( full_price_vat+full_price )  as sm_full_price_with_benefits, ";
        $sSQL.= " b.title_full as benefits_title,d.title as route_title ";
        $sSQL.= " from tickets as t ";
        $sSQL.= " Left Join routes as r ON t.routeid=r.id ";
        $sSQL.= " Left Join benefitslist as b ON t.benefits_id=b.id ";
        $sSQL.= " Left Join handbooks as d    ON r.stationrate=d.id ";
        $sSQL.= " where t.benefits_id!=0 AND t.status in ('paid') ";
        $sSQL.= " AND ( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') ";
        $sSQL.= " AND t.station_buy='".$ststionID."'";
        $sSQL.= " Group By r.stationrate,b.title_full,d.title ";
        $sSQL.= " Order By r.stationrate,b.title_full ";
        return $this->getAdapter()->fetchAll($sSQL);
    }

    public function getSumDeduckationsTickets($DtS,$DtE,$kassauid=null,$statid=null){

        $sSQL = " select SUM(
            t.stat_tariff
           +t.conv_tariff
           +t.conv_tariff_vat
           +t.stat_tariff_vat
          ) as col6_5
          , SUM(t.conv_tariff_vat+t.stat_tariff_vat) as col2
          , SUM(t.stat_tariff+t.conv_tariff) as col1
          , SUM(t.conv_tariff_vat) as conv_sum_vat
          , SUM(t.stat_tariff_vat) as stat_sum_vat
          , SUM(t.stat_tariff) as stat_tariff_sum
          , SUM(t.conv_tariff) as conv_tariff_sum
          , COUNT(t.id) as num";
        $sSQL.= " from  tickets as t";
        $sSQL.= " where ( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') ";
        if($kassauid) $sSQL.= " AND t.kassauid    = ".(int)$kassauid;
        if($statid)   $sSQL.= " AND t.station_buy = ".(int)$statid;
        $sSQL.= " AND t.status  in ( 'deduction' ) ";

        //echo $sSQL;
        //exit;

        return $this->getAdapter()->fetchRow($sSQL);

    }

    public function getTicketsGroupByKassaUID($DtS,$DtE,$ststionID){


        $sSQL = " select op.openamount, ";
        /* По вед */
        $sSQL.= "SUM (t.conv_tariff_with_benefits*1.0 + t.stat_tariff_with_benefits*1.0 + t.conv_tariff_with_benefits_vat*1.0 + t.stat_tariff_with_benefits_vat*1.0) as col1, ";
        /* Тар. Пер. */
        $sSQL.= "SUM(t.conv_tariff_with_benefits*1.0 + t.conv_tariff_with_benefits_vat*1.0) as col2, ";
        /* Тар. Орг. */
        $sSQL.= "SUM(t.stat_tariff_with_benefits*1.0 + t.stat_tariff_with_benefits_vat*1.0) as col3, ";
        /*Страх. сбiр.*/
        $sSQL.= "SUM(insurer_tariff_with_benefits) as col4, ";
        $sSQL.= "SUM(station_tax_tariff_with_benefits*1.0+station_tax_tariff_with_benefits_vat*1.0) as col5, ";
        /* Предв. */
        $sSQL.= "SUM(t.prepaid_vat*1.0+t.prepaid*1.0) as col6, ";
        /* +Усл. З */
       $sSQL.= " SUM(0.0) as col7,";
       /* +Кам. хр. */
       $sSQL.= " SUM(0.0) as col8,";
        /* +в тч ПДВ. */
	   $sSQL.= "SUM(t.conv_luggage_tariff_vat*1.0
           +t.stat_luggage_tariff_vat*1.0
           +t.paidfromother_vat*1.0
           +t.conv_tariff_with_benefits_vat*1.0
           +t.stat_tariff_with_benefits_vat*1.0
           +t.station_tax_tariff_with_benefits_vat*1.0
           +t.prepaid_vat*1.0) as col9, ";
        $sSQL.=" SUM(t.conv_luggage_tariff*1.0+t.stat_luggage_tariff*1.0+t.conv_luggage_tariff_vat*1.0+t.stat_luggage_tariff_vat*1.0) as col13,";
        $sSQL.=" SUM(t.conv_luggage_tariff_vat*1.0+t.stat_luggage_tariff_vat*1.0) as col14,";
        $sSQL.= "cl.closeamount, ";
        $sSQL.= "i.amount as inkasamount, ";
        $sSQL.= "count(t.place) as places , ";
		$sSQL.= "t.kassauid , ";
		$sSQL.= "u.id, ";
		$sSQL.= "u.login, ";
		$sSQL.= "u.lastname, ";
		$sSQL.= "u.middlename, ";
		$sSQL.= "u.firstname ";
        $sSQL.= "from  tickets as t,userlogindata_view u ";
        $sSQL.= "Left Join maxsumm_from_kassaoperation('".$DtS."', 'OpenDay') as op ON op.kassauid=u.id ";
        $sSQL.= "Left Join maxsumm_from_kassaoperation('".$DtE."','CloseDay') as cl ON cl.kassauid=u.id ";
        $sSQL.= "Left Join summ_from_kassaoperation('".$DtS."','".$DtE."','Inkas')  as i ON i.kassauid=u.id ";
        $sSQL.= "where u.perm_title='kassa' AND u.id=t.kassauid AND( t.lastchange between '".strtotime($DtS)."' AND '".strtotime($DtE)."') AND t.station_buy='".$ststionID."'";
        $sSQL.= " AND t.status  in ('paid','refund','bpaid','deduction' ) ";
        $sSQL.= "Group By t.kassauid ,u.login, u.lastname,u.middlename,u.firstname,cl.closeamount,op.openamount,i.amount,u.id ";
        return $this->getAdapter()->fetchAll($sSQL);
    }
}