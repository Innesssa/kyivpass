<?php

class Application_Form_DictinaryStationRate extends Zend_Form
{

    public function init()
    {
        $this->setName('HandBooks');
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

        $elm = new Zend_Form_Element_Hidden('par_id');
        $elm->addFilter('int');
        $elm->setValue(0);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Hidden('type');
        $elm->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Не обрано тип довідника')));
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->setLabel('Назва: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'handbooks',
                'field'     => 'title',
                //'exclude'   => array('field'=>'type','value'=>$this->type),
                'messages' => array(
                    Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => 'Таке значення вже присутнє')
            )) ;

        ;


        $elm->setAttrib("class", "input-xxlarge");

        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));


        $this->addElement($elm);


        $elm = new Zend_Form_Element_Select('additional_text');
        $elm->setLabel(Application_Model_Dictionary::getName(Application_Model_Dictionary::TARIFFMATRIX).": ");
        $elm->setRequired(true)
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES),
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES),
                    Zend_Validate_Digits::INVALID            =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::ORGTYPES))))
            ->addPrefixPath('Valid', APPLICATION_LIBRARY.'/Valid', 'validate')
            ->addValidator('TypeChecker',true);




        $db = new Application_Model_DbTable_Dictionary();
        $rs = $db->selectDataByType(Application_Model_Dictionary::TARIFFMATRIX,0);

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


        $elm = new Zend_Form_Element_Textarea('description');
        $elm->setLabel('Опис :')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setAttrib("rows", "3");
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

