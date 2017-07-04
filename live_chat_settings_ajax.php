<script>
$(document).ready(function()
{ 

	// When click Yes or No for "Have your own buttons?", it should remove the selected icon and restore to normal ui
	$("#S_OB_R1, #S_OB_L1").click(function(){  

		// Set not clicked select in step 1 to visible and can be clickable again  
		$("#pnw-select-div label").each(function( index  ){  
			$(this).css('z-index','2');   
		});  

	})

	// Click the select button in step 1
	$("#pnw-select-div #pnw-select").click(function(){ 

		// Set not clicked select in step 1 to visible and can be clickable again  
		$("#pnw-select-div label").each(function( index  ){  
			$(this).css('z-index','2');   
		});  

		// Set clicked selected as in backward
		$(this).css('z-index', '0');  

	}); 
 
	$('ul.ctabs li').click(function(){

		// check tab with 
		if($(this).attr('data-visited') == 'active') {   

			var tab_id = $(this).attr('data-tab');
			$('ul.ctabs li').removeClass('current');
			$('.ctab-content').removeClass('current'); 
			$(this).addClass('current');
			$("#"+tab_id).addClass('current');

		}
 
	});

	disablefields();
});

function disablefields()
{
	$("#p_ctxt1").prop('disabled',true);
	$("#p_ctxt1").val(null);
	$("#p_ctxt2").prop('disabled',true);
	$("#p_ctxt2").val(null);
	$("#S_OB_L1").prop('checked', true);
	$("#S_OB_R1").prop('checked', false);
}

function enablefields()
{
	$("#p_ctxt1").prop('disabled',false);
	$("#p_ctxt2").prop('disabled',false);
	<?php
		if($getRowsCI->ci_buttontype != "CUSTOM"){
			echo '$("#p_ctxt1").val(null);';
			echo '$("#p_ctxt2").val(null);';
		}else{
			echo '$("#p_ctxt1").val(\''.$getRowsCI->ci_imgpathon.'\');';
			echo '$("#p_ctxt2").val(\''.$getRowsCI->ci_imgpathoff.'\');';
		}
	?>
	$(".switch-input").prop('checked', false);
} 

function processicons()
{

	console.log("Test");
	var managechat = $('#manageicons').serialize();
	$('#preloader').show();

	jQuery.ajax({
	   
	   type: "POST", // HTTP method POST or GET
	   
	   url: "<?php echo admin_url('admin-ajax.php'); ?>", //Where to make Ajax calls
	   //dataType:"text", // Data type, HTML, json etc.
	   
	   data:managechat,

	   success:function(response){
 	 
	   	console.log( " response = " + response); 
	  
	 	if (response == 0 || 
           response == "success" || 
           response == 'success' || 
           response === "success" || 
           response === 'success' || 
           response.indexOf("success") > -1
        ) {    


		 	console.log("success");  
			changeTab('open tab 2');  
			
			// Set current tab visited and can be visited by time to time
			$("#menu-tab-2").attr('data-visited', 'active');

			// add data-visited attribute to active in step 1


		 } else { 

		 	console.log("failed"); 
			 $('#error_container').html(response); 

		 } 

		//get_script();
	   },

	   error:function (xhr, ajaxOptions, thrownError){

		alert("Error: " + thrownError);

	   },

	   complete: function(){

			$('#preloader').hide();

	   }

	});

	return false;
} 

/**
 *  Step 2 field validation 
 */
function processWebsite(action, id) 
{ 

	switch(action) {  

		case 'Add Domain': 

				var domain = document.getElementById('pdw-domain-validation').value;  
				var total  = document.getElementById('pnw-total-site');   
				var total_counter  = document.getElementById('pnw-total-site-counter');   

				// Hide empty text when no domain found and added new domain
				$(".dataTables_empty").css('display', 'none');  

				if (isValidURL(domain)) {   

					if(total_counter.value < 5) {  
 
	 					// Increment total sites 
						total.value = parseInt(total.value) + 1 ; 

	 					// Increment total sites counter
						total_counter.value = parseInt(total_counter.value) + 1 ; 

	  					// Append new html 
						$("#web_list tbody").append("<tr id='pnw-domain-container-"+total.value+"'><td>" + total.value + "</td><td><span id='pnw-domain-text-"+total.value+"' >"+domain+"</span><input type='hidden' value='"+domain+"' name='pnw_domain_values[]' id='pnw-domain-value-"+total.value+"' /></td><td>Active</td><td><button type='button'  onclick='processWebsite(\"Edit Domain\", "+total.value+")' >Edit</button></td><td><button type='button' onclick='processWebsite(\"Delete Domain\", "+total.value+")'>Delete</button></td></tr>");  

					 		// Clean the domain field
 							$('#pdw-domain-validation').val('');
						  
					} else {	  

						alert(total_counter.value + " domain allowed for now.");
 
						// display message if exceed 5 domain
						// $("#pnw-adding-domain-message").html("<div class='alert alert-danger'><b>5 domain allowed for now.</b></div>");  

					}

					 console.log("Domain is valid and now try to insert to database");  

				} else { 
					alert("Please provide valid domain."); 
				}
 
			break;

		case 'Edit Domain':  

				console.log( " id " + id + " Domain Value " + $("#pnw-domain-value-"+id).val()); 


 				// Get domain from display text and Add domain in the field to update
 				$('#pdw-domain-validation').val($("#pnw-domain-value-"+id).val()); 

 				// Assign id recent edited for update hit button
 				$edit_id = id;   
 
 				// show update button and hide add button
 				$("#pnw-domain-add-button").css('display', 'none');
 				$("#pnw-domain-update-button").css('display', 'block'); 

			break;  

		case 'Update Domain':  
 
				// Update domain display text 
 				$("#pnw-domain-text-"+$edit_id).html($('#pdw-domain-validation').val());

				// Update domain input hidden text
 				$("#pnw-domain-value-"+$edit_id).val($('#pdw-domain-validation').val());
 		  
 				// Clean the domain field
 				$('#pdw-domain-validation').val('');

 				// Show add button and hide update button
 				$("#pnw-domain-add-button").css('display', 'block');
 				$("#pnw-domain-update-button").css('display', 'none'); 

			break; 

		case 'Delete Domain':

				if(confirm('Are you sure you want to delete this domain?')) {

					$("#pnw-domain-container-" + id).html("");

					var total_counter  = document.getElementById('pnw-total-site-counter');   

					total_counter.value = parseInt(total_counter.value) - 1 ; 

				} 

			break;  

		case 'Save And Continue':

				// Get all the domain in serialized format 
				var data = $("#pnw_form").serialize(); 
 
				// Show loader
				$("#pnw-save-domain-loader").css("display", "block");

				// Send all the domain via REST API_KEYI in serialized format   
				jQuery.ajax({
				   
				   type: "POST", // HTTP method POST or GET
				   
				   url: "<?php echo admin_url('admin-ajax.php'); ?>", // Where to make Ajax calls
 				   
				   data:data,

				   success:function(response) { 
 
				   		if(response == 'success' || response== 0) { 
					   		// Alert response
					   		console.log(response);  
  							
  							// Set current tab visited and can be visited by time to time
					   		$("#menu-tab-3").attr("data-visited", 'active'); 
 
	 						// Proceed to tab 4
	 						changeTab('open tab 3');
 						}

						// Hide loader
 						$("#pnw-save-domain-loader").css("display", "none"); 

				   },

				   error:function (xhr, ajaxOptions, thrownError){

						alert("Error: " + thrownError);
						$("#pnw-save-domain-loader").css("display", "none");

				   },

				   complete: function(){
			 			
			 			$("#pnw-save-domain-loader").css("display", "none"); 

				   }

				});

			break;


		default:

			// console.log("Default");
			// console.log("Domain start validation..");  
			// var domain = document.getElementById('pdw-domain-validation').value;  
			// if (isValidURL(domain)) {

			// 	console.log("valid domain"); 
			// 	changeTab('open tab 3');  
			// } else { 

			// 	console.log("not valid domain");  
			// } 
		break;
	}

}




/**
 *  Step 3 hit save then should validate some fields 
 */
function proceeChatSettings()
{  

				console.log("proceess chat settings"); 
				// Set current tab visited and can be visited by time to time
				//$("#menu-tab-4").attr("data-visited", "active");

				//changeTab('open tab 4'); 


				// Get all the domain in serialized format 
				var data = $("#pnw_chat_settings").serialize(); 
 	
 				console.log(data); 

				// Show loader
			 	$("#pnw-chat-settings-loader").css("display", "block");

				// Send all the domain via REST API_KEYI in serialized format   
				jQuery.ajax({
				   
				   type: "POST", // HTTP method POST or GET
				   
				   url: "<?php echo admin_url('admin-ajax.php'); ?>", // Where to make Ajax calls
 				   
				   data:data,

				   success:function(response) { 
 
				   		if(response == 'success' || response== 0) {  
  							// Set current tab visited and can be visited by time to time
					   		$("#menu-tab-3").attr("data-visited", 'active'); 
					   		$("#menu-tab-4").attr("data-visited", 'active'); 
  
	 						changeTab('open tab 4');
 						}

						// Hide loader
 					 	$("#pnw-chat-settings-loader").css("display", "none"); 

				   },

				   error:function (xhr, ajaxOptions, thrownError){

						alert("Error: " + thrownError);
					 	$("#pnw-chat-settings-loader").css("display", "none");

				   },

				   complete: function(){
			 			
			 			$("#pnw-chat-settings-loader").css("display", "none"); 

				   }

				});

}
 
/**
 *  Helper Javascript  
 */
function isValidURL(str) 
{

  	var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
  	
  	if(!regex .test(str)) {

    	console.log("Please enter valid URL.");
    	return false;

  	} else {

    	return true;

  	}
}

function changeTab(tab) 
{ 

	console.log(tab); 

	/** Set all tabs as not selected */
 	$('ul.ctabs li').removeClass('current');

 	/** Set all tab content as not selected */
 	$('.ctab-content').removeClass('current'); 

	if(tab == 'open tab 2') {
		
	 	/** Set second tab as selected  */
	 	$('#menu-tab-2').addClass('current');
	    $("#tab-2").addClass('current');

	} else if (tab == 'open tab 3') {
		
		 /** Set third tab as selected  */
	 	$('#menu-tab-3').addClass('current');
	    $("#tab-3").addClass('current');

	} else if (tab == 'open tab 4') {
		
		/** Set fourth tab as selected  */
	 	$('#menu-tab-4').addClass('current');
	    $("#tab-4").addClass('current');

	}   
} 
</script>