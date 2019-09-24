<?php 

	namespace Atiksoftware\Opencart\Catalog;


	class Options extends \Atiksoftware\Opencart\Collection
	{ 

		function getList($where = []){ return $this->select($where); }
		function select($where = []){
			$options = $this->_select("option",$where);
			$option_descriptions = $this->_select("option_description",$where);
			$option_values = $this->_select("option_value",$where);
			$option_value_descriptions = $this->_select("option_value_description",$where); 
			 
			foreach($options as &$option){
				if(!isset($list_attribute_group["values"])){
					$list_attribute_group["values"] = [];
				}
				foreach($option_descriptions as &$option_description){
					if($option["option_id"] == $option_description["option_id"]){
						$option = array_merge($option,$option_description);
					}
				}
				foreach($option_values as &$option_value){ 
					if($option["option_id"] == $option_value["option_id"]){
						foreach($option_value_descriptions as $option_value_description){
							if($option_value["option_value_id"] == $option_value_description["option_value_id"]){
								 
								$option_value = array_merge($option_value,$option_value_description);
							}
						}  
						$option["values"][] = $option_value;
					}
				}
			}

			return $options;
		}

		function getOptionById($id){
			$items = $this->getList(["option_id" => $id]);
			if(count($items)){ 
				return $items[0]; 
			}
			return false;
		}
		function getOptionByName($option_name){
			$list = $this->_select("option_description",["name" => $option_name]); 
			if(count($list)){
				return $this->getOptionById($list[0]["option_id"]);
			}
			return false;
		}
 
		function checkByname($option_name, $value_name){
			$option = $this->getOptionByName($option_name);
 
			if(!$option){ 
				$option_id = $this->addOption($option_name);
				$option = $this->getOptionById($option_id);
			}   
			foreach($option["values"] as $value){
				if($value["name"] == $value_name){
					return $value["option_value_id"];
				}
			}
			return $this->addValueToOption($option["option_id"],$value_name); 
		}

		function addOption($option_name){
			$option_id = $this->_insert("option",[
				"type" => "select",
				"sort_order" => count($this->_select("option",[])) + 1
			]); 
			$this->_insert("option_description",[
				"option_id" => $option_id,
				"language_id" => $this->oc->localisation->language->getLanguageId(),
				"name" => $option_name ,
			]);
			return $option_id;
		}
		function addValueToOption($option_id,$value_name){
			$option_value_id = $this->_insert("option_value",[
				"option_id" => $option_id,
				"sort_order" => count($this->_select("option_value",[
					"option_id" => $option_id
				])) + 1,
				"image" => ""
			]);
			$this->_insert("option_value_description",[
				"option_value_id" => $option_value_id,
				"option_id" => $option_id,
				"language_id" => $this->oc->localisation->language->getLanguageId(),
				"name" => $value_name ,
			]);
			return $option_value_id;
		}

 
		function removeByAll(){
			$this->_remove("option",[]);
			$this->_remove("option_description",[]);
			$this->_remove("option_value",[]);
			$this->_remove("option_value_description",[]);
		}

		    

	}

 