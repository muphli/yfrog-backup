<?php 

	//read arguments, store username
	$argv = $_SERVER['argv'];
	$username = $argv[1];
	$foldername = $argv[2];
	$path = '';
	$devKey = '';
		
	//parse response, count images, calculate pages
	$response = json_decode(file_get_contents("http://yfrog.com/api/photocount.json?devkey=" . $devKey . "&screen_name=" . $username));
	$imageCounter = $response->result->count;
	$pageCounter = ceil(($imageCounter)/25);
					
	//create destination-folder
	if (isset($foldername)){
		mkdir($foldername);
		$path = $foldername;
		
	}
	else {
		$path = "yfrog-backup_" . $username;
		mkdir($path);		
	} 
	
	//print total number of images
	echo "\n@" . $username . " uploaded " . $imageCounter. " images to yFrog.\n";
	echo "\nDownload initiated...\n";
	
	//for all pages...
	for ($i = 1; $i <= $pageCounter; $i++){
	  
  		//read xml
  		$response = file_get_contents("http://yfrog.com/api/userphotos.json?limit=25&screen_name=" . $username . "&page=" . $i . "&devkey=" . $devKey);
    	$page = json_decode($response);
    	    
    	//for all images of one page...
    	for ($j = 0; $j < count($page->result->photos); $j++){
    	    	    	
    		//read short-id and timestamp
    		$imageID = "image_" . $i . "_" . $j;
      		
      		//set url
      		$imageUrl = $page->result->photos[$j]->photo_link . ":medium";
      		      		      		      		
      		//set filename
      		$filename = $imageID . ".jpg";
      		
      		//download image
      		file_put_contents($path . "/" . $filename, file_get_contents($imageUrl));
      		
      		//decrement counter
      		$imageCounter--;
      		
      		//print status message for each image
      		echo "Download of image with ID '" . $imageID . "' successful (" . $imageCounter . " images remaining).\n";
    	
    	}
    	
    	if ($imageCounter == 0){
    	
    		//print status message if download is complete
	    	echo "Download complete.";
	    	
    	}
  
  	}
  	
?> 
	

  
    
  	
