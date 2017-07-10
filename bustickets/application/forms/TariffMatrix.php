<?php

class Application_Form_TariffMatrix extends Zend_Form
{

    public function init()
    {
        $this->setName('TariffMatrix');
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





        $elm = new Zend_Form_Element_Text('title_full');
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
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')));
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
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')));
        $elm->setAttrib("class", "input-xxlarge");
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

        $elm = new Zend_Form_Element_Select('recoup');
        $elm->setRequired(true)
            ->setLabel('Компенсатор: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву компенсатора')));
        $db = new Application_Model_DbTable_OrganizationList();
        $rs = $db->selectDataByType('1344');
        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->title);
        }
        $elm->setAttrib("class", "input-xxmedium");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

/*
        $elm = new Zend_Form_Element_Textarea('recoup_attribute');
        $elm->setLabel('Реквизити: ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "input-xxsmall");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);
*/

        $elm = new Zend_Form_Element_Text('benefit_perc');
        $elm->setRequired(true);
        $elm->setLabel('Знижка %: ')
            ->addFilter('int')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть % знижки')));
        $elm->setAttrib("class", "input-small");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Checkbox('document');
        $elm->setRequired(true);
        $elm->setLabel('Потребує документ: ');
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

