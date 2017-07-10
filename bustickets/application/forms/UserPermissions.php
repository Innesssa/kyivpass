<?php

class Application_Form_UserService extends Zend_Form
{

    public function init()
    {
        $db = new Application_Model_DbTable_Dictionary();
        $this->setName('UserServiceData');

        $elm = new Zend_Form_Element_Hidden('idu');
        $elm->addFilter('int');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано користувача')));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('lnkuser2systemfrom');
        $elm->setLabel('Ролі доступу : ');
        $db = new Application_Model_DbTable_PermissionRoles();
        $res = $db->getAll();
        if($res) foreach($res as $r) $elm->addMultiOption($r->id,$r->title);
        $elm->setAttrib("class", "select box from");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('lnkuser2systemto');
        $elm->setRequired(true)
            ->setLabel('Назначені ролі доступу : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано ролі доступу')));
        $elm->setAttrib("class", "select box to");
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Select('areasfrom');
        $elm->setLabel('Cтанції доступу : ');
        $db = new Application_Model_DbTable_Dictionary();
        $res = $db->selectDataByType(Application_Model_Dictionary::AREA);
        if($res) foreach($res as $r) $elm->addMultiOption($r->id,$r->title);
        $elm->setAttrib("class", "select box from");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('areasto');
        $elm->setRequired(true)
            ->setLabel('Назначені станції доступу : ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Повинно бути обрано станції доступу')));
        $elm->setAttrib("class", "select box to");
        $this->addElement($elm);





        $elm = new Zend_Form_Element_Text('startDate');
        $elm->setLabel('Початоковий інтервал : ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "edit date");
        $elm->setAttrib("readonly", "true");
        $elm->setAttrib("placeholder", "dd/mm/YYYY");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('endDate');
        $elm->setLabel('Кінцевий інтервал : ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "edit date");
        $elm->setAttrib("readonly", "true");
        $elm->setAttrib("placeholder", "dd/mm/YYYY");
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('type');
        $elm->setLabel('Часова ознака : ')
            ->addFilter('int');
        $elm->addMultiOption(Application_Model_TimeInterval::DAILY,'завжди');
        $elm->addMultiOption(Application_Model_TimeInterval::ONCE,'один раз');
        $elm->setAttrib("class", "select middle");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('access');
        $elm->setLabel('Тип доступу : ')
            ->addFilter('int');
        $elm->addMultiOption('allow','дозволити');
        $elm->addMultiOption('forbidden','заборонити');
        $elm->setAttrib("class", "select middle");
        $this->addElement($elm);




        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

