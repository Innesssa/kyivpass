<?php

class Application_Form_UserService extends Zend_Form
{

    public function init()
    {
        $db = new Application_Model_DbTable_Dictionary();
        $this->setName('UserServiceData');

        $elm = new Zend_Form_Element_Hidden('id');
        $elm->addFilter('int');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано користувача')));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('persnum');
        $elm->setRequired(true)
            ->setLabel('Табельний номер : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано табельний номер')));
        $elm->setAttrib("class", "edit middle");
        $elm->setAttrib("placeholder", "Табельний номер");
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('company');
        $elm->setLabel('Організація : ')
            ->addFilter('int');
        $elm->addMultiOption(0,'виберіть організацію');
        try {
            $res = $db->selectDataByType(Application_Model_Dictionary::ORGANIZATION);
            if($res) foreach($res as $r){
                $elm->addMultiOption($r->id,$r->title);
            }
        }catch(Exception $e){

        }
        $elm->setAttrib("class", "select middle");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('position');
        $elm->setRequired(true)
            ->setLabel('Посада : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано посаду')));
        $elm->addMultiOption("",'виберіть посаду');
        try {
            $res = $db->selectDataByType(Application_Model_Dictionary::POSITION);
            if($res) foreach($res as $r){
                $elm->addMultiOption($r->id,$r->title);
            }
        }catch(Exception $e){

        }
        $elm->setAttrib("class", "select middle");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('dept');
        $elm->setRequired(true)
            ->setLabel('Відділ : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано відділ')));
        $elm->addMultiOption("",'виберіть відділ');
        try {
            $res = $db->selectDataByType(Application_Model_Dictionary::DEPARTAMENT);
            if($res) foreach($res as $r){
                $elm->addMultiOption($r->id,$r->title);
            }
        }catch(Exception $e){

        }
        $elm->setAttrib("class", "select middle");
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('workstart');
        $elm->setRequired(true)
            ->setLabel('Початок роботи : ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть дату початока роботи')));
        $elm->setAttrib("class", "edit date");
        $elm->setAttrib("readonly", "true");
        $elm->setAttrib("placeholder", "dd/mm/YYYY");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('workend');
        $elm->setLabel('Дата звільнення : ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "edit date");
        $elm->setAttrib("readonly", "true");
        $elm->setAttrib("placeholder", "dd/mm/YYYY");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

