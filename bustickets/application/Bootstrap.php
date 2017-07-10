<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


    protected function setLayout($layout)
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout($layout);
    }
    protected function _initALL()
    {

        $acl = new Zend_Acl();
        $acl->addResource('index');
        $acl->addResource('error');
        $acl->addResource('dictionary');
        $acl->addResource('organization');
        $acl->addResource('conveyors');
        $acl->addResource('routes');
        $acl->addResource('tickets');
        $acl->addResource('races');
        $acl->addResource('users');
        $acl->addResource('reports');
        $acl->addResource('buhgalteria');
        $acl->addResource('benefits');
        $acl->addResource('points');
        $acl->addResource('tariff');
        $acl->addResource('kassa');
        $acl->addResource('serviceslist');
        $acl->addResource('security');


        $acl->addRole('guest');
        $acl->addRole('admin', 'guest');
        $acl->addRole('kassa', 'guest');
        $acl->addRole('dispatcher', 'guest');
        $acl->addRole('technologist', 'guest');
        $acl->addRole('booker', 'guest');
        $acl->addRole('dispbooker', 'guest');


        $acl->allow('guest', 'error', array('index', 'error'));
        $acl->allow('guest', 'index', array('login', 'newtoken','imhere'));
        $acl->allow('guest', 'tickets', array('store'));
        $acl->allow('guest', 'kassa', array('store'));

        $acl->allow('admin', 'index', array('index','logout', 'iamhere','time'));
        $acl->allow('admin', 'dictionary', array('index', 'refresh', 'edit', 'delete','search','reserves'));
        $acl->allow('admin', 'benefits', array('index', 'refresh', 'edit', 'delete','search'));
        $acl->allow('admin', 'tariff', array('index', 'refresh', 'edit', 'delete','edit-matrix'));
        $acl->allow('admin', 'organization', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('admin', 'conveyors', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('admin', 'routes', array('index', 'refresh', 'edit', 'delete','editschema','add-station','delete-station','search','indexschema','route-tariffs','edit-route-tariff','delete-route-tariff','busout','race-open','race-open-print','indexschemareadonly','show-history'));
        $acl->allow('admin', 'tickets', array('index', 'back','find-check','filter','show-tickets-form','lock')); /*,'refresh','edit','delete'*/
        $acl->allow('admin', 'users', array('index', 'refresh', 'edit', 'delete')); /*,'refresh','edit','delete'*/
        $acl->allow('admin', 'races', array('index')); /*,'refresh','edit','delete'*/
        $acl->allow('admin', 'reports', array(
                                                'index',
                                                'tickets',
                                                'kassa',
                                                'disp',
                                                'debet',
                                                'month',
                                                'list',
                                                'nextperiod',
                                                'failedraces',
                                                'allraces',
                                                'benefitstickets',
                                                'minustickets',
                                                'prevperiod'
                                            )
        ); /*,'refresh','edit','delete'*/
        $acl->allow('admin', 'buhgalteria', array('index')); /*,'refresh','edit','delete'*/
        $acl->allow('admin', 'points', array('index','filter'));
        $acl->allow('admin', 'kassa', array('index','save-operation'));
        $acl->allow('admin', 'serviceslist', array('index','refresh','save-operation'));
        $acl->allow('admin', 'security', array('index', 'refresh', 'edit', 'delete','search','reserves'));


        $acl->allow('kassa', 'index', array('index','logout', 'iamhere'));
        $acl->allow('kassa', 'dictionary', array('search'));
        $acl->allow('kassa', 'kassa', array('index','save-operation'));
        $acl->allow('kassa','routes',array('indexschemareadonly'));
        $acl->allow('kassa', 'tickets', array('index', 'back','find-check','filter','show-tickets-form','lock')); /*,'refresh','edit','delete'*/


        $acl->allow('dispatcher', 'index', array('index','logout', 'iamhere'));
        $acl->allow('dispatcher', 'routes', array('index', 'refresh', 'edit', 'delete','editschema','add-station','delete-station','search','indexschema','route-tariffs','edit-route-tariff','delete-route-tariff','busout','race-open','race-open-print','show-history'));
        $acl->allow('dispatcher', 'dictionary', array('search'));
        $acl->allow('dispatcher', 'tickets', array('index','filter','show-tickets-form'));

        $acl->allow('dispbooker', 'index', array('index','logout', 'iamhere'));
        $acl->allow('dispbooker', 'routes', array('index', 'refresh', 'edit', 'delete','editschema','add-station','delete-station','search','indexschema','route-tariffs','edit-route-tariff','delete-route-tariff','busout','race-open','race-open-print','show-history'));
        $acl->allow('dispbooker', 'dictionary', array('search'));
        $acl->allow('dispbooker', 'organization', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('dispbooker', 'buhgalteria', array('index')); /*,'refresh','edit','delete'*/
        $acl->allow('dispbooker', 'conveyors', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('dispbooker', 'reports', array(
                                                    'tickets',
                                                    'kassa',
                                                    'disp',
                                                    'debet',
                                                    'month',
                                                    'list',
                                                    'nextperiod',
                                                    'failedraces',
                                                    'allraces',
                                                    'benefitstickets',
                                                    'minustickets',
                                                    'prevperiod'
                                                )
        ); /*,'refresh','edit','delete'*/
        $acl->allow('dispbooker', 'tickets', array('index','filter','show-tickets-form'));




        $acl->allow('technologist', 'index', array('index','logout', 'iamhere'));
        $acl->allow('technologist', 'points', array('index','filter'));
        $acl->allow('technologist', 'users', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('technologist', 'routes', array('index', 'refresh', 'edit', 'delete','editschema','add-station','delete-station','search','indexschema','route-tariffs','edit-route-tariff','delete-route-tariff'));
        $acl->allow('technologist', 'dictionary', array('index', 'refresh', 'edit', 'delete','search','reserves'));
        $acl->allow('technologist', 'benefits', array('index', 'refresh', 'edit', 'delete','search'));
        $acl->allow('technologist', 'tariff', array('index', 'refresh', 'edit', 'delete','edit-matrix'));
        $acl->allow('technologist', 'conveyors', array('index', 'refresh', 'edit', 'delete'));

        $acl->allow('booker', 'index', array('index','logout', 'iamhere'));
        $acl->allow('booker', 'organization', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('booker', 'buhgalteria', array('index')); /*,'refresh','edit','delete'*/
        $acl->allow('booker', 'conveyors', array('index', 'refresh', 'edit', 'delete'));
        $acl->allow('booker', 'reports', array(
                                                'tickets',
                                                'kassa',
                                                'disp',
                                                'debet',
                                                'month',
                                                'list',
                                                'nextperiod',
                                                'failedraces',
                                                'allraces',
                                                'benefitstickets',
                                                'minustickets',
                                                'prevperiod'
                                            )
        ); /*,'refresh','edit','delete'*/
        $acl->allow('booker', 'dictionary', array('search'));


        // получаем экземпляр главного контроллера

        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_AccessCheck($acl, Zend_Auth::getInstance()));


        $this->bootstrapView();
        $view = $this->getResource('view');
        $_oSession = Zend_Auth::getInstance()->getStorage()->read();
        $rolemenu = !empty($_oSession->perm_title) ? $_oSession->perm_title : 'guest';
        $file = is_file(APPLICATION_PATH . '/scripts/menus/'.$rolemenu.'.yml') ? APPLICATION_PATH . '/scripts/menus/'.$rolemenu.'.yml' : APPLICATION_PATH . '/scripts/menus/empty.yml';


        $front = Zend_Controller_Front::getInstance();
        //$front->registerPlugin(new Application_Plugin_Layout());
        $front->registerPlugin(new Application_Plugin_ActionsLogger($acl, Zend_Auth::getInstance()));

        $config    = new Zend_Config_Yaml($file, 'nav');
        $container = new Zend_Navigation($config);
        // Передаємо контейнер в View
        $view->menu = $container;


        //timezone and time options
        //date_default_timezone_set('UTC');
        date_default_timezone_set('Europe/Kiev');
        //date_default_timezone_set(‘Europe/London’);

    }



}