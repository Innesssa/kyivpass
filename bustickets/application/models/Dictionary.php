<?php
/**
 * Created by PhpStorm.
 * User: v0id
 * Date: 20.12.2014
 * Time: 16:06
 */

class Application_Model_Dictionary {
 const  ORGANIZATION      ='organization',
        POSITION          ='position',
        DEPARTAMENT       ='departament',
        COUNTRY           ='country',
        REGIONS           ='regions',
        DISTRICTS         ='districts',
        //CITIES          ='',
        VILLAGES          ='villages',
        STATION           ='station',
        UNIQUECODE        ='uniquecode',
        OWNNAME           ='ownname',
        INSURCODE         ='insurcode',
        ORGTYPES          ='orgtypes',
        PRICE             ='price',
        VECHICLETYPE      ='vehicletype',
        SITNUMBER         ='sitnumber',
        INSURERRATE       ='insurerrate',
        SERVICETYPE       ='servicetype',
        TYPETICKETSPRICE  ='typeticketsprice',
        TYPELUGGAGEPRICE  ='typeluggageprice',
        BENEFITS          = 'benefits',
        RESERVES          = 'reserves',
        STATIONRATE       = 'stationrate',
        WITHDRAWALS       = 'withdrawals',
        CANCELREASON      = 'cancelreason',
        TARIFFPERKM       = 'tariffperkm',
        TARIFFMATRIX      = 'tariffmatrix';

 static private
   $_aTypes = array(
     self::ORGANIZATION     =>  array('type' =>'organization',    'name'  =>  'Організація',                          'childs'=>null,    "parent"=>null),
     self::POSITION         =>  array('type' =>'position',        'name'  =>  'Посада',                               'childs'=>null,    "parent"=>null),
     self::DEPARTAMENT      =>  array('type' =>'departament',     'name'  =>  'Відділ',                               'childs'=>null,    "parent"=>null),
     self::UNIQUECODE       =>  array('type' =>'uniquecode',      'name'  =>  'Унікальний код',                       'childs'=>null,    "parent"=>null),
     self::OWNNAME          =>  array('type' =>'ownname',         'name'  =>  'Назва',                                'childs'=>null,    "parent"=>null),
     self::INSURCODE        =>  array('type' =>'insurcode',       'name'  =>  'Код страхової компанії',               'childs'=>null,    "parent"=>null),
     self::ORGTYPES         =>  array('type' =>'orgtypes',        'name'  =>  'Тип юридичної особи'    ,              'childs'=>null,    "parent"=>null),
     self::PRICE            =>  array('type' =>'price',           'name'  =>  'Тип ціноутворення'     ,               'childs'=>null,    "parent"=>null),
     self::VECHICLETYPE     =>  array('type' =>'vehicletype',     'name'  =>  'Тип транспортного засобу',             'childs'=>null,    "parent"=>null),
     self::SITNUMBER        =>  array('type' =>'sitnumber',       'name'  =>  'Місць в автобусі',                     'childs'=>null,    "parent"=>null),
     self::INSURERRATE      =>  array('type' =>'insurerrate',     'name'  =>  'Страхові ставки',                      'childs'=>null,    "parent"=>null),
     self::COUNTRY          =>  array('type' =>'country',         'name'  =>  'Держава',                              'childs'=>array(self::REGIONS),    "parent"=>null),
     self::REGIONS          =>  array('type' =>'regions',         'name'  =>  'Області',                              'childs'=>array(self::DISTRICTS),    "parent"=>self::COUNTRY),
     self::DISTRICTS        =>  array('type' =>'districts',       'name'  =>  'Райони',                               'childs'=>array(self::VILLAGES,self::STATION),     "parent"=>self::REGIONS),
     self::VILLAGES         =>  array('type' =>'villages',        'name'  =>  'Села та селища',                       'childs'=>array(self::STATION),      "parent"=>self::DISTRICTS),
     self::STATION          =>  array('type' =>'station',         'name'  =>  'Зупинки',                              'childs'=>null,     "parent"=>self::VILLAGES),
     self::SERVICETYPE      =>  array('type' =>'servicetype',     'name'  =>  'Станційні послуги',                    'childs'=>null,     "parent"=>null),
     self::TYPETICKETSPRICE =>  array('type' =>'typeticketsprice','name'  =>  'Вид розрахунку вартості квитка',       'childs'=>null,     "parent"=>null),
     self::TYPELUGGAGEPRICE =>  array('type' =>'typeluggageprice','name'  =>  'Вид розрахунку вартості багаж',        'childs'=>null,     "parent"=>null),
     self::BENEFITS         =>  array('type' =>'benefits',        'name'  =>  'Вид пільг',                            'childs'=>null,     "parent"=>null),
     self::RESERVES         =>  array('type' =>'reserves',        'name'  =>  'Вид бронювання',                       'childs'=>null,     "parent"=>null),
     self::STATIONRATE      =>  array('type' =>'stationrate',     'name'  =>  'Типи рейсів',                          'childs'=>null,     "parent"=>null),
     self::WITHDRAWALS      =>  array('type' =>self::WITHDRAWALS, 'name'  =>  'Виведення коштів',                     'childs'=>null,     "parent"=>null),
     self::CANCELREASON     =>  array('type' =>self::CANCELREASON,'name'  =>  'Причина відміни рейсу',                'childs'=>null,     "parent"=>null),
     self::TARIFFPERKM      =>  array('type' =>self::TARIFFPERKM, 'name'  =>  'Тарифи за км',                          'childs'=>null,     "parent"=>null),
     self::TARIFFMATRIX     =>  array('type' =>self::TARIFFMATRIX,'name'  =>  'Тарифні сітки',                        'childs'=>null,     "parent"=>null)
   );


  static public function isValid($type){
     return (!empty(self::$_aTypes[$type]) )? true : false;
  }
  static public function getName($type){
      return self::isValid($type) ? self::$_aTypes[$type]['name'] : null;
  }

  static public function getParent($type){
        return self::isValid($type) ? self::$_aTypes[$type]['parent'] : null;
  }

  static public function getChilds($type,$asString=0){
        if(! self::isValid($type)) return null;

        if(!$asString) return self::$_aTypes[$type]['childs'];
        $sResult=""; $zp="";
        if(self::$_aTypes[$type]['childs']) foreach (self::$_aTypes[$type]['childs'] as $child){
           $sResult.=$zp."<a href=\"/[controller]/[action]/type/".$child."/par_id/[id]\">".self::$_aTypes[$child]['name']."</a>";
           $zp=",";
       }
      return $sResult;
  }

}



