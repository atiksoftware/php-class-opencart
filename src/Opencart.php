<?php 


	namespace Atiksoftware\Opencart;

	use Atiksoftware\Database\PDOModel;

	class Opencart
	{

		public $db_hostname = "";
		public $db_username = "";
		public $db_password = "";
		public $db_database = "";
		public $db_prefix   = "oc_";
		
		public $db;

		public $catalog;
		public $localisation;
		public $filesystem;
		public $reports;
		public $customers;
		public $settings;

		function __construct($param = []){
			$this->db_hostname = isset($param["hostname"]) ? $param["hostname"] : "localhost";
			$this->db_username = isset($param["username"]) ? $param["username"] : "root";
			$this->db_password = isset($param["password"]) ? $param["password"] : "";
			$this->db_database = isset($param["database"]) ? $param["database"] : "mysite";
			$this->db_prefix = isset($param["prefix"]) ? $param["prefix"] : "oc_";
			$this->db = new PDOModel();
			$this->db->connect($this->db_hostname, $this->db_username, $this->db_password, $this->db_database);

			$this->loadMethods();
		}

		function loadMethods(){
			$this->catalog = (object)[];
			$this->catalog->categories = new \Atiksoftware\Opencart\Catalog\Categories($this);
			$this->catalog->products = new \Atiksoftware\Opencart\Catalog\Products($this);
			$this->catalog->attributes = new \Atiksoftware\Opencart\Catalog\Attributes($this);
			$this->catalog->options = new \Atiksoftware\Opencart\Catalog\Options($this);

			$this->localisation = (object)[];
			$this->localisation->tax = new \Atiksoftware\Opencart\Localisation\Tax($this);
			$this->localisation->language = new \Atiksoftware\Opencart\Localisation\Language($this);
			$this->localisation->store = new \Atiksoftware\Opencart\Localisation\Store($this);

			$this->filesystem = (object)[];
			$this->filesystem->upload = new \Atiksoftware\Opencart\Filesystem\Upload($this);

			$this->settings = (object)[];
			$this->settings->stock = new \Atiksoftware\Opencart\Settings\Stock($this);

		}

	}