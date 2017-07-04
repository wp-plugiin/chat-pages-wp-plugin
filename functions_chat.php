<?php
    require_once ('live_chat_settings_helper.php');
    require_once("db/wpdb_chat_queries.class.php");
    require_once("db/LiveChatSettings.php");  

 
    use CHAT_QUERIES\Chat_Queries;  


	function sendmailscript( ){
		global $wpdb, $FULLNAME, $FILECONTS;
		
		$current_user = wp_get_current_user( );
		
		$CLIENTID 	= $_POST[ 'contentz' ];
		//$MCONTENTS 	= "HELLO WORLD!";
		$FILESOURCE	= OF_FILEPATH . '/js/clients/code/'.$CLIENTID.'.txt';
		
		$FILECONTS 	= file_get_contents($FILESOURCE);
		
		$USEREMAIL	= $current_user->user_email;
		$USERFNAME	= $current_user->user_firstname;
		$USERLNAME	= $current_user->user_lastname;
		$USERUNAME 	= $current_user->user_login;
		$FULLNAME   = $USERFNAME . ' ' . $USERLNAME;
		$ADMINEMAIL = get_option( 'admin_email' );
		$TITLE		= get_field('email_subject','option');
		//$TITLE		= "Sample Title";
		$FROMNAME	= get_field('email_name','option');
		
		$MESSAGEC	= get_field('email_message','option');
		
		$TO			= $USEREMAIL;
		$HEADERS	= 'From: ' . $FROMNAME . ' <' . $ADMINEMAIL . '>' . "\r\n";
		$ECONTENT	= $MESSAGEC;
		//$ECONTENT	= "Sample Message";
		add_filter( 'wp_mail_content_type', 'set_html_content_type' );
		
		$STATUS 	= wp_mail($TO, $TITLE, $ECONTENT, $HEADERS);
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
 		 
		if($STATUS){
			echo 'SUCCESS';
		}else{
			echo 'FAILED';
		}  
		die( ); 
		
	}
	add_action('wp_ajax_sendmailscript', 'sendmailscript');
	
	function processwidget( ){
		global $wpdb;
		$ERR_MSG_NOICON		= "Live Chat Icons must be selected.";
		$ERR_MSG_NOCICON	= "Custom Live Chat Icons must have valid Image URL.";
		$ERR_MSG_FAIL		= "1 Your chat has not made any changes. If you feel that this error needs help, please feel free to contact us.";
		$ERR_MSG_IMGNF		= "Custom Chat Icon does not have a valid Image URL.";
		$ERR_MSG_IMGSIZE	= "Custom Images must have 200px by 100px dimensions.";
		$ERR_MSG_NOTREG		= "Your account is not associated with our backend system.";
		$SUC_MSG_INSERT	 	= "Live Chat successfully created.";
		$SUC_MSG_UPDATE	 	= "Live Chat successfully updated.";	
		$P_V_CID		= $_POST[ 'p_clientid' ];
		$P_V_ICON 		= $_POST[ 'p_cicon' ];
		$P_V_CICON 		= $_POST[ 'p_obut' ];
		$P_V_CICOURLON 	= $_POST[ 'p_cctxt1' ];
		$P_V_CICOURLOFF = $_POST[ 'p_cctxt2' ];
		$P_V_CWINDOW 	= $_POST[ 'p_cwchat' ];
		$P_V_PACTIVE 	= $_POST[ 'p_cpactive' ];
		$P_V_POPEXIT 	= $_POST[ 'p_cexitpop' ];
		$CHECK_ID 		= $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "widgetoptions WHERE wid_accountid='" . $P_V_CID . "'");
		$response		= "";
		if(!$P_V_CID){
			$response = generate_response("error", $ERR_MSG_NOTREG);
		}elseif(empty($P_V_ICON) && $P_V_CICON == 0){
			$response = generate_response("error", $ERR_MSG_NOICON);
		}elseif($P_V_CICON == 1 && (empty($P_V_CICOURLON) || empty($P_V_CICOURLOFF))){
			$response = generate_response("error", $ERR_MSG_NOCICON);
		}else{
			if(empty($P_V_ICON) && (!empty($P_V_CICOURLON) || !empty($P_V_CICOURLOFF))){
				$IMG1 = @getimagesize($P_V_CICOURLON);
				$IMG2 = @getimagesize($P_V_CICOURLOFF);
				if(!$IMG1 || !$IMG2){
					$response = generate_response("error", $ERR_MSG_IMGNF);
				}elseif($IMG1[0] > 200 || $IMG1[1] > 100 || $IMG2[0] > 200 || $IMG2[1] > 100){
					$response = generate_response("error", $ERR_MSG_IMGSIZE);
				}else{
					$BTYPE = 'CUSTOM';
					//$CHECK_ID = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "widgetoptions WHERE wid_accountid = " . $getName[0]->id);
					
					if(count($CHECK_ID) > 0){
						$update_data = array(
							'wid_imgpathon'		=> $P_V_CICOURLON,
							'wid_imgpathoff'	=> $P_V_CICOURLOFF,
							'wid_chattype'		=> $P_V_CWINDOW,
							'wid_proactive'		=> $P_V_PACTIVE,
							'wid_exitpop'		=> $P_V_POPEXIT,
							'wid_buttontype'	=> $BTYPE
						);
						$update_format = array(
							'%s',
							'%s',
							'%s',
							'%d',
							'%d',
							'%s'
						);
						
						$query_update = $wpdb->update($wpdb->prefix . "widgetoptions", $update_data, array('wid_accountid' => $P_V_CID), $update_format, array('%s'));
						if($query_update){
							$response = generate_response("success", $SUC_MSG_UPDATE);
						}else{
							$response = generate_response("error", $ERR_MSG_FAIL);
						}
					}else{
						$insert_data = array(
							'wid_accountid'		=> $P_V_CID,
							'wid_imgpathon'		=> $P_V_CICOURLON,
							'wid_imgpathoff'	=> $P_V_CICOURLOFF,
							'wid_chattype'		=> $P_V_CWINDOW,
							'wid_proactive'		=> $P_V_PACTIVE,
							'wid_exitpop'		=> $P_V_POPEXIT,
							'wid_buttontype'	=> $BTYPE
						);
						$insert_format = array(
							'%s',
							'%s',
							'%s',
							'%s',
							'%d',
							'%d',
							'%s'
						);
						$query_insert = $wpdb->insert($wpdb->prefix . 'widgetoptions', $insert_data, $insert_format);
						
						if($query_insert){
							$response = generate_response("success", $SUC_MSG_INSERT);
						}else{
							$response = generate_response("error", $ERR_MSG_FAIL);
						}
					} 
					
				}
			}else{
				$BTYPE 		= 'DEFAULT';
				$IMG_ON 	= str_replace("CHATICON-OFF", "CHATICON-ON", $P_V_ICON);
				$IMG_OFF	= $P_V_ICON;
				if(count($CHECK_ID) > 0){
					
					$update_data = array(
						'wid_imgpathon'		=> $IMG_ON,
						'wid_imgpathoff'	=> $IMG_OFF,
						'wid_chattype'		=> $P_V_CWINDOW,
						'wid_proactive'		=> $P_V_PACTIVE,
						'wid_exitpop'		=> $P_V_POPEXIT,
						'wid_buttontype'	=> $BTYPE
					);
					$update_format = array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%s'
					);
					
					$query_update = $wpdb->update($wpdb->prefix . "widgetoptions", $update_data, array('wid_accountid' => $P_V_CID), $update_format, array('%s'));
					if($query_update){
						$response = generate_response("success", $SUC_MSG_UPDATE);
					}else{
						$response = generate_response("error", $ERR_MSG_FAIL);
					}
				}else{
					$insert_data = array(
						'wid_accountid'		=> $P_V_CID,
						'wid_imgpathon'		=> $IMG_ON,
						'wid_imgpathoff'	=> $IMG_OFF,
						'wid_chattype'		=> $P_V_CWINDOW,
						'wid_proactive'		=> $P_V_PACTIVE,
						'wid_exitpop'		=> $P_V_POPEXIT,
						'wid_buttontype'	=> $BTYPE
					);
					$insert_format = array(
						'%s',
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%s'
					);
					$query_insert = $wpdb->insert($wpdb->prefix . 'widgetoptions', $insert_data, $insert_format);
					
					if($query_insert){
						$response = generate_response("success", $SUC_MSG_INSERT);
					}else{
						$response = generate_response("error", $ERR_MSG_FAIL);
					}
				}
			}
		}
		echo $response;			
		die( );
	}	
	add_action('wp_ajax_processwidget', 'processwidget');
	
	function process_icons(){
 		


		global $wpdb;
		
		$ERR_MSG_NOICON		= "Please select a Live Chat Icons before you can proceed.";
		$ERR_MSG_NOCICON	= "Custom Live Chat Icons must have valid Image URL.";
		$ERR_MSG_FAIL		= "2 Your chat has not made any changes. If you feel that this error needs help, please feel free to contact us.";
		$ERR_MSG_IMGNF		= "Custom Chat Icon does not have a valid Image URL.";
		$ERR_MSG_IMGSIZE	= "Custom Images must have 200px by 100px dimensions.";
		$ERR_MSG_NOTREG		= "Your account is not associated with our backend system.";	
		
		$P_V_CID			=  (pnw_is_local() == true) ? 77333 : $_POST[ 'p_clientid' ];
		$P_V_ICON 			= $_POST[ 'p_cicon' ];
		$P_V_CICON 			= $_POST[ 'p_obut' ];
		$P_V_CICOURLON 		= $_POST[ 'p_cctxt1' ];
		$P_V_CICOURLOFF 	= $_POST[ 'p_cctxt2' ];
		
		$CHECK_ID 		= $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "chat_icons WHERE ci_accountid='" . $P_V_CID . "'");
		$response		= "";
		if(!$P_V_CID){
			$response = generate_response("error", $ERR_MSG_NOTREG);
		}elseif(empty($P_V_ICON) && $P_V_CICON == 0){
			$response = generate_response("error", $ERR_MSG_NOICON);
		}elseif($P_V_CICON == 1 && (empty($P_V_CICOURLON) || empty($P_V_CICOURLOFF))){
			$response = generate_response("error", $ERR_MSG_NOCICON);
		}else{
			if(empty($P_V_ICON) && (!empty($P_V_CICOURLON) || !empty($P_V_CICOURLOFF))){
				$IMG1 = @getimagesize($P_V_CICOURLON);
				$IMG2 = @getimagesize($P_V_CICOURLOFF);
				if(!$IMG1 || !$IMG2){
					$response = generate_response("error", $ERR_MSG_IMGNF);
				}elseif($IMG1[0] > 200 || $IMG1[1] > 100 || $IMG2[0] > 200 || $IMG2[1] > 100){
					$response = generate_response("error", $ERR_MSG_IMGSIZE);
				}else{
					$BTYPE = 'CUSTOM';
					//$CHECK_ID = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "widgetoptions WHERE wid_accountid = " . $getName[0]->id);
					
					if(count($CHECK_ID) > 0){
						$update_data = array(
							'ci_imgpathon'	=> $P_V_CICOURLON,
							'ci_imgpathoff'	=> $P_V_CICOURLOFF,
							'ci_buttontype'	=> $BTYPE
						);
						$update_format = array(
							'%s',
							'%s',
							'%s'
						);
						
						$query_update = $wpdb->update($wpdb->prefix . "chat_icons", $update_data, array('ci_accountid' => $P_V_CID), $update_format, array('%s'));
						if($query_update){
							$response = "success";
						}else{
							$response = generate_response("error", $ERR_MSG_FAIL);
						}
					}else{
						$insert_data = array(
							'ci_accountid'	=> $P_V_CID,
							'ci_imgpathon'	=> $P_V_CICOURLON,
							'ci_imgpathoff'	=> $P_V_CICOURLOFF,
							'ci_buttontype'	=> $BTYPE
						);
						$insert_format = array(
							'%s',
							'%s',
							'%s',
							'%s'
						);
						
						$insert_datao = array(
							'co_accountid'	=> $P_V_CID,
							'co_chattype'	=> 1,
							'co_proactive'	=> 1,
							'co_exitpop'	=> 1,
							'co_chatformat'	=> 1
						);
						$insert_formato = array(
							'%s',
							'%d',
							'%d',
							'%d',
							'%d'
						);
						$query_insert 	= $wpdb->insert($wpdb->prefix . 'chat_icons', $insert_data, $insert_format);
						$query_inserto 	= $wpdb->insert($wpdb->prefix . 'chat_options', $insert_datao, $insert_formato);
						
						if($query_insert && $query_inserto){
							$response = "success";
						}else{
							$response = generate_response("error", $ERR_MSG_FAIL);
						}
					} 
					
				}
			}else{
				$BTYPE 		= 'DEFAULT';
				$IMG_ON 	= str_replace("CHATICON-OFF", "CHATICON-ON", $P_V_ICON);
				$IMG_OFF	= $P_V_ICON;
				if(count($CHECK_ID) > 0){
					
					$update_data = array(
						'ci_imgpathon'	=> $IMG_ON,
						'ci_imgpathoff'	=> $IMG_OFF,
						'ci_buttontype'	=> $BTYPE
					);
					$update_format = array(
						'%s',
						'%s',
						'%s'
					);
					
					$query_update = $wpdb->update($wpdb->prefix . "chat_icons", $update_data, array('ci_accountid' => $P_V_CID), $update_format, array('%s'));
					if($query_update){
						$response = "success";
					}else{
						$response = generate_response("error", $ERR_MSG_FAIL);
					}
				}else{
					$insert_data = array(
						'ci_accountid'		=> $P_V_CID,
						'ci_imgpathon'		=> $IMG_ON,
						'ci_imgpathoff'	=> $IMG_OFF,
						'ci_buttontype'	=> $BTYPE
					);
					$insert_format = array(
						'%s',
						'%s',
						'%s',
						'%s'
					);
					
					$insert_datao = array(
						'co_accountid'	=> $P_V_CID,
						'co_chattype'	=> 1,
						'co_proactive'	=> 1,
						'co_exitpop'	=> 1,
						'co_chatformat'	=> 1
					);
					$insert_formato = array(
						'%s',
						'%d',
						'%d',
						'%d',
						'%d'
					);
					$query_insert 	= $wpdb->insert($wpdb->prefix . 'chat_icons', $insert_data, $insert_format);
					$query_inserto 	= $wpdb->insert($wpdb->prefix . 'chat_options', $insert_datao, $insert_formato);
					
					if($query_insert && $query_inserto){
						$response = "success";
					}else{
						$response = generate_response("error", $ERR_MSG_FAIL);
					}
				}
			}
		}
		
		echo $response;
		die();
	}
	add_action('wp_ajax_process_icons', 'process_icons');
	
	function processscript( ){
		$clientid	= $_POST[ 'clientid' ];
		$domainn	= $_POST[ 'domainn' ];
		$scripturl	= $_POST[ 'scripturl' ];		
		$proactives	= $_POST[ 'proactive' ];
		$exitpops	= $_POST[ 'exitpopup' ];
		$filesrz	= OF_FILEPATH . '/js/clients/'.$clientid.'.js';
		$scriptz	= '
				function load_chat( ){
					var chatURL, po, s;
					var acct = '.$clientid.';
					
					var LHCChatOptions = {};
					LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:520,popup_width:500};
								
					var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf(\'://\')+1)) : \'\';
					var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : \'\';
					
					po = document.createElement(\'script\');
					po.type = \'text/javascript\'; 
					po.async = true;
					s = document.getElementsByTagName(\'script\')[0];
					
					chatURL = \'//livewebchatcode.com/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true'.($proactives == 0 ? '/(disable_pro_active)/true' : '').'/(theme)/1?r=\'+referrer+\'&l=\'+location+\'&idn=\'+acct;

					po.src = chatURL;					 
					s.parentNode.insertBefore(po, s);
					
					'.($exitpops == 1 ? 'bind_exitpop( );' : '').'
				}

				function trigger_click( ){
					lh_inst.lh_openchatWindow( );
					setTimeout(function( ) {
						$(\'#lhc_remote_window\').trigger(\'click\');
					}, 1000);
					
				}
				function bind_exitpop( ){
					$(window).bind(\'beforeunload\', function( ){
						return \'Please dont go!\';
					});
				}
		';
		file_put_contents($filesrz, $scriptz, LOCK_EX);		
		$tfilesrc	= OF_FILEPATH . '/js/clients/code/'.$clientid.'.txt';
		$iframesrc 	= htmlentities('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<script src="'.get_template_directory_uri( ).'/js/clients/'.$clientid.'.js"></script>
		<script type="text/javascript">
			$(document).ready(function( ) {
		var Vclientid 	= \''.$clientid.'\';
		var Vhashid	= \''.md5($clientid).'\';
		var parts = window.location.hostname.split('.');
		var subdomain = parts.shift( );
		var upperleveldomain = parts.join('.');
		$.ajax({
		   type: "POST", // HTTP method POST or GET
		   url: "http://testing.umbrellasupport.co.uk/chat-process/", //Where to make Ajax calls
		   //dataType:"text", // Data type, HTML, json etc.
		   data:{
			   clientid:Vclientid,
			   hashid:Vhashid,
			   domainn:upperleveldomain
		   },
		   success:function(response){
			 //responseaction
			$(\'#chatcontainer\').html(response);
			if(response == \'<img src="http://testing.umbrellasupport.co.uk/wp-content/uploads/2016/07/NotAllowed.png">\'){
				
			}else{
				load_chat( );
			}
		   },
		   error:function (xhr, ajaxOptions, thrownError){
			alert("Error: " + thrownError);
		   }
		});
			});
		</script>

		<div id="chatcontainer"></div>');  
		file_put_contents($tfilesrc, $iframesrc, LOCK_EX);		
		$contents = file_get_contents($tfilesrc);		
		echo $contents . ' ';
		die( );
	}
	add_action('wp_ajax_processscript', 'processscript');
	
	function generate_response($type, $message){
		if($type == "success") $response = "<div class='success'>".$message."</div>";
		else $response = "<div class='error'>".$message."</div>";
		
		return $response;
	} 
	
	function get_site_client(){
		global $wpdb;
		$PARTNERID	= $_POST['pid'];
		$QUEGETSITE = "SELECT * FROM " . $wpdb->prefix . "clientsites WHERE s_accountid='" . $PARTNERID . "'";
		$RESULTGETS = $wpdb->get_results($QUEGETSITE);
		
		return $RESULTGETS;
	}

	add_action('wp_ajax_process_domain', 'process_domain');
	function process_domain() 
	{	   
		// Initialized db class
		$chat_queries = new Chat_Queries('wp_clientsites'); 
 
		// Delete all the domain by partner id 
		$chat_queries->wpdb_delete(['s_accountid'=>getCurrentLogggedInAccountId()]); 
 
		// Get serialized post request
		$request = $_REQUEST;
		
		// For each request  
		if(!empty($request['pnw_domain_values'])) {  
			foreach($request['pnw_domain_values'] as $pnw_domain_value) { 
				$chat_queries->wpdb_insert(
					[
						's_accountid'=>getCurrentLogggedInAccountId(), 
						's_website' => $pnw_domain_value, 
						's_accountidhash' => base64_encode(getCurrentLogggedInAccountId()),
						's_status' => 1
					]
				);
			}  
		}

		return 'success';
 	 	  
	}

	function getCurrentLogggedInAccountId() 
	{
		if(pnw_is_local() == true) {  
			return 77333; 
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
 	 
 	add_action('wp_ajax_save_chat_settings', 'save_chat_settings');
 	/**
 	* Chat settings save or update
 	*/ 
	function save_chat_settings() 
	{ 
		$chat_queries = new Chat_Queries('wp_chat_options'); 

		$partner_id = getCurrentLogggedInAccountId();

		$request = $_REQUEST;
 
		$chatSettings = [ 		
			'co_chatformat' => ($request['LC_OPT'] != null) ? $request['LC_OPT'] : 1,
			'co_chattype'   => $request['p_cwchat'],
			'co_proactive'  => $request['p_cpactive'],
			'co_exitpop'    => $request['p_cexitpop'] 
		]; 

		$results = $chat_queries->wpdb_get_result("select * from wp_chat_options where co_accountid = " . $partner_id); 
  
		if(!empty($results)) {  
			$status = $chat_queries->wpdb_update(
				$chatSettings,
				[
					'co_accountid'=> $partner_id
				]
			); 
   
		} else {  

			$chatSettings['co_accountid'] = $partner_id;
			
			print_r($chatSettings); 

			$status = $chat_queries->wpdb_insert($chatSettings);

			if($status)
				print "insert success";
			else 
				print "insert failed";
		}  
	} 
?>