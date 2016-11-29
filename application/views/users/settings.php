<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print lang('my_settings')?>
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
            							array('id'=>'user-form','class'=>'padding-bottom10','enctype'=>'multipart/form-data'),
            							array("fn" => md5($this->controller.$this->method)) ); ?>

            	<input type="hidden" id="delete-logo-input" name="delete_logo" value="">
            	
                <div class="form-group">
					<label><?php print lang('firstname')?>*</label>
					<input type="text" class="form-control" name="first_name" value="<?php print set_value('first_name', empty($result->first_name) ? '' : $result->first_name) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('lastname')?>*</label>
					<input type="text" class="form-control" name="last_name" value="<?php print set_value('last_name', empty($result->last_name) ? '' : $result->last_name) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('email')?>*</label>
					<input type="text" class="form-control" name="email" value="<?php print set_value('email', empty($result->email) ? '' : $result->email) ?>"/>
				</div>
				<div class="form-group">
					<label><?php print lang('password')?></label>
					<input type="password" class="form-control" name="password"/>
				</div>
				<div class="form-group">
					<label><?php print lang('currency')?></label>
					<input type="text" class="form-control" name="currency_symbol" value="<?php print set_value('currency_symbol', empty($result->currency_symbol) ? '' : $result->currency_symbol) ?>"/>
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
			     <div class="form-group">
					<label><?php print lang('logo')?></label>
					<div>Following logo formats are supported: PNG, JPG, GIF and JPEG.</div>
					<div class="margin-bottom20">Max. allowed logo dimension 180 x 80 and 2MB in size.</div>
					<input type="file" name="userfile" />
					<?php 
					   if (!empty($result->logo_path))
					   {
					       print '<div class="margin-top20 margin-bottom20"><img src="'.base_url("assets/uploads/".$result->rid.'/'.$result->logo_path).'"/></div>';
					       print '<div class="cursor-pointer" id="delete-logo"><a>Delete Logo</a></div>';
					   }
					?>
					
				</div>
				
				<p class="margin-top50">Logout and log back in your mobile app to see the changes.</p>
			
				<input type="submit" class="btn btn-success" value="<?php print empty($result->id) ? lang('create') : lang('save')?>"/>
				
            	<?php print form_close()?>
			</div>
		</div>
	</div>	<!-- /.container-fluid -->

</div>