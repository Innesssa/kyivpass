<?php
class BenefitsController extends Zend_Controller_Action
{

    public function init()
    {

        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
        $this->view->headScript()
            ->appendFile('/js/controllers/'.$this->getRequest()->getControllerName().'.js');
        $this->view->headLink()
            ->appendStylesheet('/js/libs/tablesorter/themes/blue/style.css');


        $this->view->sActionName    =   "index";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();

        $this->_helper->contextSwitch()
            ->addActionContext('edit', array('json'))
            ->addActionContext('refresh', array('json'))
            ->addActionContext('delete', array('json'))
            ->addActionContext('search', array('json'))
            ->initContext();

    }
    public function preDispatch()
    {

    }

    public function indexAction(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
        $formData = $this->getRequest()->getParams();
        $db = new Application_Model_DbTable_Benefitslist();

        $this->view->sTitle = "Перелік пільг";
        $this->view->diclist = $db->getAll();
        //$this->view->diclist = $db->fetchAll(null,"title_short ASC");
        //trace($this->view->diclist);
        //trace($this->view->diclist->toArray());
    }

    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
        $formData = $this->getRequest()->getParams();

        $result = array();
        $db = new Application_Model_DbTable_Benefitslist();
        $this->view->sTitle = "Перелік пільг";
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
        $result['content']  = $this->view->render('benefits/index.phtml');
        $this->_helper->json->sendJson($result);
    }
    public function editAction(){
        $data = $this->getRequest()->getPost();
        $this->view->form = new Application_Form_DictinaryBenefits();
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
    public function deleteAction(){

        $formData = $this->getRequest()->getParams();

        $result = array();
        if(empty($formData['delete']) || !is_numeric($formData['delete']))  $result['error'] = "Не обрано елемент довідника";
        else {

                $db = new Application_Model_DbTable_Benefitslist();
                $r = $db->fetchRow("id=" . (int)$formData['delete'] );
                $title = ($r) ? $r->title : "елемент";
                $rs = $db->selectChildsByID($formData['delete']);
                if (!$rs || count($rs) == 0) {
                    $result['error'] = "\"" . $title . "\" - не видалено, зверніться до адміністратора.";
                    if ($db->delete("id=" . (int)$formData['delete'])) {
                        $result['success'] = true;
                    }
                } else {
                    $result['error'] = "На \"" . $title . "\" довідника присутні посилання, спочатку видаліть їх.";
                }


        }
        $this->_helper->json->sendJson($result);
    }


}