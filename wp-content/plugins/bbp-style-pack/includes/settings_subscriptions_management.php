<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//quote style settings page

function bsp_style_settings_subscriptions_management () {
	global $bsp_style_settings_sub_management ;
	?> 
	<form method="post" action="options.php">
		<?php wp_nonce_field( 'style-settings_subscriptions_management', 'style-settings-nonce' ) ?>
		<?php settings_fields( 'bsp_style_settings_sub_management' );
		bsp_clear_cache();
		?>
		<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php esc_html_e ('Subscriptions Management' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
		
		<tr>
			<td>
				<p><i><b>
					<?php esc_html_e('This section adds the Subscriptions Management tools which were in the bbPress Toolkit plugin which is no longer maintained.', 'bbp-style-pack'); ?>
				</b></i></p>
				
			</td>
		</tr>
		
	</table>
	<?php
	if (function_exists( 'forums_toolkit_page')) { ?>
	
	
		<table>
		<tr>
			<td>
				<p><b>
					<?php esc_html_e('****NOTE: You already have the bbPress Toolkit plugin activate, deactivate the bbPress Toolkit plugin if you want to use this version in this plugin, and return here to activate.****  ', 'bbp-style-pack' ); ?>
				</b></p>
			</td>
		</tr>
			
		</table>	<?php
	?>
	
	<?php
	}
	else {
	?>
	<hr>
		
	
	<table class="form-table">
	<!-- CREATE TOPIC BUTTON  -->	
	<!-- checkbox to activate  -->
		<tr valign="top">  
			<th>
			<?php esc_html_e('Activate Subscriptions Management', 'bbp-style-pack'); ?>
			</th>
			
			<td>
				<?php 
				$item = (!empty( $bsp_style_settings_sub_management['subscriptions_management_activate'] ) ?  $bsp_style_settings_sub_management['subscriptions_management_activate'] : '');
				echo '<input name="bsp_style_settings_sub_management[subscriptions_management_activate]" id="bsp_style_settings_sub_management[subscriptions_management_activate]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
			</td>
		</tr>
		<tr>
		<td colspan=2>
		<?php
		if (!empty($bsp_style_settings_sub_management['subscriptions_management_activate'])) {
		echo '<p>'; esc_html_e('Subscribe new users automatically to the below ticked forums:', 'bbp-style-pack'); echo  '<br>';
		$all_forums = bsptoolkit_forum_structure();
		foreach ($all_forums as $myforum) {
			echo '<input type="checkbox" name="bsp_style_settings_sub_management['.$myforum['id'].']" value="'.$myforum['id'].'" ';
			if (!empty( $bsp_style_settings_sub_management[$myforum['id']])) { echo 'checked'; }
			echo '>' . esc_html($myforum['title']).'<br>';
		}
		}
		echo '</p>';
		?>
		
		</td>
		</tr>
		
		</table>
		
			<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
		
		<?php
	}
	?>
	<table>
	<tr>
			<td colspan='2'>
				<p>
				<?php
		
		esc_html_e( 'This section lets you manage subscriptions letting you subscribe or unsubscribe users to forums and topics', 'bbp-style-pack' ); 
		if (empty($bsp_style_settings_sub_management['subscriptions_management_activate'])) {
			echo '<p>' ;
			esc_html_e( 'Once Activated...', 'bbp-style-pack' ); 
			echo '</p>' ;
		}			
		echo '<p>' ;
		esc_html_e( 'To manage subscriptions of a forum, ', 'bbp-style-pack' ); 
		echo '<a href="' . esc_url(site_url()) . '/wp-admin/edit.php?post_type=forum' . '">' ;
		esc_html_e( 'edit the forums ', 'bbp-style-pack' ); 
		echo '</a>' ;
		esc_html_e( 'and click on "Subscriptions" as an action of the forum, or edit the forum and find the "Manage Subscriptions" button (somewhere below the Forum Attributes)', 'bbp-style-pack' ); 
		echo '</p>';
		echo '<p>' ;
		esc_html_e( 'To manage subscriptions for topics, ', 'bbp-style-pack' ); 
		echo '<a href="' . esc_url(site_url()) . '/wp-admin/edit.php?post_type=topic' . '">' ;
		esc_html_e( 'edit the topics ', 'bbp-style-pack' ); 
		echo '</a>' ;
		esc_html_e( 'and find "Subscriptions" as an action for each topic.', 'bbp-style-pack' ); 
		echo '</p>' ;
		esc_html_e( 'To manage subscriptions for a user, ', 'bbp-style-pack' ); 
		echo '<a href="' . esc_url(site_url()) . '/wp-admin/users.php' . '">' ;
		esc_html_e( 'edit the users ', 'bbp-style-pack' ); 
		echo '</a>' ;
		esc_html_e( 'and find "Subscriptions" as an action for each user.', 'bbp-style-pack' ); 
		echo '</p>' ;
		?>
				</p>
			</td>
		</tr>
		</table>
        </form>
<?php
}
