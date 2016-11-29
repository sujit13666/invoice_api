<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print empty($result->id) ? lang('new_item') : lang('edit_item')?>
				</h1>
			</div>
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-lg-6">
				<?php 
					if (isset($error))
					{
						print '<div class="alert alert-danger">'.$error.'</div>';
					}
				
            		print form_open(base_url($this->controller.'/'.$this->method).'/'.(isset($result->rid) ? $result->rid : ''),
            							array('id'=>'user-form'),
            							array("fn" => md5($this->controller.$this->method)) ); ?>
            	
            	<?php if ($this->ion_auth->is_admin()) {?>
            	<div class="form-group">
					<label><?php print lang('user')?>*</label>
					<?php print my_form_dropdown('user_id',$users, set_value('user_id', empty($result->user_id) ? 0 : $result->user_id), 'class="form-control"');	?>
				</div>
                <?php }?>
                
                <div class="form-group">
					<label><?php print lang('item_name')?>*</label>
					<input type="text" class="form-control" name="name" value="<?php print set_value('name', empty($result->name) ? '' : $result->name) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('item_description')?></label>
					<textarea type="text" class="form-control" name="description"><?php print set_value('description', empty($result->description) ? '' : $result->description) ?></textarea>
				</div>
				<div class="form-group">
					<label><?php print lang('rate')?></label>
					<input type="text" class="form-control" name="rate" value="<?php print set_value('rate', empty($result->rate) ? '' : $result->rate) ?>"/>
				</div>
				
				<input type="submit" class="btn btn-success" value="<?php print empty($result->id) ? lang('create') : lang('save')?>"/>
				<a href="<?php print base_url($this->controller)?>" class="btn btn-default"><?php print lang('cancel')?></a>
            	<?php print form_close()?>
			</div>
		</div>
	</div>	<!-- /.container-fluid -->

</div>