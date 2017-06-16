<?php

	/**
	 * Template Name: Chat Settings Page
	 */
    
	 get_header(); 
	 
	 require_once ('live_chat_settings_helper.php'); 
	 require_once ('live_chat_settings_ajax.php');  
  
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

?>

	<div id="page-content">
		<h2><?php echo $chat_settings_page_title; ?></h2>
		<p>
			<?php echo $chat_settings_title_description; ?>
		</p> 
		<div class="chat-container">
			<ul class="ctabs">
				<li class="ctab-link  transact-none" data-tab="tab-1" id="menu-tab-1" ><b>Chat Icons</b><span>(Step 1)</span></li>
				<li class="ctab-link current transact-none" data-tab="tab-2" id="menu-tab-2" ><b>Websites</b><span>(Step 2)</span></li></li>
				<li class="ctab-link transact-none" data-tab="tab-3" id="menu-tab-3" ><b>Chat Settings</b><span>(Step 3)</span></li>
				<li class="ctab-link transact-none" data-tab="tab-4" id="menu-tab-4" ><b>Generated Script</b><span>(Step 4)</span></li>
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



			<div id="tab-1" class="ctab-content  transaction-query">
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
                    <?php
                        //echo photo_gallery(6);
                        $query 	= "SELECT * FROM ".$wpdb->prefix."posts WHERE post_title LIKE '%CHATICON%' ORDER BY post_title DESC";
                        $images = $wpdb->get_results($query);

                        if(count($images)>0){
                            echo '<div style="width: 100%; margin: 0 auto; clear: both; margin-bottom: 50px; padding: 10px 50px;">';
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
                                        echo '<label for="S_CI_'.$incnum.'" class="switch-label switch-label-on">Select</label>';
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
                                        echo '<label for="S_CI_'.$incnum.'" class="switch-label switch-label-on">Select</label>';
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
                    <div id="error_container"></div><br />
                    <div id="preloader"  style="height: 40px;display: none;" ><img src="http://testing.umbrellasupport.co.uk/wp-content/uploads/2016/07/preload.gif" /></div>
                    <input type="hidden" name="action" value="process_icons"/> 
                    <input id="bigbutton" type="button" onClick="processicons()" name="p_submit" value="Save and Continue" />
				</form>
			</div>

			
			<div id="tab-2" class="ctab-content current">
				<table cellpadding="10" cellspacing="">
					<tr>
						<td style="vertical-align:middle">
							<b>Input Website Address:</b>
						</td>
						<td>
							<i>http://</i> <input id="pdw-domain-validation" type="text" name="" value="" class="search"  >  
 
							<!-- add domain -->
							<button type="button" id="pnw-domain-add-button" class="query-wrapper query-add" ><img src="<?php echo get_template_directory_uri().'/images/add.png';?>"  onclick="processWebsite('Add Domain')" ></button>
 
							<!-- edit domain -->
							<button type="button" id="pnw-domain-update-button" class="query-wrapper query-add" style="display:none" ><img src="<?php echo get_template_directory_uri().'/images/add.png';?>"  onclick="processWebsite('Update Domain')" ></button>
							 
							<div id="pnw-adding-domain-message"> </div>
						</td>
					</tr>
					<tr>
						<td colspan="2"> 
							
							<input type="text" id="pnw-total-site" value="0" />
							<input type="text" id="pnw-total-site-counter" value="0" />
						
						<form id="pnw_form" action="" method="POST" > 
								
							<input type="text" value="process_domain" name="action" />
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

				<div id="pnw-save-domain-loader" style="display:none">Saving...</div>
				<button onclick="processWebsite('Save And Continue')">Save and Continue </button>
			</div> <!-- end tab 2 -->
			<div id="tab-3" class="ctab-content">
				<table cellpadding="10" cellspacing="">
					<tr>
						<td>
							Chat Type
						</td>
						<td>
							<input type="radio" value="1" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 1 ? "checked" : ""); ?> required/> Chat Bar with Image Icon above <br />
							<input type="radio" value="2" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 2 ? "checked" : ""); ?>> Chat Bar with separate Image Icon <br />
							<input type="radio" value="3" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 3 ? "checked" : ""); ?>> Chat Bar only <br />
							<input type="radio" value="4" name="LC_OPT" <?php echo ($getRowsCO->co_chatformat == 4 ? "checked" : ""); ?>> Default Chat Settings <br />
						</td>
					</tr>
					<tr>
						<td>
							Window Chat Type
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
							Enable Pro-Active Popup Invitation
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
							Enable Pop-up on Page Close
						</td>
						<td>
							<div class="switch-field">
								<input type="radio" id="S_TWC_L3" name="p_cexitpop" value="1" <?php echo ($getRowsW->wid_exitpop == 1 ? "checked" : ""); ?> required/>
								<label for="S_TWC_L3">Yes</label>&nbsp;&nbsp;
								<input type="radio" id="S_TWC_R3" name="p_cexitpop" value="0" <?php echo ($getRowsW->wid_exitpop == 0 ? "checked" : ""); ?> />
								<label for="S_TWC_R3">No</label>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" value="Save" class="btn btn-default" onclick="proceeChatSettings()" >
						</td>
					</tr>
				</table>
			</div>
			<div id="tab-4" class="ctab-content">
				Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.

					<!-- multistep form -->
						<form id="msform" style="display:none">
							<!-- progressbar -->
							<ul id="progressbar">
								<li class="active-step">Account Setup</li>
								<li>Social Profiles</li>
								<li>Personal Details</li>
							</ul>
							<!-- fieldsets -->
							<fieldset>
								<h2 class="fs-title">Create your account</h2>
								<h3 class="fs-subtitle">This is step 1</h3>
								<input type="text" name="email" placeholder="Email" />
								<input type="password" name="pass" placeholder="Password" />
								<input type="password" name="cpass" placeholder="Confirm Password" />
								<input type="button" name="next" class="next-step action-button" value="Next" />
							</fieldset>
							<fieldset>
								<h2 class="fs-title">Social Profiles</h2>
								<h3 class="fs-subtitle">Your presence on the social network</h3>
								<input type="text" name="twitter" placeholder="Twitter" />
								<input type="text" name="facebook" placeholder="Facebook" />
								<input type="text" name="gplus" placeholder="Google Plus" />
								<input type="button" name="previous" class="previous-step action-button" value="Previous" />
								<input type="button" name="next" class="next-step action-button" value="Next" />
							</fieldset>
							<fieldset>
								<h2 class="fs-title">Personal Details</h2>
								<h3 class="fs-subtitle">We will never sell it</h3>
								<input type="text" name="fname" placeholder="First Name" />
								<input type="text" name="lname" placeholder="Last Name" />
								<input type="text" name="phone" placeholder="Phone" />
								<textarea name="address" placeholder="Address"></textarea>
								<input type="button" name="previous" class="previous-step action-button" value="Previous" />
								<input type="submit" name="submit" class="submit action-button" value="Submit" />
							</fieldset>
						</form>
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