<?
use Govsource\Exceptions\IsEmptyException;

abstract class Application_Model_Abstract {
    //use Application_Model_Traits_Logger; // PHP 5.4

    /**
     * @deprecated
     * @var bool|array
     */
    protected $_data;
    /**
     * @var Zend_Db_Table_Row
     */
    protected $_row;
    protected $_dbTable;

    protected $_logger;

    /**
     * @return Application_Model_DbTable_Abstract
     */
    abstract function getDbTable();

    public function __construct($id = false)
    {
        if ($id) {
            $this->_load($id);
        }
    }

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * @return bool|integer
     */
    public function getId() {
        if (!is_object($this->_row) || !$primarykeys = $this->_row->getPrimaryKey()) {
            return false;
        }
        return array_shift($primarykeys);
    }


    /**
     *
     * @param string $key
     * @param mixed $default
     * @return bool|array
     */
    public function getData($key = '', $default = null)
    {
        if ($this->_row) {
            if ($key) return $this->_row->$key;
            return $this->_row->toArray();
        }
        if (empty($this->_data) || ($key && !isset($this->_data[$key]))) return false;
        if ($key) return ($this->_data[$key] ? $this->_data[$key] : $default);
        else return $this->_data;
    }

    /**
     * @param string $key
     * @return bool|array
     */
    public function toArray()
    {
        if (!is_object($this->_row)) throw new \Govsource\Exceptions\NotExistException('Row of the data does not exist.');
        return $this->_row->toArray();
    }

    /**
     * @return bool|mixed|Zend_Db_Table_Row
     */
    public function getRow()
    {
        if (!is_object($this->_row)) throw new \Govsource\Exceptions\NotExistException('Row of the data does not exist.');
        return $this->_row;
    }

    public function setData($data)
    {
        if (empty($data)) throw new IsEmptyException('Data var, pushed to model, is empty.');
        if (is_object($data)) {
            //echo get_class($data); exit;
            if (get_class($data) != $this->getDbTable()->getRowClass()) {
                throw new Exception_Internal('Incorrect object in setData. Must be ' . $this->getDbTable()->getRowClass());
            }
            $this->_row = $data;
            $this->_data = $this->_row->toArray();
            return $this;
        }

        if (!$this->_data && !$this->_row) {
            $this->_row = $this->getDbTable()->createRow($data);
            $this->_data = $data;
        }
        else {
            $this->_row->setFromArray($data);
            foreach ($data as $key => $value) {
                $this->_data[$key] = $value;
            }
        }
        return $this;
    }

    protected function getLogger()
    {
        if (!$this->_logger) {
            $this->_logger=Zend_Registry::get('Zend_Log');
        }
        return $this->_logger;
    }

    /**
     * @param $id
     * @throws Exception
     */
    protected function _load($id)
    {
        $column = $this->getDbTable()->getPrimaryColumn();
        if (!$column) $column = 'id';
        $this->_row = $this->getDbTable()->fetchRow($column . '=' . $id);
        if (empty($this->_row)) throw new \Govsource\Exceptions\NotExistException('Wrong id = '.$id);
        $this->_data = $this->_row->toArray();
    }
}