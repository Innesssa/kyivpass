<?php



class Application_Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract
{
    private $_acl = null;
    private $_auth = null;

    /*
     * Инициализируем данные
     */
    public function __construct(Zend_Acl $acl, Zend_Auth $auth)
    {
        $this->_acl = $acl;
        $this->_auth = $auth;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // получаем имя текущего ресурса       
        $resource = $request->getControllerName();
        // получаем имя action
        $action = $request->getActionName();
        // получаем доступ к хранилищу данных Zend,
        // и достаём роль пользователя
        $identity = $this->_auth->getStorage()->read();
        // если в хранилище ничего нет, то значит мы имеем дело с гостем
        $role = !empty($identity->perm_title) ? $identity->perm_title : 'guest';

        Zend_Registry::set('sessuser',$identity);
        // если пользователь не допущен до данного ресурса,
        // то отсылаем его на страницу авторизации 
        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            //exit("AUTH STOP".$role." ".$resource." ".$action);
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) { echo "<script>document.location.href='/login';</script>"; exit;}
            header("Location: /login");
            exit();
        }

    }
}