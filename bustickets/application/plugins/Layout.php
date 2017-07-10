<?

class Application_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch()
    {

        $_oSession = Zend_Auth::getInstance()->getStorage()->read();
        $rolemenu = !empty($_oSession->perm_title) ? $_oSession->perm_title : 'guest';
        $layout = Zend_Layout::getMvcInstance();
        switch ($rolemenu) {
            case 'kassa':
                $layout->setLayout('layout');
                break;

            case 'normal':
                $layout->setLayout('layout');
                break;

            default:
                $layout->setLayout('layout');
                break;
        }
    }
}
?>