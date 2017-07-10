<?php

/**
 * Class Application_Model_DbTable_UserLoginData
 */
class Application_Model_DbTable_Dictionary extends Application_Model_DbTable_Abstract
{
    protected $_name = 'handbooks';
    protected static $_instance;
    protected static $_fullName = '';
    protected $_fields = array("id","par_id","type","title","description","additional_text");
    protected $_elm = array();


    protected function _setupTableName()
    {
        self::$_fullName = $this->_name;
    }

    public static function getTableName()
    {
        if (self::$_fullName != '') {
            return self::$_fullName;
        }
        // проверяем актуальность экземпляра
        if (null === self::$_instance) {
            // создаем новый экземпляр
            self::$_instance = new self();
        }
        // возвращаем значение созданного или существующего экземпляра
        return self::$_instance->getTableName();
    }
    public function selectDataByType($type,$par_id=0){
        if( Application_Model_Dictionary::isValid($type)  ){
            return $this->fetchAll("\"type\"='" . addslashes($type)."' and par_id=".(int)$par_id  , array("title ASC"));
        }else{
            die("00001:".$type);
        }

    }
    public function setData(Array $data){
        $toSave = array();
        if(!isset($data['id'])) $data['id']=0;
        if(!isset($data['par_id'])) $data['par_id']=0;
        if(!isset($data['type']) || !Application_Model_Dictionary::isValid($data['type'])  ) {
            die("00001:". (isset($data['type'])? $data['type'] : ""));
        }


        foreach($this->_fields as $field){
            if($field!="id" && isset($data[$field])){
                $toSave[$field] = $data[$field];
            }
        }
        if(count($toSave)==0) return false;
        if($data['id']>0){
            $this->update($toSave,"id=".$data['id']." AND par_id=".$data['par_id']);
            return $data['id'];
        }
        return $this->insert($toSave);

    }
    public function selectChildsByID($id){
        return $this->fetchAll("par_id=".(int)$id );
    }
    public function  getPath($par_id,$level=0) {
        if ($par_id) {
            $rp = $this->fetchRow("id=" . $par_id);
            $this->_elm[$level] = array("title"=>$rp->title,"type"=>$rp->type,"par_id"=>$rp->par_id,"id"=>$rp->id);
            $level++;
            if($rp->par_id) $this->getPath($rp->par_id,$level);
        }
        return $this->_elm;
    }

    public function searchItems($q,$view){

        $select = $this->getAdapter()->select();
        $select->from($view,array("title","id","description"));
        $select->where("title ilike '".addslashes($q)."%' ")
               ->order("title ASC")->limit(20);
        return  $this->getAdapter()->fetchAll($select);

    }

    public function getFullPath($id,$type){
        $select = $this->getAdapter()->select();
        $select->from(array("g"=>$this->_name),array("title","id"));
        $tp = $type;
        $i=1;
        $pref="g";
        while($tp){
            $tp = Application_Model_Dictionary::getParent($tp);
            if($tp){
                $select->join(array(('a'.$i)=>$this->_name)  ,('a'.$i).'.id = '.$pref.'.par_id'      ,array('a'.$i.'_title'=>'title'));
                $pref='a'.$i;
                $i++;
            }
        }
        $select->where("g.id = ".(int)$id." and g.type='".addslashes($type)."'");
        $r = $this->getAdapter()->fetchRow($select);
        $aResult = array();
        if( $r ){
            $name=$r['title'];
            for($k=1;$k<$i;$k++){
                $name.= "/".$r['a'.$k.'_title'];
            }
            $aResult[]=array("name"=>$name,"id"=>$r['id']);
        }
        return $aResult;
    }

    // TODO Временная функция, которую потом можно будет удалить
    public function getItemTitle($id){
        $select = $this->getAdapter()->select();
        $select->from(array("g"=>$this->_name),array("title","id"))
               ->where('id='.$id);
        return $this->getAdapter()->fetchRow($select);
    }

    public function getItemTitleOnly($id){
        $r = $this->getItemTitle($id);
        return ($r['title']) ? $r['title'] : "";
    }


/*
    public function getStationList(){
        $_name_ent='villages';
        $select = $this->getAdapter()->select();

        $select->from (array("h1"=>$this->_name),  array("id", "title"))
            //->joinLeft(array('h2'=>'handbooks'),  'h2.id = h1.par_id',  array('district_name'=>'title'))
            //->joinleft(array("h3"=>'handbooks'),  'h3.id = h2.par_id',  array('regions_name'=>'title'))
            ->where('type=?',$_name_ent)
        ;

        $page =(!empty($aFilter['page']) && $aFilter['page']>0 ) ? (int)$aFilter['page'] : 1;
        if(!empty($aFilter['search_name']))  $select->where('title like "'.addslashes(trim($aFilter['search_title'])).'%"');
        $sort = 'h1.title';
        $order = 'ASC';
        if(isset($aFilter['sort'])){
            if(in_array($aFilter['sort'],$this->_fields)){
                $sort = $aFilter['sort'];
                $order = $aFilter['order'] && $aFilter['order']=='desc'? 'DESC' : 'ASC';
            }
        }
        $select->order($sort." ".$order);

        $stationlist = $this->fetchAll();

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(25);
        $paginator->setPageRange(10);

        return $stationlist;
    }
*/

    public function selectStationList($type){

        if( Application_Model_Dictionary::isValid($type)  ){
            $select = $this->getAdapter()->select();
            $select->from(array("h1"=>$this->_name),array("id","title"))
                   ->joinLeft(array('h2'=>'handbooks'),  'h2.id = h1.par_id',  array('village_name'=>'title'))
                   ->joinleft(array("h3"=>'handbooks'),  'h3.id = h2.par_id',  array('district_name'=>'title','regions_name'=>'(select title from handbooks where id=h3.par_id)'))
//                ->joinLeft(array('h2'=>'handbooks'),  'h2.id = h1.par_id',  array('district_name'=>'title'))
//                ->joinleft(array("h3"=>'handbooks'),  'h3.id = h2.par_id',  array('regions_name'=>'title'))
                   ->where("h1.type='" . addslashes("station") . "'")
                   //->limit(10) // TODO: delete debugging
                   ->order("h1.title ASC");

            $rs=$this->getAdapter()->fetchAll($select);
            $aResult = array();
            foreach($rs as $r){
                $name=$r['title'] . ", " . $r['village_name'] . ", " . $r['district_name'] . ", " . $r['regions_name'];
                $aResult[]=array("title"=>$name,"id"=>$r['id']);
            }
            //Zend_Debug::dump($aResult);
            return $aResult;

            //return $this->fetchAll("\"type\"='" . addslashes($type) . "'", array("title ASC"));
        }else{
            die("00001:".$type);
        }


    }





}