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