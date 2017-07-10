<?php

class Application_Form_PayService extends Zend_Form
{



    public function init()
    {

        $this->setName('ServiceForm');
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',

        ));
        $elm = new Zend_Form_Element_Select('service');
        $elm->setRequired(true)
            ->setLabel("Послуга: ")
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть послугу')))
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть послугу',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть послугу',
                    Zend_Validate_Digits::INVALID            =>  'Оберіть послугу')));
        $elm->setAttrib("onchange", "calService();");
        $elm->setAttrib("style", "width:200px;");
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "12");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        //code 	character varying(50) NOT NULL 	номер маршруту
        $elm = new Zend_Form_Element_Hidden('num');
        $elm->setRequired(true)
            ->setLabel('Кількість: ')
            ->addFilter('Int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Кількість'))) ;
        $elm->setValue(1);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('amount');
        $elm->setRequired(true)
            ->setLabel('Ціна: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Ціна послуги порожня'))) ;;

        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("style", "width:200px;");
        $elm->setAttrib("readonly", "readonly");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('description');
        $elm->setLabel('Коментар: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("style", "width:200px;");
        $elm->setAttrib("maxlength", "43");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Button('adService');
        $elm->setLabel('Виконати');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setAttrib("style", "width:200px;");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $this->setMethod('post');


    }


}

