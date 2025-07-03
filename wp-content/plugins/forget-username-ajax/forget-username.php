<?php
/*
Plugin Name: Forgot Username?(ajax)
Plugin URI: http://wordpress.arckimial.com
Version: 1.3
Description: This plugin is used to retrive forgotten username. Go to <strong>Settings &gt; Forgot Username</strong> to know how to use it.
Author: arckimial
Author URI: http://arckimial.com 
*/

add_action( 'wp_enqueue_scripts', 'add_my_asset' );
add_filter('widget_text', 'do_shortcode');
add_shortcode('forget-username-form', 'forgetusername_form_shortcode');
add_action('admin_menu', 'fu_menu_options');
add_option('fu_mail_subject','Your Username request for '.get_option('blogname'));
add_option('fu_mail_header',"Hello dear user,\n\n Thanks for using our this service to help you.");
add_option('fu_mail_footer',"Regards,\n".get_option('blogname')."\n".site_url());
add_option('fu_submit_val','Get Username');
add_option('fu_mail_body_lbl','Your Username is,');
add_option('fu_mail_success','We have sent you your username, please check your inbox.');
add_option('fu_mail_error','oops! server is busy.');
add_option('fu_mail_email_not_exist','your entered email is not registered with us.');
add_action("wp_ajax_my_username", "my_username");
add_action("wp_ajax_nopriv_my_username", "my_username");

function add_my_asset() {
	wp_register_style( 'fu-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style( 'fu-style' );
	wp_register_script('fu-ajax', plugins_url('js/ajax.js', __FILE__),array(),'',true);
	wp_enqueue_script('fu-ajax');
}
function forgetusername_admin_css() {
    wp_register_style($handle = 'forget-username-admin-style', $src = plugins_url('css/fu-admin.css', __FILE__), $deps = array(), $ver = '1.0.0', $media = 'all');
    wp_enqueue_style('forget-username-admin-style');
}
add_action('admin_print_styles', 'forgetusername_admin_css');
 
function forgetusername_form_shortcode() {
	?>
	<form name="lostusername" id="lostusername" action="<?php echo admin_url('admin-ajax.php?action=my_username');?>" method="post">
	<p>
	<label for="fu-email" ><?php _e('E-mail:') ?><br />
	<input type="text" name="fu_email" id="fu_email" class="input" size="20" /></label>
	</p>
	<input type="hidden" name="fu_mail_subject" value="<?php echo get_option('fu_mail_subject');?>" />
	<input type="hidden" name="fu_mail_header" value="<?php echo get_option('fu_mail_header');?>" />
	<input type="hidden" name="fu_mail_footer" value="<?php echo get_option('fu_mail_footer');?>" />
	<input type="hidden" name="fu_mail_body_lbl" value="<?php echo get_option('fu_mail_body_lbl');?>" />
    <input type="hidden" name="fu_mail_success" value="<?php echo get_option('fu_mail_success');?>" />
    <input type="hidden" name="fu_mail_error" value="<?php echo get_option('fu_mail_error');?>" />
    <input type="hidden" name="fu_mail_email_not_exist" value="<?php echo get_option('fu_mail_email_not_exist');?>" />
	<p class="submit"><input type="submit" name="fu-submit" id="fu-submit" class="button button-primary button-large" value="<?php esc_attr_e(get_option('fu_submit_val')); ?>" /></p>
	</form>
<?php }

function fu_menu_options(){
	$plugin_page=add_options_page('Forgot Username', 'Forgot Username', 'manage_options', 'fu-menu-options', 'fu_menu');
	add_action( 'admin_footer-'.$plugin_page,'fu_admin_footer' );
	
	if(isset($_POST['fu_update_formsettings'])){
		update_option('fu_submit_val',$_POST['fu_submit_val']);
		update_option('fu_mail_success',$_POST['fu_mail_success']);
		update_option('fu_mail_error',$_POST['fu_mail_error']);
		update_option('fu_mail_email_not_exist',$_POST['fu_mail_email_not_exist']);
	}
	if(isset($_POST['fu_update_mailsettings'])){
		update_option('fu_mail_subject',$_POST['fu_mail_subject']);
		update_option('fu_mail_header',$_POST['fu_mail_header']);
		update_option('fu_mail_footer',$_POST['fu_mail_footer']);
		update_option('fu_mail_body_lbl',$_POST['fu_mail_body_lbl']);
	}
}
function fu_admin_tabs( $current = 'form' ) {
    $tabs = array( 'formsetting' => 'Form Settings', 'mailsetting' => 'Mail Settings' );
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=fu-menu-options&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}
function fu_menu(){

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	//generic HTML and code goes here
	
	
	?>

	<div class="wrap" id="fu-admin-page">
    	<div id="icon-options-general" class="icon32"></div>
      <h2>Forgot Username</h2><br/>
      <div>
        <h2>To add Forgot Username form to your website</h2>
        <h4>Method 1</h4>
        <p>Add this code <strong>[forget-username-form]</strong> in post, page or widget.</p>
        <h5>OR</h5>	
        <h4>Method 2</h4>
        <p>Add this code <strong>&lt;?php echo do_shortcode('[forget-username-form]');?&gt;</strong> in your template file.</p>
    </div><br/>
    <h3>Settings</h3><br/>
      <?php if ( isset ( $_GET['tab'] ) ) fu_admin_tabs($_GET['tab']); else fu_admin_tabs('formsetting');?>
		
            <?php if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
				   else $tab = 'formsetting';?>
            <form method="post" action="options-general.php?page=fu-menu-options&tab=<?php echo $tab;?>">
            <?php wp_nonce_field('update-options');?>        
			<?php echo '<table class="form-table"><tbody>';
				   switch ( $tab ){
					  case 'formsetting' :
						 ?>
				                        
                    <tr valign="top">
                        <th scope="row"><label for="buttonval">Button Text</label></th>
                        <td><input type="text" name="fu_submit_val" value="<?php echo get_option('fu_submit_val');?>" /></td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="successmsg">Success Message</label></th>
                        <td><input type="text" name="fu_mail_success" size="53" value="<?php echo get_option('fu_mail_success');?>" /></td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="errormsg">Error Message</label></th>
                        <td><input type="text" name="fu_mail_error" size="53" value="<?php echo get_option('fu_mail_error');?>" /></td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="notexistmsg">Non-Registered Email Message</label></th>
                        <td><input type="text" name="fu_mail_email_not_exist" size="53" value="<?php echo get_option('fu_mail_email_not_exist');?>" /></td>
					</tr>
					<?php
                    break;
                    case 'mailsetting' :
                    ?>
                    <script type="application/javascript">
						jQuery(document).ready(function(){
							jQuery("#viewoutput").click(function(e){
								e.preventDefault();
								jQuery(".output-box").show();
								jQuery(".output-box h3").html(jQuery("#esubject").val());
								jQuery(".output-box .body").html(jQuery("#ebody").val().replace(/\n/g, "<br>")+"<p>"+jQuery("#eusertitle").val()+" {username}</p><br/><p><small>"+jQuery("#esign").val().replace(/\n/g, "<br>")+"</small></p>");
							}); 
						});
					</script>
					<tr valign="top">
                        <th scope="row"><label for="mailsubject">Subject</label></th>
                        <td><input type="text"  name="fu_mail_subject" id="esubject" value="<?php echo get_option('fu_mail_subject');?>" size="53"/></td>
                        <td rowspan="4" valign="top" class="rowmerge" align="left">
                        	<button id="viewoutput" class="button-secondary">View Email Output</button>
                            <div class="output-box">
                            	<h3>&nbsp;</h3>
                                <div class="body"></div>
                            </div>
                        </td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="mailheader">Content</label></th>
                        <td><textarea name="fu_mail_header" id="ebody" rows="8" cols="50"><?php echo get_option('fu_mail_header');?></textarea></td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="usertitle">Username heading<br/><small>(For Example., "Your Username")</small></label></th>
                        <td><input type="text"  name="fu_mail_body_lbl" id="eusertitle" value="<?php echo get_option('fu_mail_body_lbl');?>"/><br  />
						<label><small>output will be -</small><span><?php echo get_option('fu_mail_body_lbl');?></span> {username}</label></td>
					</tr>
                    <tr valign="top">
                        <th scope="row"><label for="mailsign">Signature</label></th>
                        <td><textarea name="fu_mail_footer" id="esign" rows="8" cols="50"><?php echo get_option('fu_mail_footer');?></textarea></td>
					</tr>
                    
						 <?php
					  break;
				   }
				   echo '</tbody></table>';?>
                   <p><i>[Note : To save your changes, please press "Update Option" button before leaving tab.]</i></p>
                <input type="submit" class="button-primary" value="<?php _e('Update Option')?>" name="<?php echo ($tab=='formsetting')?"fu_update_formsettings":"fu_update_mailsettings";?>" />
                <input type="hidden" name="action" value="update" />
            </form>
            
       </div>    
	<?php
}
function my_username(){
	global $wpdb,$wp;
	if(!isset($_POST))
		return;

	if($_POST['fu_email']!=''){

		if(is_email($_POST['fu_email'])){

			$r=$wpdb->get_row($wpdb->prepare("select `user_login`,`user_email`,`ID` from `{$wpdb->prefix}users` where `user_email`='%s'",$_POST['fu_email']));
			
			if(!empty($r) && $r->user_login!=''){

				$subject=$_POST['fu_mail_subject']!=''?$_POST['fu_mail_subject']:'Your Username with '.get_option('blogname');

				$message_h=$_POST['fu_mail_header']!=''?$_POST['fu_mail_header']:'Welcome to '.get_option('blogname').', you can access your account with following username';

				$message_b_lbl=$_POST['fu_mail_body_lbl']!=''?$_POST['fu_mail_body_lbl']:'Your Username';

				$message_f=$_POST['fu_mail_footer']!=''?$_POST['fu_mail_footer']:'To access your account go to '.site_url();

				$message="<p>".nl2br($message_h)."</p><p>".nl2br($message_b_lbl)." ".$r->user_login."</p><br/><p><small>".nl2br($message_f)."</small></p>";


				function set_html_content_type(){return 'text/html';}

				add_filter( 'wp_mail_content_type','set_html_content_type' );

				if(@wp_mail($r->user_email,$subject,$message))
					echo '<p class="success">'.get_option('fu_mail_success').'</p>';

				else
					echo '<p class="error">'.get_option('fu_mail_success').'</p>';

				remove_filter( 'wp_mail_content_type','set_html_content_type' );

			}else{
				echo '<p class="warning">'.get_option('fu_mail_email_not_exist').'</p>';
			}
			
		}else{
			echo '<p class="error">Please Enter valid E-mail address</p>';
		}
	}else{
		echo '<p class="error">Please Enter E-mail address</p>';
	}
	die();
}
function fu_admin_footer(){
	echo '<style type="text/css">#wpfooter { display: none; }</style>';
	echo '<p class="alignright" id="footer-left" style="margin-right:5%"><span id="footer-thankyou">Creation of <a href="http://arckimial.com/" target="_blank">arckimial</a>.</span></p>';
}
?>