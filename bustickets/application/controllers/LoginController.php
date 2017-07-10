<?php
class LoginController extends Zend_Controller_Action
{
    protected $options;
    public function init()
    {
        $this->_helper->layout->setLayout("login");
        $this->view->headScript()
            ->appendFile('/js/global/index.js');
        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $this->options = $bootstrap->getOptions();


    }


    public function indexAction(){
        $oUser = new Application_Model_DbTable_UserLoginData();
        //$ad = $oUser->getDefaultAdapter();
        //trace($ad->fetchAll("select * from userLoginData where login='qwerty' and password='qwerty' AND quantity_false <5 "),1);

        $this->view->errMessage="";
        if (!Zend_Auth::getInstance()->hasIdentity()){
            $this->_helper->layout->setLayout("login");
            $form = new Application_Form_LoginAdm();
            $this->view->form = $form;
            // Если к нам идёт Post запрос
            if ($this->getRequest()->isPost()) {
                // Принимаем его
                $formData = $this->getRequest()->getPost();
                // Если форма заполнена верно
                if ($form->isValid($formData)) {
                    $oUser = new Application_Model_DbTable_UserLoginData();
                    // Получаем адаптер подключения к базе данных
                    $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
                    // указываем таблицу, где необходимо искать данные о пользователях
                    // колонку, где искать имена пользователей,
                    // а также колонку, где хранятся пароли
                    $authAdapter->setTableName('userlogindata_view')
                        ->setIdentityColumn('login')
                        ->setCredentialColumn('password');
                        //->setCredentialTreatment('? AND quantity_false < '.$this->options['fail_login']);
                    // получаем введённые данные
                    $username = trim($this->getRequest()->getPost('login'));
                    $password =Application_Model_DbTable_UserLoginData::convertPassword($this->getRequest()->getPost('pwd'));

                    // подставляем полученные данные из формы
                    //exit("select * from  ".DBPREFIX."users where login='".$username."' AND  pass='".$password."'");
                    $authAdapter->setIdentity($username)->setCredential($password);


                    // получаем экземпляр Zend_Auth
                    $auth = Zend_Auth::getInstance();
                    // делаем попытку авторизировать пользователя
                    try{
                        $result = $auth->authenticate($authAdapter);
                    } catch (Zend_Auth_Adapter_Exception $ex) {
                        die($ex->getPrevious()->getMessage());
                    }
                    //trace($result,1);
                    // если авторизация прошла успешно
                    if ($result->isValid()) {
                        // используем адаптер для извлечения оставшихся данных о пользователе
                        $identity = $authAdapter->getResultRowObject();
                        // получаем доступ к хранилищу данных Zend
                        $authStorage = $auth->getStorage();
                        // помещаем туда информацию о пользователе,
                        // чтобы иметь к ним доступ при конфигурировании Acl
                        $authStorage->write($identity);
                        // используем библиотечный helper для редиректа
                        // на controller = index, action = index
                        //$action, $controller, $module, $params
                        $oUser->clearFalselLogin($username);
                        $this->_redirect("/");
                    } else {
                        $this->view->errMessage = 'Login failed.';
                        $oUser->falselLogin($username);//
                    }
                }
            }
        }else {
            Zend_Auth::getInstance()->clearIdentity();
            $this->_redirect("/");
        }

    }

    public function logoutAction(){
        // уничтожаем информацию об авторизации пользователя
        Zend_Auth::getInstance()->clearIdentity();
        // и отправляем его на главную
        $this->_redirect("/login/");
    }

}