<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="media-container" class="table-responsive">	
	<!-- Set View Buttons -->
	<div id="mediaViewBtn" class="media btn-group">     	    	
		<div class="controls pull-left">
			<div class="mode">
	  		<a id="thumbs" onclick="setViewType('thumbs')" class=""><span class="glyphicon glyphicon-th-large"></span></a>
	  		<a id="details" onclick="setViewType('details')" class=""><span class="glyphicon glyphicon-list"></span></a>
			</div>    	
		</div>   
		<div class="btn-toolbar"> 
	  	<div id="toolbar-select" class="btn-wrapper hidden">               
				<button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-check"></span> <span class="hidden-xs">Select Items</span></button>        
			</div>		
		</div>   	
	</div>
	<!-- THUMB VIEW MEDIA CONTAINER -->    
	<div id="thumbsView" class="mode-view hidden">
		<?php $this->load->view('media/thumbs-view'); ?>
	</div>
	<!-- END OF THUMB VIEW MEDIA CONTAINER -->
	<!-- TABLE VIEW MEDIA CONTAINER -->
	<div id="detailsView" class="mode-view hidden">		
		<?php $this->load->view('media/details-view'); ?>
	</div>
	<!-- END OF TABLE VIEW MEDIA CONTAINER -->
</div>