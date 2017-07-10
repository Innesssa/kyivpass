<?php

class Application_Form_Role2Permission extends Zend_Form
{

    public function init()
    {
        $this->setName('role2permission');

        $elm = new Zend_Form_Element_Hidden('idr');
        $elm->addFilter('int');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано роль')));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('permissionsfrom');
        $elm->setLabel('Доступи : ');
        $db = new Application_Model_DbTable_Permissions();
        $res = $db->getAll();
        if($res) foreach($res as $r) $elm->addMultiOption($r->id,$r->title);
        $elm->setAttrib("class", "select box from");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('permissionsto');
        $elm->setRequired(true)
            ->setLabel('Назначені ролі доступу : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано доступи')));
        $elm->setAttrib("class", "select box to");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

