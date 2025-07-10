<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_settings_admin() {
 ?>
			
	<h3>
		<?php esc_html_e ('Dashboard Administration' , 'bbp-style-pack' ) ; ?>
	</h3>
	
		
	<?php global $bsp_settings_admin ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-admin', 'style-settings-admin' ) ?>
	<?php settings_fields( 'bsp_settings_admin' );
	?>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>			
	<table class="form-table">
<!-- FORUMS     ----->	
<tr>
			<th >
				<?php esc_html_e('Forums/Topics/Replies', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/user-admin-authors.png',dirname(__FILE__)  )) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td width="300" >
				1. <?php esc_html_e( 'Add an author filter', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_author_filter'] ) ?  $bsp_settings_admin['activate_author_filter'] : '');
				echo '<input name="bsp_settings_admin[activate_author_filter]" id="bsp_settings_admin[activate_author_filter]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_forum_sort]">
				<?php esc_html_e( 'This lets you find items by individual author', 'bbp-style-pack' ); ?>
				<br/><i>
				<?php esc_html_e( 'Note: on sites with lots of users, selecting this may slow display of the forum/topic/reply dashboard admin pages.  It will not affect the front end.', 'bbp-style-pack' ); ?>
				</i>
				</label>
			</td>
			
		</tr>
		<tr>	
		<tr>
			<th >
				<?php esc_html_e('Forums', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/forums-admin.png',dirname(__FILE__)  )) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td width="300" >
				1. <?php esc_html_e( 'Make topic and reply columns sortable', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_forum_sort'] ) ?  $bsp_settings_admin['activate_forum_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_forum_sort]" id="bsp_settings_admin[activate_forum_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_forum_sort]">
				<?php esc_html_e( 'This lets you sort these columns showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				2. <?php esc_html_e('Make topic and reply items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_forum_links'] ) ?  $bsp_settings_admin['activate_forum_links'] : '');
				echo '<input name="bsp_settings_admin[activate_forum_links]" id="bsp_settings_admin[activate_forum_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_forum_links]">
					<?php esc_html_e( 'When you click an item, it will list all the topics or replies for that forum', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>

		
<!-- TOPICS    ----->		
		<tr>
			<th >
				<?php esc_html_e('Topics', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/topics-admin.png',dirname(__FILE__)  )) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td>
				3. <?php esc_html_e( 'Make reply column sortable', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty($bsp_settings_admin['activate_topic_sort'] ) ?  $bsp_settings_admin['activate_topic_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_topic_sort]" id="bsp_settings_admin[activate_topic_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_topic_sort]">
				<?php esc_html_e( 'This lets you sort this column showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				4. <?php esc_html_e('Make Forum, Reply and Author items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_topic_links'] ) ?  $bsp_settings_admin['activate_topic_links'] : '');
				echo '<input name="bsp_settings_admin[activate_topic_links]" id="bsp_settings_admin[activate_topic_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_topics_links]">
					<?php esc_html_e( 'When you click an item, it will list the topics for that forum, replies for that topic, or topics by that author', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<tr>
			<td>
				5. <?php esc_html_e('Add Tags Column', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_topic_tags'] ) ?  $bsp_settings_admin['activate_topic_tags'] : '');
				echo '<input name="bsp_settings_admin[activate_topic_tags]" id="bsp_settings_admin[activate_topic_tags]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_topics_links]">
					<?php esc_html_e( 'This will add a tags column to the list, letting you see that tags set against each topic', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
<!-- REPLIES    ----->		
		<tr>
			<th >
				<?php esc_html_e('Replies', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/replies-admin.png',dirname(__FILE__)  ) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
		<tr>
			<td>
				6. <?php esc_html_e('Make Author items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_reply_links'] ) ?  $bsp_settings_admin['activate_reply_links'] : '');
				echo '<input name="bsp_settings_admin[activate_reply_links]" id="bsp_settings_admin[activate_reply_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_reply_links]">
					<?php esc_html_e( 'When you click an author, it will list replies by that author', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
<!-- Users    ----->		
		<tr>
			<th >
				<?php esc_html_e('Users', 'bbp-style-pack'); ?>
			</th>
			<td>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/users-admin.png',dirname(__FILE__)  )) . '" width=700px > '; ?>
			</td>
		</tr>
			
	<!-- checkbox to activate  -->
		
			<td>
				7. <?php esc_html_e( 'Add New Topic and Reply columns', 'bbp-style-pack' ); ?>
				
			</td>
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_columns'] ) ?  $bsp_settings_admin['activate_user_columns'] : '');
				echo '<input name="bsp_settings_admin[activate_user_columns]" id="bsp_settings_admin[activate_user_columns]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_columns]">
				<?php esc_html_e( 'This adds new topic and reply columns', 'bbp-style-pack' ); ?>
				</label>
			</td>
			
		</tr>
		<tr>
			<td>
				8. <?php esc_html_e('Make Topics and Replies Columns sortable', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_sort'] ) ?  $bsp_settings_admin['activate_user_sort'] : '');
				echo '<input name="bsp_settings_admin[activate_user_sort]" id="bsp_settings_admin[activate_user_sort]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_sort]">
					<?php esc_html_e( 'This lets you sort these columns showing most or least first', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		<tr>
			<td>
				9. <?php esc_html_e('Make topic and reply items linked', 'bbp-style-pack'); ?>
			</td>
					
			<td>
				<?php 
				$item = (!empty( $bsp_settings_admin['activate_user_links'] ) ?  $bsp_settings_admin['activate_user_links'] : '');
				echo '<input name="bsp_settings_admin[activate_user_links]" id="bsp_settings_admin[activate_user_links]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				?>
				<label class="description" for="bsp_admin[activate_user_links]">
					<?php esc_html_e( 'When you click an item, it will list all the topics or replies for that user', 'bbp-style-pack' ); ?>
				</label>
			</td>
		
		</tr>
		
		
			
		</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save', 'bbp-style-pack' ); ?>" />
		</p>
	</form>
	
<?php
}







