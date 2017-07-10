<?php
class ReportsController extends Zend_Controller_Action
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



        $this->_oSession = Zend_Auth::getInstance()->getStorage()->read();








    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }
    public function indexAction(){
        $def = array(
            array("date",     "D"),
            array("name",     "C",  50),
            array("age",      "N",   3, 0),
            array("email",    "C", 128),
            array("ismember", "L")
        );
        // создаем
        if (!dbase_create('/tmp/test.dbf', $def)) {
            echo "Ошибка, не получается создать базу данных\n";
        }else{
            exit(file_get_contents("/tmp/test.dbf"));
        }


    }
    public function debetAction(){
        $dbTickets = new  Application_Model_DbTable_Tickets();
        $dbServices = new Application_Model_DbTable_ServicesList();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/debet/');
        $this->view->sTitle = "Надходження по статтях";

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";

            $this->view->dtb = $data['date_begin'];
            $this->view->dte = $data['date_end'];

            $dbDic = new Application_Model_DbTable_Dictionary();
            $this->view->stations = $dbDic->fetchRow("id=" . (int)$this->ststionID);

            $this->view->oSession   = Zend_Auth::getInstance()->getStorage()->read();

            $this->view->totalPrice=0.0;
            $this->view->totalVat=0.0;
            $this->view->totalAmount=0.0;
            $this->view->totalCount=0.0;

            //col1
            $this->view->adServices = $dbServices->getServicesGroupByStation($DtS,$DtE,$this->ststionID);
            //trace($rs,1);



            //col2 //num
            /*ПОПЕРЕДНІЙ ПРОДАЖ*/
            $this->view->prepaid = $dbTickets->getPrepaidCash($DtS,$DtE,$this->ststionID);
            /*СТАНЦІЙНИЙ ЗБІР*/
            $this->view->station = $dbTickets->getStatCash($DtS,$DtE,$this->ststionID);
            /*СТРАХОВИЙ ЗБІР*/
            $this->view->insurer = $dbTickets->getInsureCash($DtS,$DtE,$this->ststionID);
            /* ТАРИФ ОРГ. */
            $this->view->org = $dbTickets->getOrgCash($DtS,$DtE,$this->ststionID);
            /* ТАРИФ ПЕР.НЕПЛ.*/
            $this->view->conv = $dbTickets->getConvCash($DtS,$DtE,$this->ststionID);

            $this->view->deduckation = $dbTickets->getSumDeduckationsTickets($DtS, $DtE, null, $this->ststionID);

            $item = $this->view->render('reports/items/printdebet.phtml');
            $result = array();
            $result['success'] = true;
            $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
            $result['content'] = $item;
            $this->_helper->json->sendJson($result);
            exit;

        }
    }
    public function dispAction(){
        $dbRaces = new  Application_Model_DbTable_Races();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/disp/');
        $this->view->sTitle = "Звіт про роботу диспетчера";

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";

            $this->view->dtb = $data['date_begin'];
            $this->view->dte = $data['date_end'];

            $dbDic = new Application_Model_DbTable_Dictionary();
            $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);



            $this->view->totalplaces=0;
            $this->view->oSession   = Zend_Auth::getInstance()->getStorage()->read();

            $this->view->rs = $dbRaces->getRacesGroupByDays($DtS,$DtE,$this->ststionID);

            $this->view->total_04 = 0;
            $this->view->total_08 = 0;
            $this->view->total_01 = 0;
            $this->view->total8 = 0.0;
            $this->view->total9 = 0.0;
            $this->view->total10= 0.0;
            $this->view->total11= 0.0;
            $this->view->total_aFailed = array();
            $this->view->total_forbidden = 0;
            $this->view->total_received = 0;
            $this->view->total_opened = 0;
            $this->view->total_failed = 0;
            $this->view->dt = "";

            $this->view->failed = $dbDic->selectDataByType(Application_Model_Dictionary::CANCELREASON,0)->toArray();

            $item = $this->view->render('reports/items/printdisp.phtml');
            $result = array();
            $result['success'] = true;
            $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
            $result['content'] = $item;
            $this->_helper->json->sendJson($result);
            exit;

        }
    }
    public function nextperiodAction(){
        $dbTicket = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/nextperiod/');
        $this->view->sTitle = 'Перехідні суми (у період)';
        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";
            if(strtotime($DtS)<strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);
                $this->view->atotalplaces = 0;
                $this->view->atotal1 = 0.0;
                $this->view->atotal2 = 0.0;
                $this->view->atotal3 = 0.0;
                $this->view->atotal4 = 0.0;
                $this->view->atotal5 = 0.0;
                $this->view->atotal6 = 0.0;
                $this->view->atotal7 = 0.0;

                $this->view->totalplaces = 0;
                $this->view->total1 = 0.0;
                $this->view->total2 = 0.0;
                $this->view->total3 = 0.0;
                $this->view->total4 = 0.0;
                $this->view->total5 = 0.0;
                $this->view->total6 = 0.0;
                $this->view->total7 = 0.0;

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbTicket->getTicketsNextPeriod($DtS, $DtE, $this->ststionID);
                $item = $this->view->render('reports/items/printnext.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;

            }

        }
    }

    public function prevperiodAction(){
        $dbTicket = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/prevperiod/');
        $this->view->sTitle = 'Перехідні суми (з періоду)';
        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";
            if(strtotime($DtS)<strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);
                $this->view->atotalplaces = 0;
                $this->view->atotal1 = 0.0;
                $this->view->atotal2 = 0.0;
                $this->view->atotal3 = 0.0;
                $this->view->atotal4 = 0.0;
                $this->view->atotal5 = 0.0;
                $this->view->atotal6 = 0.0;
                $this->view->atotal7 = 0.0;

                $this->view->totalplaces = 0;
                $this->view->total1 = 0.0;
                $this->view->total2 = 0.0;
                $this->view->total3 = 0.0;
                $this->view->total4 = 0.0;
                $this->view->total5 = 0.0;
                $this->view->total6 = 0.0;
                $this->view->total7 = 0.0;

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbTicket->getTicketsPrevPeriod($DtS, $DtE, $this->ststionID);
                $item = $this->view->render('reports/items/printprev.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;

            }

        }
    }

    public function monthAction(){
        $dbTicket   = new Application_Model_DbTable_Tickets();
        $dbServices = new Application_Model_DbTable_ServicesList();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/month/');
        $this->view->sTitle = 'Звіт за місяць';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";
            if(strtotime($DtS)<strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);
                $this->view->total1 = 0.00;
                $this->view->total2 = 0.00;
                $this->view->total3 = 0.00;
                $this->view->total4 = 0.00;
                $this->view->total5 = 0.00;
                $this->view->total6 = 0.00;
                $this->view->total6_5 = 0.00;
                $this->view->total7 = 0.00;
                $this->view->total8 = 0.00;
                $this->view->total8_5 = 0.00;
                $this->view->total9 = 0.00;

                $this->view->total13 = 0.00;
                $this->view->isShowLuggage=false;

                $this->view->total_inkas=0.00;
                $this->view->total_oamount=0.00;
                $this->view->total_camount=0.00;
                $this->view->totalplaces = 0;
                $this->view->total_all=0.00;
                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbTicket->getTicketsGroupByKassaUID($DtS, $DtE, $this->ststionID);
                //7 	Заїзд та виїзд м


                if($this->view->rs) foreach($this->view->rs as $idx=>$row){

                    if( $this->view->rs[$idx]['col13']*1>0) $this->view->isShowLuggage=true;
                    $rsDeduc = $dbTicket->getSumDeduckationsTickets($DtS, $DtE, $row['kassauid']);

                    //trace("UID:".$row['kassauid']);
                    //trace($rsDeduc);


                    $rs = $dbServices->getServicesGroupByKassaUID($DtS,$DtE,$row['kassauid']);
                    $this->view->rs[$idx]['col8_5']=0.0;
                    $this->view->rs[$idx]['col8']=0.0;
                    $this->view->rs[$idx]['col7']=0.0;
                    $this->view->rs[$idx]['col6_5']=(!empty($rsDeduc["col6_5"])) ? $rsDeduc["col6_5"] : 0.00;

                    $tax = ((!empty($rsDeduc["col2"])) ? $rsDeduc["col2"] : 0.00); /* Должны убрать податок */
                    //$tax+=$rsDeduc["stat_tariff_sum"]+$rsDeduc["conv_tariff_sum"]/100*20;


                    if($rsDeduc["conv_sum_vat"]*1.0<0.001){
                        $rsDeduc["conv_sum_vat"]=$rsDeduc["conv_tariff_sum"]/6.0*5.0;
                        $rsDeduc["conv_tariff_sum"]=$rsDeduc["conv_tariff_sum"]-$rsDeduc["conv_sum_vat"];
                    }
                    $tax+=$rsDeduc["conv_sum_vat"];

                    $this->view->rs[$idx]['col2']-=($rsDeduc["conv_sum_vat"]+$rsDeduc["conv_tariff_sum"]); /*Тар. Пер.*/
                    $this->view->rs[$idx]['col3']-=($rsDeduc["stat_sum_vat"]+$rsDeduc["stat_tariff_sum"]);  /*Тар. Орг.*/

                    if($rs) foreach($rs as $r){
                        //заїзд
                        if($r['serviceid']==6 ){
                            $this->view->rs[$idx]['col8']+= $r['total'];
                        }else if( in_array($r['serviceid']*1,array(1,2,3,4,5,7,8)) ) {
                            $this->view->rs[$idx]['col7']+= $r['total'];
                        }
                        $this->view->rs[$idx]['col8_5']+= $r['total'];
                        $tax+= $r['vats'];
                    }
                    $this->view->rs[$idx]['col9']+=$tax;
                    $this->view->rs[$idx]['openamount'] = $dbServices->getAmountForOpenDay($DtS,$row['kassauid']);
                }


                $item = $this->view->render('reports/items/printmonth.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;
            }

        }
    }


    public function kassaAction(){
        $dbTicket   = new Application_Model_DbTable_Tickets();
        $dbServices = new Application_Model_DbTable_ServicesList();

        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/kassa/');
        $this->view->sTitle = 'Звіт про роботу касирів';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";

            $this->view->dtb = $data['date_begin'];
            $this->view->dte = $data['date_end'];

            $dbDic = new Application_Model_DbTable_Dictionary();
            $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);
            $this->view->total1=0.00;
            $this->view->total2=0.00;
            $this->view->total3=0.00;
            $this->view->total4=0.00;
            $this->view->total5=0.00;
            $this->view->total6=0.00;
            $this->view->total6_4=0.00;
            $this->view->total6_5=0.00;
            $this->view->total7=0.00;
            $this->view->total7_5=0.00;
            $this->view->total8=0.00;
            $this->view->total9=0.00;

            $this->view->total13=0.00;
            $this->view->total14=0.00;
            $this->view->isShowLuggage=false;


            $this->view->totalplaces=0;
            $this->view->oSession   = Zend_Auth::getInstance()->getStorage()->read();

            $this->view->rs = $dbTicket->getTicketsGroupByKassaUID($DtS,$DtE,$this->ststionID);


            if($this->view->rs) foreach($this->view->rs as $idx=>$row){

                if( $this->view->rs[$idx]['col13']*1>0) $this->view->isShowLuggage=true;
                $rsDeduc = $dbTicket->getSumDeduckationsTickets($DtS, $DtE, $row['kassauid']);
                $rs = $dbServices->getServicesGroupByKassaUID($DtS,$DtE,$row['kassauid']);
                $this->view->rs[$idx]['col6_5']=0.0;
                $this->view->rs[$idx]['col8']=0.00;
                $this->view->rs[$idx]['col7']=0.00;
                $this->view->rs[$idx]['col6_4']=(!empty($rsDeduc["col6_5"])) ? $rsDeduc["col6_5"] : 0.00;

                $tax = ((!empty($rsDeduc["stat_sum_vat"])) ? $rsDeduc["stat_sum_vat"] : 0.00);


                if($rsDeduc["conv_sum_vat"]*1.0<0.001){
                    $rsDeduc["conv_sum_vat"]=$rsDeduc["conv_tariff_sum"]/6.0*5.0;
                    $rsDeduc["conv_tariff_sum"]=$rsDeduc["conv_tariff_sum"]-$rsDeduc["conv_sum_vat"];
                }
                $tax+= ((!empty($rsDeduc["conv_sum_vat"])) ? $rsDeduc["conv_sum_vat"] : 0.00);

                $this->view->rs[$idx]['col1']-=$this->view->rs[$idx]['col6_4'];
                $this->view->rs[$idx]['col2']-=($rsDeduc["conv_sum_vat"]+$rsDeduc["conv_tariff_sum"]); /*Тар. Пер.*/
                $this->view->rs[$idx]['col3']-=($rsDeduc["stat_sum_vat"]+$rsDeduc["stat_tariff_sum"]);  /*Тар. Орг.*/


                if($rs) foreach($rs as $r){
                    //заїзд
                    if($r['serviceid']==6 ){
                        $this->view->rs[$idx]['col7']+= $r['total'];
                    }else if( in_array($r['serviceid']*1,array(1,2,3,4,5,7,8)) ) {
                        $this->view->rs[$idx]['col8']+= $r['total'];
                    }
                    $this->view->rs[$idx]['col6_5']+= $r['total'];
                    $tax+= $r['vats'];
                }
                $this->view->rs[$idx]['col9']+=$tax;
                $this->view->rs[$idx]['openamount'] = $dbServices->getAmountForOpenDay($DtS,$row['kassauid']);
            }


            $item = $this->view->render('reports/items/printkassa.phtml');
            $result = array();
            $result['success'] = true;
            $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
            $result['content'] = $item;
            $this->_helper->json->sendJson($result);
            exit;

        }
    }

    /**
     *
     */
    public function ticketsAction(){
        $dbTicket = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_TicketsReport();
        //$this->view->form = new Application_Form_Organisation();


        if($this->getRequest()->isPost()){
            $this->_helper->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            ob_start();
            $data = $this->getRequest()->getPost();
            //trace($data);
            //$response = file_get_contents(APPLICATION_PATH."/scripts/migration/sanya_preved.txt");
            $aDTS = explode(".",$data['date_start']);
            $aDTS[2]=substr($aDTS[2],2);
            $aDTF = explode(".",$data['date_finish']);
            $aDTF[2]=substr($aDTF[2],2);
            // TODO: fixed station PODIL as 22
            $station=22;
            $filename=''.$station.'_'.$aDTS[0].'_'.$aDTF[0];
            //$response  = $dbTicket->get1cExport($data['stationstartid'] ,$aDTS,$aDTF);
            //$response  = $dbTicket->get1cExport(13 ,$aDTS,$aDTF);
            $response  = $dbTicket->get1cExport($station ,$aDTS,$aDTF);
            //trace($response,1);
            //$response  = $this->_dbTicket->get1cExport(1306 ,'2015-03-15');


            $entries='';
            $count=0;
            foreach($response as $r){
                $entries .= sprintf(' %02s%03s%1s%-6.6s%2s%5s%2s%2s%2s%3d%8.2d%8.2f%7.2f%7.2d%5.2d%3d%6.2f%3d%8.2f%8.2f',
                $r['dek'], $r['pach'], $r['pstrax'], iconv( "utf-8", "windows-1251",$r['n_dok']),
                $r['as'], $r['aatp'], $r['den'], $r['mes'], $r['god'],
                $r['kolvo'], $r['kolvo1'], $r['summa'], $r['sosstrax'], $r['summa1'],
                $r['sosstrax1'], $r['nbag'], $r['xbag'], $r['kolvoa'], $r['summa_k'], $r['summa_s']);
                $count+=1;
            }
            $entries .= "\x1a"; // End of DBF-file


            $content='';
            $header=array(
                array("value"=>"3",       "mask"=> "H*", "length"=> "2"),
                array("value"=>date('y'), "mask"=> "H*", "length"=> "2"),
                array("value"=>date('n'), "mask"=> "H*", "length"=> "2"),
                array("value"=>date('j'), "mask"=> "H*", "length"=> "2")
            );
            $header[]=array("value"=>$count,"mask"=> "H*", "length"=> "8");        // Число записей в базе
            $header[]=array("value"=>"673", "mask"=> "H*", "length"=> "4");        // Полная длина заголовка (с дескрипторами полей)
            $header[]=array("value"=>"92",  "mask"=> "H*", "length"=> "4");        // Длина одной записи
            $header[]=array("value"=>"0",   "mask"=> "H*", "length"=> "4");        // Зарезервировано (всегда 0)
            $header[]=array("value"=>"0",   "mask"=> "H*", "length"=> "36");       // Зарезервированная область для многопользовательского использования

            foreach($header as $ent){
                $content .= pack($ent["mask"], implode(array_reverse(str_split(sprintf("%0".$ent["length"]."X", strval($ent["value"])),2))));
            };
            // Header for 1C fields
            $content .="\x44\x45\x4B\x00\x00\x00\x00\x00\x00\x00\x00\x43\x03\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x50\x41\x43\x48\x00\x00\x00\x00\x00\x00\x00\x43\x06\x00\x00\x00\x03\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x50\x53\x54\x52\x41\x58\x00\x00\x00\x00\x00\x43\x07\x00\x00\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4E\x5F\x44\x4F\x4B\x00\x00\x00\x00\x00\x00\x43\x0D\x00\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x41\x53\x00\x00\x00\x00\x00\x00\x00\x00\x00\x43\x0F\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x41\x41\x54\x50\x00\x00\x00\x00\x00\x00\x00\x43\x14\x00\x00\x00\x05\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x44\x45\x4E\x00\x00\x00\x00\x00\x00\x00\x00\x43\x16\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4D\x45\x53\x00\x00\x00\x00\x00\x00\x00\x00\x43\x18\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x47\x4F\x44\x00\x00\x00\x00\x00\x00\x00\x00\x43\x1A\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4B\x4F\x4C\x56\x4F\x00\x00\x00\x00\x00\x00\x4E\x1D\x00\x00\x00\x03\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4B\x4F\x4C\x56\x4F\x31\x00\x00\x00\x00\x00\x4E\x25\x00\x00\x00\x08\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x55\x4D\x4D\x41\x00\x00\x00\x00\x00\x00\x4E\x2D\x00\x00\x00\x08\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x4F\x53\x53\x54\x52\x41\x58\x00\x00\x00\x4E\x34\x00\x00\x00\x07\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x55\x4D\x4D\x41\x31\x00\x00\x00\x00\x00\x4E\x3B\x00\x00\x00\x07\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x4F\x53\x53\x54\x52\x41\x58\x31\x00\x00\x4E\x40\x00\x00\x00\x05\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4E\x42\x41\x47\x00\x00\x00\x00\x00\x00\x00\x4E\x42\x00\x00\x00\x03\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x58\x42\x41\x47\x00\x00\x00\x00\x00\x00\x00\x4E\x48\x00\x00\x00\x06\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x4B\x4F\x4C\x56\x4F\x41\x00\x00\x00\x00\x00\x4E\x4B\x00\x00\x00\x03\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x55\x4D\x4D\x41\x5F\x4B\x00\x00\x00\x00\x4E\x53\x00\x00\x00\x08\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x53\x55\x4D\x4D\x41\x5F\x50\x00\x00\x00\x00\x4E\x5B\x00\x00\x00\x08\x02\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x0d";
            $content .= $entries;

            /*
            // source format for 1C
            Name,Type,Size,Dec
            DEK,Character,2,0
            PACH,Character,3,0
            PSTRAX,Character,1,0
            N_DOK,Character,6,0
            AS,Character,2,0
            AATP,Character,5,0
            DEN,Character,2,0
            MES,Character,2,0
            GOD,Character,2,0
            KOLVO,Numeric,3,0
            KOLVO1,Numeric,8,2
            SUMMA,Numeric,8,2
            SOSSTRAX,Numeric,7,2
            SUMMA1,Numeric,7,2
            SOSSTRAX1,Numeric,5,2
            NBAG,Numeric,2,0
            XBAG,Numeric,6,2
            KOLVOA,Numeric,3,0
            SUMMA_K,Numeric,8,2
            SUMMA_P,Numeric,8,2
            */

            //$contents for example
            //$content .= sprintf(' %02s%03s%1s%6s%2s%5s%2s%2s%2s%3d%8.2d%8.2d%7.2d%7.2d%5.2d%2d%6.2d%3d%8.2d%8.2d',0,1,'','N_DOK','AS','AATP','15','3','15',0,0,333,0,0,0,0,0,0,0,0);
            //$content .= sprintf(' %02s%03s%1s%6s%2s%5s%2s%2s%2s%3d%8.2d%8.2d%7.2d%7.2d%5.2d%2d%6.2d%3d%8.2d%8.2d',0,2,'','N_DOK','AS','AATP','15','3','15',0,0,333,0,0,0,0,0,0,0,0);
            //$content .= sprintf(' %02s%03s%1s%6s%2s%5s%2s%2s%2s%3d%8.2d%8.2d%7.2d%7.2d%5.2d%2d%6.2d%3d%8.2d%8.2d',0,3,'','N_DOK','AS','AATP','15','3','15',0,0,333,0,0,0,0,0,0,0,0);
            //$content .= sprintf(' %02s%03s%1s%6s%2s%5s%2s%2s%2s%3d%8.2d%8.2d%7.2d%7.2d%5.2d%2d%6.2d%3d%8.2d%8.2d',0,4,'','N_DOK','AS','AATP','15','3','15',0,0,333,0,0,0,0,0,0,0,0);
//           print_r($response);

            /*
                        $r['dek'], $r['pach'], $r['pstrax'], $r['n_dok'], $r['as'], $r['aatp'], $r['den'], $r['mes'], $r['god'], $r['kolvo'], $r['kolvo1'], $r['summa'], $r['sosstrax'], $r['summa1'], $r['sosstrax1'], $r['nbag'], $r['xbag'], $r['kolvoa'], $r['summa_k'], $r['summa_s']

                        [dek] => DEK
                        [pach] => PACH
                        [pstrax] => PSTRAX
                        [n_dok] => N_DOK
                        [as] => 1306
                        [aatp] => 14
                        [den] => 11
                        [mes] => 03
                        [god] => 15
                        [kolvo] => 15
                        [kolvo1] => 0.00
                        [summa] => 526.05
                        [sosstrax] => SOSSTRAX
                        [summa1] => SUMMA1
                        [sosstrax1] => SOSSTRAX1
                        [nbag] => NBAG
                        [xbag] => XBAG
                        [kolvoa] => KOLVOA
                        [summa_k] => SUMMA_K
                        [summa_s] => SUMMA_S


DEK,Character,2,0   -- пока всегда «00»
PACH,Character,3,0     всегда «001»
PSTRAX,Character,1,0 - пусто
N_DOK,Character,6,0 - номер ведомости
AS,Character,2,0   - код автостанции (подол 22, …)
AATP,Character,5,0   код перевозчик с как в екселе (Клименко – 00714)
DEN,Character,2,0  день
MES,Character,2,0 месяц
GOD,Character,2,0 год
KOLVO,Numeric,3,0 количество билетов
KOLVO1,Numeric,8,2  0.00
SUMMA,Numeric,8,2полный тариф + страховой збор   "conv_tariff_with_benefits" + "stat_tariff_with_benefits" + "conv_tariff_with_benefits_vat" + "stat_tariff_with_benefits_vat"+ . insurer_tariff_with_benefits
SOSSTRAX,Numeric,7,2  страховой збор  . insurer_tariff_with_benefits
SUMMA1,Numeric,7,2  0.00
SOSSTRAX1,Numeric,5,2 код страховой из вайла екселевского что раньне прислал
NBAG,Numeric,2,0 колмч багажа   "luggage_count"
XBAG,Numeric,6,2  сумма багажа "luggage_total"
KOLVOA,Numeric,3,0  0
SUMMA_K,Numeric,8,2    тариф организации +ПДВ "stat_tariff_with_benefits" + "stat_tariff_with_benefits_vat"+
SUMMA_P,Numeric,8,2  тариф перевозчика с ПДВ  "conv_tariff_with_benefits" + "conv_tariff_with_benefits_vat"

            */




            //$response = $content;



            //$name = "1C_export_test";
            $warnings = ob_get_contents();
            if (!$warnings) {
                header('Content-type: application/x-dbase');
                header('Content-Disposition: inline; filename="' . $filename . '.dbf"');
                echo $content;
                return;
            }
            else {
                echo $warnings;
                return;
            }


        }
        $this->view->sTitle = "Звіт";

    }
    public function listAction(){
        $dbTicket = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/list/');
        $this->view->sTitle = 'Реестр відомостей';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";

            $this->view->dtb = $data['date_begin'];
            $this->view->dte = $data['date_end'];

            $dbDic = new Application_Model_DbTable_Dictionary();
            $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);
            $this->view->total1=0.00;
            $this->view->total2=0.00;
            $this->view->total3=0.00;
            $this->view->total4=0.00;
            $this->view->total5=0.00;
            $this->view->totalluggage=0;
            $this->view->totalplaces=0;
            $this->view->oSession   = Zend_Auth::getInstance()->getStorage()->read();

            $this->view->rs = $dbTicket->getTicketsGroupByDriverUID($DtS,$DtE,$this->ststionID);
            $item = $this->view->render('reports/items/printlist.phtml');
            $result = array();
            $result['success'] = true;
            $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
            $result['content'] = $item;
            $this->_helper->json->sendJson($result);
            return;

        }
    }

    public function failedracesAction(){
        $dbRaces   = new Application_Model_DbTable_Races();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/failedraces/');
        $this->view->sTitle = 'Звіт про зриви';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0];
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0];
            if(strtotime($DtS)<=strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbRaces->getFailRacesGroupByConv($DtS, $DtE, $this->ststionID);

                //trace($this->view->rs,1);
                $this->view->td_Local_sum=array();
                $this->view->td_Total_sum=array();
                $this->view->curConvoer=null;
                $this->view->td_failtype=array();
                $this->view->aRaces=array();
                foreach($this->view->rs as $r){
                    //trace($r);
                    if( !isset($this->view->aRaces[$r['code']])) $this->view->aRaces[$r['code']] = array("code"=>$r['code'],"atp"=>$r['title'],"conveyorid"=>$r['conveyorid']);
                    if($r['status']=='opened') {

                        if(!isset($this->view->td_failtype["H/3"]))
                            $this->view->td_failtype["H/3"] = 0;
                            $this->view->td_failtype["H/3"]+=$r['num'];

                        $this->view->aRaces[$r['code']]["H/3"]=$r['num'];
                    } else if($r['status']=='forbidden') {

                        if(!isset($this->view->td_failtype["Coгл."]))
                        $this->view->td_failtype["Coгл."]  =0;
                        $this->view->td_failtype["Coгл."]+=$r['num'];

                        $this->view->aRaces[$r['code']]["Coгл."]=$r['num'];
                    } else if($r['status']=='failed'){

                        if(!isset($this->view->td_failtype[$r['fail_type']]))
                        $this->view->td_failtype[$r['fail_type']]=0;
                        $this->view->td_failtype[$r['fail_type']]+=$r['num'];

                        $this->view->aRaces[$r['code']][$r['fail_type']]=$r['num'];
                    }
                }

                //trace($this->view->td_failtype,1);
                $item = $this->view->render('reports/items/printfailedraces.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                return;
            }else{
                $this->error->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;
            }

        }
    }

    public function benefitsticketsAction(){
        $dbTicket   = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/benefitstickets/');
        $this->view->sTitle = 'Звіт по пільговим квиткам';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";
            if(strtotime($DtS)<strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbTicket->getBenefitsTickets($DtS, $DtE, $this->ststionID);


                $this->view->totAll=0;
                $this->view->totStat=0;
                $this->view->totConv=0;
                $this->view->totInsh=0;
                $this->view->totTot=0;
                $this->view->totNum=0;
                $this->view->totStat=0;

                $this->view->m_benefits = "";
                $this->view->m_totAll = 0.0;
                $this->view->m_totStat = 0.0;
                $this->view->m_totConv = 0.0;
                $this->view->m_totInsh = 0.0;
                $this->view->m_totTot = 0.0;
                $this->view->m_totNum = 0;
                $this->view->m_totStat = 0.0;





                $item = $this->view->render('reports/items/printbenefitstickets.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;
            }

        }
    }


    public function allracesAction(){
        $dbTicket   = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/allraces/');
        $this->view->sTitle = 'Рейсів виконано';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0];
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0];
            if(strtotime($DtS)<=strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();
                $dbRaces = new Application_Model_DbTable_Races();
                $this->view->rs = $dbRaces->getAllRacesGroupByRate($DtS, $DtE, $this->ststionID);
                //trace($this->view->rs,1);


                $this->view->totAll=0;
                $this->view->totStat=0;
                $this->view->totConv=0;
                $this->view->totInsh=0;
                $this->view->totTot=0;
                $this->view->totNum=0;
                $this->view->totStat=0;

                $this->view->m_type="";
                $this->view->m_totAll = 0;
                $this->view->m_totStat = 0;
                $this->view->m_totConv = 0;
                $this->view->m_totInsh = 0;
                $this->view->m_totTot = 0;
                $this->view->m_totNum = 0;
                $this->view->m_totStat = 0;





                $item = $this->view->render('reports/items/printallraces.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;
            }

        }
    }



    public function minusticketsAction(){
        $dbTicket   = new Application_Model_DbTable_Tickets();
        $this->view->form = new Application_Form_Period();
        $this->view->form->setAction('/reports/minustickets/');
        $this->view->sTitle = 'Недоотримання';

        if($this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();

            $data = $this->getRequest()->getPost();
            if (empty($data['date_begin'])) {
                $this->_helper->json->sendJson(array("error" => "Початкову дату не вказано."));
                return;
            }

            if (empty($data['date_end'])) {
                $this->_helper->json->sendJson(array("error" => "Кінцеву дату  не вказано."));
                return;
            }
            mb_internal_encoding("UTF-8");
            $DtS = explode(".",$data['date_begin']);
            $DtS = $DtS[2]."-".$DtS[1]."-".$DtS[0]." 00:00:00";
            $DtE = explode(".",$data['date_end']);
            $DtE = $DtE[2]."-".$DtE[1]."-".$DtE[0]." 23:59:59";
            if(strtotime($DtS)<strtotime($DtE)) {
                $this->view->dtb = $data['date_begin'];
                $this->view->dte = $data['date_end'];

                $dbDic = new Application_Model_DbTable_Dictionary();
                $this->view->station = $dbDic->fetchRow("id=" . (int)$this->ststionID);

                $this->view->oSession = Zend_Auth::getInstance()->getStorage()->read();

                $this->view->rs = $dbTicket->getMinusBenefitsTickets($DtS, $DtE, $this->ststionID);


                $this->view->totAll=0;
                $this->view->totStat=0;
                $this->view->totConv=0;
                $this->view->totInsh=0;
                $this->view->totTot=0;
                $this->view->totNum=0;
                $this->view->totStat=0;

                $this->view->m_type="";
                $this->view->m_totAll = 0.0;
                $this->view->m_totStat = 0.0;
                $this->view->m_totConv = 0.0;
                $this->view->m_totInsh = 0.0;
                $this->view->m_totTot = 0.0;
                $this->view->m_totNum = 0;
                $this->view->m_totStat = 0.0;





                $item = $this->view->render('reports/items/printminustickets.phtml');
                $result = array();
                $result['success'] = true;
                $item = "<input type=\"button\" onclick=\"this.style.display='none';print();window.close();\" value=\"Друк\">" . $item;
                $result['content'] = $item;
                $this->_helper->json->sendJson($result);
                exit;
            }else{
                $this->_helper->json->sendJson(array("error" => "Дата початку більша або рівна до дати закінчення."));
                return;
            }

        }
    }



}