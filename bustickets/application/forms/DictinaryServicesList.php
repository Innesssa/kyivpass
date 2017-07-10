<?php

class Application_Form_DictinaryBenefits extends Zend_Form
{

    public function init()
    {
        $this->setName('ServicesList');
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

        $elm = new Zend_Form_Element_Text('code');
        $elm->setRequired(true)
            ->setLabel('Код: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть код')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'benefitslist',
                'field'     => 'title_full',
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


        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->setLabel('Назва: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'benefitslist',
                'field'     => 'title_full',
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


        $elm = new Zend_Form_Element_Text('title_short');
        $elm->setRequired(true)
            ->setLabel('Коротка назва: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'benefitslist',
                'field'     => 'title_short',
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


        $elm = new Zend_Form_Element_Text('title_print');
        $elm->setRequired(true)
            ->setLabel('Назва для друку: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'benefitslist',
                'field'     => 'title_print',
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


        $elm = new Zend_Form_Element_Text('price');
        $elm->setRequired(true);
        $elm->setLabel('Ціна: ')
            //->addFilter('int')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть ціну')));
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('vat');
        $elm->setRequired(true);
        $elm->setLabel('ПДВ: ')
            //->addFilter('int')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть сумму ПДВ')));
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('article');
        $elm->setRequired(true);
        $elm->setLabel('Артикуль: ')
            //->addFilter('int')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть артикуль')));
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('group_vat');
        $elm->setRequired(true);
        $elm->setLabel('Група ПДВ: ')
            //->addFilter('int')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть групу ПДВ')));
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('description');
        $elm->setRequired(true)
            ->setLabel('Повний опис пільги: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть опис')));
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

