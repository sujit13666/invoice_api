<div class="container" id="login-container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<h1 class="color-light-grey"><?php echo lang('login_heading');?></h1>

			<?php if (isset($error)) print '<div class="alert alert-danger">'.$error.'</div>';?>
			
			<?php
            print form_open(base_url($this->controller . '/' . $this->method), array(
                'id' => 'user-form'
            ), array(
                "fn" => md5($this->controller . $this->method)
            ));
            ?>
			
				<div class="form-group">
				<label class="color-light-grey" for="identity"><?php print lang('login_identity_label')?></label>
				<input type="text" class="form-control" id="identity" placeholder=""
					name="identity">
			</div>


			<div class="form-group">
				<label class="color-light-grey" for="identity"><?php print lang('login_password_label')?></label>
				<input type="password" class="form-control" id="password"
					placeholder="" name="password">
			</div>

			<input type="submit" class="btn btn-default"
				value="<?php print lang('login_submit_btn')?>">
			
			<?php echo form_close();?>
        </div>
	</div>
</div>

