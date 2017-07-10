<?php

class Application_Form_Tariffs extends Zend_Form
{
    private $_routeID=0;
    public function __construct($routeid){
        $this->_routeID=$routeid;
        $this->init();
    }
    public function init()
    {
        $this->setName('TariffsForm');
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

        $elm = new Zend_Form_Element_Hidden('routeid');
        $elm->setRequired(true)
            ->addFilter('int')
            ->setValue($this->_routeID);
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);




        $elm = new Zend_Form_Element_Button('ticketsTR');
        $elm->setLabel('Квитки');
        $elm->setAttrib("class", "btn inline");
        $elm->setAttrib("onclick", "ticketsShower(1);");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Button('luggageTR');
        $elm->setLabel('Багаж');
        $elm->setAttrib("class", "btn inline");
        $elm->setAttrib("onclick", "luggageShower(1);");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Button('backTR');
        $elm->setLabel('Повернення');
        $elm->setAttrib("class", "btn inline");
        $elm->setAttrib("onclick", "backShower(1);");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Radio('pricetype');
        $elm->setLabel("Розрахунок квитків: ");
        $elm->setRequired(true);


        $elm->addMultiOption("fixed","Фіксована");
        $elm->addMultiOption("calculated","Розрахункова");
        $elm->setAttrib("onchange","ticketsSwitcher();");
        $elm->setSeparator("");

        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group pricetype')),
        ));

        $this->addElement($elm);


        $elm = new Zend_Form_Element_Radio('luggagetype');
        $elm->setLabel("Розрахунок багажу: ");
        $elm->setRequired(true);


        $elm->addMultiOption("fixed","Фіксована");
        $elm->addMultiOption("calculated","Розрахункова");
        $elm->setAttrib("onchange","luggageSwitcher();");
        $elm->setSeparator("");



        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group luggage')),
        ));

        $this->addElement($elm);



        $elm = new Zend_Form_Element_Radio('backtype');
        $elm->setLabel("Поверненя квитків: ");
        $elm->addMultiOption("calculated","Розрахункова");
        $elm->setValue("calculated");
        $elm->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class'  => 'controls')),
            array('Label',array('class'=>'control-label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group backtickets')),
        ));
        $elm->setAttrib("disabled","disabled");
        $this->addElement($elm);



        $elm = new Zend_Form_Element_Text('priceperkm');
        $elm->setLabel('Ціна за км:')
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


        $elm = new Zend_Form_Element_Button('rebuild');
        $elm->setLabel('Перерахувати');
        $elm->setAttrib("class", "btn legend");
        $elm->setAttrib("onclick", "rebuildPrices();");
        $elm->setDecorators(array('ViewHelper'));
        $this->addElement($elm);




        $dbLnk = new Application_Model_DbTable_LnkStation2Route();
        $aStationList = $dbLnk->getStationList($this->_routeID);
        //trace($aStationList,1);
        $sTabsHTML="";
        $sBodiesHTML="";
        $aElms = array();
        for($i=0;$i<(count($aStationList)-1);$i++){
            $sTabsHTML.="<li><a id=\"statLI".$aStationList[$i]['stationid']."\" href=\"#stat".$aStationList[$i]['stationid']."\"><span>".$aStationList[$i]['pos'].".&nbsp;".$aStationList[$i]['station_name']."</span></a></li>";
            $sBodiesHTML.="<div id=\"stat".$aStationList[$i]['stationid']."\">";
            $tr=0;
            $km = $aStationList[$i]['distantion'];
            for($y=($i+1);$y<count($aStationList);$y++){
                $tr++;
                //$sBodiesHTML .= ( $tr%2 ) ?  "<tr>" : "";
                $sBodiesHTML.="<label for=\"servicetype\" class=\"control-label required\">".$aStationList[$y]['pos'].".&nbsp;".$aStationList[$y]['station_name']." (".sprintf("%.2f km",$aStationList[$y]['distantion'])."):</label><div class=\"controls\"><input data-info=\"".($aStationList[$y]['distantion']*1.0-$km*1.0)."\" id=\"price_".$aStationList[$i]['stationid']."_".$aStationList[$y]['stationid']."\" type=\"text\" name=\"tariffs[".$aStationList[$i]['stationid']."][".$aStationList[$y]['stationid']."]\" class=\"input-small\" value=\"\"></div></br>";
                //$km = $aStationList[$y]['distantion'];
                $aElms[] = "price_".$aStationList[$i]['stationid']."_".$aStationList[$y]['stationid'];
                //$sBodiesHTML.= ( $tr%2 ) ?  "" : "</tr>" ;

            }
            //$sBodiesHTML.= ( $tr%2 ) ?  "</tr>" : "" ;
            $sBodiesHTML.="</div>";
        }

        $addHTML="";
        /*Block for fixed Tickets
        $addHTML="<div class=\"control-group calculated\">
                <div class=\"multyfields\">Відстань(km): Від&nbsp;<input type=\"text\" name=\"calculatedticketsS[]\"  value=\"\" class=\"input-small calculated\">&nbsp;До&nbsp;<input type=\"text\" name=\"calculatedticketsE[]\"  value=\"\" class=\"input-small calculated\">&nbsp;Ціна:&nbsp;<input type=\"text\" name=\"calculatedprice[]\"  value=\"\" class=\"input-small calculated\"><button name=\"remove\"  type=\"button\" class=\"btn inline2\" onclick=\"delRow(this);\">Видалити</button></div>
                <button name=\"add\"  id=\"idTicketCalcFirst\" type=\"button\" class=\"btn inline3\" onclick=\"addRow(this);\">Додати рядок</button>
        </div>";
        */

        //Block for fixed Luggage
        $addHTML.="<div class=\"control-group luggage\" id=\"idluggagecalculated\"><div class=\"multyfields\">Відстань(km): Від&nbsp;<input type=\"text\" name=\"luggageticketsS[]\"  value=\"\" class=\"input-small luggage\">&nbsp;До&nbsp;<input type=\"text\" name=\"luggageticketsE[]\"  value=\"\" class=\"input-small luggage\">&nbsp;Ціна:&nbsp;<input type=\"text\" name=\"luggageprice[]\"  value=\"\" class=\"input-small luggage\"><button name=\"remove\"  type=\"button\" class=\"btn inline2\" onclick=\"delRow(this);\">Видалити</button></div>
                <button name=\"add\"  id=\"idluggageCalcFirst\" type=\"button\" class=\"btn inline3\" onclick=\"addRow(this);\">Додати рядок</button>
                </div>
                <div class=\"control-group luggage\" id=\"idluggagefixed\"><label for=\"luggagepercent\" class=\"control-label required\">Відсоток від ціни квитка:</label>
                <div class=\"controls\"><input type=\"text\" name=\"luggagepercent\" id=\"luggagepercent\" value=\"\" class=\"input-middle\"></div></div>";

        //Block for backtickets
        $addHTML.="<div class=\"control-group backtickets\" id=\"idbacktickets\"><div class=\"multyfields\">Час(у хвилинах):&nbsp;Від&nbsp;<input type=\"text\" name=\"backticketstimeS[]\"  value=\"\" class=\"input-small backtickets\">&nbsp;До&nbsp;<input type=\"text\" name=\"backticketstimeE[]\"  value=\"\" class=\"input-small backtickets\">&nbsp;Відсоток:&nbsp;<input type=\"text\" name=\"backticketsprice[]\"  value=\"\" class=\"input-small backtickets\"><button name=\"remove\"  type=\"button\" class=\"btn inline2\" onclick=\"delRow(this);\">Видалити</button></div>
                <button name=\"add\"  id=\"idBackCalcFirst\" type=\"button\" class=\"btn inline3\" onclick=\"addRow(this);\">Додати рядок</button>
                </div>";

        $decorator = new Zend_Form_Decorator_Fieldset();
        $decorator->setOption('escape', false);
        $decorator->setLegend("<div id=\"tabs\"><ul>".$sTabsHTML."</ul>".$sBodiesHTML."</div><script>$( \"#tabs\" ).tabs(); jStation=".json_encode( $aElms )."; </script>".$addHTML);


        $elm = new Zend_Form_Element_Hidden('needstore');
        $elm->addFilter('int');
        $elm->setValue(1);
        $elm->addDecorators(array($decorator));
        $this->addElement($elm);
           /*
            $elm = new Zend_Form_Element_Text('pricestabs');
            $elm->setValue("<div id=\"tabs\"><ul>".$sTabsHTML."</ul>".$sBodiesHTML."</div><script>$( \"#tabs\" ).tabs(); </script>")->setDecorators(array('ViewHelper'))->helper = 'formNote';
            $this->addElement($elm);
           */
        //}
        $elm = new Zend_Form_Element_Hidden('idactivepart');
        $elm->addFilter('int');
        $elm->setValue(1);
        $this->addElement($elm);

        $this->setMethod('post');


    }


}

