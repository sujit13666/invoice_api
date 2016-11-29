<div id="page-wrapper">
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php print lang($this->controller)?>
					<a href="<?php print base_url($this->controller.'/edit') ?>" class="btn btn-default pull-right margin-left10"><?php print lang('new_invoice')?></a>
					
					
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
                            	<th><?php print lang('invoice') ?> #</th>
                            	<th><?php print lang('client')?></th>
                            	<th><?php print lang('invoice_date')?></th>
                            	<th><?php print lang('due_date')?></th>
                            	<th><?php print lang('tax_rate')?>(%)</th>
                            	<th><?php print lang('is_paid')?></th>
                            	<th><?php print lang('notes')?></th>
                            	<th><?php print lang('total')?></th>
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
	                     				print '<td>'.sprintf('%04d',  $val->invoice_number).'</td>';
	                     				print '<td>'.$val->client_name.'</td>';
	                     				print '<td>'.date('m-d-Y',$val->invoice_date).'</td>';
	                     				print '<td>'.(empty($val->due_date) ? '' : date('m-d-Y',$val->due_date)).'</td>';
	                     				print '<td>'.number_format($val->tax_rate,2).'</td>';
	                     				print '<td>'.($val->is_paid == 1 ? lang('yes') : lang('no')).'</td>';
	                     				print '<td>'.$val->notes.'</td>';
	                     				print '<td>'.number_format($val->total,2).'</td>';
	                     				print '<td>';
	                     					print '<a href="'.base_url($this->controller.'/delete/'.$val->rid).'" class="pull-right"><i class="fa fa-trash delete-item" data-message="'.lang('delete_invoice_question').'"></i></a>';
		                     				
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