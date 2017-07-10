<?php
class PointsController extends Zend_Controller_Action
{
    public function init()
    {


        $this->view->headScript()
            ->appendFile('/js/libs/jquery-ui.js');
        $this->view->headScript()
            ->appendFile('/js/controllers/'.$this->getRequest()->getControllerName().'.js');
        $this->view->headLink()
            ->appendStylesheet('/css/custom-theme/jquery-ui-tab.css');

        $this->view->sActionName    =   "index";
        $this->view->sControllerName=   $this->getRequest()->getControllerName();



        $this->_helper->contextSwitch()
            ->addActionContext('filter', array('json'))
            ->initContext();

    }

    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }

    public function indexAction(){
        $this->view->form = new Application_Form_Points();
        //$this->view->form = new Application_Form_Organisation();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $this->getRequest()->isPost()) {
            $this->_helper->getHelper('layout')->disableLayout();
        }
        $formData = $this->getRequest()->getParams();
        $this->view->sTitle = "Автостанції";

    }

    function filterAction(){
        //find routes


    }



}