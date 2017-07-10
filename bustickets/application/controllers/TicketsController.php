<?php
class TicketsController extends Zend_Controller_Action
{
    private $_oTicket, $_dbTicket;
    public function init()
    {


        $this->view->headScript()
            ->appendFile('/js/libs/jquery-ui.js');
        $this->view->headScript()
            ->appendFile('/js/libs/ui.datepicker-ua.js');
        $this->view->headScript()
            ->appendFile('/js/libs/maskedinput.js');
        $this->view->headScript()
            ->appendFile('/js/controllers/'.$this->getRequest()->getControllerName().'.js');
        $this->view->headLink()
            ->appendStylesheet('/js/libs/tablesorter/themes/blue/style.css');
        $this->view->headLink()
            ->appendStylesheet('/css/custom-theme/jquery-ui-tab.css');

        $this->view->sActionName    =   "index";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();



        $this->_helper->contextSwitch()
            ->addActionContext('showTicketsForm', array('json'))
            ->addActionContext('filter', array('json'))
            ->addActionContext('lock', array('json'))
            ->addActionContext('findCheck', array('json'))
            ->initContext();
        $this->_oSession = Zend_Auth::getInstance()->getStorage()->read();
        $this->_db = new Application_Model_DbTable_Routes();
        $this->_dbTicket = new Application_Model_DbTable_Tickets();

    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }
    public function indexAction(){
        $this->view->form = new Application_Form_TicketFilter();
        //$this->view->form = new Application_Form_Organisation();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
        $formData = $this->getRequest()->getParams();
        $this->view->sTitle = "Продаж квитків";
        //trace($this->_db->getUserByLogin("admin","qwerty"));
    }
    public function backAction(){
        $this->view->form = new Application_Form_BackTicket();
    }
    function filterAction(){
        //find routes

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form = new Application_Form_TicketFilter();
            if ($form->isValid($formData)) {
                // Спациально для махинаций Василича. только на 20.04.2015
                $dtCurent = date("Ymd")-4;

                $dtMax = date("Ymd",time()+45*24*3600);
                $aDate = explode(".",$formData["date_begin"]);
                $sTime = $formData["time_begin"];
                $dtSelect = $aDate[2].$aDate[1].$aDate[0];
                if($dtSelect>=$dtCurent && $dtSelect<=$dtMax ) {
                    //trace($this->_db->getRouteid($formData['stationstartid'],$formData['stationendid']));
                   // номер час відправлення\ час прибуття\ назва маршруту\ тип тз\ ціна\ ссілка на схему\
                    //echo ($sTime." | ");
                    $this->view->aListRoutes = $this->_db->getListRoutes($formData['stationstartid'], $formData['stationendid'],$aDate);//$sTime
                   // trace($this->view->aListRoutes,1);
                    if ($this->view->aListRoutes) {
                        foreach($this->view->aListRoutes as $key => $ticket){

                            $oTicket = new Application_Model_Ticket($ticket['id'],$ticket['stationstartid'],$ticket['stationendid'],$aDate);
                            $oTicket->loadAdditionalData();
                            $price = $oTicket->getPriceTicket();//$oTicket->loadAdditionalData();
                            $govVAT=20.0;
                            $stationTax = 15.0;
                            $insurerPercent = $oTicket->getProperty('insurance_rate');
                            $insurer_tariff = $price * 1.0 / 100 * $insurerPercent * 1.0;
                            $station_tax_tariff = ($price * 1.0 + $insurer_tariff * 1.0) / 100 * $stationTax * 1.0;
                            $full_price_with_benefits_vat = ($price+$station_tax_tariff)/100.0*$govVAT;
                            $this->view->aListRoutes[$key]['full_price'] = ($price + $insurer_tariff + $station_tax_tariff + $full_price_with_benefits_vat);

                        }
                        //            $result['id']       = (isset($this->view->aListRoutes['id'])) ? $this->view->aListRoutes['id'] : "0";
                        $result['success'] = true;
                        $result['content'] = $this->view->render('tickets/filter.phtml');
                    } else {
                        $result['error'] = "Немає доступних рейсів за цими даними";
                    }
                }else{
                    //checking date
                    $result['error'] = "Помилка - невірна дата";
                }

            }else {
                //isValid
                $result['error'] = "Помилка - неповні дані про початкову/кінцеву зупинку";
            }
         } else {
            //else isPost
            $result['error']  = "Помилка - невірний запит";
        }
        $this->_helper->json->sendJson($result);
    }
    function findCheckAction(){
        $result = array();

        $this->view->kassauid  =   $this->_oSession->id;
        $this->view->kassaname =   $this->_oSession->login;

        $data = $this->getRequest()->getPost();
        $form = new Application_Form_BackTicket();
        $form->removeElement('date_begin');
        $form->removeElement('code');
        $form->removeElement('place');
        if($form->isValid($data)){
            $data['checknumber'] = sprintf("%010d",$data['checknumber']);
            $sSQL =  " ppo='".$form->ppo->getValue()."' AND ";
            $dbT = $this->_dbTicket->fetchRow($sSQL."checknumber='".$data['checknumber']."' AND \"status\"='paid' and back_date ISNULL ");
            if($dbT) {
                $this->view->data = $dbT->toArray();

                $dbRoute = new Application_Model_DbTable_Routes();
                $rRoute = $dbRoute->getRoute($this->view->data["routeid"]);
                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->view->data["station_buy"]);
                //trace($rRoute,1);
                $this->view->title          =   $rRoute['title'];
                $this->view->conveyor_name  =   $rRoute['conveyor_name'];
                $this->view->insurer_title  =  $rRoute['insurer_name'];
                $this->view->vehiclename    =  $rRoute['vehicle_name'];
                $this->view->from = $dbDic->fetchRow("id=" . (int)$this->view->data["from_id"]);
                $this->view->to   = $dbDic->fetchRow("id=" . (int)$this->view->data["to_id"]);
                $dbBnfts = new Application_Model_DbTable_Benefitslist();
                $this->view->benefits_title="";
                $this->view->benefits_name="";
                $r = $dbBnfts->fetchRow("id=" . (int)$this->view->data["benefits_id"]);
                if($r){
                    $this->view->benefits_title=$r->title_print;
                    $this->view->benefits_name =$r->title_full;
                }


                $result['content'] = $this->view->render('tickets/findcheck.phtml');
                $result['success'] = true;
            }else{
               $result['error'] = "чек не знайдено.";
            }
        }else{
            $result['error'] = "не вказано номер чека.";
        }
        $this->_helper->json->sendJson($result);

    }
    function showTicketsFormAction(){
        //modal for buy tickets

        $data = $this->getRequest()->getPost();
        if(empty($data['routeid'])) {
            $this->_helper->json->sendJson(array("error"=>"Не обрано маршрут."));
            return;
        }
        $dtCurent = date("Ymd");
        $dtMax = date("Ymd",time()+45*24*3600);
        $aDate = explode(".",$data["date_begin"]);
        $dtSelect = $aDate[2].$aDate[1].$aDate[0];
        $this->view->dtSelect=$dtSelect;
        if( count($aDate)<3 && !($dtSelect>=$dtCurent && $dtSelect<=$dtMax) ) {
            $this->_helper->json->sendJson(array("error"=>"Обрано невірну дату до маршруту."));
            return;
        }
        $oTicket = new Application_Model_Ticket($data['routeid'],$data['stationstartid'],$data['stationendid'],$aDate);
        $oTicket->loadAdditionalData();

        //$oRoute = $this->_db->getRoute($data['routeid']);
        if(!$oTicket) { $this->_helper->json->sendJson(array("error"=>"Не обрано маршрут.")); return; }
            $this->view->form = new Application_Form_TicketsSchema($oTicket->getProperty('countplaces'));
            $this->view->form->populate($data);

            $this->view->form->routeid->setValue($oTicket->getRaceID());
        //if(empty($data['buy'])) {
            $this->view->raceID = $oTicket->getRaceID();
            $this->view->form->startid->setValue($oTicket->getProperty('stationstartid'));
            $this->view->form->endid->setValue($oTicket->getProperty('stationendid'));

            $this->view->dt_start   =  $oTicket->getProperty('dt_start');
            $this->view->dt_finish  =  $oTicket->getProperty('dt_finish');

            $this->view->dt_time_begin =  $oTicket->getProperty('dt_time_begin');
            $this->view->dt_time_finish=  $oTicket->getProperty('dt_time_finish');


            $this->view->code           =   $oTicket->getProperty('code');
            $this->view->title          =   $oTicket->getProperty('title');
            $this->view->vehiclename    =  $oTicket->getProperty('vehiclename');
            $this->view->vehicletype    =  $oTicket->getProperty('vehicletype');
            $this->view->countplaces    =  $oTicket->getProperty('countplaces');
            $this->view->platform       =  $oTicket->getProperty('platform');

            $this->view->kassauid  =   $this->_oSession->id;
            $this->view->kassaname =   $this->_oSession->login;

            $this->view->start  =   $oTicket->getProperty('stationfrom');
            $this->view->end    =   $oTicket->getProperty('stationto');
            $this->view->dt_finish=   $oTicket->getProperty('dt_finish');
        // TODO: опять исправить время на корректную формулу
            $aTime = explode(" ",$oTicket->getProperty('dt_start'));
            $this->view->dt     =   $aTime[0];//$oTicket->getProperty('date_begin');
            $this->view->time   =   $aTime[1]; // . ' (' . $oTicket->getProperty('minute') . ')';
            $this->view->form->dt_begin->setValue($data['date_begin']);

            $this->view->distance = sprintf("%.2f",$oTicket->getProperty('distance'));
            $this->view->triptime = $oTicket->getProperty('triptime');

            $this->view->tprice                 =   $oTicket->getPriceTicket();
            $this->view->lprice                 =   $oTicket->getPriceLuggage();
            $this->view->insurance_rate         =   sprintf("%.2f",$oTicket->getProperty('insurance_rate'));
            $this->view->insurer_id             =   $oTicket->getProperty('insurerid_for_conveyor');
            $this->view->insurer_name           =   $oTicket->getProperty('insurer_name');
            $this->view->insurer_print_name     =   $oTicket->getProperty('insurer_print_name');
            $this->view->conveyor_name          =   $oTicket->getProperty('conveyor_name');
            $this->view->conveyor_id            =   $oTicket->getProperty('conveyorid');
            $this->view->conveyor_print_name    =   $oTicket->getProperty('conveyor_print_name');
            $this->view->conveyor_vat           =   $oTicket->getProperty('conveyor_vat');
            $this->view->bm                     =   $oTicket->getListBenefits();
            $this->view->busy_places           =    json_encode($this->_getBusyPlaces($data['routeid'],$this->_oSession->id,$data['stationstartid'],$data['stationendid'],$oTicket->getProperty('dt_time_begin')));
            foreach((array)$this->view->bm as $id => $b){
                $this->view->form->benefitsid->addMultiOption($id,$b['title'] . ' - ' . $b['title_full']);
            }



        if(isset($data['buy'])){

            if($this->view->form->isValid($data)){
                $dbLnk = new Application_Model_DbTable_LnkStation2Route();
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

        if(!empty($data['id'])){
            $r = $dbLnk->getStation($data['routeid'],$data['id']);
            if(!$r) {
                $result['error'] = "Станцію не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $this->view->form->populate($r);
        }

        $result['success'] = true;
        $result['content'] = $this->view->render('tickets/showticketsform.phtml');
        $this->_helper->json->sendJson($result);
    }
    public function lockAction(){
        $data = $this->getRequest()->getPost();

        if(empty($data['serviceid'])) $data['serviceid']=0;
        $data['serviceid']=(int)$data['serviceid'];

        $isDone = false;
        $data['kassauid']=$this->_oSession->id;
        if(
                !empty($data['place']) && (int)$data['place']>0
            &&  !empty($data['routeid']) && (int)$data['routeid']>0
            &&  !empty($data['kassauid']) && (int)$data['kassauid']>0
            &&  !empty($data['from_id']) && (int)$data['from_id']>0
            &&  !empty($data['to_id']) && (int)$data['to_id']>0
            &&  !empty($data['station_buy']) && (int)$data['station_buy']>0
            &&  !empty($data['dt_time_begin'])

         ) {


            switch ($data['status']) {
                case "drop":
                    try {
                        $isDone = $this->_dbTicket->delete(
                            "       place=" . (int)$data['place']
                            . " AND routeid=" . (int)$data['routeid']
                            . " AND kassauid=" . (int)$data['kassauid']
                            . " AND from_id=".(int)$data['from_id']
                            . " AND station_buy=" . (int)$data['station_buy']
                            . " AND dt_time_begin='" . addslashes( $data['dt_time_begin'])."'"
                            . " AND ( \"status\"='inprocess' OR \"status\"='locked' ) "
                        );
                        $result['success'] = array("place"=>(int)$data['place'],'status'=>$data['status']);

                    } catch (Exception $e) {
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                    }
                    break;
                case "unlocked":
                    try {
                        $isDone = $this->_dbTicket->delete(
                            "       place=" . (int)$data['place']
                            . " AND routeid=" . (int)$data['routeid']
                            . " AND kassauid=" . (int)$data['kassauid']
                            . " AND from_id=".(int)$data['from_id']
                            . " AND to_id=".(int)$data['to_id']
                            . " AND station_buy=" . (int)$data['station_buy']
                            . " AND dt_time_begin='" . addslashes( $data['dt_time_begin'])."'"
                            . " AND \"status\"='locked'"
                        );
                        $result['success'] = array("place"=>(int)$data['place'],'status'=>$data['status']);

                        } catch (Exception $e) {
                            $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                        }
                    break;
                case "locked":
                    try {
                     $this->_dbTicket->delete("place='".(int)$data['place']."' AND  routeid='".(int)$data['routeid']."' AND from_id='".(int)$data['from_id']."' AND dt_time_begin='".$data['dt_time_begin']."' AND   kassauid ='".(int)$data['kassauid']."' AND \"status\"='locked'");

                     if(!$this->_dbTicket->getRowByFields(array(
                                                            'place'  =>(int)$data['place'],
                                                            'routeid'=>(int)$data['routeid'],
                                                            'from_id'=>(int)$data['from_id'],
                                                            'dt_time_begin'=>$data['dt_time_begin'],
                                                            'exclude'=>"'bpaid','deduction','refund','failed'"
                                                    )))
                        $isDone = $this->_dbTicket->insertData($data);
                        if(is_numeric($isDone)) {
                            $result['success'] = array("ticketID" => $isDone, "place" => (int)$data['place'], 'status' => $data['status']);
                            $isDone=true;
                        } else{
                            $result['error'] = array("message"=>$isDone['error'],"place"=>(int)$data['place'],'status'=>'unknown');
                            $isDone=false;
                        }
                    } catch (Exception $e) {
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                        $isDone=false;
                    }
                    break;
                case "inprocess":
                    try { //вошли в предпродажную блокировку
                        $r = $this->_dbTicket->getRowByFields(array(
                                                            'place'  =>(int)$data['place'],
                                                            'routeid'=>(int)$data['routeid'],
                                                            'from_id'=>(int)$data['from_id'],
                                                            'to_id'=>(int)$data['to_id'],
                                                            'dt_time_begin'=>$data['dt_time_begin'],
                                                            'status'=>'locked',
                                                            'kassauid'=>(int)$data['kassauid']
                                                        ));
                        if($r) {
                            $data['id']=$r->id;
                            $isDone = $this->_dbTicket->updateData($data);
                            if(is_numeric($isDone)) {
                                $result['success'] = array("ticketID" => $data['id'], "place" => (int)$data['place'], 'status' => $data['status']);
                                $isDone=true;
                            } else {
                                $result['error'] = array("message"=>$isDone['error'],"place"=>(int)$data['place'],'status'=>'unknown');
                                $isDone=false;
                            }
                        }else{
                            if(!$this->_dbTicket->getRowByFields(array(
                                'place'  =>(int)$data['place'],
                                'routeid'=>(int)$data['routeid'],
                                'from_id'=>(int)$data['from_id'],
                                'dt_time_begin'=>$data['dt_time_begin'],
                                'exclude'=>"'bpaid','deduction','refund'"
                            ))) {
                                $isDone = $this->_dbTicket->insertData($data);
                                if(is_numeric($isDone)) {
                                    $result['success'] = array("ticketID" => $isDone, "place" => (int)$data['place'], 'status' => $data['status']);
                                    $isDone=true;
                                } else {
                                    $result['error'] = array("message"=>$isDone['error'],"place"=>(int)$data['place'],'status'=>'unknown');
                                    $isDone=false;
                                }
                            }else  $result['error'] = array("message"=>'place is not locked',"place"=>(int)$data['place'],'status'=>'unknown');
                        }
                    } catch (Exception $e) {
                        $isDone=false;
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                    }
                    break;
                case "ispaid":
                    try{
                        $r = $this->_dbTicket->getRowByFields(array(
                            'id'=>(int)$data['id'],
                            'place'  =>(int)$data['place'],
                            'routeid'=>(int)$data['routeid'],
                            'from_id'=>(int)$data['from_id'],
                            'to_id'=>(int)$data['to_id'],
                            'dt_time_begin'=>$data['dt_time_begin'],
                            'status'=>'paid'
                        ));


                        if($r){

                            $percent = 0;
                            $statReturn = 0;
                            if(time()-$r->lastchange<= 10*60) {
                                $percent=100;
                                $statReturn = 100;
                            }
                            else {
                                $route = $this->_db->fetchRow("id=".(int)$data['routeid']);
                                if ($route) {
                                    $dbRace = new Application_Model_DbTable_Races();
                                    $aDT = explode(" ",$data['dt_time_begin']);
                                    $race = $dbRace->fetchRow("routeid='".(int)$data['routeid']."' and date_begin='".$aDT[0]."' and \"status\" in ( 'failed','forbidden' )");
                                    /* проверяем статус рейса и если он отменен или стоит запрет продажи,
                                    то возвращаем всю сумму билета
                                    */
                                    if($race){
                                        $percent=100;
                                        $statReturn = 100;
                                    }else {
                                        $backMatrix = json_decode($route->back, true);
                                        //trace($backMatrix);
                                        $delta = round((strtotime($data['dt_time_begin']) - time()) / 60);
                                        //trace($delta);
                                        if (is_array($backMatrix['backCalc'])) foreach ($backMatrix['backCalc'] as $row) {
                                            if ($delta > $row['s'] && $delta <= $row['e']) {
                                                $percent = $row['p'];
                                                break;
                                            }
                                        }
                                    }// else if($race){
                                }
                            }
                            //trace(("percent=".$percent."statReturn:".$statReturn),1);
                            if($percent>0.001) {
                                $result['success'] = array("ticketID" => $r->id, "percent" => $percent,"statReturn"=>$statReturn, 'status' => $data['status']);
                                $isDone = $r->id;
                            }else{
                                $result['error'] = array("message"=>'Квиток не підлягає поверненю',"place"=>(int)$data['place'],'status'=>'unknown');
                            }
                        }else{
                            $result['error'] = array("message"=>'place is not paid',"place"=>(int)$data['place'],'status'=>'unknown');
                        }
                    } catch (Exception $e) {
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                    }
                    break;
                case "deduction":
                    unset($data['id']);
                    $data['status']         =   'deduction';
                    $isDone = $this->_dbTicket->insertData($data);
                    if(is_numeric($isDone)) {
                        $result['success'] = array("data" => "stored");
                        $this->_helper->json->sendJson($result);
                        return;
                    }
                    $isDone = false;
                    $result['error'] = array("message"=>$isDone['error'].'. Place is not paid. icorrect update',"place"=>(int)$data['place'],'status'=>'unknown');
                    break;
                case "bpaid":
                    $this->_dbTicket->getAdapter()->beginTransaction();
                    try{
                        $r = $this->_dbTicket->getRowByFields(array(
                            'id'=>(int)$data['id'],
                            'place'  =>(int)$data['place'],
                            'routeid'=>(int)$data['routeid'],
                            'from_id'=>(int)$data['from_id'],
                            'to_id'=>(int)$data['to_id'],
                            'dt_time_begin'=>$data['dt_time_begin'],
                            'status'=>'paid'
                        ));

                        if($r) {
                            $data['id']=$r->id;
                            $data['status']='bpaid';
                            $data['back_date']=date("Y-m-d H:i:s");
                            $newA=array("checknumber"=>$data['checknumber'],"ppo"=>$data['ppo'],"kassauid"=>$data['kassauid']);
                            unset($data['checknumber']);
                            unset($data['ppo']);
                            unset($data['kassauid']);
                            $isDone = $this->_dbTicket->updateData($data);
                            if(is_numeric($isDone)){
                                unset($data['id']);
                                $data['checknumber']    =   $newA['checknumber'];
                                $data['ppo']            =   $newA['ppo'];
                                $data['kassauid']       =   $newA['kassauid'];
                                $data['status']         =   'refund';
                                $data['price_tariff']   =   $data['price_tariff']*-1.0;
                                $data['price_tariff_with_benefits'] = $data['price_tariff_with_benefits']*-1.0;
                                $data['benefits_percent'] = -1.0*$data['benefits_percent'];
                                $data['conv_tariff'] = -1.0*$data['conv_tariff'];
                                $data['stat_tariff'] = -1.0*$data['stat_tariff'];
                                $data['conv_tariff_vat'] = -1.0*$data['conv_tariff_vat'];
                                $data['stat_tariff_vat'] = -1.0*$data['stat_tariff_vat'];
                                $data['insurer_percent'] = -1.0*$data['insurer_percent'];
                                $data['insurer_tariff'] = -1.0*$data['insurer_tariff'];
                                $data['station_tax_tariff'] = -1.0*$data['station_tax_tariff'];
                                $data['station_tax_tariff_vat'] = -1.0*$data['station_tax_tariff_vat'];
                                $data['luggage_total'] = -1.0*$data['luggage_total'];
                                $data['full_price'] = -1.0*$data['full_price'];
                                $data['full_price_vat'] = -1.0*$data['full_price_vat'];
                                $data['total_price'] = -1.0*$data['total_price'];
                                $data['total_price_with_benefits'] = -1.0*$data['total_price_with_benefits'];
                                $data['full_price_with_benefits_vat'] = -1.0*$data['full_price_with_benefits_vat'];
                                $data['full_price_with_benefits'] = -1.0*$data['full_price_with_benefits'];
                                $data['station_tax_tariff_with_benefits_vat'] = -1.0*$data['station_tax_tariff_with_benefits_vat'];
                                $data['station_tax_tariff_with_benefits'] = -1.0*$data['station_tax_tariff_with_benefits'];
                                $data['insurer_tariff_with_benefits'] = -1.0*$data['insurer_tariff_with_benefits'];
                                $data['stat_tariff_with_benefits_vat'] = -1.0*$data['stat_tariff_with_benefits_vat'];
                                $data['conv_tariff_with_benefits_vat'] = -1.0*$data['conv_tariff_with_benefits_vat'];
                                $data['stat_tariff_with_benefits'] = -1.0*$data['stat_tariff_with_benefits'];
                                $data['conv_tariff_with_benefits'] = -1.0*$data['conv_tariff_with_benefits'];
                                $data['prepaid_vat'] = -1.0*$data['prepaid_vat'];
                                $data['prepaid'] = -1.0*$data['prepaid'];
                                $data['paidfromother_vat'] = -1.0*$data['paidfromother_vat'];
                                $data['paidfromother'] = -1.0*$data['paidfromother'];
                                $data['conv_luggage_tariff'] = -1.0*$data['conv_luggage_tariff'];
                                $data['stat_luggage_tariff'] = -1.0*$data['stat_luggage_tariff'];
                                $data['conv_luggage_tariff_vat'] = -1.0*$data['conv_luggage_tariff_vat'];
                                $data['stat_luggage_tariff_vat'] = -1.0*$data['stat_luggage_tariff_vat'];
                                $isDone=$this->_dbTicket->insertData($data);
                                if(is_numeric($isDone)) {
                                        $result['success'] = array("data" => $data);
                                        $this->_dbTicket->getAdapter()->commit();
                                        $this->_helper->json->sendJson($result);
                                        $isDone=true;
                                        return;
                                }
                            }
                            $result['error'] = array("message"=>$isDone['error'].'. Place is not paid. Icorrect update',"place"=>(int)$data['place'],'status'=>'unknown');
                            $this->_dbTicket->getAdapter()->rollback();
                            $isDone=false;
                        }else{
                            $isDone=false;
                            $result['error'] = array("message"=>'Place is not paid',"place"=>(int)$data['place'],'status'=>'unknown');
                        }
                    } catch (Exception $e) {
                        $this->_dbTicket->getAdapter()->rollback();
                        $isDone=false;
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                    }
                    break;
                case "paid":
                    try{
                        $this->_dbTicket->getAdapter()->beginTransaction();
                        $r = $this->_dbTicket->getRowByFields(array(
                            'place'  =>(int)$data['place'],
                            'routeid'=>(int)$data['routeid'],
                            'from_id'=>(int)$data['from_id'],
                            'to_id'=>(int)$data['to_id'],
                            'dt_time_begin'=>$data['dt_time_begin'],
                            'status'=>'inprocess',
                            'kassauid'=>(int)$data['kassauid']
                        ));
                        if($r) {

                            $data['id']=$r->id;
                            $isDone = $this->_dbTicket->updateData($data);
                            if(is_numeric($isDone)){
                                $result['success'] = array("ticketID" => (int)$data['id'], "place" => (int)$data['place'], 'status' => $data['status']);
                                $isDone=true;
                                $this->_dbTicket->getAdapter()->commit();
                            } else {
                                $result['error'] = array("message"=>$isDone['error'],"place"=>(int)$data['place'],'status'=>'unknown');
                                $isDone=false;
                                $this->_dbTicket->getAdapter()->rollback();
                            }
                        }else{
                            $isDone=false;
                            $result['error'] = array("message"=>'place is not locked',"place"=>(int)$data['place'],'status'=>'unknown');
                        }
                    } catch (Exception $e) {
                        $isDone=false;
                        $this->_dbTicket->getAdapter()->rollback();
                        $result['error'] = array("message"=>$e->getMessage(),"place"=>(int)$data['place'],'status'=>'unknown');
                    }
                    break;


            }

            //$isDone = false;
            if(!$isDone ){
                if(isset($result['success'])) { $result['error']=$result['success']; unset($result['success']);}
                if(!isset($result['error'])) $result['error']=array("message"=>"operation is not done","place"=>(int)$data['place'],'status'=>'unknown');
            }

            $result["busy_places"] = $this->_getBusyPlaces($data['routeid'],$data['kassauid'],$data['from_id'],$data['to_id'],$data['dt_time_begin']);
            $result["request"]     = $data;
        }else {    // if(!empty($data['place']) && (int)$data['place']>0) {
            $result['error']="Не всі данні передено";
        }
        $this->_helper->json->sendJson($result);
    }

    private function _getBusyPlaces($routeid,$kassauid,$from_id,$to_id,$dt_time_begin){
        $aPlaces= array();

        if(
            !empty($routeid) && (int)$routeid>0
            &&  !empty($kassauid) && (int)$kassauid>0
            &&  !empty($from_id) && (int)$from_id>0
            &&  !empty($to_id) && (int)$to_id>0
            &&  !empty($dt_time_begin)

        ) {


            $aDate = date("d.m.Y", strtotime($dt_time_begin));
            $aDate = explode(".", $aDate);
            $this->_oTicket = (!$this->_oTicket) ? new Application_Model_Ticket($routeid, $from_id, $to_id, $aDate) : $this->_oTicket;
            $aPlaces['reserve'] = $this->_oTicket->getReserv();
            $aPlaces['locked']  = $this->_dbTicket->getLockedPlaces($routeid,$from_id ,$dt_time_begin,$kassauid);
            $aPlaces['blocked'] = $this->_dbTicket->getBuyPlaces($routeid,$from_id ,$dt_time_begin,$kassauid);

        }
        return $aPlaces;
    }

    public function storeAction(){
        $this->_helper->json->sendJson('ok');
        exit;
    }


}