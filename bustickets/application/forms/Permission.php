<?php

class Application_Form_Permissions extends Zend_Form
{

    public function init()
    {
        $this->setName('permissions');

        $elm = new Zend_Form_Element_Hidden('id');
        $elm->addFilter('int');
        $elm->setValue(0);
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Multiselect('id_c2afrom');
        $elm->setLabel('Модуль : ')
            ->addFilter('int');
        $elm->setAttrib("class", "select box from");

        $ml = new Application_Model_Permissions();
        $res = $ml->getAllLinked();
        if($res) foreach($res as $r)
        $elm->addMultiOption($r->pid,$r->ctitle."::".$r->atitle);
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Multiselect('id_c2a');
        $elm->setRequired(true)
            ->setLabel('Модуль(обраний) : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинні бути обрані модулі')));
        $elm->setAttrib("class", "select box to");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('access');
        $elm->setRequired(true)
            ->setLabel('Тип доступу : ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано тип доступу')));
        $elm->addMultiOption("read",'читання');
        $elm->addMultiOption("write",'редагування і читання');
        $elm->setAttrib("class", "select short");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Textarea('description');
        $elm->setLabel('Примітка :')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "textbox middle");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

