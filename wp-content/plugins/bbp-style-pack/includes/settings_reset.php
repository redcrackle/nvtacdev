<?php

//reset settings page

function bsp_style_settings_reset () {
	//calls the delete function to get rid of settings
		if ( $_POST && wp_verify_nonce( $_POST['style-settings-reset-nonce'], 'style-settings_reset' ) ) :
			bsp_reset_settings () ;
		endif ;
	?>
	<Form method="post">
	<?php wp_nonce_field( 'style-settings_reset', 'style-settings-reset-nonce' ) ?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php _e ('Reset Settings' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p>
					<?php _e('This section allows you to reset any or all of the tabs in this plugin', 'bbp-style-pack'); ?>
					
				</p>
				<p>
				<strong>
					<?php _e('WARNING - RESETTING deletes data for the tab(s) selected - use with care !', 'bbp-style-pack'); ?>
				</strong>
				</p>
			</td>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
			
	<table class="form-table">
	
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Forums Index Styling', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'forums-index-styling' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Forum Templates', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'forum-templates' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Forum Display', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'forums-display' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Freshness Display', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'freshness-display' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Breadcrumbs', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'breadcrumbs' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Buttons', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'buttons' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Login', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'login' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Login Failures', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'login-failures' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Forum Roles', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'forum-roles' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Subscription emails', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'email' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Topic Order', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'topic-order' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Topics Index Styling', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'topics-index-styling' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Topic Previews', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'topic-preview' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Topic/Reply Display', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'topic-reply-styling' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Topic/Reply Form', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'topic-reply-form' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Profile', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'profile' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Search Styling', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'search-styling' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Unread Posts', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'unread-posts' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Latest Activity Widget Styling', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'latest-activity-widget-styling' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('CSS Location', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'css-location' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Translations', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'translations' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	<!-- checkbox to activate  -->
	<tr>
	<th>
				<?php _e('Bug Fixes', 'bbp-style-pack'); ?>
	</th>
	<td>
	<?php
	$item =  'bug-fixes' ;
	$item1 = '' ;
		echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
		_e('Click to reset', 'bbp-style-pack');
	?>
	</td>
	</tr>
	
	
	
	</table>
	
	

					
<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
</form>
		
</div><!--end sf-wrap-->

</div><!--end wrap-->
	
	 
<?php
}

function bsp_reset_settings () {
	
	if (!empty($_POST['forums-index-styling'] )) delete_option ( 'bsp_style_settings_f' );
	if (!empty($_POST['forum-templates'])) delete_option ( 'bsp_templates' );
	if (!empty($_POST['forums-display'])) delete_option ( 'bsp_forum_display' );
	if (!empty($_POST['forums-order'])) delete_option ( 'bsp_forum_order' );	
	if (!empty($_POST['freshness-display'])) delete_option ( 'bsp_style_settings_freshness' );
	if (!empty($_POST['breadcrumbs'])) delete_option ( 'bsp_breadcrumb' );
	if (!empty($_POST['buttons'])) delete_option ( 'bsp_style_settings_buttons' );
	if (!empty($_POST['login'])) delete_option ( 'bsp_login' );
	if (!empty($_POST['login-failures'])) delete_option ( 'bsp_login_fail' );	
	if (!empty($_POST['forum-roles'] )) delete_option ( 'bsp_roles' );
	if (!empty ($_POST['email'] )) delete_option ( 'bsp_style_settings_email' );	
	if (!empty($_POST['topic-order'])) delete_option ( 'bsp_topic_order' );
	if (!empty($_POST['topics-index-styling'])) delete_option ( 'bsp_style_settings_ti' );
	
	if (!empty($_POST['topic-preview'])) delete_option ( 'bsp_style_settings_topic_preview' );
	
	if (!empty($_POST['topic-reply-styling'] )) delete_option ( 'bsp_style_settings_t' );
	if (!empty ($_POST['topic-reply-form'] )) delete_option ( 'bsp_style_settings_form' );
	if (!empty ($_POST['profile'])) delete_option ( 'bsp_profile' );
	if (!empty ($_POST['search-styling'] )) delete_option ( 'bsp_style_settings_search' );
	
	if (!empty ($_POST['unread-posts'] )) delete_option ( 'bsp_style_settings_unread' );
	
	if (!empty ($_POST['latest-activity-widget-styling'] )) delete_option ( 'bsp_style_settings_la' );
	if (!empty ($_POST['css-location'] )) delete_option ( 'bsp_css_location' );
	
	if (!empty($_POST['translations'])) delete_option ( 'bsp_style_settings_translation' );
	if (!empty($_POST['bug-fixes'])) delete_option ( 'bsp_style_settings_bugs');
	
	//create a style.css on saving
	generate_style_css() ;
	
}