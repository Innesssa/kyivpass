<?php
class RoutesController extends Zend_Controller_Action
{

    protected $ststionID = 1306;

    public function init()
    {


        $this->view->headScript()
            ->appendFile('/js/libs/jquery-ui.js');
        $this->view->headScript()
            ->appendFile('/js/libs/ui.datepicker-ua.js');
        $this->view->headScript()
            ->appendFile('/js/controllers/'.$this->getRequest()->getControllerName().'.js');
        $this->view->headLink()
            ->appendStylesheet('/js/libs/tablesorter/themes/blue/style.css');
        $this->view->headLink()
           ->appendStylesheet('/css/custom-theme/jquery-ui-tab.css');

        $this->view->sActionName    =   "index";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();
        $this->_helper->contextSwitch()
            ->addActionContext('edit', array('json'))
            ->addActionContext('editschema', array('json'))
            ->addActionContext('addStation', array('json'))
            ->addActionContext('deleteStation', array('json'))
            ->addActionContext('refresh', array('json'))
            ->addActionContext('delete', array('json'))
            ->addActionContext('indexschema', array('json'))
            ->addActionContext('indexschemareadonly', array('json'))
            ->addActionContext('routeTariffs', array('json'))
            ->addActionContext('editRouteTariff', array('json'))
            ->addActionContext('deleteRouteTariff', array('json'))
            ->addActionContext('raceOpen', array('json'))
            ->addActionContext('raceClose', array('json'))
            ->addActionContext('raceOpenPrint', array('json'))
            ->addActionContext('showHistory', array('json'))
            ->initContext();

        $this->_db = new Application_Model_DbTable_Routes();

    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }

    public function indexAction(){
        //$this->view->form = new Application_Form_Organisation();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $formData = $this->getRequest()->getParams();
        $this->view->sTitle = "Маршрути";
        $this->view->aFilter = json_encode(array(
            "sort"   => (!empty($formData['sort']) ? $formData['sort'] : ''),
            "order"  => (!empty($formData['order']) ? $formData['order'] : ''),
            "status" => (!empty($formData['status']) ? $formData['status'] : '')
        ));

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partials/paginator.phtml');
        $paginator = $this->_db->getRoutesList($this->getRequest()->getParams());
        //$paginator->setView($this->view);
        $this->view->paginator = $paginator;
        //trace($this->_db->getUserByLogin("admin","qwerty"));

    }

    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
        $formData = $this->getRequest()->getParams();
        if(isset($formData["date_begin"])) $this->busoutAction();
        else $this->indexAction();
        $result['success']  = true;
        $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
        if(isset($formData["date_begin"]))  $result['content']  = $this->view->render('routes/busout.phtml');
        else  $result['content']  = $this->view->render('routes/index.phtml');
        $this->_helper->json->sendJson($result);
    }



    public function editschemaAction(){
        $this->view->form = new Application_Form_RoutesSchema();
        $data = $this->getRequest()->getPost();
        if(empty($data['routeid'])) { $this->_helper->json->sendJson(array("error"=>"Не обрано маршрут.")); return; }
        /*
        $dbstationlist = new Application_Model_DbTable_Dictionary();
        $Stationlist=Application_Model_DbTable_Dictionary::getStationList($this->getRequest()->getParams());
        Zend_Debug::dump($Stationlist, $label='Stationlist', $echo=true);
        */
        $dbLnk = new Application_Model_DbTable_LnkStation2Route();

        if(isset($data['needstore'])){

            if($this->view->form->isValid($data)){
                $data['id'] = $dbLnk->setStation2Route($data);
                if(!is_numeric($data['id']) || $data['id']==0 ){
                    $this->view->message = $data['stationid']." - не збережено, зверніться до адміністратора системи";
                    $this->view->result = "error";
                } else {

                    $this->view->message  =  $data['stationid']." - збережено";
                    $this->view->result = "success";
                    return $this->indexschemaAction();
                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";
            }
        }

        //trace($data,0);
        if(!empty($data['id'])){
            $r = $dbLnk->getStation($data['routeid'],$data['id']);
            if(!$r) {
                $result['error'] = "Станцію не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $this->view->form->stationid->setValue($r['stationid']);
            $this->view->form->station->setValue($r['station']);
            $this->view->form->populate($r);
        }


        $this->view->form->routeid->setValue($data['routeid']);
        $this->view->routeinfo = $this->_db->getRoute($data['routeid']);
        $this->view->title = $this->view->routeinfo['code']." - ".$this->view->routeinfo['title'];
        $this->view->aStationList = $dbLnk->getStationList($data['routeid']);
        $result['success'] = true;
        $result['content'] = $this->view->render('routes/editschema.phtml');
        $this->_helper->json->sendJson($result);
    }

    public function showHistoryAction(){
        $result = array();
        $data = $this->getRequest()->getPost();
        if(!empty($data['id'])){
            $db = new Application_Model_DbTable_UserEventsSysLog();
            $this->view->list = $db->getLog(
                array(
                    "controller" => $this->getRequest()->getControllerName(),
                    "action"     => 'race-open',
                    "request"    => $data['id']."::%"
                )
            );

            $result['success'] = true;
            $result['content'] = $this->view->render('routes/history.phtml');

        }else {
            $result['error'] = "Маршрут не знайдено";
        }
        $this->_helper->json->sendJson($result);
    }
    public function indexschemaAction(){
        $result = array();
        $data = $this->getRequest()->getPost();
        //Zend_Debug::dump($data);
        if(!empty($data['routeid'])){
            $r = $this->_db->fetchRow("id=".(int)$data['routeid']);
            if(!$r) {
                $result['error'] = "Маршрут не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $dbLnk = new Application_Model_DbTable_LnkStation2Route();
            $this->view->routeinfo = $this->_db->getRoute($data['routeid']);
            $this->view->routeID = $data['routeid'];
            $this->view->title = $this->view->routeinfo['code']." - ".$this->view->routeinfo['title'];
            $this->view->aStationList = $dbLnk->getStationList($data['routeid']);
            $result['success'] = true;
            $result['content'] = $this->view->render('routes/indexschema.phtml');
        }else{
            $result['error'] = "Маршрут не знайдено";
        }
        $this->_helper->json->sendJson($result);
    }

    function indexschemareadonlyAction(){
        $result = array();
        $data = $this->getRequest()->getPost();
        //Zend_Debug::dump($data);
        if(!empty($data['routeid'])){
            $r = $this->_db->fetchRow("id=".(int)$data['routeid']);
            if(!$r) {
                $result['error'] = "Маршрут не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $dbLnk = new Application_Model_DbTable_LnkStation2Route();
            $this->view->routeinfo = $this->_db->getRoute($data['routeid']);
            $this->view->routeID = $data['routeid'];
            $this->view->title = $this->view->routeinfo['code']." - ".$this->view->routeinfo['title'];
            $this->view->aStationList = $dbLnk->getStationList($data['routeid']);
            $result['success'] = true;
            $result['content'] = $this->view->render('routes/indexschemareadonly.phtml');
        }else{
            $result['error'] = "Маршрут не знайдено";
        }
        $this->_helper->json->sendJson($result);
    }



    public function editRouteTariffAction(){
        $data = $this->getRequest()->getPost();
        if(empty($data['routeid'])) {
            $result['error'] = "Маршрут не обрано";
            $this->_helper->json->sendJson($result);
            return;
        }
        $this->view->idactivepart = (!empty( $data['idactivepart']) ) ? $data['idactivepart'] : 1 ;

        $this->view->form = new Application_Form_Tariffs($data['routeid']);
        if(isset($data['needstore'])){
            //trace($data,1);
            if($this->view->form->isValid($data)){
                $dbLnk = new Application_Model_DbTable_LnkStation2Route();
                $aStationList = $dbLnk->getStationList($data['routeid']);
                $aTabsHTML  =   array();
                $aBodiesHTML=   array();
                if(count($aStationList)>0) {
                    for ($i = 0; $i < (count($aStationList) - 1); $i++) {
                        for ($y = ($i + 1); $y < count($aStationList); $y++) {
                            $elm = $data['tariffs'][$aStationList[$i]['stationid']][$aStationList[$y]['stationid']];
                            if (empty($elm) || !is_numeric($elm)) {
                                $aTabsHTML["statLI" . $aStationList[$i]['stationid']] = "error";
                                $aBodiesHTML["price_" . $aStationList[$i]['stationid'] . "_" . $aStationList[$y]['stationid']] = "error";
                            }
                        }
                    }
                }
                if( $data['pricetype']=='fixed' && count($aTabsHTML) && count($aBodiesHTML) ){
                    $this->view->htmlpart="<script>
                        var jLI = ".json_encode($aTabsHTML)."; var jPrices = ".json_encode($aBodiesHTML).";
                        $.each(jLI, function(i, val) { $(\"#\" + i).addClass(\"li-error\");});
                        $.each(jPrices, function(i, val) { $(\"#\" + i).addClass(\"input-error\");});</script>";
                    $this->view->message="Помилка заповнення тарифів";
                    $this->view->result = "error";
                }else{


                    //$data['tariffs']=$data['price'];
                    $data['id'] = $this->_db->setData($data);
                    if(!is_numeric($data['id']) || $data['id']==0 ){
                        $this->view->message = "Тарифи - не збережено, зверніться до адміністратора системи";
                        $this->view->result = "error";
                    } else {

                        $this->view->message  =  "Тариф - збережено";
                        $this->view->result = "success";
                        $this->view->form->populate($data);
                        //$this->view->title= "\"".$data['code']."\" - редагування " ;
                        //$result['callback'] = "refresh(".$data['id'].")";
                    }

                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";
            }
        }


        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $this->view->routeinfo = $this->_db->getRoute($data['routeid']);

        $r = $this->_db->getRoute($data['routeid']);

        if($r) {
            $this->view->form->populate($r);
        }
        $htmlpart="<script>";
        if(!empty($r['ticket'])){
                $aTariffs = json_decode($r['ticket'],true);
                //trace($aTariffs);
                if(isset($aTariffs['tariffsFixed'])) {
                    //$aTariffs['tariffsFixed']=json_decode($aTariffs['tariffsFixed'],true);
                    //trace($aTariffs['tariffsFixed']);
                    foreach ($aTariffs['tariffsFixed'] as $i => $rs)
                        //trace($rs);
                        foreach ($rs as $y => $rss) {
                            $htmlpart .= "$(\"#price_" . $i . "_" . $y . "\").val(\"" . $rss . "\");";
                        }
                }
                $htmlpart .="\n";

        }

        if(!empty($r['luggage'])){
            $aTariffs = json_decode($r['luggage'],true);
            //trace($aTariffs);
            if(isset($aTariffs['luggageFixed'])) {
                 $htmlpart .= "$(\"#luggagepercent\").val(\"" . $aTariffs['luggageFixed'] . "\");";
            }
            $htmlpart .="\n";
            if(isset($aTariffs['luggageCalc'])) {
                $htmlpart .= 'elm = $("#idluggageCalcFirst").parent();';
                foreach ($aTariffs['luggageCalc'] as $i => $rs) {
                    if($i>0){
                        $htmlpart .= 'elm = addRow("#idluggageCalcFirst");';
                    }
                    $htmlpart .= "$(elm).find(\"input[name='luggageticketsS[]']\").val(".$rs["s"].");";
                    $htmlpart .= "$(elm).find(\"input[name='luggageticketsE[]']\").val(".$rs["e"].");";
                    $htmlpart .= "$(elm).find(\"input[name='luggageprice[]']\").val(".$rs["p"].");";
                }
            }
        }

        if(!empty($r['back'])){
            $aTariffs = json_decode($r['back'],true);
            $htmlpart .="\n";
            if(isset($aTariffs['backCalc'])) {
                $htmlpart .= 'elm = $("#idBackCalcFirst").parent();';
                foreach ($aTariffs['backCalc'] as $i => $rs) {
                    if($i>0){
                        $htmlpart .= 'elm = addRow("#idBackCalcFirst");';
                    }
                    $htmlpart .= "$(elm).find(\"input[name='backticketstimeS[]']\").val(".$rs["s"].");";
                    $htmlpart .= "$(elm).find(\"input[name='backticketstimeE[]']\").val(".$rs["e"].");";
                    $htmlpart .= "$(elm).find(\"input[name='backticketsprice[]']\").val(".$rs["p"].");";
                }
            }

        }

        $htmlpart.="</script>";
        //trace($htmlpart,1);

        $this->view->routeID = $data['routeid'];
        $this->view->title ="Тарифи ".$this->view->routeinfo['code']." - ".$this->view->routeinfo['title'];

        if(!empty($data['idactivepart'])){
            $this->view->form->idactivepart->setValue($data['idactivepart']);
        }

        $this->view->htmlpart.=$this->view->render('routes/dinamicly.phtml').$htmlpart;
        //trace($htmlpart,1);
        $result['success'] = true;
        $result['content'] = $this->view->render('routes/edit.phtml');
        //echo "FORM:".$this->view->form;
        //trace($result);

        $this->_helper->json->sendJson($result);
    }
    public function deleteRouteTariffAction(){
        $formData = $this->getRequest()->getParams();
        //$dbLnk = new Application_Model_DbTable_LnkStation2Route();
        //if($dbLnk->delete( "id=".(int)$formData['id']." AND routeid=".(int)$formData['routeid'])){
        //    $result['success'] = true;
        //}else{
            $result['error'] = "тариф  не видалено, зверніться до адміністратора.";
        //}
        $this->_helper->json->sendJson($result);
    }

    public function editAction(){
        $this->view->form = new Application_Form_Route();
        $data = $this->getRequest()->getPost();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="Новий маршрут";



        if(!empty($data['id'])){
            $r = $this->_db->fetchRow("id=".(int)$data['id']);
            if(!$r) {
                $result['error'] = "Маршрут не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $this->view->title= "\"".$r->title."\" - редагування " ;
            $this->view->jReserve = (($r->reserv)? $r->reserv : "{}");
            $this->view->form->populate($r->toArray());
            //trace($r->type);


        }


        if(isset($data['needstore'])){
           /* Array
            (
                [needstore] => 1
                [id] => 9
                [code] => N4 - Маршрут 4 тест
                [title] => Одеса-Зеленопілля
                [date_begin] => 13/02/2015
                [date_end] => 29/02/2016
                [year] => 2015
                [month] => 2
                [day] => /1
                [hour] => 08
                [minute] => 10
                [reserv] => 1,4,6,7
                [paritet] => 0
                [conveyorid] => 9
                [typeprice] => 1277
                [typeluggageprice] => 1324
                [luggage] => хз
                [vehicletypeid] => 1279
                [countplaces] => 1288
                [description] => тест перевірка
                [csrf_token] =>
            )
           */
            //we have data from form

            $reserv = array();
            foreach($data['reservT'] as $i=>$r)if( !empty(  $data['reservP'][$i]  ) )  $reserv[$r]=trim($data['reservP'][$i]);
            $data['reserv']=json_encode($reserv);
            $this->view->jReserve = $data['reserv'];
            if($this->view->form->isValid($data)){
                $data['paritet'] = (!empty($data['paritet'])) ?  true : false;
                $data['id'] = $this->_db->setData($data);
                if(!is_numeric($data['id']) || $data['id']==0 ){
                    $this->view->message = $data['code']." - не збережено, зверніться до адміністратора системи";
                    $this->view->result = "error";
                } else {

                    $this->view->message  =  $data['code']." - збережено";
                    $this->view->result = "success";
                    $this->view->form->populate($data);
                    $this->view->title= "\"".$data['code']."\" - редагування " ;
                    $result['callback'] = "refresh(".$data['id'].")";
                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";

            }
        }
        $result['success'] = true;
        $result['content'] = $this->view->render('routes/edit.phtml');
        $this->_helper->json->sendJson($result);

    }

    public function deleteStationAction(){
        $formData = $this->getRequest()->getParams();
        $dbLnk = new Application_Model_DbTable_LnkStation2Route();
        if($dbLnk->delete( "id=".(int)$formData['id']." AND routeid=".(int)$formData['routeid'])){
            $result['success'] = true;
        }else{
            $result['error'] = "зупинку  не видалено, зверніться до адміністратора.";
        }
        $this->_helper->json->sendJson($result);
    }


    public function deleteAction(){

        $formData = $this->getRequest()->getParams();

        $result = array();
        $r = $this->_db->fetchRow("id=".(int)$formData['delete']);
        if(!$r || empty($r->id))  $result['error'] = "Не обрано елемент довідника";
        else {

            $rs = $this->_db->hasChild($formData['delete']);
            if (!$rs || count($rs) == 0) {
                $result['error'] = "\"" . $r->title . "\" - не видалено, зверніться до адміністратора.";
                if ($this->_db->delete("id=" . (int)$formData['delete'])) {
                    $result['success'] = true;
                }
            } else {
                $result['error'] = "На \"" . $r->title . "\" юр.особи присутні посилання, спочатку видаліть їх.";
            }

        }
        $this->_helper->json->sendJson($result);
    }


    public function addStationAction(){
        $this->_helper->json->sendJson(array("error"=>"ok"));
    }


    public function busoutAction(){
        $this->view->form = new Application_Form_RaceFilter();
        $this->view->sTitle = "Рейси";
        $this->view->form->setAction("/routes/busout/");

        $dt = ($this->getRequest()->getParam("date_begin",date("d.m.Y")));
        $this->view->form->date_begin->setValue($dt);
        //$this->ststionID = 1306; //КИЇВ Поділ АС, Подільський р-н м.Києва, Київ місто
        $this->view->list = $this->_db->getRaces($this->ststionID,$dt); //$this->_db->getOutputRaces($this->ststionID,$dt);
        $this->view->iAllRaces      =   0;
        $this->view->iFailRaces     =   0;
        $this->view->iPaidTickets   =   0;
        if($this->view->list) {
            $lnk = new Application_Model_DbTable_LnkStation2Route();
            $dbTick = new Application_Model_DbTable_Tickets();

            foreach ($this->view->list as $key => $elm) {
                $dtNotEQ = false;
                $this->view->iAllRaces++;
                if($elm['rcstatus']=="failed") $this->view->iFailRaces++;

                /*
                        время прибытия на станцию (время установки статуса - ПРИНЯТЫЙ)
                        если дата отличается от текущей , тогда показываем ДАТУ ВРЕМЯ
                */
                if (!empty($elm['dt_received']) && $elm['rcdate'] != $elm['dt_received']) {
                    $dtNotEQ = true;
                    $this->view->list[$key]['tm_received'] = date("d.m.Y H:i", strtotime($elm['dt_received'] . " " . $elm['tm_received']));
                }

                /*
                    3. время отправления
                */
                $this->view->list[$key]['tm_finish']=0;
                if (empty($elm['tm_sent'])) {
                    if (!empty($elm['rcdate'])) {
                        $this->view->list[$key]['tm_sent'] = $elm['rctime'];
                        $this->view->list[$key]['dt_sent'] = $elm['rcdate'];
                    } else {
                        $this->view->list[$key]['tm_sent'] = $elm['hour'] . ":" . sprintf("%02d", $elm['minute']);
                        $aDT = explode(".", $dt);
                        $this->view->list[$key]['dt_sent'] = $aDT[2] . "-" . $aDT[1] . "-" . $aDT[0];
                    }
                    $this->view->list[$key]['tm_finish']=(int)($lnk->getRoutesTime($elm['id']));
                }
                //strtotime($elm['dt_sent'] . " " . $elm['tm_sent'])+


                /*
                    4. прибытие на конечную станцию, если дата отличается от текущей , тогда показываем ДАТУ ВРЕМЯ
                 */
                if($this->view->list[$key]['tm_finish']>0){
                    $tm = $this->view->list[$key]['tm_finish']*1+ $elm['hour']*60+$elm['minute']*1;
                    $hours = (int)floor($tm/60);
                    $minutes = $tm-$hours*60;
                    $days = (int)floor($hours/24);

                    //echo "TM:".$tm."|hours:".$hours."|minutes:".$minutes."|days:".$days."<br/>";

                    $this->view->list[$key]['tm_finish']="";
                    if($days>0){
                        $hours = $hours-24*$days;
                        $this->view->list[$key]['tm_finish']=date("d.m.Y ",strtotime( $this->view->list[$key]['dt_sent'] . " " .  $this->view->list[$key]['tm_sent'])+$days*24*3600);
                    }
                    $this->view->list[$key]['tm_finish'].=sprintf("%02d",$hours).":".sprintf("%02d",$minutes);

                }else{
                    $this->view->list[$key]['tm_finish']="--";
                }
                if ($dtNotEQ) {
                    $this->view->list[$key]['tm_sent'] = date("d.m.Y H:i", strtotime( $this->view->list[$key]['dt_sent'] . " " .  $this->view->list[$key]['tm_sent']));
                }






                /*
                    количество мест - (например 12 1 17 30 ) по два символа
                    продано - красный фон
                    бронь - желтый фон
                    свободно - зеленый фон
                    всего - светлосерый фон
                 */

                if($elm['places']==""){
                    $this->view->list[$key]['places']=(int)$elm['vcplanplaces'];
                }
                $aDT = explode(".", $dt);
                $aPlaces = $dbTick->getPlacesStatus($this->ststionID,$elm["id"],$aDT[2] . "-" . $aDT[1] . "-" . $aDT[0]);



                $timeLeft = time()-strtotime($this->view->list[$key]['dt_sent']." ".$this->view->list[$key]['tm_sent']);
                $this->view->list[$key]['order_places'] =   count(Application_Model_Ticket::parseReserv(json_decode($elm['reserv'], true),$timeLeft));
                $this->view->list[$key]['paid_places']  =   0;
                $this->view->list[$key]['bpaid_places'] =   0;
                if($aPlaces)foreach($aPlaces as $p){
                    switch($p["status"]){
                        case "order": $this->view->list[$key]['order_places']=$p["num"]; break;
                        case "paid":  $this->view->list[$key]['paid_places']=$p["num"]; break;
                        //case "bpaid": $this->view->list[$key]['bpaid_places']=$p["num"]; break;
                    }

                }
                $this->view->iPaidTickets+=($this->view->list[$key]['paid_places']-$this->view->list[$key]['bpaid_places']);

           }
        }
        $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

    }

    public function raceOpenAction(){
        $oSession   = Zend_Auth::getInstance()->getStorage()->read();
        $data = $this->getRequest()->getPost();
        if(empty($data['routeid']) || (int)$data['routeid']==0 )  { $this->_helper->json->sendJson(array("error"=>"Маршрут не знайдено.")); return;}
        $rs = $this->_db->getIORace((int)$data['routeid'],$this->ststionID );

        $result = array();

        if(!$rs){ $this->_helper->json->sendJson(array("error"=>"Маршрут не знайдено.")); return;}

        $this->view->title = "Відправлення рейсу: ".$rs['code']." ".$rs['title']." ".$data['date_begin'];

        $this->view->form = new Application_Form_RaceOpen();
        $this->view->form->setAction("/routes/race-open/");
        $this->view->form->routeid->setValue($rs['id']);
        $this->view->form->tp->setValue('out');
        $this->view->form->date_begin->setValue($data['date_begin']);
        $this->view->form->time_begin->setValue($rs['hour'].":".sprintf("%02d",$rs['minute']));

        $dbRaces = new Application_Model_DbTable_Races();
        if(isset($data['needstore'])){
            if($data['status']=="failed"){
                $this->view->form->getElement("fail_type")
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  Application_Model_Dictionary::getName(Application_Model_Dictionary::CANCELREASON))));
            }
            if($data['status']=="sent"){
                $this->view->form->getElement('govnumber')
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть державний номер ТЗ')));
                $this->view->form->getElement('driver_name')
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть ПІБ водія')));
                $this->view->form->getElement('time_begin')
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть час відправлення рейсу')));
            }

            if($this->view->form->isValid($data)){

                $data['stationid'] = $this->ststionID;
                //if($this->view->form->status->getValue()=="sent"){

                    mb_internal_encoding("UTF-8");
                    $data['fio'] = $oSession->lastname." ".mb_substr($oSession->firstname,0,1).".".mb_substr($oSession->middlename,0,1).". #".$oSession->id;
                    $data['dispuid'] = $oSession->id;
                //}

                    $data['tm_'.$this->view->form->status->getValue()] = $data['time_begin'];
                    $aDT= explode(".",$data['dt_received']);
                    $data['dt_'.$this->view->form->status->getValue()] = $aDT[2]."-".$aDT[1]."-".$aDT[0];
                    if($this->view->form->status->getValue()!='received'){
                        unset($data['dt_received']);
                    }


                //trace($data,1);
                try {
                    //trace($data,1);
                    $data['id'] = $dbRaces->setData($data);
                    $this->view->message = "збережено";
                    $this->view->result = "success";

                    $aDT = explode("-",$data['date_begin']);
                    if(count($aDT)==3) $data['date_begin'] = $aDT[2].".".$aDT[1].".".$aDT[0];
                    $result['callback'] = "refresh(".((!empty($data['id']))?$data['id']:0).",'".$data['date_begin']."')";
                    //trace($oSession->id."|".$oSession->perm_title);
                    Application_Model_EventsLog::addToLog($oSession->id,$this->getRequest()->getControllerName(),$this->getRequest()->getActionName(),$oSession->perm_title,$data['id']."::".$data['status'],print_r($data,true));
                }catch(Exception $e){
                    $this->view->message="Помилка запису, зверніться до розробника".$e->getMessage();
                    $this->view->result = "error";
                    $result = array();
                    $result['success'] = true;
                    $result['content'] = $this->view->render('routes/raceopen.phtml');
                    //$result['callback'] = "refresh(0,'".$this->view->form->date_begin->getValue()."')";
                    $this->_helper->json->sendJson($result);
                    return;
                }
            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";
                $result = array();
                $result['success'] = true;
                $result['content'] = $this->view->render('routes/raceopen.phtml');
                $this->_helper->json->sendJson($result);
                return;
            }
        }

        if(!empty($data['id'])){
            $rc = $dbRaces->fetchRow("id=".(int)$data['id']);
            if($rc){
                $this->view->form->populate($rc->toArray());
                $dt= $this->view->form->dt_received->getValue();
                $aDT = explode("-",$dt);
                if(count($aDT)==3){
                    $this->view->form->dt_received->setValue($aDT[2].".".$aDT[1].".".$aDT[0]);
                }
                if(empty($dt)) $this->view->form->dt_received->setValue(date("d.m.Y"));

                $aTM = explode(":",$rc->time_begin);
                $this->view->form->time_begin->setValue($aTM[0].":".$aTM[1]);

                $tm = $this->view->form->time_begin->getValue();
                if(empty( $tm ) ){
                    $this->view->form->time_begin->setValue(date("H:i"));
                }


            }
        }


        if(!$this->view->form->vehicletitle->getValue()){
            //$dbDic = new Application_Model_DbTable_Dictionary();
            //$r = $dbDic->fetchRow("id=".(int)$rs['vehicletypeid']);
            $this->view->form->vehicletitle->setValue((int)$rs['vehicletypeid']);
            $this->view->form->places->setValue($this->view->form->getCountPlaces((int)$rs['vehicletypeid']));
        }
        /*

                govnumber varchar(10),держ номер ТЗ
                driver_name varchar(250),// ПІБ водія
                date_begin date,// дата відправлення рейсу
                time_begin time,// ' час відправлення рейсу
                vehicletitle varchar(100) //тип траспортного засобу
                description text,// коментар
          */


        $result['success'] = true;
        $result['content'] = $this->view->render('routes/raceopen.phtml');

        $this->_helper->json->sendJson($result);

    }


    public function raceOpenPrintAction(){
        $oSession   = Zend_Auth::getInstance()->getStorage()->read();
        $data = $this->getRequest()->getPost();
        if(empty($data['date_begin'])){ $this->_helper->json->sendJson(array("error"=>"Дату не вказано.")); return; }
        if(empty($data['routeid']) || (int)$data['routeid']==0 )  { $this->_helper->json->sendJson(array("error"=>"Маршрут не знайдено.")); return;}
        $rs = $this->_db->fetchRow("id=".(int)$data['routeid']);
        if(!$rs){ $this->_helper->json->sendJson(array("error"=>"Маршрут не знайдено.")); return;}

        $aDT = explode(".",$data['date_begin']);
        if(count($aDT)!=3) { $this->_helper->json->sendJson(array("error"=>"Дату не вказано.")); return; }

        $dbOrg     = new Application_Model_DbTable_OrganizationList();
        $dbDic     = new Application_Model_DbTable_Dictionary();
        $dbRaces   = new Application_Model_DbTable_Races();
        $dbTickets = new Application_Model_DbTable_Tickets();
        $dbStations= new Application_Model_DbTable_LnkStation2Route();

        $rStat = $dbStations->getStationList($data['routeid']);
        $aStation = array();
        $dist = 0;
        mb_internal_encoding("UTF-8");
        foreach($rStat as $elm){
            $title = trim($elm['station_name']);
            $title = (mb_strlen($title, 'UTF-8')>15) ? mb_strcut($title,0,15) :$title;
            for($i=mb_strlen($title, 'UTF-8');$i<15;$i++) $title.=" ";

            $aStation[$elm['stationid']]=array('pos'=>$elm['pos'],'distantion'=>($elm['distantion']-$dist),'title' => $title,"total_tickets"=>0,'total_benefits'=>0,'total_order'=>0,'total_luggage'=>0);
            //$dist = $elm['distantion'];
        }
        $aStatTotal=array('distantion'=>0,"total_tickets"=>0,'total_benefits'=>0,'total_order'=>0,'total_luggage'=>0);
        //trace($aStation,1);
        /*
         <pre>Array
                    (
                        [0] => Array
                            (
                                [id] => 118
                                [routeid] => 36
                                [stationid] => 1306
                                [pos] => 1
                                [timeperiod] => 0
                                [holdtime] => 2
                                [distantion] => 0.00
                                [description] =>
                                [station_name] => КИЇВ Поділ АС
                            )

                        [1] => Array
                            (
                                [id] => 119
                                [routeid] => 36
                                [stationid] => 1365
                                [pos] => 2
                                [timeperiod] => 65
                                [holdtime] => 2
                                [distantion] => 49.00
                                [description] =>
                                [station_name] => Обухів м.
                            )

         * */

        $this->view->date_begin = $data['date_begin'];
        $this->view->station    = $dbDic->fetchRow("id=".(int)$this->ststionID);
        $this->view->vehicle    = $dbDic->fetchRow("id=".(int)$rs->vehicletypeid);
        $this->view->route      = $rs;
        $this->view->conveyor   = $dbOrg->getConveyor($rs->conveyorid);
        $this->view->aStation   = $aStation;
        $this->view->aStatTotal = $aStatTotal;
        $this->view->oSession   = Zend_Auth::getInstance()->getStorage()->read();
        //trace($this->view->aStation);
        $this->view->luggageCount   =   0;
        $this->view->totalUA        =   0.0;

        $this->view->convTotalUA    =   0.0;
        $this->view->convLuggageUA  =   0.0;
        $this->view->convLuggageVat =   0.0;

        $this->view->totalInsurer   =   0.0;
        $this->view->totalLuggage   =   0.0;
        $this->view->totalVat       =   0.0;



        $this->view->race    = $dbRaces->fetchRow("routeid=".(int)$data['routeid']." AND date_begin='".$aDT[2]."-".$aDT[1]."-".$aDT[0]."' AND stationid=".$this->ststionID);//AND tp='out'
        if(!empty($this->view->race->vehicletitle)) {
            if($this->view->race->vehicletitle==$this->view->vehicle->id) $this->view->vehicletitle=$this->view->vehicle->title;
            else {
                $r = $dbDic->fetchRow("id=" . (int)$this->view->race->vehicletitle);
                if ($r) $this->view->vehicletitle = $r->title;
            }
        }



        $this->view->tickets = $dbTickets->getAllTicketsByRoute($data['routeid'],($aDT[2]."-".$aDT[1]."-".$aDT[0]." ".sprintf("%02d",$rs->hour).":".sprintf("%02d",$rs->minute).":00"),"paid");


         //    $dbTickets->fetchAll("routeid=".(int)$data['routeid']." AND dt_time_begin='".$aDT[2]."-".$aDT[1]."-".$aDT[0]." ".sprintf("%02d",$rs->hour).":".sprintf("%02d",$rs->minute).":00'");
        if(!$this->view->tickets) $this->view->tickets=array();
        $result = array();
        $result['success'] = true;
        $item1 = $this->view->render('routes/print.phtml');

        $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">".str_replace("@@@#@@@","1",$item1)."\n-------------------------------------------------------------------------------------------------------------------------------------------------------\n".str_replace("@@@#@@@","2",$item1);
        $result['content'] = $item;
        Application_Model_EventsLog::addToLog($oSession->id,$this->getRequest()->getControllerName(),'race-open',$oSession->perm_title,$data['id']."::print",print_r($data,true));
        $this->_helper->json->sendJson($result);


    }



}