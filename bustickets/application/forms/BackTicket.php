<?php

class Application_Form_BackTicket extends Zend_Form
{
    public function init()
    {
        $this->setName('BackTicket');
        $this->setAction("/kassa/back/");
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',
        ));


        $elm = new Zend_Form_Element_Text('checknumber');
        $elm->setRequired(true);
        $elm->setLabel('Номер чека: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Номер чека')));
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "10");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('ppo');
        $elm->setRequired(true);
        $elm->setLabel('Номер PPO: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Номер PPO')));
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "10");
        $elm->setAttrib("onblur", "findCheck();");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);






        $elm = new Zend_Form_Element_Text('date_begin');
        $elm->setRequired(true);
        $elm->setLabel('Дата придбання: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть початок дії рейсу')))
            ->addValidator('Regex',true, array('/^\d{2}.\d{2}\.\d{4}/'));
        $elm->setAttrib("class", "input-middle datepicker");
        $elm->setAttrib("maxlength", "10");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $elm->setValue(date("d.m.Y"));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('code');
        $elm->setRequired(true);
        $elm->setLabel('Номер рейсу: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Номер рейсу')));
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "10")
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('place');
        $elm->setRequired(true);
        $elm->addFilter("Int");
        $elm->setLabel('Номер місця: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Номер місця')));
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "10");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Button('ticketfind');
        $elm->setLabel('ПОШУК');
        $elm->setAttrib("class", "btn inline");
        $elm->setAttrib("onclick", "findCheck();");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $this->setMethod('post');
    }
}

