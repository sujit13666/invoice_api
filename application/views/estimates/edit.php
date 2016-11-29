<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print empty($result->id) ? lang('new_estimate') : lang('edit_estimate')?>
					<input type="hidden" name="currency_symbol" id="currency_symbol"/>
				</h1>
			</div>
		</div>
		<!-- /.row -->
<?php
		print form_open(base_url($this->controller.'/'.$this->method).'/'.(isset($result->rid) ? $result->rid : ''),
										array('id'=>'estimate-form'),
            							array("fn" => md5($this->controller.$this->method)) ); ?>
            <?php if (isset($result->rid)) print '<input type="hidden" id="rid" value="'.$result->rid.'">'?>
			<div class="row">
				<div class="col-lg-6">
					<?php 
						if (isset($error))
						{
							print '<div class="alert alert-danger">'.$error.'</div>';
						}
					?>
	            	
	            	<?php if ($this->ion_auth->is_admin()) {?>
	            	<div class="form-group">
						<label><?php print lang('user')?>*</label>
						<?php 
						if (!empty($result->rid) && !empty($result->user_id))
						{
							print my_form_dropdown('user',$users, set_value('user_id', empty($result->user_id) ? 0 : $result->user_id), 'class="form-control" id="user" disabled');
							print '<input type="hidden" id="user_id" name="user_id" value="'.set_value('user_id', empty($result->user_id) ? 0 : $result->user_id).'"></input>';
						}
						else
							print my_form_dropdown('user_id',$users, set_value('user_id', empty($result->user_id) ? 0 : $result->user_id), 'class="form-control" id="user_id"');
							?>
					</div>
	                <?php }?>
	              
	                <div class="form-group">
						<label><?php print lang('client')?>*</label>
						<input type="text" class="form-control" name="client" id="client" value="<?php print set_value('client', empty($result->client) ? '' : $result->client) ?>"/>
						<input type="hidden" name="client_id" id="client_id" value="<?php print set_value('client_id', empty($result->client_id) ? '' : $result->client_id) ?>"/>
					</div>
					
	                <div class="form-group">
						<label><?php print lang('estimate_number')?>*</label>
						<input type="text" class="form-control" id="estimate_number" name="estimate_number" value="<?php print set_value('estimate_number', empty($result->estimate_number) ? '' : sprintf('%04d',  $result->estimate_number)) ?>"/>
					</div>
					
					<div class="form-group">
						<label><?php print lang('estimate_date')?>*</label>
						<input type="text" readonly class="form-control" name="estimate_date" id="estimate_date" value="<?php print set_value('estimate_date', (empty($result->estimate_date) ? date('m/d/Y') : date('m/d/Y',$result->estimate_date))) ?>"/>
					</div>
					
					<div class="form-group">
						<label><?php print lang('expiry_date')?></label>
						<input type="text" readonly class="form-control" name="due_date" id="due_date" 	value="<?php print set_value('due_date', (empty($result->due_date) ? '' : date('m/d/Y',$result->due_date))) ?>"/>
					</div>
					
					<div class="form-group">
						<label><?php print lang('notes')?></label>
						<textarea class="form-control" name="notes" id="notes"><?php print set_value('notes', empty($result->notes) ? '' : $result->notes) ?></textarea>						
					</div>
								
					<div class="form-group">
						<label><?php print lang('tax_rate')?>(%)</label>
						<input type="text" class="form-control" id="tax_rate" name="tax_rate" value="<?php print set_value('tax_rate', empty($result->tax_rate) ? '' : $result->tax_rate) ?>"/>
					</div>
					
					<div class="form-group">
						<label class="margin-right10"><?php print lang('is_invoiced')?></label>
						<input type="checkbox"  name="is_invoiced" value="1" <?php if (!empty(set_value('is_invoiced', empty($result->is_invoiced) ? '' : $result->is_invoiced))) {print "checked"; } ?>/>
					</div>
					
					
				</div>
			</div>
			
			<div class="row margin-top20">
				<div class="col-lg-12">
					<a class="cursor-pointer" id="add-row"><?php print lang('add_item')?></a>
				</div>
			</div>
			
			<div class="row margin-top10 new-item-row hide">			
				<div class="col-lg-1 text-left">
					<div class="form-group">
						<label></label>
						<span class="display-block"><i class="fa fa-times cursor-pointer delete-item-row"></i></span>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="form-group">
						<label><?php print lang('item_name')?>*</label>
						<input type="text" class="form-control item" name="items[]"/>
						<input type="hidden" class="description" name="descriptions[]"/>
					</div>
				</div>
				<div class="col-lg-1">
					<div class="form-group">
						<label><?php print lang('rate')?></label>
						<input type="text" class="form-control rate" name="rates[]"/>
					</div>
				</div>
				<div class="col-lg-1">
					<div class="form-group">
						<label><?php print lang('quantity')?></label>
						<input type="text" class="form-control quantity" name="quantities[]"/>
					</div>
				</div>
				<div class="col-lg-1 text-right">
					<div class="form-group">
						<label><?php print lang('line_total')?></label>
						<span class="display-block line-total">00.00</span>
					</div>
				</div>
			</div>
			
			<?php if (isset($result->rid) && count($lines) > 0)      { 
					foreach($lines as $key => $val) {
				?>	        
		        <div class="row margin-top10 item-row">			
					<div class="col-lg-1 text-left">
						<div class="form-group">
							<label></label>
							<span class="display-block"><i class="fa fa-times cursor-pointer delete-item-row"></i></span>
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
							<label><?php print lang('item_name')?>*</label>
							<input type="text" class="form-control item" name="items[]" value="<?php print $val->name;?>"/>
							<input type="hidden" class="item_id" name="item_ids[]" />
							<input type="hidden" class="description" name="descriptions[]" value="<?php print $val->description;?>"/>
						</div>
					</div>
					<div class="col-lg-1">
						<div class="form-group">
							<label><?php print lang('rate')?></label>
							<input type="text" class="form-control rate" name="rates[]" value="<?php print $val->rate;?>"/>
						</div>
					</div>
					<div class="col-lg-1">
						<div class="form-group">
							<label><?php print lang('quantity')?></label>
							<input type="text" class="form-control quantity" name="quantities[]" value="<?php print $val->quantity;?>"/>
						</div>
					</div>
					<div class="col-lg-1 text-right">
						<div class="form-group">
							<label><?php print lang('line_total')?></label>
							<span class="display-block line-total">00.00</span>
						</div>
					</div>
				</div>			
	          <?php } }?>
	              	
			<div class="row text-right margin-top20" id="total-container">
				<div class="col-lg-6">
					<div class="row">
						<div class="col-lg-10"><?php print lang('subtotal')?></div>
						<div class="col-lg-2" id="subtotal-value">0.00</div>
					</div>
					<div class="row margin-top5">
						<div class="col-lg-10"><?php print lang('tax')?></div>
						<div class="col-lg-2" id="tax-value">0.00</div>
					</div>
					
					<hr/>
					
					<div class="row">
						<div class="col-lg-10"><strong><?php print lang('total')?></strong></div>
						<div class="col-lg-2"><strong id="total-value">0.00</strong></div>
					</div>
				</div>
			</div>
			
			<div class="row margin-top20">
				<div class="col-lg-12">
					<input type="submit" class="btn btn-success" value="<?php print empty($result->id) ? lang('create') : lang('save')?>"/>
					<a href="<?php print base_url($this->controller)?>" class="btn btn-default"><?php print lang('cancel')?></a>
				</div>
			</div>
		<?php print form_close()?>
	</div>	<!-- /.container-fluid -->

</div>