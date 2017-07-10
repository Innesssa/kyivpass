<?php
/**
 * Created by PhpStorm.
 * User: v0id
 * Date: 20.12.2014
 * Time: 16:06
 */

class Application_Model_Ticket {

     private $_routeID = null;
     private $_stationStartId = null,
             $_stationEndId   = null;
     private $_Date = null;
     private $_Time = null;
     private $_aProperty = null;
     private $_dbRoute = null;
     static private   $_dbDic = null;
     private $_dbTickets = null;
     private $_tariffMatrix = null;
     private $_timeLeft=0; //осталось времени до рейса

    static public function parseReserv($aTickets,$timeLeft){
        $aPlaces = array();
        //trace($aTickets);
        if(!self::$_dbDic ) self::$_dbDic = new Application_Model_DbTable_Dictionary();
        /* {"1345":"3-5","1346":"1-2","1333":"25-35"} */
        if(!empty($aTickets) && is_array($aTickets)) foreach ($aTickets as $tp=>$vals){
            $rt = self::$_dbDic->fetchRow("id='".$tp."'");
            //trace($rt->toArray());
            if( (int)$rt->description<=0 || (int)$rt->description >= $timeLeft ) { /*
                                                                если время в минутах типа бронирования
                                                                больше времени до отправления то места
                                                                попадают в массив резерва
                                                             */

                $tmpPlaces = (strpos($vals, ",") !== false) ? explode(",", $vals) : array($vals);
                foreach ($tmpPlaces as $p) {
                    if (strpos($p, "-") !== false) {
                        $a = explode("-", $p);
                        if (count($a) == 2) for ($i = (int)$a[0]; $i <= (int)$a[1]; $i++) if ($i != 0) $aPlaces[] = $i;
                    } else if ((int)$p > 0) $aPlaces[] = (int)$p;
                }
            }

        }
        //trace($aPlaces,1);
        return $aPlaces;

    }

    public function __construct($id,$startid,$endid,$aDate)
    {
        $this->_dbRoute   = new Application_Model_DbTable_Routes();
        if(!self::$_dbDic ) self::$_dbDic = new Application_Model_DbTable_Dictionary();
        $this->_dbTickets = new Application_Model_DbTable_Tickets();
        //$this->_dbTickets = new Application_Model_DbTable_Tickets();
        $r = $this->_dbRoute->getRace($id, $startid, $endid, $aDate);
        if (!$r) return null;

        $this->_routeID = $r['id'];
        $this->_stationStartId = $startid;
        $this->_stationEndId = $endid;
        $this->_Date = $aDate;
        //$this->_Time = null;
        $this->_aProperty = $r;
        if(!empty($r['rcstatus']) ) $this->_aProperty['countplaces']=(int)$r['rcplaces'];
        if(!empty($r['rcplatform'])) $this->_aProperty['platform']=(int)$r['rcplatform'];
        //trace( $this->_aProperty,1);
        /*
             Array(
                    [id] => 18
                    [code] => 4854
                    [title] => КИЇВ ПОЛІССЯ-СУВИД
                    [conveyorid] => 9
                    [pricetype] => fixed
                    [vehicletype] => 1284
                    [vehiclename] БОГДАН(27)
                    [countplaces] => 27
                    [description] =>
                    [year] => *
                    [month] => *
                    [day] => *
                    [hour] => 19
                    [minute] => 1170
                    [date_begin] => 2014-12-01 00:00:00
                    [date_end] => 2018-01-01 23:59:59
                    [stationstartid] => 1343
                    [stationendid] => 446
                    [stationstart] => КИЇВ ПОЛІССЯ АС
                    [stationfinish] => Сувид с.
                    [distance]
                    [triptime]
               )
        */
        $this->_aProperty['date_begin'] = $aDate[0] . "." . $aDate[1] . "." . $aDate[2];
        $this->_aProperty['dt_time_begin']  = $aDate[2]."-".$aDate[1]."-".$aDate[0]." ".sprintf("%02d",$this->_aProperty['hour']) . ":" . sprintf("%02d",($this->_aProperty['minute']-floor($this->_aProperty['minute']/60)*60)) . ":00";

        $current = mktime((int)$this->_aProperty['hour'],$this->_aProperty['minute'],0,(int)$aDate[1],(int)$aDate[0],(int)$aDate[2]);


        $this->_timeLeft = $current - time();

        $this->_aProperty['dt_start']       = $aDate[0].".".$aDate[1].".".$aDate[2]." ".sprintf("%02d",$this->_aProperty['hour']) . ":" . sprintf("%02d",($this->_aProperty['minute']-floor($this->_aProperty['minute']/60)*60)) . ":00";
        //trace($this->_aProperty['dt_start'],1);
        //$aDate[0].".".$aDate[1].".".$aDate[2];
        $triptime = $this->_aProperty['hour']*60+$this->_aProperty['minute']*1+$this->_aProperty['triptime']*1;


        $days=0;
        $hours = floor($triptime/60);
        $minutes = $triptime-$hours*60;
        if($hours>24){
           $days = floor($hours/24);
           $hours = $hours - $days*24;
        }
        //mktime(h,i,s,m,d,Y)
        $ymd = date("d.m.Y",mktime(0,0,0,(int)$aDate[1],(int)$aDate[0]+$days,$aDate[2]));


        $this->_aProperty['dt_finish']      = $ymd." ".sprintf("%02d",$hours).":".sprintf("%02d",$minutes).":00";
        $this->_aProperty['dt_time_finish'] =  $aDate[2]."-".sprintf("%02d",$aDate[1])."-".sprintf("%02d",$aDate[0])." ".sprintf("%02d",$hours).":".sprintf("%02d",$minutes).":00"; ;
        //date("Y-m-d H:i",$current+$this->_aProperty['triptime']*60).":00";
        //trace($this->_aProperty['dt_start']);
        //trace($this->_aProperty['dt_finish']);
        //trace($triptime,1);




        $this->_aProperty['stationfrom'] = self::$_dbDic->getItemTitleOnly($this->_aProperty['stationstartid']);
        $this->_aProperty['stationto']   = self::$_dbDic->getItemTitleOnly($this->_aProperty['stationendid']);
        //trace($this->_aProperty);

        //$this->loadAdditionalData();
    }
    public function getReserv(){
        $oTmp = $this->_dbRoute->getRoute($this->_routeID);
        $timeLeft = time()-strtotime($oTmp['date_begin']);
        return self::parseReserv(json_decode($oTmp['reserv'], true),$timeLeft); //бронировка
    }


    public function getAllTickets(){
        $this->_routeID;
        $this->_stationStartIdd;
        $this->_stationEndIdd;
        $this->_Date;
        /*$oTmp = $this->_dbTickets->Метод выборка проданных мест;
         array( 1=>'status'
        )возврат - массив с н7омерами мест
        */
    }
    public function setStatus($place,$status,$uid){
        $this->_routeID;
        $this->_stationStartIdd;
        $this->_stationEndIdd;
        $this->_Date;
        $aResult = false;
        /*$aResult = $this->_dbTickets->Метод установки статуса;
          $aResult - 'Messeage about this transaction '
        */
        $result =  $this->getAllTickets();
        $result['status'] = $aResult;
        return $result;
    }

    public function loadAdditionalData(){

         //load Prices
         $oTmp = $this->_dbRoute->getRoute($this->_routeID);


         /*
          * Array
                    (
                        [id] => 18
                        [code] => 4854
                        [title] => КИЇВ ПОЛІССЯ-СУВИД
                        [conveyorid] => 9
                        [pricetype] => fixed
                        [vehicletypeid] => 1342
                        [countplaces] => 1284
                        [description] =>
                        [paritet] =>
                        [luggage] => {"luggageFixed":"","luggageCalc":[{"s":"0","e":"999999","p":"5"}]}
                        [priceperkm] =>
                        [year] => *
                        [month] => *
                        [day] => *
                        [hour] => 19
                        [minute] => 30
                        [date_begin] => 2014-12-01 00:00:00
                        [date_end] => 2018-01-01 23:59:59
                        [reserv] =>
                        [stationrate] => 1336
                        [luggagetype] => calculated
                        [ticket] => {"tariffsFixed":{"1343":{"1325":"4","434":"15.88","412":"16.68","417":"19.07","447":"21.05","446":"21.05"},"1325":{"434":"4","412":"4","417":"4","447":"4","446":"4"},"434":{"412":"4","417":"4","447":"4","446":"4"},"412":{"417":"4","447":"4","446":"4"},"417":{"447":"4","446":"4"},"447":{"446":"4"}},"tariffsCalc":[{"s":"","e":"","p":""}]}
                        [back] => {"backCalc":[{"s":"","e":"","p":""}]}
                        [conveyor_name] => 192-ДИМЕРСЬКЕ АТП ТОВ
                        [insurerid_for_driver] => 10
                        [insurer_name] => ГУ ПрАТ "УПСК"
                        [vehicle_name] => БОГДАН(27)
                        [station_rate] => внутрішньообласний
                    )
          */

                        //[luggagetype] => calculated
                        //[ticket] => {"tariffsFixed":{"1343":{"1325":"4","434":"15.88","412":"16.68","417":"19.07","447":"21.05","446":"21.05"},"1325":{"434":"4","412":"4","417":"4","447":"4","446":"4"},"434":{"412":"4","417":"4","447":"4","446":"4"},"412":{"417":"4","447":"4","446":"4"},"417":{"447":"4","446":"4"},"447":{"446":"4"}},"tariffsCalc":[{"s":"","e":"","p":""}]}
                        //[back] => {"backCalc":[{"s":"","e":"","p":""}]}
          $timeLeft = time()-strtotime($oTmp['date_begin']);
          $this->_aProperty['reserv']    = self::parseReserv( json_decode($oTmp['reserv'],true),$timeLeft); //бронировка

          //trace($oTmp['conveyorid'],0);
          $dbOrg = new Application_Model_DbTable_OrganizationList();
          $Org = $dbOrg->getConveyor($oTmp['conveyorid']);
          if(!$Org) return null;
          //trace($Org,1);
/*         [0] => Array
         (
            [id] => 9
            [title] => 192-ДИМЕРСЬКЕ АТП ТОВ
            [printedfield]
            [type] => 1272
            [ipn] => 111111111111
            [edrpou] => 11111111
            [mfo] => 111111
            [accountnr] => 11111111111111111111
            [bank] => АБВГДЕЖ
            [legaladdress] => АБВ
            [realaddress] =>
            [email] =>
            [printedfield] => ДИМЕРСЬКЕ АТП ТОВ
            [code] => 192
            [vat] => 1
            [insurerid] => 10
            [benefits] => ["1332"]
            [insurerrate] => 1.50
            [type_name] => перевізник
            [insurer_name] => ГУ ПрАТ "УПСК"
            [insurer_print_name]
        )
*/


          //TODO:потрібно підтягнути зайняті місця

          $this->_aProperty['stationrate']   = $oTmp['stationrate'];// тип рейсу приклад 1336
          $this->_aProperty['station_rate']  = $oTmp['station_rate'];// назва типу рейсу приклад внутрішньообласний

          $this->_aProperty['insurerid_for_conveyor'] = $Org['insurerid'];// ID страхової
          $this->_aProperty['insurer_name']           = $Org['insurer_name'];        // назва страхової
          $this->_aProperty['insurance_rate']         = $Org['insurerrate'];        // страхова премія
          $this->_aProperty['insurer_print_name']     = $Org['insurer_print_name']; // назва для друку



          $this->_aProperty['conveyorid']            = $Org['id'];        // ID перевізника
          $this->_aProperty['conveyor_name']         = $Org['title'];     // назва перевізника
          $this->_aProperty['conveyor_print_name']   = $Org['printedfield']; // назва для друку
          $this->_aProperty['conveyor_vat']          = (!empty($Org['vat'])) ? (int)$Org['vat'] : 0; // платник не платник ндс


          $this->_aProperty['description']   = $oTmp['description'];
          $this->_aProperty['paritet']       = $oTmp['paritet'];

          $this->_aProperty['priceperkm']    = $oTmp['priceperkm'];
          $this->_aProperty['luggagetype']   = $oTmp['luggagetype'];
          $this->_aProperty['tickettype']    = $oTmp['pricetype'];

          $this->_ticketMatrix              = @json_decode($oTmp['ticket'],true);
          $this->_luggageMatrix             = @json_decode($oTmp['luggage'],true);
          $this->_backTicketMatrix          = @json_decode($oTmp['back'],true);
          $this->_benefitsMatrix            = @json_decode($Org['benefits'],true);        // пільги


            //loading calculated tickets price
            if($oTmp['pricetype']=='calculated'){

                $r = self::$_dbDic->fetchRow("id=".(int)$oTmp['stationrate']);
                //trace($r->toArray());
                if(!empty($r->additional_text) && is_numeric($r->additional_text) ) {
                    $dbMx = new Application_Model_DbTable_TariffMatrix();
                    $tariffMatrix = $dbMx->fetchAll('"type"=' . (int)$r->additional_text);
                    if ($tariffMatrix) $this->_ticketMatrix['tariffsCalc'] = $tariffMatrix->toArray();
                    //trace($this->_ticketMatrix['tariffsCalc']);
                }
            }

          //load all benefits propery

          if(count($this->_benefitsMatrix)){
              $dbDic = new Application_Model_DbTable_Benefitslist();
              $aBenefits = array();
              $rs = $dbDic->fetchAll(null,"title_short ASC");
              foreach($rs as $r){
                  if(in_array($r->id,$this->_benefitsMatrix)) $aBenefits[$r->id]=array("title"=>$r->title_short,"doc"=>$r->document,"discount"=>$r->benefit_perc, "title_full"=>$r->title_full,"title_print"=>$r->title_print);
              }
              $this->_benefitsMatrix = $aBenefits;

          }
        //trace($this->_aProperty,1);
    }
    public function getListBenefits(){
        return $this->_benefitsMatrix;
    }

    public function getPriceTicket(){
        //trace($this->_aProperty['tickettype']);
        //trace($this->_aProperty['stationstartid']);
        //trace($this->_aProperty['stationendid']);

        //trace($this->_ticketMatrix['tariffsCalc'],1);
        $tariff = 0;
        switch($this->_aProperty['tickettype']){
            case 'calculated':
                  $step = 0;
                  $begin = 0;
                  $end = 0;
                  //trace($this->_aProperty['distance']);
                  //trace($this->_ticketMatrix['tariffsCalc'],1);
                  foreach( $this->_ticketMatrix['tariffsCalc'] as $dif){
                      /* [id] => 17 [zone] => [begin] => 0 [end] => 1 [step] => 1 [description] =>  [type] => 1380 */

                      if( $dif['begin'] < $this->_aProperty['distance'] && $dif['end'] >= $this->_aProperty['distance'] ){
                          $step  = $dif['step'];
                          $begin = $dif['begin'];
                          $end   = $dif['end'];
                          break;
                      }
                  }//foreach( $this->_ticketMatrix['tariffsCalc'] as $dif){
                  $_begin = $begin;
                  //trace("B=".$begin."|E=".$end."|S=".$step."|D=".$this->_aProperty['distance']);
                  if($step>0) for($i=$begin;$i<=$end;$i+=$step){
                      //trace("B=".$_begin."|E=".$i."|S=".$step."|D=".$this->_aProperty['distance']);
                      if($_begin < $this->_aProperty['distance'] && $i>= $this->_aProperty['distance'] ){
                          $this->_aProperty['traiff_distance'] = ($_begin+$i)/2;
                          break;
                      }
                      $_begin=$i;
                  }//for
                  //trace($this->_aProperty['traiff_distance'],1);
                  if(!empty($this->_aProperty['traiff_distance']) && $this->_aProperty['traiff_distance']>0 && $this->_aProperty['priceperkm']){
                      $tariff = round($this->_aProperty['traiff_distance']*$this->_aProperty['priceperkm'],2);
                  }
                break;

            case 'fixed':
                $tariff =  (!empty($this->_ticketMatrix['tariffsFixed'][$this->_aProperty['stationstartid']][$this->_aProperty['stationendid']])) ? $this->_ticketMatrix['tariffsFixed'][$this->_aProperty['stationstartid']][$this->_aProperty['stationendid']] : 0;
                break;
        }

        if($tariff>0.001){
            $this->_aProperty['tariff']=$tariff;
        }

        return $tariff;


    }

    public function getPriceLuggage(){

        switch($this->_aProperty['luggagetype']){
            case 'calculated':
                foreach( $this->_luggageMatrix['luggageCalc'] as $dif){
                    if($dif['s']<$this->_aProperty['distance'] && $dif['e']>=$this->_aProperty['distance'] ) return $dif['p'];
                }
                return null;
            case 'fixed':
                return (!empty($this->_luggageMatrix['luggageFixed'][$this->_aProperty['stationstartid']][$this->_aProperty['stationendid']])) ? $this->_ticketMatrix['tariffsFixed'][$this->_aProperty['stationstartid']][$this->_aProperty['stationendid']] : null;
        }

        return null;
    }

    public function getProperty($key=''){
        if(!$key) return $this->_aProperty;
        return (!empty($this->_aProperty[$key])) ? $this->_aProperty[$key] : '';
    }

    public function getRaceID(){
        return $this->_routeID;
    }





} 