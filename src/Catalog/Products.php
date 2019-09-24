<?php 

	namespace Atiksoftware\Opencart\Catalog;


	class Products extends \Atiksoftware\Opencart\Collection
	{ 


		function getList($where = []){ return $this->select($where); }
		function select($where = []){
			$products = $this->_select("product",$where);

			$cagoriesList = $this->oc->catalog->categories->getList();

			$product_attributes = $this->_select("product_attribute",$where);
			$product_descriptions = $this->_select("product_description",$where);
			$product_discounts = $this->_select("product_discount",$where);
			$product_filters = $this->_select("product_filter",$where);
			$product_images = $this->_select("product_image",$where);
			$product_options = $this->_select("product_option",$where);
			$product_option_values = $this->_select("product_option_value",$where);
			$product_recurrings = $this->_select("product_recurring",$where);
			$product_related = $this->_select("product_related",$where);
			$product_reward = $this->_select("product_reward",$where);
			$product_specials = $this->_select("product_special",$where);
			$product_to_categorys = $this->_select("product_to_category",$where);
			$product_to_downloads = $this->_select("product_to_download",$where);
			$product_to_layouts = $this->_select("product_to_layout",$where);
			$product_to_stores = $this->_select("product_to_store",$where);

			$taxs = $this->oc->localisation->tax->getList();

			foreach($products as &$product){
				$product["attributes"] = isset($product["attributes"]) ? $product["attributes"] : [];
				$product["discounts"] = isset($product["discounts"]) ? $product["discounts"] : [];
				$product["filters"] = isset($product["filters"]) ? $product["filters"] : [];
				$product["images"] = isset($product["images"]) ? $product["images"] : [];
				$product["options"] = isset($product["options"]) ? $product["options"] : [];
				$product["category_ids"] = isset($product["category_ids"]) ? $product["category_ids"] : [];
				$product["category_names"] = isset($product["category_names"]) ? $product["category_names"] : [];

				foreach($product_attributes as &$attribute){
					if($product["product_id"] == $attribute["product_id"]){
						$product["attributes"][] = [
							"attribute_id" => $attribute["attribute_id"],
							"text" => $attribute["text"],
						];
					}
				}

				foreach($product_descriptions as &$description){
					if($product["product_id"] == $description["product_id"]){
						$description["description"] = html_entity_decode($description["description"]);
						$product = array_merge($product,$description);
					}
				}

				foreach($product_discounts as &$discount){
					if($product["product_id"] == $discount["product_id"]){
						$product["discounts"][] = $discount;
					}
				}
				foreach($product_filters as &$filter){
					if($product["product_id"] == $filter["product_id"]){
						$product["filters"][] = $filter["filter_id"];
					}
				}
				$product["images"] = $product["image"] != "" ? [$product["image"]] : [];
				foreach($product_images as &$image){
					if($product["product_id"] == $image["product_id"]){
						$product["images"][] = $image["image"];
					}
				}
				foreach($product_to_categorys as &$product_to_category){
					if($product["product_id"] == $product_to_category["product_id"]){
						$product["category_ids"][] = $product_to_category["category_id"];
						foreach($cagoriesList as $cagoriesItem){
							if($product_to_category["category_id"] == $cagoriesItem["category_id"]){
								$product["category_names"][] = $cagoriesItem["displayName"];
							}
						}
					}
				}
				
				# Options
				foreach($product_options as &$product_option){
					$product_option["values"] = [];
					if($product["product_id"] == $product_option["product_id"]){
						foreach($product_option_values as &$product_option_value){
							if($product_option["product_option_id"] == $product_option_value["product_option_id"]){
								$product_option["values"][] = $product_option_value;
							}
						}
						$product["options"][] = $product_option;
					}
				}

				# Abonelik

				#Seo Url -> product_id=50
				$urls = $this->_select("seo_url",["query" => "product_id=".$product["product_id"]]);
				$product["seo_url"] = count($urls) ? $urls[0]["keyword"] : "";

				$product["kdv"] = "--";
				$product["price_total"] = $product["price"];
				foreach($taxs as $tax){
					if($product["tax_class_id"] == $tax["tax_class_id"]){
						$product["kdv"] = $tax["title"];
						$product["price_total"] = \number_format( ($product["price"] / 100 * (100 + (float)$tax["rate"])) , 2, ',','');
					}
				}
			}

			return $products;
		}

		function getInfoById($id){
			$items = $this->getList(["product_id" => $id]);
			if(count($items)){
				return $items[0]; 
			}
			return false;
		}
 
		function save($fields){  
			$itemFields = [
				"model" => @$fields["model"],
				"sku"  => isset($fields["sku"]) ? $fields["sku"] : "",
				"upc"  => isset($fields["upc"]) ? $fields["upc"] : "",
				"ean"  => isset($fields["ean"]) ? $fields["ean"] : "",
				"jan"  => isset($fields["jan"]) ? $fields["jan"] : "",
				"isbn" => isset($fields["isbn"]) ? $fields["isbn"] : "",
				"mpn"  => isset($fields["mpn"]) ? $fields["mpn"] : "",
				"location"  => isset($fields["location"]) ? $fields["location"] : "", 

				"quantity" => (int)@$fields["quantity"],
				"stock_status_id" => (int)@$fields["stock_status_id"],
				"image" => isset($fields["images"]) && count($fields["images"]) ? $fields["images"][0] : "",

				"manufacturer_id" => isset($fields["manufacturer_id"]) ? $fields["manufacturer_id"] : 0,

				"price" => @$fields["price"], 
				"tax_class_id" => @$fields["tax_class_id"],

				"status" => 1,
				"date_available" => date("2018-01-01 00:00:00"),
				"date_added" => date("Y-m-d H:i:s"),
				"date_modified" => date("Y-m-d H:i:s"),
			];
			if(isset($fields["product_id"])){
				$itemFields["product_id"] = $product_id = $fields["product_id"]; 
				$this->_remove("product",["product_id" => $product_id]);
				$this->_insert("product",$itemFields); 
			}
			else{
				$product_id = $this->_insert("product",$itemFields); 
			}

			file_put_contents("asd.json",json_encode($this->db->error,JSON_PRETTY_PRINT));

			$this->_remove("product_description",["product_id" => $product_id]);
			$this->_insert("product_description",[
				"product_id" => $product_id,
				"language_id" => $this->oc->localisation->language->getLanguageId(),
				"name" => @$fields["name"],
				"description" => isset($fields["description"]) ? htmlentities($fields["description"]) : @$fields["name"],
				"tag" => isset($fields["tag"]) ? (is_array($fields["tag"]) ? implode(",",$fields["tag"]) : $fields["tag"] ) : str_replace(" ",",",@$fields["name"]), 
				// "meta_title" => isset($fields["meta_title"]) ? $fields["meta_title"] : @$fields["name"],
				// "meta_description" => isset($fields["meta_description"]) ? $fields["meta_description"] : @$fields["name"],
				// "meta_keyword" => isset($fields["meta_keyword"]) ? $fields["meta_keyword"] : str_replace(" ",",",@$fields["name"]),
				"meta_title" => @$fields["name"],
				"meta_description" => @$fields["name"],
				"meta_keyword" => str_replace(" ",",",@$fields["name"])
			]); 


			$this->_remove("product_image",["product_id" => $product_id]);
			if(isset($fields["images"])){
				foreach($fields["images"] as $imageIndex => $image){
					$this->_insert("product_image",["product_id" => $product_id,"image" => $image,"sort_order" => $imageIndex]); 
				}
			}
			$this->_remove("seo_url",["query" => "product_id=".$product_id]);
			if(isset($fields["seo_url"])){
				$this->_insert("seo_url",[ 
					"store_id" => $this->oc->localisation->store->getStoreId(),
					"language_id" => $this->oc->localisation->language->getLanguageId(),
					"query" =>  "product_id=".$product_id,
					"keyword" => $fields["seo_url"]
				]); 
			}

			#Category
			$this->_remove("product_to_category",["product_id" => $product_id]);
			if(isset($fields["category_ids"])){
				foreach($fields["category_ids"] as $category_id){
					$this->_insert("product_to_category",["product_id" => $product_id,"category_id" => $category_id]); 
				}
			}
			# Layout
			$this->_remove("product_to_layout",["product_id" => $product_id]);
			$this->_insert("product_to_layout",["product_id" => $product_id,"store_id" => $this->oc->localisation->store->getStoreId(),"layout_id" => 0]); 
			# Store
			$this->_remove("product_to_store",["product_id" => $product_id]);
			$this->_insert("product_to_store",["product_id" => $product_id,"store_id" => $this->oc->localisation->store->getStoreId()]); 
		}  


		function removeById($id){
			 
		}

		    

	}

 