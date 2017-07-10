<?php

/**
 * Class Application_Model_DbTable_UserLoginData
 */
class Application_Model_DbTable_Kassa extends Application_Model_DbTable_Abstract
{

    protected $ststionID = 1306;
    protected $_name = 'kassaoperation';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_primary = 'id'; // primary column name
    protected $_fields = array(
                               "kassauid",/*	Ідентифікатор касира */
                               "operation", /* Операція/дія */
                               "amount", /*Кошти*/
                               "dt", /*Дата*/
                               "usr",/* Користувач/касир з касового апарату*/
                               "ppo", /* номер касового апарату*/
                               "serviceid", /* номер доп послуги */
                               "price", /* ціна за один доп послуги */
                               "vat", /* ПДВ за один доп послуги */
                               "num", /* кількість доп послуг */
                               "description", /* коментар до доп послуг */
                               "ststionid", /* id станції*/
                               "symmcass_n1",
                               "symmcass_n2",
                               "symmcass_n3",
                               "symmcass_n4",
                               "symmcass_n5",
                               "symmcass_n6",
                               "symmcass_n7",
                               "symmcass_n8",
                               "symmcass_k1",
                               "symmcass_k2",
                               "symmcass_k3",
                               "symmcass_k4",
                               "symmcass_k5",
                               "symmcass_k6",
                               "symmcass_k7",
                               "symmcass_k8",
                               "nomfromfisc01",
                               "nomfromfisc02",
                               "nomfromfisc03",
                               "nomfromfisc04",
                               "nomfromfisc05",
                               "nomfromfisc06",
                               "nomfromfisc07",
                               "typeppo"
                            );

    public function setData(Array $data){
        $toSave = array();

        foreach($this->_fields as $field){
             if($field!="id" && isset($data[$field])){
                $toSave[$field] = urldecode($data[$field]);
            }
        }
        if ( empty($data['vat']))   $toSave['vat'] = 0; else  $toSave['vat']=sprintf("%.2f",$data['vat']);
        if ( empty($data['serviceid']))   $toSave['serviceid'] = 0; else  $toSave['serviceid']=(int)$data['serviceid'];

        if(count($toSave)) {
            $toSave['stationid']=$this->ststionID;
            //trace($toSave);
            try {
                return array("success"=>true,"id"=>$this->insert($toSave));
            } catch (Exception $e) {
                return array("error"=>$e->getCode(),"message"=> $e->getMessage());
            }
        }
        return  array("error"=>"002","message"=> "Відсутні данні для запису");
    }

}