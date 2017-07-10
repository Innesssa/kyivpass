<?php

class Application_Form_RaceFilter extends Zend_Form
{
    public function init()
    {
        $this->setName('RaceFilter');
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',
        ));


        $elm = new Zend_Form_Element_Text('date_begin');
        $elm->setRequired(true);
        $elm->setLabel('Дата : ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть дату рейсу')))
            ->addValidator('Regex',true, array('/^\d{2}.\d{2}\.\d{4}/'));
        $elm->setAttrib("class", "input-middle datepicker");
        $elm->setAttrib("readonly", "readonly");

        
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



        $elm = new Zend_Form_Element_Submit('idroutesfind');
        $elm->setLabel('ПОШУК');
        $elm->setAttrib("class", "btn inline");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $this->setMethod('post');
    }
}

