<?php 

	namespace Atiksoftware\Opencart\Localisation;


	class Tax extends \Atiksoftware\Opencart\Collection
	{ 

		function getList($where = []){ return $this->select($where); }
		function select($where = []){
			$tax_rules = $this->_select("tax_rule",$where);
			$tax_classs = $this->_select("tax_class",$where);
			$tax_rates = $this->_select("tax_rate",$where);
 
			 
			foreach($tax_rules as &$tax_rule){ 
				foreach($tax_classs as &$tax_class){
					if($tax_rule["tax_class_id"] == $tax_class["tax_class_id"]){
						$tax_rule = array_merge($tax_rule,$tax_class);
					}
				}
				foreach($tax_rates as &$tax_rate){
					if($tax_rule["tax_rate_id"] == $tax_rate["tax_rate_id"]){
						$tax_rule = array_merge($tax_rule,$tax_rate);
					}
				} 
			}

			return $tax_rules;
		}
 
		    

	}

 