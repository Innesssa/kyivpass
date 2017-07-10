<?php

class Application_Form_Points extends Zend_Form
{
    public function init()
    {
        $this->setName('Points');
        $this->setAction("/points/filter/");
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',
        ));

        $elm = new Zend_Form_Element_Hidden('stationstartid');
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


        $this->setMethod('post');
    }
}

