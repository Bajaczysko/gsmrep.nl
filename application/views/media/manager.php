<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="media-manager" class="container-fluid" style="margin-top: -10px;">
  <!-- BEGIN HEADER -->

  <!-- BEGIN TOOLBAR CONTAINER -->
  <div id="controls" style="height: 45px;" class="row">
    <div class="col-md-12">
      <div id="toolbar" class="btn-toolbar">
        <div id="toolbar-offcanvas" class="btn-wrapper">               
          <button id="showTreeView" type="button" class="btn btn-sm btn-default visible-xs-inline-block"><span class="glyphicon glyphicon-list"></span> <span class="hidden-xs">Folders</span></button>
        </div>                                  
        <div id="toobar-upload" class="btn-wrapper">
          <button type="button" data-toggle="collapse" data-target="#collapseUpload" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> <span class="hidden-xs">Upload</span></button>
        </div>
        <div id="toolbar-create" class="btn-wrapper">
          <button type="button" data-toggle="collapse" data-target="#collapseFolder" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-folder-open"></span> <span class="hidden-xs">Create New Folder</span></button>
        </div>
        <div id="toolbar-delete" class="btn-wrapper">          
          <button type="button" class="btn btn-sm btn-default" onclick="delete_media()"><span class="glyphicon glyphicon-remove"></span> <span class="hidden-xs">Delete</span></button>
        </div>                    
      </div>
    </div>
  </div> 
  <!-- END TOOLBAR CONTAINER -->
  <!-- PAGE CONTAINER -->
  <div id="page-view" class="row">               
    <!-- BEGIN NOTIFICATIONS CONTAINER -->
    <div id="notifications" class="col-md-12">
    <?php $msg = $this->session->userdata('notifications'); ?>
    <?php if(isset($msg) && !empty($msg)): ?>              
      <?php if(isset($msg['success']) && !empty($msg['success'])): // Success Messages ?>          
        <div class="alert alert-success fade in" role="alert">    
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>        
          <?php foreach($msg['success'] as $key => $sc): ?>
            <?php echo $sc; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>        
      <?php if(isset($msg['errors']) && !empty($msg['errors'])): // Error Messages ?>
        <div class="alert alert-warning fade in" role="alert">    
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>        
        <?php foreach($msg['errors'] as $key => $error): ?>
          <?php echo $error; ?>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>        
    <?php endif; ?>
    <?php $this->session->unset_userdata('notifications'); ?>
    </div> 
    <!-- END NOTIFICATIONS CONTAINER -->    
    <div id="wrap" class="wrap">
      <!-- FOLDER TREE STRUCTURE CONTAINER -->
      <div id="treeview" class="col-lg-2 col-md-3 col-sm-3 oc">                                                                          
        <ul id="media-home">
          <li>
            <?php $path = $this->session->userdata('path'); ?>           
            <a class="<?php if(!$path) echo 'active'; ?> mediapath" href="home"><span class="glyphicon glyphicon-folder-open"></span> 
            <?php echo $params->media_path; ?> <?php echo get_media_count(NULL); ?></a>            
            <?php if(isset($foldertree)): ?>
              <?php generate_folder_tree($foldertree,$path); ?>
            <?php endif; ?> 
          </li>
        </ul>                                                
      </div>
      <!-- END OF FOLDER TREE STRUCTURE CONTAINER -->      
      <div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">    
        <!-- File Upload Form -->        
        <div id="collapseUpload" class="media-actions collapse">   
          <form action="<?php echo site_url(CN_BASE.'do_upload'); ?>" id="uploadForm" class="form-inline dropzone" role="form" method="post" enctype="multipart/form-data">                        
            <div class="form-group fallback">              
              <label for="upload-file">Upload file</label>      
              <input type="file" id="upload-file" class="form-control" name="filedata[]" multiple>  
              <button class="btn btn-primary" id="upload-submit"><span class="glyphicon glyphicon-upload"></span> Start Upload</button>
            </div>   
            <div class="meter"><span class="roller"></span></div>             
            <button type="button" class="btn btn-primary upload-btn"><span class="glyphicon glyphicon-upload"></span> Start Upload</button>                                        
          </form>
          <p class="help-block">Upload files (Maximum Size: <?php echo $params->max_size; ?> MB)</p>          
        </div>      
        <!-- Create Folder Form -->
        <div id="collapseFolder" class="media-actions collapse">
          <form action="<?php echo site_url(CN_BASE.'create_folder'); ?>" class="form-inline" role="form" method="post">
            <div class="form-group">
              <label class="sr-only" for="folderpath">Folder path</label>
              <input type="text" id="folderpath" class="form-control input-sm" readonly value="<?php echo $this->session->userdata('path').'/'; ?>">
            </div>
            <div class="form-group">
              <label class="sr-only" for="foldername">Folder name</label>
              <input type="text" id="foldername" name="foldername" class="form-control input-sm">
            </div>          
            <button type="submit" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-folder-open"></span> Create Folder</button>                              
          </form>
        </div>
        <!-- Media View -->
        <div id="mediaview">                   
          <form id="mediaForm" action="<?php echo site_url(CN_BASE.'index'); ?>" method="POST">
            <?php include 'medialist.php'; ?>
            <input id="path" name="path" type="hidden" />                        
          </form>
        </div>           
      </div>
    </div>                      
  </div>
  <!-- END PAGE CONTAINER -->  
</div>

<div id="media-modal-popup" class="container mfp-hide">
  <div class="row white-popup">
    <div class="mfp-close"></div>
    <div class="col-md-7">
        <h2 class="mfp-imageName"></h2>
        <div class="mfp-imageLink"></div>
    </div>
    <div class="col-md-5"><div class="mfp-imageTitle"></div></div>
  </div>
</div>
<script>var site_url = '<?php echo site_url(CN_BASE); ?>';</script>
<script>var max_size = '<?php echo $params->max_size; ?>';</script>
<script>var max_files = '<?php echo $params->max_files; ?>';</script>
<?php
// Function to generate folder tree structure
function generate_folder_tree($arr,$path)
{  
  ?>
  <ul class="folder-list">
  <?php foreach($arr as $k => $v): ?>    
    <li>
      <?php if($path == $arr[$k]['path']): // if current opened folder ?>
      <a class="active mediapath" href="<?php echo $arr[$k]['path']; ?>">
      <span class="glyphicon glyphicon-folder-open"></span> <?php echo $k; ?> <?php echo get_media_count($arr[$k]['path']); ?></a>    
      <?php else: ?>
      <a class="mediapath" href="<?php echo $arr[$k]['path']; ?>">
      <span class="glyphicon glyphicon-folder-close"></span> <?php echo $k; ?> <?php echo get_media_count($arr[$k]['path']); ?></a>
      <?php endif; ?>
      <?php if(isset($arr[$k]['children'])): // Recursive call ?>
        <?php generate_folder_tree($arr[$k]['children'],$path); ?>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  </ul>
  <?php 
}

function get_media_count($path)
{
  if($path){
    $path = '/'.$path.'/';
  } else {
    $path = '/';
  }
  $ci = &get_instance();
  $ci->db->where('path',$path);
  $result = $ci->db->count_all_results('media');

  if($result){
    return '('.$result.')';
  }
}
?>