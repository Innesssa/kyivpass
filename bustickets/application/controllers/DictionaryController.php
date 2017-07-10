<?php
class DictionaryController extends Zend_Controller_Action
{
    private function _getBreadCrums($par_id){
        $this->view->breadcrums = array();
        $db = new Application_Model_DbTable_Dictionary();
        $aNav = $db->getPath($par_id);
        $breadCrums = array();
        for($i=count($aNav)-1;$i>-1;$i--){
            $breadCrums[]=array (
                'uri'	=> '/dictionary/index/par_id/'.$aNav[$i]['par_id'].'/type/'.$aNav[$i]['type'],
                'label'         => $aNav[$i]['title']
            );
        }
        $this->view->breadcrums = $breadCrums; //new Zend_Navigation($breadCrums);
    }
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
            ->addActionContext('search', array('json'))
            ->initContext();

    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }


    public function reservesAction(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
        $formData = $this->getRequest()->getParams();
        $formData['type']=Application_Model_Dictionary::RESERVES;
        $db = new Application_Model_DbTable_Dictionary();
        $this->view->dictype = $formData['type'];
        $this->view->sTitle = Application_Model_Dictionary::getName($formData['type']);
        $this->view->diclist = $db->selectDataByType($formData['type'],0);
    }

    public function indexAction(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
            $formData = $this->getRequest()->getParams();
            //exit ( Application_Model_Dictionary::isValid( $formData['type'] ) ? "present" : "not present");
            if( !empty( $formData['type'] ) && Application_Model_Dictionary::isValid( $formData['type'] ) ) {
                $db = new Application_Model_DbTable_Dictionary();
                $par_id =  !empty( $formData['par_id'] ) ? $formData['par_id'] : 0;
                $this->_getBreadCrums($par_id );

                if(!empty( $formData['id'] )){
                    $r = $db->fetchRow("id=".(int)$formData['id']." AND type='".$formData['type']."'");
                    if(!$r) {
                        $this->view->dicname = "Помилка";
                        $this->view->sErrorMessage = "Не вказано довідник";
                        return;
                    }
                    $par_id = $r->par_id;
                }
                $this->view->par_id = $par_id;
                $this->view->dictype = $formData['type'];
                $this->view->sTitle = Application_Model_Dictionary::getName($formData['type']);
                $this->view->diclist = $db->selectDataByType($formData['type'],$par_id);
                //$this->view->form = new Application_Form_Dictinary();
            }else{
                $this->view->dicname = "Помилка";
                $this->view->sErrorMessage = "Не вказано довідник";
            }

    }
    public function refreshAction(){
        //$this->_helper->viewRenderer->setRender('index');
        $formData = $this->getRequest()->getParams();

        $result = array();
        if( !empty( $formData['type'] ) && Application_Model_Dictionary::isValid( $formData['type'])  ) {
            $db = new Application_Model_DbTable_Dictionary();
            $this->view->dictype = $formData['type'];
            $this->view->sTitle = Application_Model_Dictionary::getName($formData['type']);
            $this->view->diclist = $db->selectDataByType($formData['type']);
            $par_id =  !empty( $formData['par_id'] ) ? $formData['par_id'] : 0;
            if(!empty( $formData['id'] )){
                $r = $db->fetchRow("id=".(int)$formData['id']." AND type='".$formData['type']."'");
                if(!$r) {
                    $result['error'] = "Не вказано довідник";
                    $this->_helper->json->sendJson($result);
                    return;
                }
                $par_id = $r->par_id;
            }
            $result['success']  = true;
            $result['id']       = (isset($formData['id'])) ? $formData['id'] : "0";
            $result['type']     = $formData['type'];
            $result['par_id']   = $par_id;
            if     ($formData['type']==Application_Model_Dictionary::RESERVES) $result['content']  = $this->view->render('dictionary/' . Application_Model_Dictionary::RESERVES . '.phtml');
            else $result['content']  = $this->view->render('dictionary/index.phtml');

        }else{
            $result['error'] = "Не обрано довідник";
        }
        $this->_helper->json->sendJson($result);
    }
    public function editAction(){
        $data = $this->getRequest()->getPost();
        if     ($data['type']==Application_Model_Dictionary::STATIONRATE) $this->view->form = new Application_Form_DictinaryStationRate();
        else if     ($data['type']==Application_Model_Dictionary::RESERVES) $this->view->form = new Application_Form_DictinaryReserves();
        else $this->view->form = new Application_Form_Dictinary();
        $this->view->form->setAction("/".$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/");
        $result = array();
        $this->view->title="";

        if(!isset($data['type'])){
            $result['error'] = "Не обрано довідник";
            $this->_helper->json->sendJson($result);
            return;
        }

        //$type = strtoupper($data['type']);
        //trace($data);
        if(!Application_Model_Dictionary::isValid( $data['type'] ) ){
            $result['error'] = "Не обрано довідник";
            $this->_helper->json->sendJson($result);
            return;
        }



        $this->view->form->getElement('type')->setValue($data['type']);
        $this->view->title.= "\"".Application_Model_Dictionary::getName($data['type'])."\" ";



        $par_id = (empty($data['par_id'])) ? 0 : $data['par_id'];
        if(!empty($data['par_id'])) $this->view->form->getElement('par_id')->setValue($data['par_id']);
        $data['par_id']=$par_id;
        $this->_getBreadCrums($par_id );

        $db = new Application_Model_DbTable_Dictionary();
        if(!empty($data['id'])){
                $r = $db->fetchRow("id=".(int)$data['id']." AND \"type\"='".addslashes($data['type'])."' AND par_id=".(int)$par_id);
                if(!$r) {
                    $result['error'] = "Елемент довідника не знайдено";
                    $this->_helper->json->sendJson($result);
                    return;
                }
                $this->view->title.= "\"".$r->title."\" - редагування " ;
                $this->view->form->populate($r->toArray());
                if     ($data['type']==Application_Model_Dictionary::STATIONRATE ) {
                    if(!empty($r->additional_text)) $this->view->form->getElement('additional_text')->removeMultiOption('empty');
                    else $this->view->form->getElement('additional_text')->setValue('empty');
                }



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
                        if($data['type']==Application_Model_Dictionary::STATIONRATE   )  $this->view->form->getElement('additional_text')->removeMultiOption('empty');

                            $this->view->title.= "\"".$data['title']."\" - редагування " ;
                        $result['callback'] = "refresh('".$data['type']."',".$data['id'].",".$data["par_id"].")";
                    }

                }else{
                    $this->view->message="Помилка заповнення";
                    $this->view->result = "error";

                }
        }



        $this->view->title.= (empty($data['id'])) ? " - новий ":"";
        $result['success'] = true;
        $result['content'] = $this->view->render('dictionary/edit.phtml');
        $this->_helper->json->sendJson($result);

    }
    public function deleteAction(){

        $formData = $this->getRequest()->getParams();

        $result = array();
        if(empty($formData['delete']) || !is_numeric($formData['delete']))  $result['error'] = "Не обрано елемент довідника";
        else {
            if (!empty($formData['type']) && Application_Model_Dictionary::isValid( $formData['type'] ) ) {
                $db = new Application_Model_DbTable_Dictionary();
                $r = $db->fetchRow("id=" . (int)$formData['delete'] . " AND \"type\"='" . addslashes($formData['type']) . "' AND par_id=". (int)$formData['par_id']);
                $title = ($r) ? $r->title : "елемент";
                $rs = $db->selectChildsByID($formData['delete']);
                if (!$rs || count($rs) == 0) {
                    $result['error'] = "\"" . $title . "\" - не видалено, зверніться до адміністратора.";
                    if ($db->delete("id=" . (int)$formData['delete'] . " AND \"type\"='" . addslashes($formData['type']) . "'AND par_id=". (int)$formData['par_id'])) {
                        $result['success'] = true;
                    }
                } else {
                    $result['error'] = "На \"" . $title . "\" довідника присутні посилання, спочатку видаліть їх.";
                }

            } else {
                $result['error'] = "Не обрано довідник";
            }
        }
        $this->_helper->json->sendJson($result);
    }

    public function searchAction(){
        $q    = trim($this->_getParam('kwd'));
        $view = trim($this->_getParam('type'));
        $result = array();
         if (strlen($q)>0 && !empty($view)  ) {
             $db = new Application_Model_DbTable_Dictionary();
             try {
                 $rs = $db->searchItems($q, $view);
                 foreach ($rs as $row) $result[] = array('name' => $row['title'], 'id' => $row['id']);
             }catch(Exception $e){

             }
         }
        $this->_helper->json->sendJson($result);
    }
}