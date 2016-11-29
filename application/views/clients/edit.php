<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print empty($result->id) ? lang('new_client') : lang('edit_client')?>
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
					<label><?php print lang('client_name')?>*</label>
					<input type="text" class="form-control" name="name" value="<?php print set_value('name', empty($result->name) ? '' : $result->name) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('email')?></label>
					<input type="text" class="form-control" name="email" value="<?php print set_value('email', empty($result->email) ? '' : $result->email) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('address1')?></label>
					<input type="text" class="form-control" name="address1" value="<?php print set_value('address1', empty($result->address1) ? '' : $result->address1) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('address2')?></label>
					<input type="text" class="form-control" name="address2" value="<?php print set_value('address2', empty($result->address2) ? '' : $result->address2) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('city')?></label>
					<input type="text" class="form-control" name="city" value="<?php print set_value('city', empty($result->city) ? '' : $result->city) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('state')?></label>
					<input type="text" class="form-control" name="state" value="<?php print set_value('state', empty($result->state) ? '' : $result->state) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('postcode')?></label>
					<input type="text" class="form-control" name="postcode" value="<?php print set_value('postcode', empty($result->postcode) ? '' : $result->postcode) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('country')?></label>
					<input type="text" class="form-control" name="country" value="<?php print set_value('country', empty($result->country) ? '' : $result->country) ?>"/>
				</div>
				
				<input type="submit" class="btn btn-success" value="<?php print empty($result->id) ? lang('create') : lang('save')?>"/>
				<a href="<?php print base_url($this->controller)?>" class="btn btn-default"><?php print lang('cancel')?></a>
            	<?php print form_close()?>
			</div>
		</div>
	</div>	<!-- /.container-fluid -->

</div>