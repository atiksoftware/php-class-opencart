<?php 

	namespace Atiksoftware\Opencart\Localisation;


	class Language extends \Atiksoftware\Opencart\Collection
	{ 
		
		public $languages = [];

	 
		function getLanguageId(){
			if(!count($this->languages)){
				$this->languages = $this->_select("language",[],["sort_order ASC"] ); 
			}  
			return $this->languages[0]["language_id"];
		}
 
		    

	}

 