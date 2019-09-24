<?php 

	namespace Atiksoftware\Opencart\Filesystem;
 

	class Upload extends \Atiksoftware\Opencart\Collection
	{ 
 
		function save( $_file , $_dir_image ){
			$uuid4 = \Ramsey\Uuid\Uuid::uuid4();
			$file_id = $uuid4->toString();
			$file_folder = "products";

			$localFolder = "{$_dir_image}/catalog/products/";
			$localFile = "{$localFolder}/{$file_id}.jpg";

			if(!file_exists($localFolder)){
				mkdir($localFolder);
			}

			$remoteFile = "catalog/products/{$file_id}.jpg";

			move_uploaded_file($_file['tmp_name'], $localFile);

			return [
				"success" => true,
				"src" => $remoteFile
			];
		}
 
		    

	}

 