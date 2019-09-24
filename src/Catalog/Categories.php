<?php 

	namespace Atiksoftware\Opencart\Catalog;


	class Categories extends \Atiksoftware\Opencart\Collection
	{ 

		function getList($where = []){ 
			$list = $this->select($where); 
			foreach($list as &$item){
				$parent_id = $item["parent_id"];
				if(!isset($item["displayName"])){
					$item["displayName"] = $item["name"];
				}
				while ($parent_id > 0) {
					foreach($list as &$row){
						if($row["category_id"] == $parent_id){
							$item["displayName"] = $row["name"]." > ".$item["displayName"];
							$parent_id = $row["parent_id"];
						}
					}
				}
			}
			return $list;
		}
		function select($where = []){
			$list_categories = (array)$this->_select("category",$where);

			$list_categories_descriptions = (array)$this->_select("category_description",$where);
			$list_category_to_stores = (array)$this->_select("category_to_store",$where);
			$list_category_to_layouts = (array)$this->_select("category_to_layout",$where);
			$list_category_paths = (array)$this->_select("category_path",$where);

			foreach($list_categories as &$list_categorie){
				$list_categorie["name"] = "isimsiz";
				$list_categorie["meta_title"] = "isimsiz";
				foreach($list_categories_descriptions as &$list_categories_description){
					if($list_categorie["category_id"] == $list_categories_description["category_id"]){
						$list_categorie = array_merge($list_categorie,$list_categories_description);
					}
				}
				foreach($list_category_to_stores as &$list_category_to_store){
					if($list_categorie["category_id"] == $list_category_to_store["category_id"]){
						$list_categorie = array_merge($list_categorie,$list_category_to_store);
					}
				}
				foreach($list_category_to_layouts as &$list_category_to_layout){
					if($list_categorie["category_id"] == $list_category_to_layout["category_id"]){
						$list_categorie = array_merge($list_categorie,$list_category_to_layout);
					}
				}
				foreach($list_category_paths as &$list_category_path){
					if($list_categorie["category_id"] == $list_category_path["category_id"]){
						$list_categorie = array_merge($list_categorie,$list_category_path);
					}
				}
			}
			// print_r($list_categories);
			// exit; 
			return $list_categories;
		}

		function getInfoById($id){
			$items = $this->getList(["category_id" => $id]);
			if(count($items)){
				return $items[0]; 
			}
			return false;
		}
 
		function add($fields){
			$parent_id = isset($fields["parent_id"]) && (int)@$fields["parent_id"] ? $fields["parent_id"] : 0;
			$itemFields = [
				"image" => @$fields["image"],
				"parent_id" => $parent_id,
				"top" => 0,
				"column" => 1,
				"sort_order" => 0,
				"status" => 1,
				"date_added" => date("Y-m-d H:i:s"),
				"date_modified" => date("Y-m-d H:i:s"),
			];
			if(isset($fields["category_id"])){
				$category_id = $itemFields["category_id"] = $fields["category_id"];
				$this->_insert("category",$itemFields); 
			}else{
				$category_id = $this->_insert("category",$itemFields); 
			} 
			$this->_insert("category_description",[
				"category_id" => $category_id, 
				"language_id" => $this->oc->localisation->language->getLanguageId(), 
				"name" => $fields["name"], 
				"description" => @$fields["description"] ? $fields["description"] : $fields["name"], 
				"meta_title" => @$fields["meta_title"] ? $fields["meta_title"] : $fields["name"], 
				"meta_description" => @$fields["meta_description"] ? $fields["meta_description"] : $fields["name"] , 
				"meta_keyword" => isset($fields["meta_keyword"]) ? (is_array($fields["meta_keyword"]) ? implode(",",$fields["meta_keyword"]) : $fields["meta_keyword"])  : str_replace(" ",",",strtolower($fields["name"])), 
			]);
			$this->_insert("category_to_store",[
				"category_id" => $category_id, 
				"store_id" => $this->oc->localisation->language->getStoreId()
			]);
			$this->_insert("category_to_layout",[
				"category_id" => $category_id, 
				"store_id" => $this->oc->localisation->language->getStoreId(),
				"layout_id" => 0,
			]);

			/** Kategori Path Bilgileri */
			$path_level = 0;
			$paths = $this->_select("category_path",["category_id" => $parent_id],["level ASC"]);
			foreach($paths as $path){
				$this->_insert("category_path",[ "category_id" => $category_id, "path_id" => $path["path_id"], "level" => $path_level ]);
				$path_level++;
			}
			$this->_insert("category_path",[ "category_id" => $category_id, "path_id" => $category_id, "level" => $path_level ]);

			return $category_id;
		}

		function update($category_id,$fields){
			$item = $this->getInfoById($category_id);

			$fields = array_merge($item,$fields);

			$this->removeById($category_id);

			$this->add($fields); 

			return $category_id;
		}


		function removeById($id){
			$this->_remove("category",["category_id" => $id]);
			$this->_remove("category_description",["category_id" => $id]);
			$this->_remove("category_filter",["category_id" => $id]);
			$this->_remove("category_to_store",["category_id" => $id]);
			$this->_remove("category_to_layout",["category_id" => $id]);
			$this->_remove("product_to_category",["category_id" => $id]);
			$this->_remove("seo_url",["category_id" => $id]);
			$this->_remove("coupon_category",["category_id" => $id]);
		}

		    

	}

 