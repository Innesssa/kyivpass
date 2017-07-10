<?php

class Application_Form_Route extends Zend_Form
{



    public function init()
    {
        $dbDic = new Application_Model_DbTable_Dictionary();
        $this->setName('RouteForm');
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',

        ));



        $elm = new Zend_Form_Element_Hidden('id');
        $elm->addFilter('int');
        $elm->setValue(0);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);



        //code 	character varying(50) NOT NULL 	номер маршруту
        $elm = new Zend_Form_Element_Text('code');
        $elm->setRequired(true)
            ->setLabel('Маршрут: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву маршруту')))
            ->addValidator('StringLength',true,array('min'=>1,'max'=>50,
                    'messages'=>array(
                        Zend_Validate_StringLength::INVALID   => "Назва не коректна",
                        Zend_Validate_StringLength::TOO_SHORT => "Назва не може бути меньше за 1 символи",
                        Zend_Validate_StringLength::TOO_LONG  => "Назва не може бути більшою за 50 символів",
                    )))
            /*
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'routes',
                'field'     => 'code',
                //'exclude'   => array('field'=>'type','value'=>$this->type),
                'messages' => array(
                    Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => 'Таке значення вже присутнє')
            ))*/ ;
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
        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->setLabel('Опис маршруту: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть опис маршруту')))

            ->addValidator('StringLength',true,array('min'=>5,'max'=>250,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Опис не коректний",
                    Zend_Validate_StringLength::TOO_SHORT => "Опис не може бути меньше за 5 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Опис не може бути більшою за 250 символів",
                )));

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



        $elm = new Zend_Form_Element_Text('date_begin');
        $elm->setRequired(true)
            ->setLabel('Початок дії рейсу: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть початок дії рейсу')));
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
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('date_end');
        $elm->setRequired(true)
            ->setLabel('Кінець дії рейсу: ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть початок дії рейсу')));
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
        $this->addElement($elm);




        $elm = new Zend_Form_Element_Select('year');
        $elm->setLabel("Рік: ");
        for( $i=date("Y") ; $i<(date("Y")+11) ; $i++ ){
            $elm->addMultiOption($i,$i);
        }
        $elm->addMultiOption('*',"без строку");
        $elm->setAttrib("class", "input-medium");
        $elm->setAttrib("maxlength", "4");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('month');
        $elm->setLabel("Місяць: ");
        $elm->addMultiOption(1 ,"cічень");
        $elm->addMultiOption(2 ,"лютий");
        $elm->addMultiOption(3 ,"березень");
        $elm->addMultiOption(4 ,"квітень");
        $elm->addMultiOption(5 ,"травень");
        $elm->addMultiOption(6 ,"червень");
        $elm->addMultiOption(7 ,"липень");
        $elm->addMultiOption(8 ,"серпень");
        $elm->addMultiOption(9 ,"вересень");
        $elm->addMultiOption(10,"жовтень");
        $elm->addMultiOption(11,"листопад");
        $elm->addMultiOption(12,"грудень");
        $elm->addMultiOption('*',"щомісячно");
        $elm->setValue(date("n"));
        $elm->setAttrib("class", "input-medium");
        $elm->setAttrib("maxlength", "2");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);




        $elm = new Zend_Form_Element_Select('day');
        $elm->setLabel("День: ");
        $elm->addMultiOption("*","кожного дня");
        $elm->addMultiOption("*/1","не парні");
        $elm->addMultiOption("*/2","парні");
        $elm->addMultiOption("1","Понеділок");
        $elm->addMultiOption("2","Вівторок");
        $elm->addMultiOption("3","Середа");
        $elm->addMultiOption("4","Четвер");
        $elm->addMultiOption("5","П'ятниця");
        $elm->addMultiOption("6","Субота");
        $elm->addMultiOption("7","Неділя");
        $elm->setAttrib("class", "input-medium");
        $elm->setAttrib("maxlength", "2");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        //hour	varchar(10)	година
        $elm = new Zend_Form_Element_Text('hour');
        $elm->setRequired(true)
            ->setLabel('Час: ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть час')))
        ;
        $elm->setAttrib("class", "input-middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);






        //minute	varchar(10)	хвилина
        $elm = new Zend_Form_Element_Text('minute');
        $elm->setRequired(true)
            ->setLabel('Хвилини: ')
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





        $elm = new Zend_Form_Element_Checkbox('paritet');
        $elm->setLabel("Рейс в паритеті: ");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);




        //conveyorid 	integer NOT NULL  юридична особа-власник маршруту
        $elm = new Zend_Form_Element_Select('conveyorid');
        $elm->setRequired(true)
            ->setLabel("Перевізник: ")
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть Тип ціноутворення ціни квитка')))
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть перевізника',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть перевізника',
                    Zend_Validate_Digits::INVALID            =>  'Оберіть перевізника')))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'organizationlist_conveyor_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не вірне значення перевізника')))        ;


        $db = new Application_Model_DbTable_OrganizationList();
        $rs = $db->getAllConveyors();

        foreach($rs as $r){
            $elm->addMultiOption($r['id'],$r['title']);
        }
        $elm->addMultiOption('empty',"оберіть необхідне");
        $elm->setValue('empty');
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




        //vehicletypeid 	integer NOT NULL  тип траспортного засобу
        $elm = new Zend_Form_Element_Select('vehicletypeid');
        $elm->setRequired(true)
            ->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::VECHICLETYPE).': ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть тип траспортного засобу')))

            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Введіть МФО банку',
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_vehicletype_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не заданий тип транспортного засобу')));


        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::VECHICLETYPE,0);

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->title);
        }
        $elm->addMultiOption('empty',"оберіть необхідне");
        $elm->setValue('empty');

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


        //racetype 	тип рейсу
        $elm = new Zend_Form_Element_Select('stationrate');
        $elm->setRequired(true)
            ->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::STATIONRATE ).': ')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Оберіть '. Application_Model_Dictionary::getName(Application_Model_Dictionary::STATIONRATE))))
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Повинні бути тільки цифри',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть ' . Application_Model_Dictionary::getName(Application_Model_Dictionary::STATIONRATE),
                    Zend_Validate_Digits::INVALID            =>  'Повинні бути тільки цифри')))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_stationrate_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не обраний тип рейсу')));


        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::STATIONRATE,0);

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->title);
        }
        $elm->addMultiOption('empty',"оберіть необхідне");
        $elm->setValue('empty');

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



        // description 	text коментар
        $elm = new Zend_Form_Element_Text('description');
        $elm->setRequired(false)
            ->setLabel('Коментар: ')
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




        //reserv	text	массив заброньованих місць



        $elm = new Zend_Form_Element_Hidden('needstore');
        $elm->addFilter('int');
        $elm->setValue(1);

        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::RESERVES,0);
        $sBodiesHTML = "";
        $addHTML="<label>Бронь</label>";
        foreach($rs as $r)  $sBodiesHTML.= "<option value='".$r->id."'>".$r->title."</option>";
        $addHTML.="<div class=\"control-group reserv\" id=\"idreserv\"><div class=\"multyfields\">Місця: <input type=\"text\" name=\"reservP[]\"  value=\"\" class=\"input-small reserve\">&nbsp;Тип: <select name=\"reservT[]\" class=\"input-large reserve\">".$sBodiesHTML."</select><button name=\"remove\"  type=\"button\" class=\"btn inline2\" onclick=\"delRow(this);\">Видалити</button></div>
                <button name=\"add\"  id=\"idButtonReserve\" type=\"button\" class=\"btn inline3\" onclick=\"addRow(this);\">Додати рядок</button>
                </div>";

        $decorator = new Zend_Form_Decorator_Fieldset();
        $decorator->setOption('escape', false);
        $decorator->setLegend($addHTML);

        $elm->addDecorators(array($decorator));
        $this->addElement($elm);

        $this->setMethod('post');


    }


}

