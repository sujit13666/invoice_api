<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print lang($this->controller)?>
					<a href="<?php print base_url($this->controller.'/edit') ?>" class="btn btn-default pull-right margin-left10"><?php print lang('new_item')?></a>
					
					
						<?php print form_open(base_url($this->controller.'/'.$this->method),
            							array('id'=>'search-form','class'=>'col-lg-2 pull-right','name'=>'search_form'),
            							array("fn" => md5($this->controller.$this->method)) ); ?>
            							<div class="form-group input-group">
                                <input type="text" class="form-control" name="search_text" value="<?php print empty($search) ? '':  $search?>">
                                <span class="input-group-btn"><button class="btn btn-default" type="submit" name="search_button" value="1"><i class="fa fa-search"></i></button></span>
                                </div>
                         <?php print form_close()?>
                            
				</h1>
			</div>
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-lg-12">
            	<div class="table-responsive">
            		<table class="table table-bordered table-hover table-striped">
                    	<thead>
                        	<tr>
                            	<?php if ($this->ion_auth->is_admin()) print '<th>'.lang('user').'</th>' ?>
                            	<th><?php print lang('client_name')?></th>
                            	<th><?php print lang('email')?></th>
                            	<th><?php print lang('address1')?></th>
                            	<th><?php print lang('address2')?></th>
                            	<th><?php print lang('city')?></th>
                            	<th><?php print lang('state')?></th>
                            	<th><?php print lang('postcode')?></th>
                            	<th><?php print lang('country')?></th>
                            	<th></th>
                        	</tr>
                         </thead>
                     	<tbody>
                     		<?php 
                     		if (isset($result) && isset($result[0]))
                     		{
                     			foreach($result as $key=>$val)
                     			{
                     				print '<tr>';
                     					if ($this->ion_auth->is_admin()) print '<td>'.$val->full_name.'</td>';	                     				
	                     				print '<td>'.$val->name.'</td>';
	                     				print '<td>'.$val->email.'</td>';
	                     				print '<td>'.$val->address1.'</td>';
	                     				print '<td>'.$val->address2.'</td>';
	                     				print '<td>'.$val->city.'</td>';
	                     				print '<td>'.$val->state.'</td>';
	                     				print '<td>'.$val->postcode.'</td>';
	                     				print '<td>'.$val->country.'</td>';
	                     				print '<td>';
	                     					print '<a href="'.base_url($this->controller.'/delete/'.$val->rid).'" class="pull-right"><i class="fa fa-trash delete-item" data-message="'.lang('delete_client_question').'"></i></a>';
		                     				
		                     				print '<a href="'.base_url($this->controller.'/edit/'.$val->rid).'" class="pull-right margin-right10"><i class="fa fa-edit"></i></a>';
	                     				print '</td>';
                     				print '</tr>';
                     			}
                     		}                     		
                     		?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /.row -->
		
		<div class="row">
			<div class="col-lg-12">
				<span id="pagination"><?php print $pagination ?></span>
				<span class="pull-right">
					<?php print lang('showing').' <b>'.($offset == 0 ? 1 : $offset).' to '.( ($offset + $rows_per_page) > $total_rows ? $total_rows :  $offset + $rows_per_page).'</b> of '.$total_rows.' '.lang('entries');?>
				</span>
			</div>
		</div>
	</div>	<!-- /.container-fluid -->

</div>