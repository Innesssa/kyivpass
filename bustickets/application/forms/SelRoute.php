<?php

class Application_Form_SelRoute extends Zend_Form
{

// CONST CONVTYPEID = 1272; //
//id 	integer NOT NULL  	id маршруту
//code 	character varying(50) NOT NULL 	номер маршруту
//title 	character varying(250) NOT NULL  назва


    public function init()
    {
        $this->setName('SelRoute');
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',

        ));

        $elm = new Zend_Form_Element_Select('routeID');
        $db = new Application_Model_DbTable_Routes();
        $rs =  $db->fetchAll(null,array("code ASC","title ASC"));

        foreach($rs as $r){
            $elm->addMultiOption($r->id,$r->code."::".$r->title);
        }
        $elm->addMultiOption('empty',"оберіть маршрут");
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


    }


}

