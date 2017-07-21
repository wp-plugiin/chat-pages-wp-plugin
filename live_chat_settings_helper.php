<?php 
 
if(!function_exists('pnw_is_local')) {  
	function pnw_is_local() 
	{ 
	    $whitelist = array( '127.0.0.1', '::1' );
	    if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) ) { 
	        return true;
	    }
	}
}


if(!function_exists("pnw_print_r_pre")) {
	function pnw_print_r_pre($array) {
		print "<pre>";
			print_r($array); 
		print "</pre>"; 
	}
}

if(!function_exists('pnw_partner_id')) {

	function pnw_partner_id() {  

	if(pnw_is_local() == true) {  

			return 77331; 

		} else {

			$current_user = wp_get_current_user();

			$API_URL	= 'http://api.ontraport.com/1/objects?';

			$API_DATA	= array(
				'objectID'		=> 0,
				'performAll'	=> 'true',
				'sortDir'		=> 'asc',
				'condition'		=> "email='".$current_user->user_email."'",
				'searchNotes'	=> 'true'
			);

			$API_KEY 					  	 = get_field('custom_api_key','option');
			$API_ID						     = get_field('custom_api_id','option');
			$chat_settings_page_title 	     = get_field('chat_settings_page_title','option'); 
			$chat_settings_title_description = get_field('chat_settings_title_description','option');
 
			$API_RESULT = op_query($API_URL, 'GET', $API_DATA, $API_ID, $API_KEY);

			$getName = json_decode($API_RESULT); 

			return $getName->data[0]->id; 

		} 
	}
}