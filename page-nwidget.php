<?php

	/**
	 * Template Name: Chat Settings Page
     *
     * Database table
     * wp_clientsites - where sites saved
     * wp_chat_icons - icon selected saved
     * wp_chat_options - options for chat
     * wp_widgetoptions
	 */
    
	 get_header(); 
	  
	 require_once ('live_chat_settings_ajax.php'); 
	 require_once ('live_chat_settings_helper.php');

 	global $wpdb;
 	
	if(!is_user_logged_in()) {

		echo '<center><h1>Please login to view this page</h1></center>';

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
 
		if(pnw_is_local() == true) {  

			$API_KEY 						=  'Kiok5B2tzM00Oqf';   
			$API_ID							 = '2_7818_ubHppKG8C';   
			$chat_settings_page_title   	 = 'Live Chat Settings Title'; 
			$chat_settings_title_description = 'Live Chat Settings Desc';  
			$getName->data[0]->id            = 77333; 

		} else {

			$API_KEY 					  	 = get_field('custom_api_key','option');
			$API_ID						     = get_field('custom_api_id','option');
			$chat_settings_page_title 	     = get_field('chat_settings_page_title','option'); 
			$chat_settings_title_description = get_field('chat_settings_title_description','option');

			//$API_RESULT	= query_api_call($postargs, $API_ID, $API_KEY);
			$API_RESULT = op_query($API_URL, 'GET', $API_DATA, $API_ID, $API_KEY);

			$getName = json_decode($API_RESULT);

			if(!$getName->data[0]->id){

				echo '<div id="page-content"><center><h2>You are not allowed to view this page!</h2></center></div>';
				get_footer();
				exit();

			} 

		}


		
 
		$QUEGETSITE = "SELECT * FROM " . $wpdb->prefix . "clientsites WHERE s_accountid='" . $getName->data[0]->id . "'";
		$RESULTGETS = $wpdb->get_results($QUEGETSITE);

		$getUserCI	= "SELECT * FROM ".$wpdb->prefix."chat_icons WHERE ci_accountid = '".$getName->data[0]->id."'";
		$getRowsCI	= $wpdb->get_row($getUserCI);

		$getUserCO	= "SELECT * FROM ".$wpdb->prefix."chat_options WHERE co_accountid = '".$getName->data[0]->id."'";
		$getRowsCO	= $wpdb->get_row($getUserCO);
 
 		// pnw_print_r_pre($getRowsCO); 

		/**
		 * Jesus Functions
		 */ 
		$liveChatSettings	= new LiveChatSettings(new CHAT_QUERIES\Chat_Queries('wp_clientsites'), 'wp_clientsites');  


		// print " co_chatformat = " . $getRowsCO->co_chatformat; 

?>

	<div id="page-content">
	
			<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/chat-pages/assets/css/pnw_style.css" />
 
		<h2><?php echo $chat_settings_page_title; ?></h2>
		<p>
			<?php echo $chat_settings_title_description; ?>
		</p> 
		<div class="chat-container">
			<ul class="ctabs">
				<li class="ctab-link transact-none" data-tab="tab-1" id="menu-tab-1" data-visited="active" ><b>Chat Icons</b><span>(Step 1)</span></li>
				<li class="ctab-link transact-none" data-tab="tab-2" id="menu-tab-2" data-visited="" ><b>Websites</b><span>(Step 2)</span></li></li>
				<li class="ctab-link transact-none" data-tab="tab-3" id="menu-tab-3" data-visited="" ><b>Chat Settings</b><span>(Step 3)</span></li>
				<li class="ctab-link current transact-none" data-tab="tab-4" id="menu-tab-4" data-visited="" ><b>Generated Script</b><span>(Step 4)</span></li>
			</ul>
			<div class="cmodal" id="modal-one" aria-hidden="true">
				<div class="cmodal-dialog">
					<div class="cmodal-header">
						<h2><img width="50%" height="auto" style="margin:0 auto; text-align:center; display:block" alt="umbrella support centre" src="<?php echo get_template_directory_uri().'/images/Umbrella-logo.png';?>"></h2>
						<a href="#" class="btn-close" araa-hidden="true">×</a>
					</div>
					<div class="cmodal-body" style="text-align:center"></div>
				</div>
			</div> 
			<div id="tab-1" class="ctab-content transaction-query">

			

				<form id="manageicons" type="post" action="">
				<input type="hidden" id="p_clientid" name="p_clientid" value="<?php echo $getName->data[0]->id; ?>" />
                    <p>
                        <span style="color: #008000;"><strong>Online Image:</strong></span>
                        <input type="text" class="urltext" name="p_cctxt1" id="p_ctxt1" value="<?php echo ($getUserCI->ci_buttontype == "CUSTOM" && $getRowsW->wid_imgpathon ? $getRowsW->wid_imgpathon : ""); ?>">
                        <!-- <span style="font-size:11px">Example: <i>http://domain.com/images/online.gif</i></span>-->
                    </p>
                    <p>
                        <span style="color: #ff0000;"><strong>Offline Image:</strong></span>
                        <input type="text" class="urltext" name="p_cctxt2" id="p_ctxt2" value="<?php echo ($getRowsCI->ci_buttontype == "CUSTOM" && $getRowsCI->ci_imgpathoff ? $getRowsCI->ci_imgpathoff : ""); ?>">
                        <!-- <span style="font-size:11px">Example: <i>http://domain.com/images/offline.gif</i></span>-->
                    </p> 
                   <div style="margin: 0 auto;">
                       <p>Have your own Buttons?</p>
                       <div class="switch-field" style="padding: 10px 15px 50px 10px;">
                           <input onClick="disablefields()" checked="checked" type="radio" id="S_OB_L1" name="p_obut" value="0" <?php echo ($getRowsCI->ci_buttontype == "DEFAULT" ? "checked" : ""); ?> required/>
                           <label for="S_OB_L1">No</label>&nbsp;&nbsp;
                           <input onClick="enablefields()" type="radio" id="S_OB_R1" name="p_obut" value="1" <?php echo ($getRowsCI->ci_buttontype == "CUSTOM" ? "checked" : ""); ?> />
                           <label for="S_OB_R1">Yes</label>
                       </div>
            
                   </div> 
            		
            		<div class="image-icon-online-offline" >

	                    <?php
	                        //echo photo_gallery(6);
	                        $query 	= "SELECT * FROM ".$wpdb->prefix."posts WHERE post_title LIKE '%CHATICON%' ORDER BY post_title DESC";
	                        $images = $wpdb->get_results($query);

	                        if(count($images)>0){
	                            echo '<div style="width: 100%; margin: 0 auto; clear: both; margin-bottom: 50px; padding: 10px 50px;" id="pnw-select-div" >';
	                                $reccount 	= 0;
	                                $incnum		= 1;
	                                $maxCount   = sizeof($images);
	                                $endOfArr 	= 0;
	                                foreach($images as $img){
	                                    $reccount++;
	                                    $endOfArr++;

	                                    if($reccount == 1){
	                                        echo '<div style="float:left; width: 50%">';
	                                    }
	                                    echo '<img style="width:140px;" src="'.$img->guid.'"> ';

	                                    if($reccount==2){
	                                        echo '<section class="container">';
	                                        echo '<div class="switch switch-blue">';
	                                        echo '<input onClick="disablefields()" type="radio" class="switch-input" id="S_CI_'.$incnum.'" name="p_cicon" value="'.$img->guid.'" '.($getRowsCI->ci_buttontype == "DEFAULT" && $getRowsCI->ci_imgpathoff == $img->guid ? "checked" : "").'>';
	                                        echo '<label for="S_CI_'.$incnum.'" class="switch-label switch-label-on" id="pnw-select" >Select</label>';
	                                        echo '<span class="switch-selection"></span>';
	                                        echo '</div>';
	                                        echo '</section>';
	                                        echo '</div>';
	                                        if($endOfArr < $maxCount)
	                                        	echo '<div style="float:right; width: 50%">';
	                                    }
	                                    if($reccount == 4){
	                                        //echo '<div class="funkyradio">';
	                                        //echo '<div class="funkyradio-success">';
	                                        //echo '<input type="radio" id="S_CI_'.$incnum.'" name="p_cicon" value="1"/>';
	                                        //echo '<label for="S_CI_'.$incnum.'">Choose Icon</label>';
	                                        echo '<section class="container">';
	                                        echo '<div class="switch switch-blue">';
	                                        echo '<input  onClick="disablefields()" type="radio" class="switch-input" id="S_CI_'.$incnum.'" name="p_cicon" value="'.$img->guid.'" '.($getRowsCI->ci_buttontype == "DEFAULT" && $getRowsCI->ci_imgpathoff == $img->guid ? "checked" : "").'>';
	                                        echo '<label for="S_CI_'.$incnum.'" class="switch-label switch-label-on" id="pnw-select" >Select</label>';
	                                        echo '<span class="switch-selection"></span>';
	                                        echo '</div>';
	                                        echo '</section>';
	                                        echo '</div>';
	                                        $reccount = 0;
	                                    }
	                                    $incnum++;
	                                }
	                                echo '</div>';
	                        }else{
	                            echo "<h2>There are no images found in the gallery.</h2>";
	                        }
	                    ?>
					</div>

                    <div style="clear:both">   </div>
                    <div id="error_container"></div><br /> 
                    <input type="hidden" name="action" value="process_icons"/> 
                 	<div id="preloader" class="preloader" >
				 		<img src="http://testing.umbrellasupport.co.uk/wp-content/uploads/2016/07/preload.gif" />
				 	</div>

                    <input id="bigbutton" type="button" onClick="processicons()" name="p_submit" value="Save and Continue" />
				</form>
			</div>
 
			<div id="tab-2" class="ctab-content  content-tab-2"> 

				


				<?php  
					$domains = $liveChatSettings->getClientSites(getCurrentLogggedInAccountId()); 
					$domain_total  = count($domains);   
				?> 

				<table cellpadding="10" cellspacing="">
					<tr>
						<td style="vertical-align:middle;width: 20%;padding-bottom: 40px;">
							<b>Input Website Address:</b>
						</td>
						<td>
							<table style="width: 60% !important;float: left !important;">
								<tbody>
									<tr>
										<td style="width: 100px;"> 
	  										<i>http://</i> 
	  										<input id="pdw-domain-validation" type="text" name="" value="" class="search" style="padding: 2px;">   
    									</td> 
    									<td>  
  											<button type="button" id="pnw-domain-add-button" class="query-wrapper query-add" style="display: block;">
  											<img src="<?php echo get_template_directory_uri().'/images/add.png';?>" onclick="processWebsite('Add Domain')" style="height: 23px;">
  
  												</button> <button type="button" id="pnw-domain-update-button" class="query-wrapper query-add" style="display: none;"><img src="http://icons.iconarchive.com/icons/custom-icon-design/mono-general-2/256/edit-icon.png" onclick="processWebsite('Update Domain')" style="height: 23px;"></button><div id="pnw-adding-domain-message">  
						      				</div>

						      			</td> 
						      		</tr>
						      	</tbody>
      						</table> 
						</td>   
					</tr>
					<tr>
						<td colspan="2">  
							<input type="hidden" id="pnw-total-site" value="<?php print $domain_total; ?>" />
							<input type="hidden" id="pnw-total-site-counter" value="<?php print $domain_total; ?>" /> 
						<form id="pnw_form" action="" method="POST" >  
							<input type="hidden" value="process_domain" name="action" />
							<table id="web_list" class="display" cellspacing="0">
								<thead>
									<tr>
										<th bgcolor="#CCCCCC" >#</th>
										<th bgcolor="#CCCCCC">Website Address</th>
										<th bgcolor="#CCCCCC">Action</th>
										<th class="no-sort" bgcolor="#CCCCCC" >Edit</th>
										<th class="no-sort" bgcolor="#CCCCCC" >Delete</th>
									</tr>
								</thead>
								<tbody>   
									<?php    

										foreach($domains as $domain) {   

											$partner_id = $domain['s_ID'];
											$domain     = $domain['s_website'];

											echo "<tr id='pnw-domain-container-$partner_id'><td>$partner_id</td><td><span id='pnw-domain-text-$partner_id' >$domain</span><input type='hidden' value='$domain' name='pnw_domain_values[]' id='pnw-domain-value-$partner_id' /></td><td>Active</td><td><button type='button'  onclick='processWebsite(\"Edit Domain\", $partner_id )' >Edit</button></td><td><button type='button' onclick='processWebsite(\"Delete Domain\", $partner_id )'>Delete</button></td></tr>";

										} 

									?>  
									<!-- <tr id="pnw-domain-conteiner-1" >
										<td>
											1 
										</td>
										<td>
											<span>
											http://www.domain.com
											</span>
											<input type="hidden" value="http://www.domain.com" name='pnw_domain_value' id='pnw-domain-value-1' />
									 	</td>
										<td>Active</td>
										<td><button onclick="processWebsite('Edit Domain', 1)">Edit</button></td>
										<td><button onclick="processWebsite('Delete Domain', 1)">Delete</button></td> 
									</tr> -->
								
 
									<?php
										// if($RESULTGETS){
										// 	$count = 1;
										// 	foreach($RESULTGETS as $R){
										// 		echo '<tr>';
										// 		echo '<td align="center">'.$count.'</td>';
										// 		echo '<td><b>'.$R->s_website.'</b></td>';
										// 		echo '<td>
										// 				<button type="submit" class="query-wrapper query-edit"><img src="'.get_template_directory_uri().'/images/edit.png"></button>
										// 				<button type="submit" class="query-wrapper query-delete"><img src="'.get_template_directory_uri().'/images/delete.png"></button>
										// 			 </td>';
										// 		echo '</tr>';
										// 		$count++;
										// 	}
										// }
										
									?>
								</tbody>
							</table>


						</form>


						</td>
					</tr>
				</table>

			
				<div id="pnw-save-domain-loader" class="preloader" >  
					<img src="http://testing.umbrellasupport.co.uk/wp-content/uploads/2016/07/preload.gif" /> 
				</div>	
				<!-- <button id="bigbutton" onclick="processWebsite('Save And Continue')">Save and Continue </button> -->
				<input type="button" id="bigbutton" onclick="processWebsite('Save And Continue')" value="Save and Continue">
 
			</div> <!-- end tab 2 -->
			<div id="tab-3" class="ctab-content ">

			

				<form action="#" method="post" id="pnw_chat_settings" >

				<input type="hidden" value="save_chat_settings" name="action" />

					<table cellpadding="10" cellspacing="">
						<tr>
							<td>
								<b>
									Chat Type
								</b>
							</td>
							<td>
								<input type="radio" value="1" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 1 or $getRowsCO->co_chatformat == null) ? "checked" : ""; ?> required/> Chat Bar with Image Icon above <br />
								<input type="radio" value="2" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 2 ? "checked" : ""); ?>> Chat Bar with separate Image Icon <br />
								<input type="radio" value="3" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 3 ? "checked" : ""); ?>> Chat Bar only <br />
								<input type="radio" value="4" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 4 ? "checked" : ""); ?>> Default Chat Settings <br />
							</td>
						</tr>
						<tr>
							<td>
								<b>
									Window Chat Type
								</b>
							</td>
							<td>
								<div class="switch-field">
									<input type="radio" id="S_TWC_L1" name="p_cwchat" value="1" <?php echo ($getRowsCO->co_chattype == 1 ? "checked" : ""); ?> required/>
									<label for="S_TWC_L1">Pop-up</label>&nbsp;&nbsp;
									<input type="radio" id="S_TWC_R1" name="p_cwchat" value="0" <?php echo ($getRowsCO->co_chattype == 0 ? "checked" : ""); ?>/>
									<label for="S_TWC_R1">Window</label>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<b>
									Enable Pro-Active Popup Invitation
								</b>
							</td>
							<td>
								<div class="switch-field">
									<input type="radio" id="S_TWC_L2" name="p_cpactive" value="1" <?php echo ($getRowsCO->co_proactive == 1 ? "checked" : ""); ?> required/>
									<label for="S_TWC_L2">Yes</label>&nbsp;&nbsp;
									<input type="radio" id="S_TWC_R2" name="p_cpactive" value="0" <?php echo ($getRowsCO->co_proactive == 0 ? "checked" : ""); ?> />
									<label for="S_TWC_R2">No</label>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<b>
									Enable Pop-up on Page Close
								</b>
							</td>
							<td> 
								<div class="switch-field">
									<input type="radio" id="S_TWC_L3" name="p_cexitpop" value="1" <?php echo ($getRowsCO->co_exitpop == 1 ? "checked" : ""); ?> required/>
									<label for="S_TWC_L3">Yes</label>&nbsp;&nbsp;
									<input type="radio" id="S_TWC_R3" name="p_cexitpop" value="0" <?php echo ($getRowsCO->co_exitpop == 0 ? "checked" : ""); ?> />
									<label for="S_TWC_R3">No</label>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">

							
								<div id="pnw-chat-settings-loader" class="preloader" >  
									<img src="http://testing.umbrellasupport.co.uk/wp-content/uploads/2016/07/preload.gif" /> 
								</div>
								<input type="button" id="bigbutton" onclick="proceeChatSettings()" value="Save and Continue"> 
							</td>
						</tr>
					</table>

				</form> 
			</div>

			<div id="tab-4" class="ctab-content current ">

				<!-- Place this tag where you want the Live Helper Plugin to render. -->
				 
				<!-- Place this tag after the Live Helper Plugin tag. -->
				

<pre class="prettyprint">  
&lt;script type="text/javascript" &gt;
var LHCChatOptions = {};
LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:520,popup_width:500};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
po.src = '//localhost/erwin/richard/debug/live121support.com/index.php/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(check_operator_messages)/true/(top)/350/(units)/pixels/(leaveamessage)/true?r='+referrer+'&l='+location+'&partner_id=12345';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})(); 
&lt;/script&gt;
 
</pre>


			</div>

		</div><!-- container -->
	
	</div>
	<!-- jQuery -->
	<!-- jQuery easing plugin -->
	<script src="http://thecodeplayer.com/uploads/js/jquery.easing.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			//jQuery time
			var current_fs, next_fs, previous_fs; //fieldsets
			var left, opacity, scale; //fieldset properties which we will animate
			var animating; //flag to prevent quick multi-click glitches

			$(".next-step").click(function(){
				if(animating) return false;
				animating = true;

				current_fs = $(this).parent();
				next_fs = $(this).parent().next();

				//activate next step on progressbar using the index of next_fs
				$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active-step");

				//show the next fieldset
				next_fs.show();
				//hide the current fieldset with style
				current_fs.animate({opacity: 0}, {
					step: function(now, mx) {
						//as the opacity of current_fs reduces to 0 - stored in "now"
						//1. scale current_fs down to 80%
						scale = 1 - (1 - now) * 0.2;
						//2. bring next_fs from the right(50%)
						left = (now * 50)+"%";
						//3. increase opacity of next_fs to 1 as it moves in
						opacity = 1 - now;
						current_fs.css({'transform': 'scale('+scale+')'});
						next_fs.css({'left': left, 'opacity': opacity});
					},
					duration: 800,
					complete: function(){
						current_fs.hide();
						animating = false;
					},
					//this comes from the custom easing plugin
					easing: 'easeInOutBack'
				});
			});

			$(".previous-step").click(function(){
				if(animating) return false;
				animating = true;

				current_fs = $(this).parent();
				previous_fs = $(this).parent().prev();

				//de-activate current step on progressbar
				$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active-step");

				//show the previous fieldset
				previous_fs.show();
				//hide the current fieldset with style
				current_fs.animate({opacity: 0}, {
					step: function(now, mx) {
						//as the opacity of current_fs reduces to 0 - stored in "now"
						//1. scale previous_fs from 80% to 100%
						scale = 0.8 + (1 - now) * 0.2;
						//2. take current_fs to the right(50%) - from 0%
						left = ((1-now) * 50)+"%";
						//3. increase opacity of previous_fs to 1 as it moves in
						opacity = 1 - now;
						current_fs.css({'left': left});
						previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
					},
					duration: 800,
					complete: function(){
						current_fs.hide();
						animating = false;
					},
					//this comes from the custom easing plugin
					easing: 'easeInOutBack'
				});
			});

			$(".submit").click(function(){
				return false;
			});
		});
	</script>
	<script type="text/javascript" src="//code.jquery.com/jquery-1.12.3.js"></script>

	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" class="init">
		jQuery.noConflict();
		jQuery('#web_list').DataTable({
				responsive: true,
				"bPaginate": true,
				"bLengthChange": true,
				"bFilter": true,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": true,
				"columnDefs": [ {
					  "targets": 'no-sort',
					  "orderable": false,
				} ]
		});

	</script>
<?php
	}
 get_footer();