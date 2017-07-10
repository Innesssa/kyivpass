<?php

class Application_Form_User extends Zend_Form
{

    public function init()
    {
        $this->setName('UserForm');
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

        $elm = new Zend_Form_Element_Hidden('needstore');
        $elm->addFilter('int');
        $elm->setValue(1);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('login');
        $elm->setRequired(true)
            ->setLabel('Логін: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть логін')))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>50,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Логін не вірно введено",
                    Zend_Validate_StringLength::TOO_SHORT => "Логін не може бути меньше за 4 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Логін не може бути більшою за 50 символів",
                )))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'userlogindata',
                'field'     => 'login',
                'messages' => array(
                    Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => 'Користувач з таким логіном вже існує')
            )) ;
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Password('password');
        $elm->setLabel('Пароль:')
            ->setRequired(true)
            ->addValidator('NotEmpty', true, array( 'messages' => array(Zend_Validate_NotEmpty::IS_EMPTY => "Значення не може бути порожнім" )))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>20,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Пароль не вірно введено",
                    Zend_Validate_StringLength::TOO_SHORT => "Пароль не може бути меньше за 4 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Назва не може бути більшою за 20 символів",
                )));
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Password('cpassword');
        $elm->setLabel('Підтвердження паролю:')
            ->setRequired(true)
            ->addValidator('NotEmpty', true, array( 'messages' => array(Zend_Validate_NotEmpty::IS_EMPTY => "Значення не може бути порожнім" )))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('PasswordConfirmation',true);
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Select('id_entity');
        $elm->setLabel("Доступ: ");
        $elm->setRequired(true)
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть роль доступу до системи',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть роль доступу до системи',
                    Zend_Validate_Digits::INVALID            =>  'Оберіть роль доступу до системи')))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate');
        $db = new Application_Model_DbTable_PermissionRoles();
        $rs = $db->fetchAll();

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->description);
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



        $elm = new Zend_Form_Element_Text('lastname');
        $elm->setRequired(true)
            ->setLabel("Прізвище: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть прізвище')))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>50,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Прізвище не вірно введено",
                    Zend_Validate_StringLength::TOO_SHORT => "Прізвище не може бути меньше за 4 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Прізвище не може бути більшою за 50 символів",
                )));
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('firstname');
        $elm->setRequired(true)
            ->setLabel("Ім'я: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть ім\'я')))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>50,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Ім'я не вірно введено",
                    Zend_Validate_StringLength::TOO_SHORT => "Ім'я не може бути меньше за 4 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "Ім'я не може бути більшою за 50 символів",
                )));
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('middlename');
        $elm->setRequired(true)
            ->setLabel("По-батькові: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть по-батькові')))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>50,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "По-батькові не вірно введено",
                    Zend_Validate_StringLength::TOO_SHORT => "По-батькові не може бути меньше за 4 символів",
                    Zend_Validate_StringLength::TOO_LONG  => "По-батькові не може бути більшою за 50 символів",
                )));
        $elm->setAttrib("class", "edit middle");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Select('defaultstation');
        $elm->setRequired(true)
            ->setLabel("Автостанція: ")
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть роль доступу до системи',
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть роль доступу до системи',
                    Zend_Validate_Digits::INVALID            =>  'Оберіть роль доступу до системи')));
        $_dbDic = new Application_Model_DbTable_Dictionary();


        $rs = $_dbDic->selectStationList(Application_Model_Dictionary::STATION);
        //trace($rs,1);
        foreach($rs as $r){
            $elm->addMultiOption($r['id'],$r['title']);
        }


//        $rs = $dbDic->selectDataByType(Application_Model_Dictionary::VECHICLETYPE,0);
//        foreach($rs as $r){
//            $elm->addMultiOption($r->id,$r->title);
//        }



        //trace($elm,1);
        $elm->addMultiOption('empty',"оберіть необхідне");
        $elm->setValue('empty');
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('persnum');
        $elm->setRequired(true)
            ->setLabel("Табельний номер: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть табельний номер')))
            ->addValidator('StringLength',true,array('min'=>4,'max'=>6,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Табельний номер введено не вірно",
                    Zend_Validate_StringLength::TOO_SHORT => "Табельний номер не може бути меньшим за 4 символа",
                    Zend_Validate_StringLength::TOO_LONG  => "Табельний номер не може бути більшим за 6 символів",
                )));
        $elm->setAttrib("class", "edit small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('workstart');
        $elm->setRequired(true)
            ->setLabel("Дата початку діяльності: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть Початок діяльності')))
            ->addValidator('StringLength',true,array('min'=>10,'max'=>19,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Початок діяльності введено не вірно",
                    Zend_Validate_StringLength::TOO_SHORT => "Початок діяльності не може бути меньшим за 4 символа",
                    Zend_Validate_StringLength::TOO_LONG  => "Початок діяльності не може бути більшим за 6 символів",
                )));
        $elm->setAttrib("class", "edit small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('workend');
        $elm->setRequired(false)
            ->setLabel("Дата закінчення роботи: ")
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Дата закінчення роботи')))
            ->addValidator('StringLength',true,array('min'=>10,'max'=>19,
                'messages'=>array(
                    Zend_Validate_StringLength::INVALID   => "Дата закінчення роботи введено не вірно",
                    Zend_Validate_StringLength::TOO_SHORT => "Дата закінчення роботи не може бути меньшим за 4 символа",
                    Zend_Validate_StringLength::TOO_LONG  => "Дата закінчення роботи не може бути більшим за 6 символів",
                )));
        $elm->setAttrib("class", "edit small");
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

