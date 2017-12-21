<?php
/**
 * Media Manager for Codeigniter
 *
 * @package		CodeIgniter
 * @author 		Prashant Pareek
 * @link 		http://codecanyon.net/item/media-manager-for-codeigniter/9517058
 * @since 		Version 1.0.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Media model class
 */
class Media_model extends CI_Model {

	var $img_width = '200';	

	// database table name
    var $table = 'media';

	/**
	 * Method to get array contaning folder map
	 * in media directory
	 */
	public function get_folder_tree()
	{		
		// get array of media files and folder
		$media_map = $this->dir_map_sort(directory_map(MM_BASE));

		// get array of folder
		$folder_map = $this->get_folder_map($media_map);		
		
		if(isset($folder_map['children'])){			
			return $folder_map['children'];	
		}			
		
		return NULL;
	}	

	/**
	 * Sorts the return of directory_map() alphabetically
	 * directories listed before files	 
	 */
	function dir_map_sort($array)
	{
	    $dirs = array();	    

	    foreach ($array as $key => $val)
	    {
	        if (is_array($val)) { // if is dir
	            // run dir array through function to sort subdirs and files
	            // unless it's empty
	            $dirs[$key] = (!empty($array)) ? $this->dir_map_sort($val) : $val;
	        }
	    }

	    // sort by key (dir name)
		uksort($dirs, function($a, $b) {
	    	return strnatcasecmp($a, $b);
	    });

	    // put the sorted arrays back together
	    // swap $dirs and $files if you'd rather have files listed first
	    return $dirs; 
	}

	/**
	 * Recursive method to folder array map of supplied
	 * media files and folder array
	 *
	 * @param  array  $media_arr  array containing media files and folder map
	 * @param  string  $path   contains path of upper level folder map
	 * @return  array  $tree  processed folder map	 
	 */ 
	public function get_folder_map($media_arr, $path = null)
	{		
		$tree = array();
		
		// add path of folder to array element
		if($path){	
			$tree['path'] = $path; 
			$path .= '/';
		}

		// loop through media array
		foreach($media_arr as $key => $value)
		{	
			if(is_array($value))
			{	
				// remove backslash from folder key
				//$key = substr($key,0,-1);

				// thumb folder is used by script to store thumbnail images				
				if($key !== 'thumb') {
					// append sub folders under children array element
					// and call function recursively					
					$tree['children'][$key] = $this->get_folder_map($value,$path.$key);
				}					
			}			
		}		
		
		return $tree;
	}	

	/**
	 * Method to get folders list 
	 *
	 * @param  string  $path  media directory path
	 */
	public function get_folders_list($path = NULL)
	{
		if(!empty($path)){
            $path .= '/';
        }               

        // media directory path
        $basepath = MM_BASE.$path;

        // get media files and folder map array
        $media = $this->dir_map_sort(directory_map(realpath($basepath)));          

        // initialize variables
        $data = array();
        $count = 0;

        if(!empty($media))
        {
            // loop through media files and folders
            foreach($media as $key => $value) {                         
            	// if folders
                if(is_array($value)) {
                    // remove backslash from folder key
                   // $key = substr($key,0,-1);

                    // if not thumb folder
                    if($key !== 'thumb') {                    	
                        $data[$count] = array(
                            'name'  =>  $key,                           
                            'path'  =>  $path.$key
                        );  
                        $count++;
                    }                   
                }
            }
        }

        return $data;
	}		

	/**
     * Method to get media files list 
     *
     * @param  string  $path  media directory path
     */
    public function get_media_list($path = NULL)
    {       	    	                     
        if(!empty($path)){
            $path .= '/';
        }  

        // media directory path
        $basepath = MM_BASE.$path;     
        
        $mediapath = '/'.$path;   

        // logged-in user's ID
		$user_id = $this->session->userdata('auth_user');                      
        
        // query to fetch media files
        $this->db->where('path', $mediapath);
        $this->db->order_by('name', 'ASC');
		$query = $this->db->get($this->table);

		// initialize variables
        $data = array();
        $count = 0;

        // get media manager parameters
        $params = $this->get_params();
                
		// if media exists
		if($query->num_rows()) 
		{
			// get media files
			$media = $query->result();
			
			// loop through media files
			foreach($media as $md) {					
				// get real path of each file						
				$file_path = realpath($basepath.$md->name);
				
				// check if file exists
				if(file_exists($file_path)) {					
					// get file type
					$type = explode('/',$md->type);

					// get file extension                       
                    $tmp = explode('.', $md->name);                    
                    $ext = end($tmp);                                       
					$file_ext = strtolower($ext);
                    $raw_name = basename($md->name,'.'.$ext);

					switch($file_ext)
                    {
                        // images
                        case 'jpg':
                        case 'png':
                        case 'gif':                         
                        case 'bmp':
                        case 'jpeg':
                        case 'ico':                             
                            $img_url = $anchor_url = $params->media_path.'/'.$path.$md->name;

                            $info = @getimagesize($file_path);

                            // get image size in ratio of global variables $img_width * $img_width
                            if (($info[0] > $this->img_width) || ($info[1] > $this->img_width))
                            {
                                $dimensions = $this->image_resize($info[0], $info[1], $this->img_width);
                                $width_x = $dimensions[0];
                                $height_x = $dimensions[1];

                                $url = $params->media_path.'/'.$path.'thumb/'.$md->name;

                                if(file_exists(realpath($url))) {
                                    $img_url = $url;
                                }
                            }
                            else {
                                $width_x = @$info[0];
                                $height_x = @$info[1];                                  
                            }

                            // get image size in ratio of 16 * 16
                            if (($info[0] > 16) || ($info[1] > 16))
                            {
                                $dimensions = $this->image_resize($info[0], $info[1], 16);
                                $width_16 = $dimensions[0];
                                $height_16 = $dimensions[1];
                            }
                            else {
                                $width_16 = @$info[0];
                                $height_16 = @$info[1];
                            }

                            $data[$count] = array(
								'name'       => $md->name,  
								'raw_name'	 => $raw_name,
								'type'  	 => $md->type,  
								'file_type'  => $type[0],                               								
                                'path'       => $path.$md->name, // relative path of image or folder                                   
                                'file_url'    => $img_url, // image url
                                'anchor_url' => $anchor_url,
                                'size'       => $this->format_bytes(filesize($file_path)), // file size
                                'width'      => @$info[0], 
                                'height'     => @$info[1],
                                'width_x'    => $width_x,
                                'height_x'   => $height_x,
                                'width_16'   => $width_16,
                                'height_16'  => $height_16
							);

							$count++;
							break;
                        default:
                        	// icon image files for file format other than images
                            $icon_file = realpath(FCPATH . 'themes/default/icons/mime-icon-16/'.$file_ext.'.png');

                            if(!is_file($icon_file)){                               
                                if(($file_ext == 'html') || ($file_ext == 'htm')){ // if html file
                                    $file_ext = 'page';
                                } else { // default icon image file, if not exists for file extension
                                    $file_ext = 'blank';
                                }
                            }

                            $data[$count] = array(
                                'name'        => $md->name,
                                'raw_name'	  => $raw_name,
                                'type' 		  => $md->type,   
                                'file_type'   => $type[0],                                   
                                'path'        => $path.$md->name,                                   
                                'file_url'    => $params->media_path.'/'.$path.$md->name,
                                'icon_url-16' => 'themes/default/icons/mime-icon-16/'.$file_ext.'.png',
                                'icon_url-32' => 'themes/default/icons/mime-icon-32/'.$file_ext.'.png',
                                'size'        => $this->format_bytes(filesize($file_path)) // file size
                            );

                            $count++;
                        	break;
                    }                    					
				}
			}							
		}

		return $data;		
    }

	/**
	 * Get image dimensions with specified size ratio 
	 *
	 * @param  int  $width  width of image
	 * @param  int  $height  height of image
	 * @param  int  $size  size ratio to get image dimensions
	 *
	 * @return  array  containing width and height of image according to supplied ratio
	 */
	public function image_resize($width, $height, $size)
	{		
		// if width is greater than height
		if($width > $height){
			$percentage = ($size / $width);
		} else {
			$percentage = ($size / $height);
		}
		
		// get round of image width and height
		$width  = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}
	
	/**
	 * Get file sizes in GB, MB, KB ant bytes
	 *
	 * @param  int  $bytes  file size in bytes
	 * @return  int  $bytes  file size in GB, MB, ..
	 */
	function format_bytes($bytes)
    {
    	// get file size
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' Bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' Byte';
        } else {
            $bytes = '0 Bytes';
        }

        return $bytes;
	}
	
	/**
	 * Method to upload media files
	 *
	 * @param  array  $files  containing files object list
	 */
	public function upload_files($files)
	{		
		$basepath = MM_BASE;
		
		if($this->session->userdata('path')){
			$basepath .= $this->session->userdata('path').'/'; 
		}				

		// Get media manager settings
		$params = $this->get_params();
		$allowed_types = explode(',',$params->allowed_types);
		$allowed_types = implode('|',$allowed_types);

		// Get configration		
		$config['upload_path'] = realpath($basepath);
		$config['allowed_types'] = $allowed_types;
		$config['overwrite'] = $params->overwrite;
		$config['max_size']	= $params->max_size * 1024;
		$config['max_width']  = $params->max_width;
		$config['max_height']  = $params->max_height;
		$config['max_filename']  = $params->max_filename;
		$config['encrypt_name']  = $params->encrypt_name;				
		$config['remove_spaces']  = $params->remove_spaces;

		// make file upload configuration compatible to different browsers
		$config = $this->crossBrowserHacks($config);
		
		// load file upload library
		$this->load->library('upload');
		
		$errors = array();
		$count = 0;
		$_FILES['filedata'] = '';
		
		// Upload files one by one
		foreach($files['name'] as $key => $file) 
		{	
			// check if file size is not zero
			if($files['size'][$key]) 
			{
				$_FILES['filedata']['name'] = strip_tags($files['name'][$key]);
				$_FILES['filedata']['type'] = $files['type'][$key];
				$_FILES['filedata']['tmp_name'] = $files['tmp_name'][$key];
				$_FILES['filedata']['error'] = $files['error'][$key];
				$_FILES['filedata']['size'] = $files['size'][$key];										
				
				$this->upload->initialize($config);

				// Set errors message if files unable to upload
				if(!$this->upload->do_upload('filedata')) {										
					$errors[$key] = $this->upload->display_errors('<p><strong>'.$_FILES['filedata']['name'].': </strong>', '</p>');				
				} else {
					// Uploaded file data
					$upload_data = $this->upload->data();

					// store file details in database
					$this->save_file_details($upload_data);

					// create thumbnail for upload bigger images					
					$return = $this->create_thumb($upload_data);

					if($return != TRUE){
						$errors[$key] = $return;
					}

					$count++;			
				}
			} else {
				$errors[$key] = '<p><strong>'.$files['name'][$key].':</strong> file size set to 0.</p>';
			}												
		}		

		$data['errors'] = $errors;		

		// check if file uploaded previously in same request (drag & drop)
		$cn = (int) $this->input->post('count'); 

		if($cn) {		
			// append total uploaded file count to session	
			$upload_count = (int) $this->session->userdata('uploadcount');
			$count += $upload_count;
			$this->session->set_userdata('uploadcount',$count);

			// add error messages to current error message
			$notifications = $this->session->userdata('notifications');
			if(isset($notifications['errors'])) {
				$data['errors'] = array_merge($data['errors'],$notifications['errors']);
			}
		}		

		// Success message for no. of files upload		
		if($count){
			$no_files = ($count > 1) ? 'files' : 'file';
			$client = $this->input->post('client');							
			$data['success'] = array('<p>'.$count.' '.$no_files.' uploaded successfully</p>');			
		}

		// Set notifications				
		$this->session->set_userdata('notifications',$data);	
		return TRUE;
	}

	/**
	 * Method to store uploaded file details in db
	 *
	 * @since  version 2.0.0
	 * @param  array  $data  upload file data
	 */
	public function save_file_details($upload_data)
	{
		$path = str_replace(MM_BASE, '/', $upload_data['file_path']);

		$data = array(
			'path' => $path,
			'name' => $upload_data['file_name'],
			'type' => $upload_data['file_type']
		);

		$result = $this->db->insert($this->table, $data);

		return $result;
	}

	/**
	 * Method to create thumbnail for large images
	 * 	 
	 * @param  array  $data  details of upload file
	 */
	public function create_thumb($data)
	{
		// if uploaded file is a image
		if($data['is_image'])
		{			
    		if (($data['image_width'] > $this->img_width) || ($data['image_height'] > $this->img_width))
			{
				// get image sizes for supplied ratio
				$dimensions = $this->image_resize($data['image_width'], $data['image_height'], $this->img_width);
				$width_x = $dimensions[0];
				$height_x = $dimensions[1];

				// Get configration
				$config['source_image'] = $data['full_path'];
				$config['new_image'] = $data['file_path'].'thumb/'.$data['file_name'];				
				$config['maintain_ratio'] = TRUE;
				$config['width'] = $width_x;
				$config['height'] = $height_x;

				// load image library
				$this->load->library('image_lib');
				$this->image_lib->initialize($config); 				

				// create and save thumb of image
				if(!$this->image_lib->resize()){
					$return = $this->image_lib->display_errors('<p><strong>'.$data['orig_name'].': </strong>', '</p>');
					return $return;
				}
			}
		}

		return TRUE;
	}

	/**
	 * Method to set configuration for files to upload
	 * in case files uploaded from some special devices 
	 * or broswers, like safari of IOS.
	 *
	 * Broswer data is sent from client.js to this function
	 *
	 * @param  array  $config  configuration set for upload library
	 * @return  array  $config  modified configuration
	 */
	public function crossBrowserHacks($config)
	{
		// device data
		$client = $this->input->post('client');					

		// convert json data to array
		$client = json_decode($client,true);

		// operating system
		$os = $client['os'];						
		
		// operating system version		
		$tmp = explode('.',$client['osVersion']);
		$osVersion = $tmp[0];

		// web browser
		$browser = $client['browser'];						
		$tmp = explode('.',$client['browserVersion']);

		// browser version
		$browserVersion = $tmp[0];

		// is device a mobile
		$mobile = $client['mobile'];
		
		// if files uploaded from iphone mobile using safari browser
		if(($os == 'iOS') && ($browser == 'Safari') && ($mobile == 1))
		{				
			// possible bug of codeigniter
			$config['overwrite'] = 0;

			// a bug in iphone safari set all upload image name to image.jpg, 
			// so it is necessary to encrypt uploaded file name
			$config['encrypt_name'] = 1;
		}
		
		return $config;
	}	

	/**
	 * Method to get media manager paramters
	 */
	public function get_params()
	{
		// get user paramters
		$params = json_decode(read_file(realpath(FCPATH . 'themes/default/media-manager/params.json')));

		// get default paramters
		$default_params = json_decode(read_file(realpath(FCPATH . 'hemes/default/media-manager/default.json')));
		
		// Get default param if some params are null
		foreach($params as $key => $value){			
			if(is_null($params->$key)){
				$params->$key = $default_params->$key;
			}
		}
		
		return $params;
	}	
}

/* End of file Media_model.php */
/* Location: ./application/models/Media_model.php */