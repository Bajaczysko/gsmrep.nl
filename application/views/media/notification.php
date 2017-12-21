<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- NOTIFICATIONS CONTAINER -->
<div class="row">
	<div id="notification" class="col-md-12">  
	  <?php $notification = $this->session->userdata('notification'); ?>
	  <?php if($notification): ?> 
	    <?php echo $notification; ?>       	      
	  <?php endif; ?>
	  <?php $this->session->unset_userdata('notification'); ?>	  
	</div>
</div> 