<?php 

	namespace Atiksoftware\Opencart;


	class Collection
	{
		public $oc;
		public $db;

		function __construct(&$parent){
			$this->oc = &$parent;
			$this->db = &$parent->db;
		}


		function _select($table = "", $where = [], $orderBy = [], $limit = 0, $skip = 0){
			$this->db->resetAll();
			$limit = $limit ? $limit : 100000; 
			$this->db->limit = "$skip,$limit"; 
			if(count($orderBy)){
				$this->db->orderBy($orderBy);
			}
			foreach($where as $k => $v){
				if(is_array($v)){
					if(count($v) > 2 ){
						$this->db->andOrOperator = "AND";
					} 
					for($i = 0; $i < count($v) ; $i++){
						$this->db->where( $k , $v[$i] , $v[$i + 1] );  
						$i++;
					}
					
				}else{
					$this->db->where( $k , $v ); 
				} 
			}  
			$results = $this->db->select( $this->oc->db_prefix.$table );
			return is_array($results) ? $results : [];
		}



		function _info($table = "", $where = [], $orderBy = [], $limit = 0, $skip = 0){
			$list = $this->select($where, $orderBy, $limit, $skip);
			return count($list) ? $list[0] : false;
		}



		function _insert($table = "", $fields){
			$this->db->resetAll();
			$this->db->insert( $this->oc->db_prefix.$table , $fields );
			return $this->db->lastInsertId;
		}


		function _update($table = "", $where, $fields, $isBulk = false){
			$this->db->resetAll();
			if($isBulk){
				$this->db->updateBatch( $this->oc->db_prefix.$table, $fields, $where); 
			} else { 
				foreach($where as $k => $v){
					$this->db->where( $k , $v ); 
				} 
				$this->db->update( $this->oc->db_prefix.$table , $fields );
			}
		}
		function _remove($table = "", $where){
			$this->db->resetAll();
			foreach($where as $k => $v){
				$this->db->where( $k , $v ); 
			} 
			$this->db->delete( $this->oc->db_prefix.$table ); 
		}

	}