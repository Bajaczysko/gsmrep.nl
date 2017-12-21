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
 * Media class
 */
class Media extends CI_Controller {

	/**
     * Constructor, initializes the media model, define controller
     * constant and media base folder constant
     */
	public function __construct()
	{	 
		parent::__construct();

		// IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded!
		// It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
		$this->auth = new stdClass;

		$this->load->library('flexi_auth');

		// check if user logged-in
		if (!$this->flexi_auth->is_logged_in()) {
			// redirect user to login page						
            redirect('/');
        }

        // load media model
        $this->load->model('media_model');
		$this->load->helper('file');

		// create constant for media controller relative path
		$cn_dir = realpath(FCPATH.'application/controllers/');
		$fl_dir = realpath(dirname(__FILE__));
		$cn_path = str_replace($cn_dir,'',$fl_dir);
		$cn_path = str_replace('\\', '/', $cn_path);
		$tmp = explode('/',$cn_path);
		$tmp = array_filter($tmp);
		$cn_path = implode('/',$tmp);
		define('CN_BASE',$cn_path.'/media/');

		// create constant for user media base directory			
		$params = $this->media_model->get_params();
		$mm_base = FCPATH.'media';
		$mm_base = str_replace(DIRECTORY_SEPARATOR, '/', $mm_base.'/');

		// create folder to save user media
		if (!is_dir($mm_base)) {
			if(!mkdir($mm_base, 0777, TRUE)){
				exit('Unable to create user media directory');
			}
		}

		define('MM_BASE', $mm_base);						
	}

	/**
	 * Get media manager settings, load folder structure 
	 * media list of path selected and display the page
	 */
	public function index()
	{

		// get media folder path from POST
		$path = $this->input->post('path');

		if(empty($path)){
			// if not set then get from session
			$path = $this->session->userdata('path');
		} else{
			// if home, unset session path value
			if($path == 'home'){
				$path = null;
				$this->session->unset_userdata('path');
			} elseif($path == 'up'){
				// switch to media folder 1 level up
				$path = $this->session->userdata('path');
				$tmp = explode('/',$path);
				array_pop($tmp);

				if(!empty($tmp)){
					// set to media folder
					$path = implode('/',$tmp);
					$this->session->set_userdata('path',$path);
				} else{
					// set to root folder
					$path = null;
					$this->session->unset_userdata('path');
				}
			} else {
				// switch to specified media folder
				$this->session->set_userdata('path',$path);
			}
		}


		// get folders list
		$this->data['folders'] = $this->media_model->get_folders_list($path);

		// get media files
		$this->data['media'] = $this->media_model->get_media_list($path);
		
		// get media manager parameters
		$this->data['params'] = $this->media_model->get_params();

		// get folder tree structure
		$this->data['foldertree'] = $this->media_model->get_folder_tree();

		// Clear session for uploaded file count on every redirect
		$this->session->unset_userdata('uploadcount');

		// load view
	//	$data['page'] = 'media/manager';
	//	$this->load->view('index',$data);

		$this->interface_model->interface_no_title('media/manager', $this->data, $this->language_model->translate('Media Manager'));


	} 

	/**
	 * Method to upload media files
	 */
	public function do_upload()
	{	
		// check if files received				
		if(isset($_FILES['filedata']) && ($_FILES['filedata']['tmp_name']))
		{    
			// upload files														
			$this->media_model->upload_files($_FILES['filedata']);												
		}			

		redirect(CN_BASE.'index', 'refresh');
	}
	
	/**
	 * Method to create folder in specified media directory
	 */
	public function create_folder()
	{
		// media base directory path
		$basepath = MM_BASE;

		// Get media path
		if($this->session->userdata('path')){
			$basepath .= $this->session->userdata('path').'/'; 
		}	

		// Get folder name
		$foldername = trim(strip_tags($this->input->post('foldername')));

		// Sanitize folder name for . .. ... strings
		$foldername = str_replace('\\', '/', $foldername);
		$tmp = explode('/',$foldername);			
		$tmp = array_filter($tmp);
		$tmp = array_diff($tmp, array('.','..','...'));
		$foldername = implode('/',$tmp);	
		
		// array to set error messages
		$data = array();

		if($foldername){			
			if($foldername != 'thumb'){
				$dir = $basepath.$foldername;				

				// create folder
				if (!is_dir($dir)) {
				    if(!mkdir($dir, 0777, TRUE)){
				    	// could not create folder					
						$data['errors'] = array('Could not create folder.');									
				    } else {
				    	// Create tmp folder
				    	$dir .= '/thumb';
				    	if(!mkdir($dir, 0777, TRUE)){
					    	// could not create thumb folder					
							$data['errors'] = array('Could not create thumb folder.');									
					    }
				    }
				} else {
					// Directory already exists				
					$data['errors'] = array('Folder already exists.');
				}
			} else {
				// Could not create folder name with 'thumb'				
				$data['errors'] = array('Folder name \'thumb\' is used by system');
			}		
		} else {
			// Folder name empty			
			$data['errors'] = array('Choose appropriate name for folder.');
		}

		// Set error notifications		
		if(!empty($data)){
			$this->session->set_userdata('notifications',$data);
		}

		redirect(CN_BASE.'index', 'refresh');
	}	
	
	/**
	 * Method to delete media of folders from specified directory
	 */
	public function remove_media()
	{		
		$table = 'media';

		// media or folders to remove
		$rm_media = $this->input->post('rm');				
		
		foreach($rm_media as $rm){
			// Sanitize file or folder name
			$rm = str_replace('\\', '/', $rm);
			$tmp = explode('/',$rm);			
			$tmp = array_filter($tmp);
			$tmp = array_diff($tmp, array('.','..'));
			$rm = implode('/',$tmp);						
			
			// If name exists
			if($rm){							
				$path = realpath(MM_BASE.$rm);
				
				// If absolute path exists
				if($path){
					if(is_file($path)){	// if file
						// get file name and path	
						$tmp = explode('/',$rm);
						$file = end($tmp);
						$mediapath = str_replace($file,'',$rm);
						$mediapath = '/'.$mediapath;
						
						// remove file from database
						$this->db->where('path',$mediapath)->where('name',$file)->delete($table);

						// delete files from directory
		        		if(unlink($path)){
		        			// path to thumb folder file
							$n = count($tmp) - 1;
							$last_el = $tmp[$n];
							$rm_thumb = str_replace($last_el, 'thumb/'.$last_el, $rm);
							$path = realpath(MM_BASE.$rm_thumb);

							// delete thumb image file
							if(file_exists($path)){
								unlink($path);
							}							
		        		}
					} elseif(is_dir($path)){ // if folder
						// remove folder media from database			
						$mediapath = '/'.$rm.'/';
						$this->db->like('path',$mediapath,'after')->delete($table);

						// delete files
						delete_files($path, TRUE);

						// delete folder
						rmdir($path);
					}
				}
			}														
		}
	}	

	public function rename_media()
	{
		$table = 'media';

		// logged-in user's ID
		$user_id = $this->session->userdata('auth_user');

		// name
		$path = $this->input->post('path');
		$edited_name = $this->input->post('edited_name');

		$msg = $type = '';

		if($path && $edited_name){
			$realpath = realpath(MM_BASE.$path);

			// If absolute path exists
			if($realpath){ 				
				if(is_file($realpath)){	// if file						
					// get file name and path	
					$tmp = explode('/',$path);
					$name = end($tmp);
					$mediapath = str_replace($name,'',$path);

					// get new file name
					$tmp = explode('.',$name);				
					$ext = end($tmp);			
					$newname = $edited_name.'.'.$ext;

					// new file path
					$newpath = MM_BASE.$mediapath.$newname;		

					if(!file_exists($newpath)){
						// rename file from database
						$result = $this->db
								->where('path','/'.$mediapath)
								->where('name',$name)
								->update($table,array('name'=>$newname));
						
						if($result){							
							$return = rename($realpath,$newpath);						
							if($return){
								$msg = 'Media file renamed successfully';
								$type = 'success';
							} else {
								$msg = 'Unable to rename media file';
								$type = 'danger';
							}

							if($return){								
								echo '1';
								return;
							}						
						} else {
							$msg = 'Unable to rename media file';
							$type = 'danger';
						}			
					} else {						
						$msg = 'Media file already exists';
						$type = 'danger';
					}																
				} elseif(is_dir($realpath)){ // if folder
					// get file name and path	
					$tmp = explode('/',$path);
					array_pop($tmp);
					$tmp[] = $edited_name;
					$mediapath = implode('/',$tmp);
					$newpath = MM_BASE.$mediapath;					

					if(!file_exists($newpath)){
						// rename folder from database												
						$n = strlen($path);						
						$query = "UPDATE ".$table." SET path = CONCAT(REPLACE(LEFT(path,INSTR(path,'/".$path."/') + ".($n+1)."),
								 '/".$path."/','/".$mediapath."/'),SUBSTRING(path,INSTR(path,'/".$path."/') + ".($n+2).")) 
								 WHERE path LIKE '/".$path."/%'";
						$result = $this->db->query($query);

						if($result){
							$return = rename($realpath,$newpath);						
							if($return){
								$msg = 'Media file renamed successfully';
								$type = 'success';
							} else {
								$msg = 'Unable to rename media file';
								$type = 'danger';
							}
							
							if($return){								
								echo '1';
								return;
							}
						} else {
							$msg = 'Unable to rename folder';
							$type = 'danger';
						}
					} else {
						$msg = 'Folder already exists';
						$type = 'danger';
					}
				}
			} else {
				$msg = 'Invalid media path';
				$type = 'danger';
			}
		} else {
			$msg = 'Invalid path or new name';
			$type = 'warning';
		}

		echo json_encode(array('msg'=>$msg,'type'=>$type));

		/*if($msg){
			$this->base->set_notification($msg,$type);
		}*/
	}

}

/* End of file Media.php */
/* Location: ./application/controllers/Media.php */