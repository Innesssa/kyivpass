<?php
class KassaController extends Zend_Controller_Action
{
    public function init()
    {

        $this->view->target_URL_path = "/uimgs/";
        $this->view->target_path = realpath(PUBLIC_PATH . "/uimgs/") . "/";
        $this->aExt = array("png", "gif", "jpeg", "jpg");

        $this->_helper->contextSwitch()
            ->addActionContext('imhere', array('json'))
            ->initContext();
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

        $this->view->sTitle = "Касові операції";
        $this->view->sActionName    =   "kassa";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();
        $this->_oSession = Zend_Auth::getInstance()->getStorage()->read();

        $this->_helper->contextSwitch()
            ->addActionContext('saveOperation', array('json'));



    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }
    public function indexAction(){

       $this->view->kassaname = $this->_oSession->login;
       $this->view->form = new Application_Form_PayService();
       $dbServ = new Application_Model_DbTable_ServicesList();
       $aService = $dbServ->fetchAll(null,"title");
       if($aService) $aService=$aService->toArray();
       $aService['empty']=array("id"=>"empty","title"=>"Оберіть необхідну послугу");
       $this->view->services = json_encode($aService);
       $oSession = Zend_Auth::getInstance()->getStorage()->read();
       $this->view->lastCloseDay = $dbServ->getLastCloseDay(date("Y-m-d 00:00:00"),$oSession->id);
       $this->view->lastOpenDay  = $dbServ->getLastOpenDay(date("Y-m-d 00:00:00"),$oSession->id);





    }
    public function saveOperationAction(){
        $data = $this->getRequest()->getPost();
        $oSession = Zend_Auth::getInstance()->getStorage()->read();
        $data['kassauid']=$oSession->id;
        if(!empty($data['user'])) $data['usr']=$data['user'];
        if(!empty($data['date'])) $data['dt']=$data['date'];
        //if(!empty($data['id'])) { $data['serviceid']=$data['serviceid']; unset($data['serviceid']); }
        if(empty($data['serviceid'])) $data['serviceid']=0;
        $data['serviceid']=(int)$data['serviceid'];
        if(!empty($data['count'])) $data['num']=(int)$data['count'];
        if(empty($data['price'])) $data['price']=0;
        if(empty($data['num'])) $data['num']=0;
        if(empty($data['amount'])) $data['amount']=0;
        if(empty($data['vat'])) $data['vat']=0;
/*
        {   "success"|"error":true,
            "message":"<text>",
            "operation":"<operation name>",
            "amount":"<float>",
            "date":"YYYY-MM-DD HH:II:SS",
            "user":"0348",
            "ppo":"1234567891",
            "checknumber":
            "id":
            "price":
            "vat"
            "count"
            "description"
        }
*/
        $dbKSSA = new Application_Model_DbTable_Kassa();
        $result = $dbKSSA->setData($data);
        if($result['success']) {
            $this->_helper->json->sendJson(array("success" => true));
        }   else {
            $this->_helper->json->sendJson($result['message']);
        }
        return;
    }

    public function storeAction(){
        $this->_helper->json->sendJson('ok');
        exit;
    }





}