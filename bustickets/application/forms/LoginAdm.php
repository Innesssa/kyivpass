<?php

class Application_Form_LoginAdm extends Zend_Form
{

    public function init()
    {
        $this->setName('login');
        $elm = new Zend_Form_Element_Text('login');
        $elm->setRequired(true)
            ->setLabel('Користувач: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Enter login')));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Password('pwd');
        $elm->setRequired(true)
            ->setLabel('Пароль: ')
            ->addValidator('NotEmpty', true,  array('messages' => array('isEmpty' => 'Enter password')));
        $this->addElement($elm);
        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

