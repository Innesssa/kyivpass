<?php

class Application_Form_RoutesSchema extends Zend_Form
{
/*
id	int	id запису
routeid	int not null	id маршруту
stationid	int not null	id зупики
pos	int	номер за маршрутом
timeperiod	int	час слідування від попередньої зупинки у хвилинах
holdtime	int	час зупинки на зупинці у хвилинах
distantion	float(10,2)	відстань від
price	float(10,2)	ціна від попередньої зупинки
priceinzone	float(10,2)	ціна у зоні
description	text	коментарі
*/

    public function init()
    {
        $this->setName('RouteShemaForm');
        $this->setAction("/routes/editschema/");
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',

        ));

        $elm = new Zend_Form_Element_Hidden('needstore');
        $elm->addFilter('int');
        $elm->setValue(1);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Hidden('id');
        $elm->addFilter('int');
        $elm->setValue(0);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Hidden('routeid');
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Hidden('stationid');
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'))
            ->addValidator('Db_RecordExists', true, array(
            'table'     => 'handbooks_points_view',
            'field'     => 'id',
            'messages' => array(
                Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не вірно обрана станція або зупинка')));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('station');
        $elm->setRequired(true);
        $elm->setLabel("Зупинка: ")
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
//==================================




        $elm = new Zend_Form_Element_Text('pos');
        $elm->setLabel("Номер за маршрутом: ");
        $elm->setRequired(true);
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('timeperiod');
        $elm->setLabel("Час слідування від початку: ");
        $elm->setRequired(true);
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('holdtime');
        $elm->setLabel("Тривалість зупинки у хвилинах: ");
        $elm->setRequired(true);
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('distantion');
        $elm->setLabel("Відстань від початку: ");
        $elm->setRequired(true);
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Textarea('description');
        $elm->setLabel("Коментарі: ")
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("rows", "4");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        /*
                $elm = new Zend_Form_Element_Submit('add_station');
                $elm->setLabel("Додати");
                $elm->setAttrib("id","add_station_to_route");
                $elm->setAttrib("class","btn btn-primary");
                $this->addElement($elm);
        */
        $this->setMethod('post');


    }


}

