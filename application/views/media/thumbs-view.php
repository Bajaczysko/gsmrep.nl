<div id="masonry-container">
	<?php $path = $this->session->userdata('path'); ?>	
	<?php if(!empty($path)): // Up button if media inside folder ?>
		<div class="item text-center folder">		
			<div id="up-folder" >
				<a class="btn btn-primary mediapath" href="up"><span class="glyphicon glyphicon-arrow-up"></span></a>			
			</div>
		</div>
	<?php endif; ?>
	<!-- Media List of folders -->
	<?php if(isset($folders)): ?>
		<?php foreach($folders as $folder): ?>						
			<div class="item media-item text-center folder">
				<div class="cover"></div>																				
				<div class="media-folder">
					<input type="checkbox" name="rm[]" class="hidden" value="<?php echo $folder['path']; ?>">
					<a class="media-icon mediapath" href="<?php echo $folder['path']; ?>">
					<span class="glyphicon glyphicon-folder-close"></span></a>
				</div>
				<div class="media-link">
					<a class="media-name mediapath" href="<?php echo $folder['path']; ?>"><?php echo $folder['name']; ?></a>
					<a class="rename-item" target="_top" href="<?php echo $folder['path']; ?>" title="Rename" data-media="folder" 
					data-raw-name="<?php echo $folder['name']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
				</div>
			</div>				
		<?php endforeach; ?>
	<?php endif; ?>
	<!-- Media List -->
	<?php if(isset($media)): ?>
		<?php $i = 0; ?>
		<?php foreach($media as $md): ?>
			<?php if($md['file_type'] == 'image'): ?>
				<div class="item media-item">
					<div class="cover"></div>					
					<div class="media-image">
						<input type="checkbox" name="rm[]" class="hidden" value="<?php echo $md['path']; ?>">
						<a class="media-thumb galleryItem" data-group="1" href="<?php echo base_url($md['anchor_url']); ?>">
							<img src="<?php echo base_url($md['file_url']); ?>" alt="<?php echo $md['name']; ?>" 
							style="<?php echo 'width:'.$md['width_x'].'px;height:'.$md['height_x'].'px;'; 
							if($md['width_x'] < 90){
								echo 'margin-top:'.((90 - $md['width_x'])/2).'px';
							}
							?>" 
							width="<?php echo $md['width_x']; ?>" height="<?php echo $md['height_x']; ?>" />
						</a>						
					</div>					
					<div class="media-link">
						<a class="media-name galleryItem" data-group="2" href="<?php echo base_url($md['anchor_url']); ?>" 
						style="<?php echo 'width:'.$md['width_x'].'px'; ?>"><?php echo $md['name']; ?></a>						
						<a class="rename-item" target="_top" href="<?php echo $md['path']; ?>" title="Rename" data-media="file" 
						data-raw-name="<?php echo $md['raw_name']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>															
					</div>
				</div>
			<?php else: ?>
				<div class="item media-item text-center md">
					<div class="cover"></div>					
					<div class="media-md">
						<input type="checkbox" name="rm[]" class="hidden" value="<?php echo $md['path']; ?>">
						<a href="<?php echo base_url($md['file_url']); ?>" target="_blank">
							<img src="<?php echo base_url($md['icon_url-32']); ?>" alt="<?php echo $md['name']; ?>" 
							style="width:32px;height:32px" width="32" height="32" />						
						</a>						
					</div>					
					<div class="media-link">
						<a class="media-name" href="<?php echo base_url($md['file_url']); ?>" target="_blank">
						<?php echo $md['name']; ?></a>																		
						<a class="rename-item" target="_top" href="<?php echo $md['path']; ?>" title="Rename" data-media="file" 
						data-raw-name="<?php echo $md['raw_name']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>						
					</div>
				</div>	
			<?php endif; ?>
			<?php $i++; ?>
		<?php endforeach; ?>					
	<?php endif; ?>
</div>