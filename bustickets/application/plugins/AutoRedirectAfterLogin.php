<?php
class Application_Plugin_AutoRedirectAfterLogin extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();

        $controller = $request->getControllerName();
        $server = $request->getServer();

        if ($front->getDispatcher()->isDispatchable($request) && $controller != 'error' && $controller != 'cron') {
            // for redirect after login
            // check in loginAction && redirect in m.js
            if (!isset($server['HTTP_X_REQUESTED_WITH']) && isset($server['REQUEST_METHOD']) && $server['REQUEST_METHOD'] == 'GET' && !empty($server['REQUEST_URI'])
                && isset($server['HTTP_ACCEPT'])
                && strpos($server['HTTP_ACCEPT'], 'text/html') !== false
                && $server['REQUEST_URI'] != '/'
                && strpos($server['REQUEST_URI'], '/index') === false
                && strpos($server['REQUEST_URI'], '/admin') === false
                && strpos($server['REQUEST_URI'], '/adminalmtpl') === false
                && strpos($server['REQUEST_URI'], '/confirm') === false
                && strpos($server['REQUEST_URI'], '/forgot') === false
                && strpos($server['REQUEST_URI'], '/download') === false
                && strpos($server['REQUEST_URI'], 'project/ipopuplogin') === false
                && strpos($server['REQUEST_URI'], '/logout') === false
            ) {
                $namespace = new Zend_Session_Namespace('myRedirectData');
                $namespace->req = $server['REQUEST_URI'];
            }
        }
    }
}