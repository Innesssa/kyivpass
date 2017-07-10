<?php
require_once 'Zend/Validate/Abstract.php';
    class Valid_PasswordConfirmation extends Zend_Validate_Abstract
    {
        const NOT_MATCH = 'notMatch';
     
        protected $_messageTemplates = array(
            self::NOT_MATCH => 'Пароль не співпадає с підтвердженням'
        );
     
        public function isValid($value, $context = null)
        {
            $value = (string) $value;
            $this->_setValue($value);
     
            if (is_array($context)) {
                if (isset($context['password'])
                    && ($value == $context['password']) && strlen($value)==strlen($context['password']))
                {
                    return true;
                }
            } elseif (is_string($context) && ($value == $context) && strlen($value)==strlen($context) ) {
                return true;
            }
     
            $this->_error(self::NOT_MATCH);
            return false;
        }

    }