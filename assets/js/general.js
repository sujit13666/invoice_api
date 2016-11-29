$(document).ready(function() {
	
	$('#delete-logo').click(function () {
		$('#delete-logo-input').val(1);
		
		$(this).parents('form').submit();
	});
	
	//Delete main invoice, estimate, client etc.
	if ($('.delete-item').length > 0)
	{
		$('.delete-item').click(function (e) {
			if (!window.confirm($(this).data('message'))) {
	            e.preventDefault();
	        }
		});
	}

	 
	 if ($( "#invoice-form" ).length > 0 || $( "#estimate-form" ).length > 0)
	 {
		 get_settings();
		 		 
		 $( "#user_id" ).change(function (){
			 get_settings();
		 });
		
		 if ($('#estimate_date').length > 0) $('#estimate_date').datepicker();
		 if ($('#invoice_date').length > 0) $('#invoice_date').datepicker();
		 if ($('#due_date').length > 0) $('#due_date').datepicker();
		 
		 if ($('.rate').length > 0)
		 {
			 $(document).on("change",".rate",function() {
				 calculate_total();
			 });
		 }
		 
		 if ($('.quantity').length > 0)
		 {
			 $(document).on("change",".quantity",function() {
				 calculate_total();
			 });
		 }
		 
		 if ($('#tax_rate').length > 0)
		 {
			 $(document).on("change","#tax_rate",function() {
				 calculate_total();
			 });
		 }
		 
		 if ($('#add-row').length > 0)
		 {
			 $('#add-row').click(function () {
				var row = $('.new-item-row').clone();
				
				$(row).removeClass('new-item-row').removeClass('hide').addClass('item-row');
				
				$(row).insertBefore($('#total-container'));
				
				attach_item_events();
			 });
		 }
		 
		 if ($("#client").length > 0)
		 {
			 $("#client").autocomplete({
				 source: function( request, response ) {
				        $.ajax({
				          url: site_url + "/clients/get_autocomplete",
				          dataType: 'json',
				          data: {
				            term: request.term,
				            user_id: $('#user_id').length > 0 ? $('#user_id').val() : 0
				          },
				          success: function( data ) {
				              response( data );
				          }
				        });
				      },
			      minLength: 2,
			      select: function( event, ui ) {
			    	  if (ui.item.id > 0)
			    	  {
				    	  $('#client_id').val(ui.item.id);
			    	  }
			    	  else
			    	  {
			    		  $('#client').val('');
				    	  $('#client_id').val('');
				    	  event.preventDefault();
			    	  }
			      }
			    });
		}
		 
		 //Delete invoice_lines estimate_lines within invoice/estimate
		 if ($('.delete-item-row').length > 0)
		 {
			 $(document).on("click",".delete-item-row",function() {
			        $(this).parents('.item-row').remove();
			        
			        calculate_total();
			 });
		 }
	 }
	 
	 function get_settings()
	 {
		 $.ajax({
	          url: site_url + "/settings/get_settings",
	          dataType: 'json',
	          data: {
	            user_id: $('#user_id').length > 0 ? $('#user_id').val() : 0
	          },
	          success: function( data ) {
	              if (data.success)
	              {
	            	  $('#currency_symbol').val(data.result.currency_symbol);
	            	  
	            	  if ($('#rid').length == 0)
	            	  {
		            	  if ($('#invoice_number').length > 0) $('#invoice_number').val(data.result.invoice_number);
		            	  if ($('#estimate_number').length > 0) $('#estimate_number').val(data.result.estimate_number);
	            	   }
	         		 calculate_total();
	              }
	          }
	        });
	 }
	
	 	 
	function attach_item_events()
	{		
		$('.item:last').autocomplete({
			 source: function( request, response ) {
			        $.ajax({
			          url: site_url + "/items/get_autocomplete",
			          dataType: 'json',
			          data: {
			            term: request.term,
			            user_id: $('#user_id').length > 0 ? $('#user_id').val() : 0
			          },
			          success: function( data ) {
			              response( data );
			          }
			        });
			      },
		      minLength: 2,
		      select: function( event, ui ) {
		    	  
		    	  var parentRow = $(this).parents('.row');
		    	  
		    	  if (ui.item.id > 0)
		    	  {
		    		  $('.item_id',parentRow).val(ui.item.id);
		    		  $('.rate',parentRow).val(ui.item.rate);
		    		  $('.description',parentRow).val(ui.item.description);
		    		  $('.quantity',parentRow).val(1);
		    	  }
		    	  else
		    	  {
		    		  $('.item_id',parentRow).val('');
		    		  $('.rate',parentRow).val('');
		    		  $('.quantity',parentRow).val('');
		    		  $('.description',parentRow).val('');
			    	  event.preventDefault();
		    	  }
		    	  
		    	  calculate_total();
		      }
		    });
	}
		
	function calculate_total()
	{
		var subtotal = 0;
		var total = 0;
		var tax = 0;
		var currency = $('#currency_symbol').val();
		var tax_rate = $('#tax_rate').val() == '' ? 0 : $('#tax_rate').val();
		
		$('.item-row').each(function(index, element) {
			
			var rate = $('.rate', element).val();
			var quantity = $('.quantity', element).val();
			var line_total = rate * quantity;
			
			if (isNaN(line_total)) line_total = 0;
			
			subtotal += line_total;
			
			$('.line-total', element).text(currency + (line_total).toFixed(2));
		});
		
		tax = (subtotal * tax_rate) / 100;
		total = subtotal + tax;
		
		if (isNaN(subtotal)) subtotal = 0;
		if (isNaN(tax)) tax = 0;
		if (isNaN(total)) total = 0;
		
		$('#subtotal-value').text(currency + (subtotal).toFixed(2));		
		$('#tax-value').text(currency + (tax).toFixed(2));
		$('#total-value').text(currency + (total).toFixed(2));
	}
});