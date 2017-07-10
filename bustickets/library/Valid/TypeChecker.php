<?php
require_once 'Zend/Validate/Abstract.php';
    class Valid_TypeChecker extends Zend_Validate_Abstract
    {
       const HAS_CHILD = 'hasChild';
     
        protected $_messageTemplates = array(
            self::HAS_CHILD => "На %title%  присутні посилання, спочатку видаліть їх, та зменіть типу юр.особи."
        );
        protected $_messageVariables = array(
            'title' => '_title'
        );
        protected $_title;
     
        public function isValid($value, $context = null)
        {
            if(!isset($context['id']) || (int)$context['id']==0) return true; //it is new record
            $this->_title = $context['title'];
            $value = (int)$value;
            $this->_setValue($value);

            $db = new Application_Model_DbTable_OrganizationList();
            $rOld = $db->fetchRow("id=".$value);
            $hasChild = $db->hasChild($value);
            if($hasChild && $rOld->type!=$context['type'] ){
                $this->_error(self::HAS_CHILD);
                return false;
            }
            return true;
        }

    }