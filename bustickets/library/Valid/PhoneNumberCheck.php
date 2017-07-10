<?php
require_once 'Zend/Validate/Abstract.php';
    class Valid_PhoneNumberCheck extends Zend_Validate_Abstract
    {
        const IS_EMPTY          = 'isEmpty',
              NOT_INTERNATIONAL = 'notInternational',
              IS_SHORT          = 'isShort';
     
        protected $_messageTemplates = array(
            self::IS_EMPTY          => "Value  can't be empty",
            self::NOT_INTERNATIONAL => "Phone number should be in full international format with \"+\" prefix - e.g. +61399874654",
            self::IS_SHORT          => "Phone number should be in full international format with \"+\" prefix - e.g. +61399874654",
        );
     
        public function isValid($value, $context = null)
        {
            $value = trim((string) $value);
            if(empty($value)){
                $this->_error(self::IS_EMPTY);
                return false;
            }
            if($value[0]!="+"){
                $this->_error(self::NOT_INTERNATIONAL);
                return false;
            }
            $newval="+";
            for($i=1;$i<strlen($value);$i++){
                if($value[$i]>="0" && $value[$i]<="9") $newval.=$value[$i];
            }
            if(strlen($newval)<9){
                $this->_error(self::IS_SHORT);
                return false;
            }

            $this->_setValue($newval);
            return true;
        }
    }