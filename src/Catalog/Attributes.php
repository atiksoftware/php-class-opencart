<?php 

	namespace Atiksoftware\Opencart\Catalog;


	class Attributes extends \Atiksoftware\Opencart\Collection
	{ 

		function getList($where = []){ return $this->select($where); }
		function select($where = []){
			$list_attribute_groups = $this->_select("attribute_group",$where);
			$list_attribute_group_descriptions = $this->_select("attribute_group_description",$where);
			$list_attributes = $this->_select("attribute",[]);
			$list_attribute_descriptions = $this->_select("attribute_description",[]);
			 
			foreach($list_attribute_groups as &$list_attribute_group){
				if(!isset($list_attribute_group["attributes"])){
					$list_attribute_group["attributes"] = [];
				}
				foreach($list_attribute_group_descriptions as &$list_attribute_group_description){
					if($list_attribute_group["attribute_group_id"] == $list_attribute_group_description["attribute_group_id"]){
						$list_attribute_group = array_merge($list_attribute_group,$list_attribute_group_description);
					}
				}
				foreach($list_attributes as &$list_attribute){
					 
					if($list_attribute_group["attribute_group_id"] == $list_attribute["attribute_group_id"]){
						foreach($list_attribute_descriptions as $list_attribute_description){
							if($list_attribute_description["attribute_id"] == $list_attribute["attribute_id"]){
								 
								$list_attribute = array_merge($list_attribute,$list_attribute_description);
							}
						} 
						$list_attribute_group["attributes"][] = $list_attribute;
					}
				}
			}

			return $list_attribute_groups;
		}

		function getInfoById($id){
			$items = $this->getList(["attribute_group_id" => $id]);
			if(count($items)){ 
				return $items[0]; 
			}
			return false;
		}
		function getInfoByName($attribute_group_name){
			$list = $this->_select("attribute_group_description",["name" => $attribute_group_name]); 
			if(count($list)){
				return $this->getInfoById($list[0]["attribute_group_id"]);
			}
			return false;
		}
 
		function setByName($attribute_group_name, $attribute_name){
			$attribute_group = $this->getInfoByName($attribute_group_name);
			if(!$attribute_group){ 
				$attribute_group_id = $this->addAttributeGroup($attribute_group_name);
				$attribute_group = $this->getInfoById($attribute_group_id);
			} 
			// print_r($attribute_group );
			// exit;
			foreach($attribute_group["attributes"] as $attribute){
				if($attribute["name"] == $attribute_name){
					return $attribute["attribute_id"];
				}
			}
			return $this->addAttributeToAttributeGroup($attribute_group["attribute_group_id"],$attribute_name); 
		}

		function addAttributeGroup($attribute_group_name){
			$attribute_group_id = $this->_insert("attribute_group",[
				"sort_order" => count($this->_select("attribute_group",[])) + 1
			]); 
			$this->_insert("attribute_group_description",[
				"attribute_group_id" => $attribute_group_id,
				"language_id" => $this->oc->localisation->language->getLanguageId(),
				"name" => $attribute_group_name ,
			]);
			return $attribute_group_id;
		}
		function addAttributeToAttributeGroup($attribute_group_id,$attribute_name){
			$attribute_id = $this->_insert("attribute",[
				"attribute_group_id" => $attribute_group_id,
				"sort_order" => count($this->_select("attribute",[
					"attribute_group_id" => $attribute_group_id
				])) + 1
			]);
			$this->_insert("attribute_description",[
				"attribute_id" => $attribute_id,
				"language_id" => $this->oc->localisation->language->getLanguageId(),
				"name" => $attribute_name ,
			]);
			return $attribute_id;
		}

 
		function removeByAll(){
			$this->_remove("attribute",[]);
			$this->_remove("attribute_description",[]);
			$this->_remove("attribute_group",[]);
			$this->_remove("attribute_group_description",[]);
		}

		    

	}

 