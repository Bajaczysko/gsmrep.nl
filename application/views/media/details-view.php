<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th></th>
			<th>Preview</th>
			<th>Name</th>
			<th style="width:15%">Dimensions (px)</th>
			<th style="width:8%">File size</th>
			<th style="width:8%">Rename</th>
			<th style="width:8%">Delete</th>
		</tr>
	</thead>
	<tbody>		
		<?php $path = $this->session->userdata('path'); ?>
		<?php if(!empty($path)): // Up button if inside folder ?>
			<tr>						
				<td colspan="7"><a class="mediapath" href="up"><span class="glyphicon glyphicon-arrow-up"></span></a></td>
			</tr>
		<?php endif; ?>
		<?php if(isset($folders)): // Media List of folders ?>
			<?php foreach($folders as $folder): ?>
				<tr>
					<td><input type="checkbox" name="rm[]" value="<?php echo $folder['path']; ?>"></td>
					<td><a class="mediapath" href="<?php echo $folder['path']; ?>">
					<span class="glyphicon glyphicon-folder-close"></span></a></td>
					<td><a class="mediapath" href="<?php echo $folder['path']; ?>"><?php echo $folder['name']; ?></a></td>
					<td></td>
					<td></td>
					<td><a class="btn btn-info btn-xs rename-item" target="_top" href="<?php echo $folder['path']; ?>" title="Rename" 
					data-media="folder" data-raw-name="<?php echo $folder['name']; ?>">
					<span class="glyphicon glyphicon-pencil"></span></a></td>					
					<td><a class="btn btn-danger btn-xs delete-item" target="_top" href="<?php echo $folder['path']; ?>" title="Delete" 
					data-media="folder"><span class="glyphicon glyphicon-trash"></span></a></td>									
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if(isset($media)): // Media List ?>
			<?php $i = 0; ?>
			<?php foreach($media as $md): ?>
				<?php if($md['file_type'] == 'image'): ?>				
					<tr>
						<td><input type="checkbox" name="rm[]" value="<?php echo $md['path']; ?>"></td>
						<td><a class="galleryItem" data-group="3" href="<?php echo base_url($md['anchor_url']); ?>">
						<img src="<?php echo base_url($md['file_url']); ?>" alt="<?php echo $md['name']; ?>" 
						style="<?php echo 'width:'.$md['width_16'].'px;height:'.$md['height_16'].'px'; ?>" 
						width="<?php echo $md['width_16']; ?>" height="<?php echo $md['height_16']; ?>" /></a></td>
						<td><a class="galleryItem" data-group="4" href="<?php echo base_url($md['anchor_url']); ?>">
						<?php echo $md['name']; ?></a></td>
						<td><?php echo $md['width'].' &#215; '.$md['height']; ?></td>
						<td><?php echo $md['size']; ?></td>
						<td><a class="btn btn-info btn-xs rename-item" target="_top" href="<?php echo $md['path']; ?>" title="Rename" 
						data-media="file" data-raw-name="<?php echo $md['raw_name']; ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>									
						<td><a class="btn btn-danger btn-xs delete-item" target="_top" href="<?php echo $md['path']; ?>" title="Delete" 
						data-media="file"><span class="glyphicon glyphicon-trash"></span></a></td>									
					</tr>
				<?php else: ?>
					<tr>
						<td><input type="checkbox" name="rm[]" value="<?php echo $md['path']; ?>"></td>
						<td><a href="<?php echo base_url($md['file_url']); ?>" target="_blank">
						<img src="<?php echo base_url($md['icon_url-16']); ?>" alt="<?php echo $md['name']; ?>" 
						style="width:16px;height:16px" width="16" height="16"/></a></td>
						<td><a href="<?php echo base_url($md['file_url']); ?>" target="_blank"><?php echo $md['name']; ?></a></td>
						<td></td>
						<td><?php echo $md['size']; ?></td>
						<td><a class="btn btn-info btn-xs rename-item" target="_top" href="<?php echo $md['path']; ?>" title="Rename" 
						data-media="file" data-raw-name="<?php echo $md['raw_name']; ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>									
						<td><a class="btn btn-danger btn-xs delete-item" target="_top" href="<?php echo $md['path']; ?>" title="Delete" 
						data-media="file"><span class="glyphicon glyphicon-trash"></span></a></td>									
					</tr>
				<?php endif; ?>
				<?php $i++; ?>
			<?php endforeach; ?>
		<?php endif; ?>			
	</tbody>		
</table>