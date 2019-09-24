<?php 

	namespace Atiksoftware\Opencart\Settings;


	class Stock extends \Atiksoftware\Opencart\Collection
	{ 

		function getList($where = []){ return $this->select($where); }
		function select($where = []){
			return $this->_select("stock_status",$where);  
		}
 
		    

	}

 