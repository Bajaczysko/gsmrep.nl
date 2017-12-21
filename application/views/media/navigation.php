<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">         
        <a class="navbar-brand" href="<?php echo site_url(); ?>"> 
        <span class="glyphicon glyphicon-picture"></span>                
        <?php 
        $this->load->config('site');
        echo $this->config->item('site_name'); 
        ?>            
        </a>        
    </div>
    <!-- /.navbar-header -->
    <ul class="nav navbar-top-links navbar-right">        
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <!--<li><a href="<?php //echo site_url('/user/user_profile'); ?>"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li> -->
                <li><a id="profileLink" class="popup-link"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                <li><a id="changePwdLink" class="popup-link"><i class="fa fa-lock fa-fw"></i> Change Password</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo site_url('user/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->    
</nav>