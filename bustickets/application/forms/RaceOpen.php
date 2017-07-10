<?php

class Application_Form_RaceOpen extends Zend_Form
{
    private $_vecliesCountPlaces = array();

    public function getCountPlaces($id){
        return (!empty($this->_vecliesCountPlaces[$id])) ? $this->_vecliesCountPlaces[$id] : 0;
    }

    public function init()
    {

        $this->setName('RaceOpenForm');
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

        $elm = new Zend_Form_Element_Hidden('date_begin');
        $elm->setRequired(true);
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

        $elm = new Zend_Form_Element_Hidden('tp');
        $elm->setRequired(true);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);
        //in | out

        $elm = new Zend_Form_Element_Select('status');
        $elm->setRequired(true)
            ->setLabel('Статус: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть статус')));

        foreach(Application_Model_Races::$_aTypes as $key => $val) {
            $elm->addMultiOption($key, $val);
        }
        $elm->setAttrib("onchange", "showComments(this);");
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        //платформа
        $elm = new Zend_Form_Element_Text('platform');
        $elm->setLabel('Платформа: ')
            ->addFilter('int');
        $elm->setAttrib("class", "input-middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        //code 	character varying(50) NOT NULL 	номер маршруту
        $elm = new Zend_Form_Element_Text('govnumber');
        $elm->setLabel('Державний номер ТЗ: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        //title 	character varying(250) NOT NULL  назва
        $elm = new Zend_Form_Element_Text('driver_name');
        $elm->setLabel('ПІБ водія: ');

        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "250");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('dt_received');
        $elm->setRequired(true);
        $elm->setLabel('Дата : ');
        $elm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть дату')))
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

        $elm = new Zend_Form_Element_Text('time_begin');
        $elm->setLabel('Час: ');
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "5");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $dbDic     = new Application_Model_DbTable_Dictionary();
        //vehicletypeid 	integer NOT NULL  тип траспортного засобу
        $elm = new Zend_Form_Element_Select('vehicletitle');
        $elm->setRequired(true)
            ->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::VECHICLETYPE).': ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть тип траспортного засобу')))

            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть тип траспортного засобу',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_vehicletype_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не заданий тип транспортного засобу')));


        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::VECHICLETYPE,0);

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->title."-".$r->description." місць");
            $this->_vecliesCountPlaces[$r->id]=$r->description;
        }


        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("onchange", "changePlaces();");
        $elm->setAttrib("maxlength", "6");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);




        $elm = new Zend_Form_Element_Text('places');
        $elm->setLabel('Кількість місць для продажу: ');
        $elm->setRequired(true);
        $elm->addFilter('int');
        $elm->setAttrib("class", "input-middle");
        $elm->setAttrib("maxlength", "3");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        //vehicletypeid 	integer NOT NULL  тип траспортного засобу
        $elm = new Zend_Form_Element_Select('fail_type');
        $elm->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::CANCELREASON).': ');
        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::CANCELREASON,0);
        foreach($rs as $r){
            $elm->addMultiOption($r->description,$r->title);
        }
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "6");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        // description 	text коментар
        $elm = new Zend_Form_Element_Text('description');
        $elm->setLabel('Коментар: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('StringLength',true,array('max'=>250,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Коментар неприпустимий",
                    Zend_Validate_StringLength::TOO_LONG  => "Коментар не може бути більший за 250 символів",
                )));
        $elm->setAttrib("class", "input-xxlarge");
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

