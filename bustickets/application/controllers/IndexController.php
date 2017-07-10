<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {

        $this->view->target_URL_path = "/uimgs/";
        $this->view->target_path = realpath(PUBLIC_PATH . "/uimgs/") . "/";
        $this->aExt = array("png", "gif", "jpeg", "jpg");

        $this->_helper->contextSwitch()
            ->addActionContext('imhere', array('json'))
            ->initContext();

        $this->view->headScript()
            ->appendFile('/js/global/index.js');

        $this->view->headLink()
            ->appendStylesheet('/js/libs/tablesorter/themes/blue/style.css');


    }
    public function preDispatch()
    {
        $this->view->idactiv = 'ID' . $this->getRequest()->getActionName();
    }

    public function indexAction(){
        $_oSession = Zend_Auth::getInstance()->getStorage()->read();
        if($_oSession->perm_title=='kassa') {
            $this->_redirect('/kassa/');
            exit;
        }

    }


    public function loginAction(){

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

                        // Получаем адаптер подключения к базе данных
                        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
                        //$select = $authAdapter->getDbSelect();

                        // указываем таблицу, где необходимо искать данные о пользователях
                        // колонку, где искать имена пользователей,
                        // а также колонку, где хранятся пароли
                        $authAdapter->setTableName('userlogindata_view')
                        ->setIdentityColumn('login')
                        ->setCredentialColumn('password');
                        //->setCredentialTreatment('? AND quantity_false < 3');

                        // получаем введённые данные
                        $username = trim($this->getRequest()->getPost('login'));
                        $password = Application_Model_DbTable_UserLoginData::convertPassword($this->getRequest()->getPost('pwd'));
                        //trace( $this->getRequest()->getPost('pwd').":".$password,1);
                         // подставляем полученные данные из формы
                        //exit("select * from  ".DBPREFIX."users where login='".$username."' AND  pass='".$password."'");
                    $authAdapter->setIdentity($username)->setCredential($password);
                    //exit( $authAdapter->getDbSelect());

                        // получаем экземпляр Zend_Auth
                    $auth = Zend_Auth::getInstance();
                        // делаем попытку авторизировать пользователя
                    try{
                    $result = $auth->authenticate($authAdapter);
                    //echo "SQL:".$authAdapter->getDbSelect();
                    //trace("RESULT");
                    //trace($result,1);
                    } catch (Zend_Auth_Adapter_Exception $ex) {
                        die($ex->getPrevious()->getMessage());
                    }
                    //trace($result,1);
                    // если авторизация прошла успешно
                    $oUser = new Application_Model_DbTable_UserLoginData();
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


    public function imhereAction(){
        $auth = Zend_Auth::getInstance();
        $oSession = $auth->getStorage()->read();
        $result = array();
        if(!empty($oSession->id)) $result['SUCCESS'] = true;
        else                      $result['FAIL'] = true;
        $this->_helper->json->sendJson($result);
    }


    public function timeAction(){
        $data = $this->getRequest()->getParam("dt");
        $this->view->answer = $data ? strtotime($data) : time();
        $this->view->request = ($data) ? $data : date("Y-m-d H:i:s");
           $data = array("controller"=>"tickets",
               "action"=>"lock",
               "module"=>"default",
               "place"=>"18",
               "raceid"=>"35",
               "routeid"=>"35",
               "acname"=>"AC PODIL",
               "prepaid"=>"3.33",
               "paidfromother"=>"0.00",
               "station_buy"=>"1306",
               "from_id"=>"1306",
               "to_id"=>"893",
               "kassauid"=>"21",
               "kassaname"=>"0358",
               "code"=>"5072",
               "title"=>"\u041a\u0438\u0457\u0432 \u041f\u043e\u0434\u043e\u043b - \u0411\u043e\u0433\u0443\u0441\u043b\u0430\u0432 ",
               "dt_start"=>"15.05.2015 16:05:00",
               "dt_finish"=>"15.05.2015 19:05:00",
               "dt_time_begin"=>"2015-05-15 16:05:00",
               "dt_time_finish"=>"2015-05-15 19:05:00",
               "conv_title"=>"\u041f\u041f \"\u041c\u0435\u0440\u0435\u0436\u0430-\u0410\u0412\u0422\u041e\" 00963;09700, \u041a\u0438\u0457\u0432\u0441\u044c\u043a\u0430 \u043e\u0431\u043b.;\u043c.\u0411\u043e\u0433\u0443\u0441\u043b\u0430\u0432; \u0432\u0443\u043b.\u041c\u0438\u043a\u043e\u043b\u0430\u0457\u0432\u0441\u044c\u043a\u0430, 129-\u0410; \u0442\u0435\u043b.04561 5-13-76; \u0415\u0414\u0420\u041f\u041e\u0423 36215172",
               "insurer_title"=>"102000 \u0433\u0440\u043d.;\u041f\u0440\u0410\u0422 \"\u0404\u0432\u0440\u043e\u043f\u0435\u0439\u0441\u044c\u043a\u0438\u0439 \u0441\u0442\u0440\u0430\u0445\u043e\u0432\u0438\u0439 \u0430\u043b\u044c\u044f\u043d\u0441\", \u043c.\u041a\u0438\u0457\u0432,;\u0432\u0443\u043b.\u042f\u043c\u0441\u044c\u043a\u0430,28;\u0442.(044) 353-58-01;\u0415\u0414\u0420\u041f\u041e\u0423 19411125",
               "conv_id"=>"14",
               "insurer_id"=>"56",
               "vehiclename"=>"\u0411\u041e\u0413\u0414\u0410\u041d(27)",
               "vehicle_id"=>"1342",
               "from"=>"\u041a\u0418\u0407\u0412 \u041f\u043e\u0434\u0456\u043b \u0410\u0421",
               "to"=>"\u0427\u0430\u0439\u043a\u0438 \u0441.",
               "platform"=>"0",
               "benefits_title"=>"",
               "benefits_percent"=>"0",
               "benefits_docnum"=>"",
               "benefits_name"=>"",
               "benefits_id"=>"0",
               "price_tariff"=>"43.75",
               "price_tariff_with_benefits"=>"43.75",
               "conv_tariff"=>"39.38",
               "stat_tariff"=>"4.37",
               "conv_tariff_with_benefits"=>"39.38",
               "stat_tariff_with_benefits"=>"4.37",
               "conveyor_vat"=>"0",
               "gov_vat"=>"20",
               "conv_tariff_vat"=>"0.00",
               "stat_tariff_vat"=>"0.87",
               "conv_tariff_with_benefits_vat"=>"0.00",
               "stat_tariff_with_benefits_vat"=>"0.87",
               "insurer_percent"=>"1.5",
               "insurer_tariff"=>"0.66",
               "insurer_tariff_with_benefits"=>"0.66",
               "station_tax"=>"15",
               "station_tax_tariff"=>"6.66",
               "station_tax_tariff_with_benefits"=>"6.66",
               "station_tax_tariff_vat"=>"1.33",
               "station_tax_tariff_with_benefits_vat"=>"1.33",
               "luggage_price"=>"5",
               "conv_luggage_full_price"=>"4.50",
               "stat_luggage_full_price"=>"0.60",
               "luggage_count"=>"0",
               "luggage_total"=>"0.00",
               "conv_luggage_tariff"=>"0.00",
               "stat_luggage_tariff"=>"0.00",
               "conv_luggage_tariff_vat"=>"0.00",
               "stat_luggage_tariff_vat"=>"0.00",
               "prepaid_vat"=>"0.67",
               "paidfromother_vat"=>"0.00",
               "full_price_with_benefits"=>"54.40",
               "full_price"=>"54.40",
               "full_price_vat"=>"2.87",
               "full_price_with_benefits_vat"=>"2.87",
               "total_price"=>"57.27",
               "total_price_with_benefits"=>"57.27",
               "status"=>"paid",
               "command"=>"TicketSale",
               "success"=>"true",
               "message"=>"success",
               "typeppo"=>"1",
               "operation"=>"TicketSale",
               "num"=>"0",
               "amount"=>"57.27",
               "date"=>"2015-05-12 17:32:00",
               "user"=>"0358",
               "ppo"=>"1140032316",
               "checknumber"=>"0000036841",
               "serviceid"=>"",
               "vat"=>"0",
               "price"=>"0",
               "description"=>"",
               "symmcass_n1"=>"00000000000",
               "symmcass_n2"=>"00000020633",
               "symmcass_n3"=>"00000180000",
               "symmcass_n4"=>"00000230684",
               "symmcass_n5"=>"00000000000",
               "symmcass_n6"=>"00000071317",
               "symmcass_n7"=>"00000000000",
               "symmcass_n8"=>"00000000000",
               "symmcass_k1"=>"00000000000",
               "symmcass_k2"=>"00000020633",
               "symmcass_k3"=>"00000180000",
               "symmcass_k4"=>"00000236411",
               "symmcass_k5"=>"00000000000",
               "symmcass_k6"=>"00000077044",
               "symmcass_k7"=>"00000000000",
               "symmcass_k8"=>"00000000000",
               "nomfromfisc01"=>"0000036841",
               "nomfromfisc02"=>"0000025066",
               "nomfromfisc03"=>"0000025069",
               "nomfromfisc04"=>"0",
               "nomfromfisc05"=>"0",
               "nomfromfisc06"=>"0",
               "nomfromfisc07"=>"0939"
           );

    }




}