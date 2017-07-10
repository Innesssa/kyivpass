<?php
class UsersController extends Zend_Controller_Action
{

    protected $_db;
    public function init()
    {

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
        $this->_db = new Application_Model_DbTable_UserLoginData();


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
        $this->view->sTitle = "Користувачі";
        $this->view->aFilter = json_encode(array(
            "sort"   => (!empty($formData['sort']) ? $formData['sort'] : ''),
            "order"  => (!empty($formData['order']) ? $formData['order'] : ''),
            "status" => (!empty($formData['status']) ? $formData['status'] : '')
        ));

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partials/paginator.phtml');
        $paginator = $this->_db->getUsersList($this->getRequest()->getParams());
        //trace($paginator,1);
        //$paginator->setView($this->view);
        $this->view->paginator = $paginator;
        //trace($this->_db->getUserByLogin("admin","qwerty"));

    }

    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
            $formData = $this->getRequest()->getParams();
            $this->indexAction();
            $result['success']  = true;
            $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
            $result['content']  = $this->view->render('users/index.phtml');
            $this->_helper->json->sendJson($result);
    }
    public function editAction(){
        $this->view->form = new Application_Form_User();
        $data = $this->getRequest()->getPost();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="Новий користувач";


        if(!empty($data['id'])){
            $r = $this->_db->getUserByID((int)$data['id']);
            if(!$r) {
                $result['error'] = "Користувача не знайдено";
                $this->_helper->json->sendJson($result);
                return;
            }
            //trace($r,1);
            $this->view->title= "\"".$r['firstname']." ".$r['middlename']." ".$r['lastname'] ."\" - редагування " ;
            $this->view->form->populate($r);
            //trace($r->type);
        }


        if(isset($data['needstore'])){
            //we have data from form
            $this->view->form->getElement('login')
                ->getValidator('Db_NoRecordExists')
                ->setExclude( "id<>".(int)$data['id'] );

            if(!empty($data['id']) && $data['password']=='' && $data['cpassword']==''){
                $this->view->form->removeElement('password');
                $this->view->form->removeElement('cpassword');
                unset($data['password']);
                unset($data['cpassword']);
            }

            if($this->view->form->isValid($data)){

                $data['id'] = $this->_db->setData($data);
                if(!is_numeric($data['id']) || $data['id']==0 ){
                    $this->view->message = $data['login']." - не збережено, зверніться до адміністратора системи";
                    $this->view->result = "error";
                } else {

                    $this->view->message  =  $data['login']." - збережено";
                    $this->view->result = "success";
                    $this->view->form->populate($data);
                    $this->view->title= "\"".$data['login']."\" - редагування " ;
                    $result['callback'] = "refresh(".$data['id'].")";
                }

            }else{
                $this->view->message="Помилка заповнення";
                $this->view->result = "error";

            }
        }
        $result['success'] = true;
        $result['content'] = $this->view->render('users/edit.phtml');
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

}