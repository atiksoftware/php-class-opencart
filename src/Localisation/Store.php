<?php 

	namespace Atiksoftware\Opencart\Localisation;


	class Store extends \Atiksoftware\Opencart\Collection
	{ 
		
		public $stores = false;

	 
		function getStoreId(){
			if($this->stores === false){
				$this->stores = $this->_select("store",[]); 
			}  
			return $this->stores !== false && count($this->stores) ? $this->stores[0]["store_id"] : 0;
		}
 
		    

	}

 