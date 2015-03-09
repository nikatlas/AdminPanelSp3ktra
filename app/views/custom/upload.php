<?php
//If directory doesnot exists create it.
$output_dir = $_SERVER['DOCUMENT_ROOT']."custom/uploader/images/";

############ Configuration ##############
$thumb_square_size 		= 200; //Thumbnails will be cropped to 200x200 pixels
$max_image_size 		= 970; //Maximum image size (height and width)
$thumb_prefix			= "thumb_"; //Normal thumb Prefix
$jpeg_quality 			= 90; //jpeg quality
##########################################
// Report all PHP errors (see changelog)
error_reporting(E_ALL);

ini_set("display_errors", 1);
if(isset($_FILES["myfile"]))
{
	$ret = array();

	$error =$_FILES["myfile"]["error"];
	if( $error == 1 ){
		//echo phpinfo();
		exit("-1");
	}
	
   {
    
    	if(!is_array($_FILES["myfile"]['name'])) //single file
    	{
            $RandomNum   = time()."".rand(0,99999999);
            
            $ImageName      = str_replace(' ','-',strtolower($_FILES['myfile']['name']));
            $ImageType      = $_FILES['myfile']['type']; //"image/png", image/jpeg etc.
         
            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
            $ImageExt       = str_replace('.','',$ImageExt);
            $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
			$ImageName = date("Y_m_d_H_i");
            $NewImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;

       	 	//move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir. $NewImageName);

	       	 	$ret[0]['url']= $NewImageName;
				
				 
				$image_name = $_FILES["myfile"]["name"]; //file name
				$image_size = $_FILES["myfile"]["size"]; //file size
				$image_size_info 	= getimagesize($_FILES["myfile"]["tmp_name"]); //get image size
	
				if($image_size_info){
					$image_width 		= $image_size_info[0]; //image width
					$image_height 		= $image_size_info[1]; //image height
					$image_type 		= $image_size_info['mime']; //image type
				}else{
					die("Make sure image file is valid!");
				}
				
				$image_temp = $_FILES["myfile"]["tmp_name"];
				switch($image_type){
					case 'image/png':
						$image_res =  imagecreatefrompng($image_temp); break;
					case 'image/gif':
						$image_res =  imagecreatefromgif($image_temp); break;			
					case 'image/jpeg': case 'image/pjpeg':
						$image_res = imagecreatefromjpeg($image_temp); break;
					default:
						$image_res = false;
				}
				
				//Get file extension and name to construct new file name 
				$image_info = pathinfo($image_name);
				$image_extension = strtolower($image_info["extension"]); //image extension
				$image_name_only = strtolower($image_info["filename"]);//file name only, no extension
				
				//create a random name for new image (Eg: fileName_293749.jpg) ;
				//$new_file_name = $image_name_only. '_' .  rand(0, 9999999999) . '.' . $image_extension;
				$new_file_name = $NewImageName;
				
				//folder path to save resized images and thumbnails
				$thumb_save_folder 	= $output_dir. $thumb_prefix . $new_file_name; 
				$image_save_folder 	= $output_dir . $new_file_name;



				//call normal_resize_image() function to proportionally resize image
				if(normal_resize_image($image_res, $image_save_folder, $ImageType, $max_image_size, $image_width, $image_height, $jpeg_quality)){
					
					//call crop_image_square() function to create square thumbnails
					if(!crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality)){
						die('Error Creating thumbnail');
					}
					
				}
				
				imagedestroy($image_res); //freeup memory
    	}
    	else
    	{
            $fileCount = count($_FILES["myfile"]['name']);
    		for($i=0; $i < $fileCount; $i++)
    		{
                $RandomNum   = time();
            
                $ImageName      = str_replace(' ','-',strtolower($_FILES['myfile']['name'][$i]));
                $ImageType      = $_FILES['myfile']['type'][$i]; //"image/png", image/jpeg etc.
             
                $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
                $ImageExt       = str_replace('.','',$ImageExt);
                $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
                
				$ImageName = date("Y_m_d_H_i");
				$NewImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
                
                $ret[$i]['url']= $NewImageName;
    		    //move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$NewImageName );

				$image_name = $_FILES["myfile"]["name"][$i]; //file name
				$image_size = $_FILES["myfile"]["size"][$i]; //file size
				$image_size_info 	= getimagesize($_FILES["myfile"]["tmp_name"][$i]); //get image size
	
				if($image_size_info){
					$image_width 		= $image_size_info[0]; //image width
					$image_height 		= $image_size_info[1]; //image height
					$image_type 		= $image_size_info['mime']; //image type
				}else{
					die("Make sure image file is valid!");
				}
				$image_temp = $_FILES["myfile"]["tmp_name"];
				switch($image_type){
					case 'image/png':
						$image_res =  imagecreatefrompng($image_temp); break;
					case 'image/gif':
						$image_res =  imagecreatefromgif($image_temp); break;			
					case 'image/jpeg': case 'image/pjpeg':
						$image_res = imagecreatefromjpeg($image_temp); break;
					default:
						$image_res = false;
				}
				
				//Get file extension and name to construct new file name 
				$image_info = pathinfo($image_name);
				$image_extension = strtolower($image_info["extension"]); //image extension
				$image_name_only = strtolower($image_info["filename"]);//file name only, no extension
				
				//create a random name for new image (Eg: fileName_293749.jpg) ;
				//$new_file_name = $image_name_only. '_' .  rand(0, 9999999999) . '.' . $image_extension;
				$new_file_name = $NewImageName;
				
				//folder path to save resized images and thumbnails
				$thumb_save_folder 	= $output_dir. $thumb_prefix . $new_file_name; 
				$image_save_folder 	= $output_dir . $new_file_name;
				
				//call normal_resize_image() function to proportionally resize image
				if(normal_resize_image($image_res, $image_save_folder, $ImageType, $max_image_size, $image_width, $image_height, $jpeg_quality)){
					//call crop_image_square() function to create square thumbnails
					if(!crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality)){
						die('Error Creating thumbnail');
					}
					
				}
				
				imagedestroy($image_res); //freeup memory
    		}
    	}
    }
    echo json_encode($ret);
 
}

#####  This function will proportionally resize image ##### 
function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality){
	
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
	
	//do not resize if image is smaller than max size
	if($image_width <= $max_size){
		if(save_image($source, $destination, $image_type, $quality)){
			return true;
		}
	}
	
	//Construct a proportional size of new image
	$image_scale	= $max_size/$image_width;
	$new_width		= ceil($image_scale * $image_width);
	$new_height		= ceil($image_scale * $image_height);
	
	$new_canvas		= imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image
	
	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
		save_image($new_canvas, $destination, $image_type, $quality); //save resized image
	}

	return true;
}

##### This function corps image to create exact square, no matter what its original size! ######
function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality){
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
	
	if( $image_width > $image_height )
	{
		$y_offset = 0;
		$x_offset = ($image_width - $image_height) / 2;
		$s_size 	= $image_width - ($x_offset * 2);
	}else{
		$x_offset = 0;
		$y_offset = ($image_height - $image_width) / 2;
		$s_size = $image_height - ($y_offset * 2);
	}
	$new_canvas	= imagecreatetruecolor( $square_size, $square_size); //Create a new true color image
	
	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)){
		save_image($new_canvas, $destination, $image_type, $quality);
	}

	return true;
}

##### Saves image resource to file ##### 
function save_image($source, $destination, $image_type, $quality){
	switch(strtolower($image_type)){//determine mime type
		case 'image/png': 
			return imagepng($source, $destination); //save png file
			break;
		case 'image/gif': 
			return imagegif($source, $destination); //save gif file
			break;          
		case 'image/jpeg': case 'image/pjpeg': 
			return imagejpeg($source, $destination, $quality); //save jpeg file
			break;
		default: return false;
	}
}
?>