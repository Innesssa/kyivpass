<?php
class Application_Plugin_ActionsLogger extends Zend_Controller_Plugin_Abstract
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

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // получаем имя текущего ресурса       
        $resource = $request->getControllerName();
        $action = $request->getActionName();
        if($action!="imhere") {
            $identity = $this->_auth->getStorage()->read();
            $params = $request->getParams();
            try {
                $db = new Application_Model_DbTable_UserEventsSysLog();
                $db->insert(array(
                    'uid' => empty($identity->id) ? 0 : $identity->id,
                    'dt' => date("Y-m-d H:i:s"),
                    'permissions' => !empty($identity->perm_title) ? $identity->perm_title : 'guest',
                    'request' => json_encode(isset($params['request']) ? $params['request'] : $params),
                    'answer' => json_encode(isset($params['answer']) ? $params['answer'] : ''),
                    'controller' => $resource,
                    'action' => $action
                ));
            } catch (Exception $e) {
                // nothing to do
            }
        }
        /*
        if (strpos($params, 'SELECT ')
            || strpos($params, 'INSERT ')
            || strpos($params, 'UPDATE ')
            || strpos($params, 'DELETE ')
            || strpos($params, '0x')
            || strpos($params, 'user_privileges')
        ) {
            Zend_Registry::get('Zend_Log')
                ->setEventItem('trace', $params)
                ->log('Security breaking alert. User:' . (!empty($identity->login) ? $identity->login . '/' : '') . $role, Zend_Log::EMERG );
        }
        */
    }
}