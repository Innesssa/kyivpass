<?php
class ServiceslistController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->headScript()
            ->appendFile('/js/controllers/'.$this->getRequest()->getControllerName().'.js');
        $this->view->headLink()
            ->appendStylesheet('/js/libs/tablesorter/themes/blue/style.css');


        $this->view->sActionName    =   "index";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();

        //$db = new Application_Model_DbTable_ServicesList();

        /*
        $this->_helper->contextSwitch()
            ->addActionContext('edit', array('json'))
            ->addActionContext('refresh', array('json'))
            ->addActionContext('delete', array('json'))
            ->addActionContext('search', array('json'))
            ->initContext();
        */
    }


    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }


    public function indexAction(){

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
        $formData = $this->getRequest()->getParams();
        $formData = $this->getRequest()->getParams();

        $db = new Application_Model_DbTable_ServicesList();
        $this->view->sTitle = "Перелік послуг";
        $this->view->diclist = $db->getServicesList();
        //$this->view->diclist = $db->fetchAll(null,"title_short ASC");
        //trace($this->view->diclist);
        //trace($this->view->diclist->toArray());

    }



    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
        $formData = $this->getRequest()->getParams();

        $result = array();
        $db = new Application_Model_DbTable_ServicesList();
        $this->view->sTitle = "Перелік послуг";
        $this->view->diclist = $db->fetchAll(null,"title_short ASC");
        if(!empty( $formData['id'] )){
            $r = $db->fetchRow("id=".(int)$formData['id']);
            if(!$r) {
                $result['error'] = "Запис не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }


        }
        $result['success']  = true;
        $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
        $result['content']  = $this->view->render('serviceslist/index.phtml');
        $this->_helper->json->sendJson($result);
    }




    public function editAction(){
        $data = $this->getRequest()->getPost();
        $this->view->form = new Application_Form_DictionaryServicesList();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="";
        $this->view->title.= "Пільги ";
        $db = new Application_Model_DbTable_Benefitslist();
        if(!empty($data['id'])){
            $r = $db->fetchRow("id=".(int)$data['id']);
            if(!$r) {
                $result['error'] = "Елемент не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $this->view->title.= "\"".$r->title_full."\" - редагування " ;
            $this->view->form->populate($r->toArray());
        }


        if(isset($data['needstore'])){
            //we have data from form

            $this->view->form->getElement('title_short')
                ->getValidator('Db_NoRecordExists')
                ->setExclude("id <> '" . (int)$data['id'] . "'");

            $this->view->form->getElement('title_print')
                ->getValidator('Db_NoRecordExists')
                ->setExclude("id <> '" . (int)$data['id'] . "'");

            $this->view->form->getElement('title_full')
                ->getValidator('Db_NoRecordExists')
                ->setExclude("id <> '" . (int)$data['id'] . "'");

            if($this->view->form->isValid($data)){


                $data['id'] = $db->setData($data);
                if(!is_numeric($data['id']) || $data['id']==0 ){
                    $this->view->message = $data['title_short']." - не збережено, зверніться до адміністратора системи";
                    $this->view->result = "error";
                } else {
                    $this->view->message  =  $data['title_short']." - збережено";
                    $this->view->result = "success";
                    $this->view->form->populate($data);
                    $this->view->title.= "\"".$data['title_short']."\" - редагування " ;
                    $result['callback'] = "refresh('".$data['id']."')";
                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";

            }
        }


        $this->view->title.= (empty($data['id'])) ? " - новий ":"";
        $result['success'] = true;
        $result['content'] = $this->view->render('benefits/edit.phtml');
        $this->_helper->json->sendJson($result);

    }



    /*
        public function saveOperationAction(){
            $data = $this->getRequest()->getPost();
            $oSession = Zend_Auth::getInstance()->getStorage()->read();
            $data['kassauid']=$oSession->id;
            if(!empty($data['user'])) $data['usr']=$data['user'];
            if(!empty($data['data'])) $data['dt']=$data['date'];
            if(!empty($data['id'])) { $data['serviceid']=$data['id']; unset($data['id']); }
            if(!empty($data['count'])) $data['num']=$data['count'];
    */
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
/*        $dbKSSA = new Application_Model_Dbtable_Kassa();
        if($dbKSSA->setValue($data))  $this->_helper->json->sendJson(array("success"=>true));
        else $this->_helper->json->sendJson(array("error"=>"Данні не записано"));
        return;
    }
*/




}