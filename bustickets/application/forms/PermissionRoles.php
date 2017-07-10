<?php

class Application_Form_PermissionRoles extends Zend_Form
{

    public function init()
    {
        $this->setName('permissionroles');

        $elm = new Zend_Form_Element_Hidden('id');
        $elm->addFilter('int');
        $elm->setValue(0);
        $this->addElement($elm);


        $elm = new Zend_Form_Element_Text('title');
        $elm->setRequired(true)
            ->setLabel('Назва : ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')  
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' =>  'Введіть назву')))
            ->addValidator('Db_NoRecordExists', true, array(
                'table'     => 'permissionroles',
                'field'     => 'title',
                'messages' => array(
                    Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => 'Роль з таким ім\'ям вже існує')
            )) ;
        $elm->setAttrib("class", "edit large");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Textarea('description');
        $elm->setLabel('Опис :')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $elm->setAttrib("class", "textbox middle");
        $this->addElement($elm);

        $elm = new Zend_Form_Element_Submit('submit');
        $elm->setAttrib("class", "btn btn-primary");
        $elm->setLabel('Send');
        $this->addElement($elm);
        $this->setMethod('post');
    }


}

