<?php

class Application_Form_TicketsSchema extends Zend_Form
{
    private $_places=0;
    public function __construct($places){
        $this->_places=(int)$places;
        $this->init();
    }
    public function init()
    {
        $this->setName('TicketSchema');
        $this->setAction("/tickets/buy/");
        $this->setAttrib("class","form-horizontal");
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset'/*, 'class' => 'form-inline', 'id' => 'test_form'*/)),
            'Form',
        ));

        $elm = new Zend_Form_Element_Hidden('routeid');
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Hidden('startid');
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'))
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_points_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не вірно обрана станція або зупинка')));
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Hidden('endid');
        $elm->addFilter('int');
        $elm->setDecorators(array('ViewHelper'))
            ->addValidator('Db_RecordExists', true, array(
                'table'     => 'handbooks_points_view',
                'field'     => 'id',
                'messages' => array(
                    Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => 'Не вірно обрана станція або зупинка')));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Hidden('dt_begin');
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);


      //checkboxes for places
/*        $rows = ceil($this->_places/4);
        $sHTML = "<table class=\"tablebusplaces\">";
        $curPlaces=0;
        for($i=0;$i<$rows;$i++){
            $sHTML.= "<tr class>";
            $k=0;
            while($k<4 && $curPlaces<$this->_places){
                $curPlaces++;
                if($k==2) $sHTML.= '<td>|&nbsp;&nbsp;&nbsp;&nbsp;|</td>';
                $sHTML.= '<td id="id_td_place_'.$curPlaces.'"><input onchange="checkData(this);" tabindex="'.($i+1).'" type="checkbox" id="id_place_'.$curPlaces.'" name="place['.$curPlaces.']" value="1"/>&nbsp;'.sprintf("%03d",$curPlaces).'</td>';
                $k++;
            }
            $sHTML.= "</tr>";
        }
        $sHTML.= "</table>";
        */
//checkboxes for places - GORIZONTAL
        $cols = ceil($this->_places/4);
        $multy = 0;

        $sHTML = '<table class="tablebusplaces">';
        for ($j=0; $j<4;$j++) {
            if($j==1){
                $sHTML .= '<tr style="height:60px;">';
            }
            else{
                $sHTML .= "<tr>"; }
                $k = 1;
                for ($i = 0; $i < $cols; $i++) {
                    $curPlaces = $k + $multy;
                    if($curPlaces<=$this->_places)
                        $sHTML .= '<td id="id_td_place_' . $curPlaces . '"><input onchange="checkData(this);" tabindex="' . ($multy + 1) . '" type="checkbox" id="id_place_' . $curPlaces . '" name="place[' . $curPlaces . ']" value="1"/>&nbsp;' . sprintf("%03d", $curPlaces) . '</td>';
                    else
                        $sHTML .= '<td>&nbsp;</td>';
                    $k+=4;
                }
                $sHTML .= "</tr>";
                $multy++;

        }
        $sHTML.= "</table>";
        ////////////////////////////////////

        $decorator = new Zend_Form_Decorator_Fieldset();
        $decorator->setOption('escape', false);
        $decorator->setLegend($sHTML);


        $elm = new Zend_Form_Element_Hidden('buy');
        $elm->addFilter('int');
        $elm->setValue(1);
        $elm->addDecorators(array($decorator));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('luggagecount');
        $elm->setRequired(true);
        $elm->addFilter("int");
        $elm->setLabel('Кількість багажу: ');
        $elm->setValue(0);
        $elm->setAttrib("tabindex", $i++);
        $elm->setAttrib("class", "input-small");
        $elm->setAttrib("maxlength", "2");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);


        //benefits
        $elm = new Zend_Form_Element_Hidden('benefitsdiscount');
        $elm->addFilter("int");
        $elm->setValue('0');
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

       //benefits
        $elm = new Zend_Form_Element_Select('benefitsid');
        $elm->setLabel('Пільги: ')
            ->addValidator('Digits', true,array(
                'messages' => array(
                    Zend_Validate_Digits::NOT_DIGITS         =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::BENEFITS),
                    Zend_Validate_Digits::STRING_EMPTY       =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::BENEFITS),
                    Zend_Validate_Digits::INVALID            =>  'Оберіть '.Application_Model_Dictionary::getName(Application_Model_Dictionary::BENEFITS))));

        //$elm->addMultiOption('-',"");
        //$elm->setValue('-');
        $elm->addMultiOption('0',"");
        $elm->setValue('0');
        $elm->setAttrib("tabindex", $i++);
        $elm->setAttrib("onchange", "checkingBenefits();");
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('benefits_name');
        $elm->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setLabel('ПІБ: ');
        $elm->setAttrib("tabindex", $i++);
        $elm->setAttrib("class", "input-xxlarge");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
        ));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Text('benefits_docnum');
        $elm->addFilter('StripTags')
        ->addFilter('StringTrim');
        $elm->setLabel('№ документу: ');
        $elm->setAttrib("tabindex", $i++);
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

