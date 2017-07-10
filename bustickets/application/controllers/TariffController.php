<?php
class TariffController extends Zend_Controller_Action
{

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
            ->addActionContext('editMatrix', array('json'))
            ->initContext();

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
        $db = new Application_Model_DbTable_Dictionary();
        $this->view->sTitle = Application_Model_Dictionary::getName(Application_Model_Dictionary::TARIFFMATRIX);
        $this->view->diclist = $db->selectDataByType(Application_Model_Dictionary::TARIFFMATRIX,0);


    }
    public function refreshAction(){
        $formData = $this->getRequest()->getParams();
        $result = array();
        $this->indexAction();
        $result['success']  = true;
        $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
        $result['content']  = $this->view->render('tariff/index.phtml');
        $this->_helper->json->sendJson($result);
    }
    public function editAction(){
        $data = $this->getRequest()->getPost();
        $data['type'] = Application_Model_Dictionary::TARIFFMATRIX;
        $data['par_id']=0;
        $this->view->form = new Application_Form_Dictinary();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="";
        $this->view->form->getElement('type')->setValue($data['type']);
        $this->view->title.= "\"".Application_Model_Dictionary::getName($data['type'])."\" ";

        $db = new Application_Model_DbTable_Dictionary();
        if(!empty($data['id'])){
                $r = $db->fetchRow("id=".(int)$data['id']." AND \"type\"='".addslashes($data['type'])."' AND par_id=0");
                if(!$r) {
                    $result['error'] = "Елемент не знайдено";
                    $this->_helper->json->sendJson($result);
                    return;
                }
                $this->view->title.= "\"".$r->title."\" - редагування " ;
                $this->view->form->populate($r->toArray());
        }


        if(isset($data['needstore'])){
                //we have data from form
                $this->view->form->getElement('title')
                    ->getValidator('Db_NoRecordExists')
                    ->setExclude("\"type\"='".addslashes($data['type'])."' AND id<>".(int)$data['id']   );

                if($this->view->form->isValid($data)){
                    $data['id'] = $db->setData($data);
                    if(!is_numeric($data['id']) || $data['id']==0 ){
                        $this->view->message = $data['title']." - не збережено, зверніться до адміністратора системи";
                        $this->view->result = "error";
                    } else {
                        $this->view->message  =  $data['title']." - збережено";
                        $this->view->result = "success";
                        $this->view->form->populate($data);
                        $this->view->title.= "\"".$data['title']."\" - редагування " ;
                        $result['callback'] = "refresh(".$data['id'].")";
                    }

                }else{
                    $this->view->message="Помилка заповнення";
                    $this->view->result = "error";

                }
        }
        $this->view->title.= (empty($data['id'])) ? " - новий ":"";
        $result['success'] = true;
        $result['content'] = $this->view->render('tariff/edit.phtml');
        $this->_helper->json->sendJson($result);

    }
    public function deleteAction(){

        $formData = $this->getRequest()->getParams();
        $formData['type']=Application_Model_Dictionary::TARIFFMATRIX;
        $result = array();
        if(empty($formData['delete']) || !is_numeric($formData['delete']))  $result['error'] = "Не обрано елемент довідника";
        else {

                $db = new Application_Model_DbTable_Dictionary();
                $r = $db->fetchRow("id=" . (int)$formData['delete'] . " AND \"type\"='" . addslashes($formData['type'])."'" );
                $title = ($r) ? $r->title : "елемент";
                $dbMx = new Application_Model_DbTable_TariffMatrix();
                $rs = $dbMx->fetchRow('"type"='.$formData['delete']);
                if (!$rs) {
                    $result['error'] = "\"" . $title . "\" - не видалено, зверніться до адміністратора.";
                    if ($db->delete("id=" . (int)$formData['delete'] . " AND \"type\"='" . addslashes($formData['type'])."'" )) {
                        $result['success'] = true;
                    }
                } else {
                    $result['error'] = "На \"" . $title . "\" довідника присутні посилання, спочатку видаліть їх.";
                }


        }
        $this->_helper->json->sendJson($result);
    }

    public function editMatrixAction(){
        $data = $this->getRequest()->getPost();
        if(empty($data['tariffID'])) {
            $result['error'] = "Тариф не обрано";
            $this->_helper->json->sendJson($result);
            return;
        }
        $db = new Application_Model_DbTable_Dictionary();
        $r = $db->fetchRow("id=".(int)$data['tariffID']." AND \"type\"='".addslashes(Application_Model_Dictionary::TARIFFMATRIX)."' AND par_id=0");
        if(!$r) {
            $result['error'] = "Елемент не знайдено";
            $this->_helper->json->sendJson($result);
            return;
        }
        $this->view->title   .= "\"".$r->title."\" - тарифна сітка " ;
        $this->view->tariffID.= $r->id ;
        $dbMx = new Application_Model_DbTable_TariffMatrix();
        //it here need put needstore
        if(!empty($data['needstore']) && count($data['begin'])==count($data['end'])&& count($data['end'])==count($data['step'])){
            $dbMx->delete('"type"='.$this->view->tariffID);
            for($i=0;$i<count($data['begin']);$i++) {
                $toSave = array('type' => $this->view->tariffID, 'begin' => $data['begin'][$i], 'end' => $data['end'][$i], 'step' => $data['step'][$i]);
                $dbMx->insert($toSave);
            }
            $this->view->message  = "Сітку \"".$this->view->title."\" - збережено";
        }

        $rs = $dbMx->fetchAll('"type"='.$this->view->tariffID,"id ASC");
        $this->view->jReserve = (($rs)? json_encode($rs->toArray()) : "{}");

        //$htmlpart="";
        //$this->view->htmlpart.=$this->view->render('tariff/items/dinamicly.phtml').$htmlpart;
        //trace($htmlpart,1);
        $result['success'] = true;
        $result['content'] = $this->view->render('tariff/editmatrix.phtml');
        //echo "FORM:".$this->view->form;
        //trace($result);

        $this->_helper->json->sendJson($result);
    }
}