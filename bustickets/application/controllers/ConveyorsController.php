<?php
class ConveyorsController extends Zend_Controller_Action
{

    protected $_db;
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
            ->initContext();
        $this->_db = new Application_Model_DbTable_OrganizationList();


    }


    public function indexAction(){
        //$this->view->form = new Application_Form_Organisation();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }

        $formData = $this->getRequest()->getParams();
        $this->view->sTitle = "Перевізники";
        $this->view->aFilter = json_encode(array(
            "sort"   => (!empty($formData['sort']) ? $formData['sort'] : ''),
            "order"  => (!empty($formData['order']) ? $formData['order'] : ''),
            "status" => (!empty($formData['status']) ? $formData['status'] : '')
        ));

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partials/paginator.phtml');
        $paginator = $this->_db->getConveyorsList($this->getRequest()->getParams());
        $paginator->setView($this->view);
        $this->view->paginator = $paginator;
    }

    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
            $formData = $this->getRequest()->getParams();
            $this->indexAction();
            $result['success']  = true;
            $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
            $result['content']  = $this->view->render('conveyors/index.phtml');
            $this->_helper->json->sendJson($result);
    }
    public function editAction(){
        $this->view->form = new Application_Form_Conveyor();
        $data = $this->getRequest()->getPost();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="Новий перевізник";


        if(!empty($data['id'])){
            $r = $this->_db->getConveyor((int)$data['id']); //$this->_db->fetchRow("id=".(int)$data['id']);
            if(!$r) {
                $result['error'] = "Елемент довідника не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            $this->view->title= "\"".$r['title']."\" - редагування " ;
            $r['benefits'] = @json_decode($r['benefits'],true);
            $this->view->form->populate($r);
            $this->view->form->getElement('type')->setValue($r['type']);
        }


        if(isset($data['needstore'])){
            //we have data from form
            $this->view->form->getElement('title')
                ->getValidator('Db_NoRecordExists')
                ->setExclude( "id<>".(int)$data['id'] );

            $this->view->form->getElement('code')
                ->getValidator('Db_NoRecordExists')
                ->setExclude( "id<>".(int)$data['id'] );

            if($this->view->form->isValid($data)){
                $data['id'] = $this->_db->setData($data);
                if(!is_numeric($data['id']) || $data['id']==0 ){
                    $this->view->message = $data['title']." - не збережено, зверніться до адміністратора системи";
                    $this->view->result = "error";
                } else {

                    $this->view->message  =  $data['title']." - збережено";
                    $this->view->result = "success";
                    $this->view->form->populate($data);
                    $this->view->title= "\"".$data['title']."\" - редагування " ;
                    $result['callback'] = "refresh(".$data['id'].")";
                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";

            }
        }
        $result['success'] = true;
        $result['content'] = $this->view->render('conveyors/edit.phtml');
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
                    $result['error'] = "На перевізника \"" . $r->title . "\"  присутні посилання, спочатку видаліть їх.";
                }

        }
        $this->_helper->json->sendJson($result);
    }

}