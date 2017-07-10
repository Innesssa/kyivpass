<?php

class Application_Form_TicketsReport extends Zend_Form
{
    public function init()
    {
        $this->setName('TicketFilter');
        $this->setAction("/reports/tickets/");
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',
        ));

        $elm = new Zend_Form_Element_Hidden('stationstartid');
        $elm->setValue(1306);
        $elm->setRequired(true);
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'))
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_points_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не вірно обрана станція або зупинка')));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('stationstart');
        $elm->setValue('КИЇВ Поділ АС, Подільський р-н м.Києва, Київ місто');
        $elm->setRequired(true);
        $elm->setLabel("Початкова зупинка: ")
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть станцію або зупинку')));
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('date_start');
        $elm->setRequired(true);
        $elm->setLabel('З : ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть початок дії рейсу')))
            ->addValidator('Regex',true, array('/^\d{2}.\d{2}\.\d{4}/'))
        ;
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


        $elm = new Zend_Form_Element_Text('date_finish');
        $elm->setRequired(true);
        $elm->setLabel('По: ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть початок дії рейсу')))
            ->addValidator('Regex',true, array('/^\d{2}.\d{2}\.\d{4}/'))
        ;
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



        $elm = new Zend_Form_Element_Submit('export');
        $elm->setLabel('Заватнажити');
        $elm->setAttrib("class", "btn inline");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $this->setMethod('post');
    }
}

