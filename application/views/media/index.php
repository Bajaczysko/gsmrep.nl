<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>
    <?php 
    $this->load->config('site');
    echo $this->config->item('site_name'); 
    ?>
    </title>    
    <link href='<?php echo base_url();?>assets/css/bootstrap.min.css' rel='stylesheet'>     
    <link href='<?php echo base_url();?>assets/css/font-awesome.min.css' rel='stylesheet'>
    <link href='<?php echo base_url();?>assets/css/magnific-popup.css' rel='stylesheet'>
    <link href='<?php echo base_url();?>assets/css/dropzone.min.css' rel='stylesheet'>
    <link href='<?php echo base_url();?>assets/css/media.css' rel='stylesheet'>    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>                
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>      
    <script src='<?php echo base_url();?>assets/js/html5shiv.min.js'></script>
    <script src='<?php echo base_url();?>assets/js/respond.min.js'></script>
    <![endif]-->    
  </head>
  <body>       
    <?php $this->load->view($page); ?>        
    <script src='<?php echo base_url(); ?>assets/js/jquery-1.11.2.min.js'></script> 
    <script src='<?php echo base_url(); ?>assets/js/bootstrap.min.js'></script>   
    <script src='<?php echo base_url(); ?>assets/js/masonry.pkgd.min.js'></script> 
    <script src='<?php echo base_url(); ?>assets/js/jquery.magnific-popup.min.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/bootbox.min.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/dropzone.min.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/cookie.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/pwstrength.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/client.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/media.js'></script>
    <script src='<?php echo base_url(); ?>assets/js/general.js'></script>  
    <script src='<?php echo base_url(); ?>assets/js/ph-fallback.js'></script>
    <script type="text/javascript">
    <?php
    if($this->session->flashdata('profile.status')){
        ?>      
        $(window).load(function(){
            $('#profileModal').modal('show');
        });      
        <?php
        $this->session->unset_userdata('profile.status');
    }
    if($this->session->flashdata('cpassword.status')){
        ?>      
        $(window).load(function(){
            $('#changePwdModal').modal('show');
        });      
        <?php
        $this->session->unset_userdata('cpassword.status');
    }
    ?>     
    </script>     
  </body>
</html>