<?php

class Application_Form_Organisation extends Zend_Form
{



//id	integer  NOT NULL
//title	character varying(120)
//type	integer	тип організації
//ipn	character varying(12)	індивідуальний податковий номер
//edrpou	character varying(8) ЄДРПОУ
//mfo	character varying(6)	МФО банку
//accountnr	character varying(20) номер банківського рахунку
//bank	character varying(120) назва банку
//legaladdress	character varying(120)	юридична адреса
//realaddress	character varying(120) діюча адреса
//email	character varying(120) електронна пошта
//printedfield character varying(120) поле для друку
//code varchar(20) 'код'
//vat  varcgar(1)  'чи є платником ПДВ'


    public function init()
    {
        $this->setName('OrganisationForm');
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

        $elm = new Zend_Form_Element_Select('type');
        $elm->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES).": ");
        $elm->setRequired(true)
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES),
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES),
                    Zend_Validate_Digits::INVALID            =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES))))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('TypeChecker',true);




        $db = new Application_Model_DbTable_Dictionary();
        $rs = $db->selectDataByType(Application_Model_Dictionary::ORGTYPES,0);

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->title);
        }
        $elm->addMultiOption('empty',"оберіть необхідне");
        $elm->setValue('empty');
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->setLabel('Назва: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('StringLength',true,array('min'=>3,'max'=>50,
                    'messages'=>array(
                        Zend_Validate_StringLength::INVALID   => "Назва не коректна",
                        Zend_Validate_StringLength::TOO_SHORT => "Назва не може бути меньше за 3 символи",
                        Zend_Validate_StringLength::TOO_LONG  => "Назва не може бути більшою за 120 символів",
                    )))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'organizationlist',
                'field'     => 'title',
                //'exclude'   => array('field'=>'type','value'=>$this->type),
                'messages' => array(
                    Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => 'Таке значення вже присутнє')
            )) ;
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        //accountnr	character varying(20) номер банківського рахунку
        $elm = new Zend_Form_Element_Text('accountnr');
        $elm->setRequired(true)
            ->setLabel('Рахунок: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть номер банківського рахунку')))

            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть номер банківського рахунку',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))

            ->addValidator('StringLength',true,array('min'=>20,'max'=>20,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Значення не коректне",
                    Zend_Validate_StringLength::TOO_SHORT=> "номер банківського рахунку не може бути меньше за 20 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "номер банківського рахунку не може бути більшою за 20 символів",
                )));
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "20");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('ipn');
        $elm->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть IПH')))
            ->setLabel('ІПН : ')
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть IПH',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))

            ->addValidator('StringLength',true,array('min'=>12,'max'=>12,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Значення не коректне",
                    Zend_Validate_StringLength::TOO_SHORT => "IПH не може бути меньше за 12 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "IПH не може бути більшою за 12 символів",
                )));
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


        //edrpou	character varying(8) ЄДРПОУ

        $elm = new Zend_Form_Element_Text('edrpou');
        $elm->setRequired(true)
            ->setLabel('ЄДРПОУ : ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть ЄДРПОУ')))

            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть ЄДРПОУ',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))

            ->addValidator('StringLength',true,array('min'=>8,'max'=>8,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "ЄДРПОУ не коректне",
                    Zend_Validate_StringLength::TOO_SHORT => "ЄДРПОУ не може бути меньше за 8 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "ЄДРПОУ не може бути більшою за 8 символів",
                )));
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "8");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        //mfo	character varying(6)	МФО банку
        $elm = new Zend_Form_Element_Text('mfo');
        $elm->setRequired(true)
            ->setLabel('МФО банку : ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть МФО банку')))

            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть МФО банку',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))


            ->addValidator('StringLength',true,array('min'=>6,'max'=>6,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "МФО банку не коректне",
                    Zend_Validate_StringLength::TOO_SHORT => "МФО банку не може бути меньше за 6 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "МФО банку не може бути більшою за 6 символів",
                )));
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




        //bank	character varying(120)
        $elm = new Zend_Form_Element_Text('bank');
        $elm->setRequired(true)
            ->setLabel('Назва банку: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву банку')))
            ->addValidator('StringLength',true,array('min'=>3,'max'=>120,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Назва банку не коректна",
                    Zend_Validate_StringLength::TOO_SHORT => "Назва банку не може бути меньше за 3 символи",
                    Zend_Validate_StringLength::TOO_LONG  => "Назва банку не може бути більшою за 120 символів",
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

        //legaladdress	character varying(120)	юридична адреса
        $elm = new Zend_Form_Element_Text('legaladdress');
        $elm->setRequired(true)
            ->setLabel('Юридична адреса: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть юридична адресу')))
            ->addValidator('StringLength',true,array('min'=>3,'max'=>120,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Юридична адреса не коректна",
                    Zend_Validate_StringLength::TOO_SHORT => "Юридична адреса не може бути меньше за 3 символи",
                    Zend_Validate_StringLength::TOO_LONG  => "Юридична адреса не може бути більшою за 120 символів",
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

        //realaddress	character varying(120) діюча адреса
        $elm = new Zend_Form_Element_Text('realaddress');
        $elm->setLabel('Діюча адреса: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('StringLength',true,array('min'=>0,'max'=>120,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Діюча адреса не коректна",
                    Zend_Validate_StringLength::TOO_SHORT => "Діюча адреса не може бути меньше за 0 символи",
                    Zend_Validate_StringLength::TOO_LONG  => "Діюча адреса не може бути більшою за 120 символів",
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



        //email	character varying(120) електронна пошта
        $elm = new Zend_Form_Element_Text('email');
        $elm->setLabel('Eлектронна пошта: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('EmailAddress',true,array('messages'=>array(
                                                        Zend_Validate_EmailAddress::INVALID => "Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::INVALID_FORMAT=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::INVALID_HOSTNAME=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::INVALID_MX_RECORD=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::INVALID_SEGMENT=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::DOT_ATOM=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::QUOTED_STRING=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::INVALID_LOCAL_PART=>"Eлектронна пошта не вірна",
                                                        Zend_Validate_EmailAddress::LENGTH_EXCEEDED=>"Eлектронна пошта не вірна",
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


        $elm = new Zend_Form_Element_Text('printedfield');
        $elm->setRequired(false)
            ->setLabel('Поле для друку : ')
            ->addValidator('StringLength',true,array('min'=>0,'max'=>120,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Поле для друку не коректне",
                    Zend_Validate_StringLength::TOO_SHORT => "Поле для друку не може бути меньше за 0 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Поле для друку не може бути більшою за 120 символів",
                )));
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("maxlength", "120");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('code');
        $elm->setRequired(false)
            ->setLabel('Код: ')
            ->addValidator('StringLength',true,array('min'=>0,'max'=>20,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Код не коректний",
                    Zend_Validate_StringLength::TOO_SHORT => "Код не може бути меньше за 0 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Код не може бути більшим за 20 символів",
                )));
        $elm->setAttrib("class", "input-small");
        $elm->setAttrib("maxlength", "20");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Checkbox('vat');
        $elm->setRequired(false)
            ->setLabel('Чи є платником ПДВ: ')
/*

            ->addValidator('StringLength',true,array('min'=>1,'max'=>1,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Поле введено з помилкою",
                    Zend_Validate_StringLength::TOO_SHORT => "Поле не може бути меньшим за 1 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Поле не може бути більшим за 1 символів",
                )))
*/
        ;
        $elm->setAttrib("class", "input-small");
        $elm->setAttrib("maxlength", "1");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        //$elm = new Zend_Form_Element_Submit('submit');
        //$this->addElement($elm);

        $this->setMethod('post');


    }


}

