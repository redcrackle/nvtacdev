<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//forum style settings page

function bsp_style_settings_form () {
	global $bsp_style_settings_form ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'style-settings-form', 'style-settings-nonce' ) ?>
	<?php settings_fields( 'bsp_style_settings_form' );
	//create a style.css on entry and on saving
	generate_style_css();
        bsp_clear_cache();
	
	if (!empty($bsp_style_settings_form['update418'])) echo '<input type="hidden" id="bsp_style_settings_form[update418]" name="bsp_style_settings_form[update418]" value="1">' ; 
	?>
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php esc_html_e ('Topic/Reply Form' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<p> <?php esc_html_e('This section allows you to amend styles.', 'bbp-style-pack'); ?> </p>
				<p> <?php esc_html_e('You only need to enter those styles and elements within a style that you wish to alter', 'bbp-style-pack'); ?>  </p></td>
			<td>	
			<?php echo '<img src="' . esc_url(plugins_url( 'images/topic-form.JPG',dirname(__FILE__) ) ) . '" > '; ?>
			</td>
		</tr>
		<tr>
			<td>
				<p> <?php esc_html_e('If you are allowing anonymous posting (dashboard>settings>forums>Anonymous)', 'bbp-style-pack'); ?> </p>
				<p> <?php esc_html_e('you can control what additional fields are shown', 'bbp-style-pack'); ?> </p>
				<td>	
			<?php echo '<img src="' . esc_url(plugins_url( 'images/form_anon.png',dirname(__FILE__) ) ) . '" > '; ?>
			</td>
		</tr>
		
	</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
		</p>
	<table class="form-table">
			
<!--Topic Title ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Labels' ;
			$name0 = __('Labels', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$item3="bsp_style_settings_form[".$name.$area3."]" ;
			$item4="bsp_style_settings_form[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_form[$name.$area3]) ? $bsp_style_settings_form[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_form[$name.$area4]) ? $bsp_style_settings_form[$name.$area4]  : '') ;
			?>
			
			<th>
				<?php echo '1. '.esc_html($name0)?>
			</th>
			<td>
				<?php echo esc_html($name1) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="small-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name2) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="bsp-color-picker" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name3) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item3).'" class="medium-text" name="'.esc_html($item3).'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name4) ; ?>
			</td>
			<td>
				<select name="<?php echo esc_html($item4) ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
			
<!--Text Area background ------------------------------------------------------------------->
		<tr valign='top'>
			<?php 
			$name = 'Text area' ;
			$name0 = __('Text area', 'bbp-style-pack') ;
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$area1='Background Color' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '2. '.esc_html($name0) ?>
			</th>
			<td>
				<?php echo esc_html($name1) ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="bsp-color-picker" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			
<!--Text Area ------------------------------------------------------------------->
		<tr>
			<?php 
			$name = 'Text area' ;
			$name0 = __('Text area', 'bbp-style-pack') ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$item3="bsp_style_settings_form[".$name.$area3."]" ;
			$item4="bsp_style_settings_form[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_form[$name.$area3]) ? $bsp_style_settings_form[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_form[$name.$area4]) ? $bsp_style_settings_form[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '3. '.esc_html($name0) ?>
			</th>
			<td>
				<?php echo esc_html($name1) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="small-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name2) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="bsp-color-picker" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name3) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item3).'" class="medium-text" name="'.esc_html($item3).'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name4) ; ?>
			</td>
			<td>
				<select name="<?php echo esc_html($item4) ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
		
<!--Button background ------------------------------------------------------------------->
		<tr valign='top'>
			<?php 
			$name = 'Button' ;
			$name0 = __('Button', 'bbp-style-pack') ;
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$name2 = __('Text Color', 'bbp-style-pack') ;
			$area1='Background Color' ;
			$area2='Text Color' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			?>
			<th>
				<?php echo '4. '.esc_html($name0) ?>
			</th>
			<td>
			<?php echo esc_html($name1) ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="bsp-color-picker" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name2) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="bsp-color-picker" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
</table>
<table class="form-table">
<!-- 5. -->			
		<tr valign="top">
			<?php
			$name='Submitting' ;
			$name0 = __('Submit', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$name2 = __('Submitting Message', 'bbp-style-pack') ;
			$area2='Submitting';
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : 'Submitting') ;
			$name3 = __('Spinner', 'bbp-style-pack') ;
			$area3='Spinner';
			$item3="bsp_style_settings_form[".$name.$area3."]" ;
			$value3 = (!empty($bsp_style_settings_form[$name.$area3]) ? $bsp_style_settings_form[$name.$area3]  : '') ;
			$area4='button_styling';
			$item4="bsp_style_settings_form[".$name.$area4."]" ;
			$value4 = (!empty($bsp_style_settings_form[$name.$area4]) ? $bsp_style_settings_form[$name.$area4]  : '') ;
			?>
			<th>
				<?php echo '5. '.esc_html($name0) ?>
				</th>
			<td colspan = '2'>
				<label class="description"><?php esc_html_e( 'You can set the submit button styling to match that of other buttons set in the "Forum Buttons" tab.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item4).'" id="'.esc_html($item4).'" type="checkbox" value="1" class="code" ' . checked( 1,$value4, false ) . ' />' ;
				esc_html_e ('Click to activate', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		<tr>
		<td>
		</td>
			</th>
			<td colspan = '2'>
				<label class="description"><?php esc_html_e( 'You can set the submit to display a different message once it is pressed eg "Submitting".This will let the user know that they have sucessfully clicked the submit.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Click to activate', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php echo esc_html($name2) ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="medium-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"' ; ?> 
			</td>
			<td>
				<label class="description"><?php esc_html_e( 'eg Submitting, Processing, Submit in progress etc', 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'If you just want the spinner below (ie no text), then put a space character in this section and activate the spinner below', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td colspan='2'>
				<label class="description"><?php esc_html_e( 'You can also select to display a spinner in addition to the above. This may rotate dependant on both client PC and server performance', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/submit.JPG',dirname(__FILE__))  ) . '" > '; ?>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item3).'" id="'.esc_html($item3).'" type="checkbox" value="1" class="code" ' . checked( 1,$value3, false ) . ' />' ;
				esc_html_e ('Click to activate spinner', 'bbp-style-pack') ; ?>
			</td>
		</tr>
<!-- 6. -->			
		<tr valign="top">
			<?php
			$name='Notify' ;
			$name0 = __('Notify Default', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '6. '.esc_html($name0) ?>
			</th>
			<td colspan='2'>
				<label class="description"><?php esc_html_e( 'By default this box is not ticked, so users forgetting to tick it are not notified of new replies.  Activating this ticks it by default - users can then unselect if they wish', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Click to activate', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		
<!-- 7. -->			
		<tr valign="top">
			<?php
			$name='Remove_edit_Logs' ;
			$name0 = __('Remove Edit Logs', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '7. '.esc_html($name0) ?>
			</th>
			<td colspan='2'>
				<label class="description"><?php esc_html_e( 'Remove \'Keep a log of this edit\' box', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Click to activate', 'bbp-style-pack') ; ?>
				
			</td>
		</tr>

		
<!-- 8. -->		
		<tr valign="top">
			<?php
			$name='Remove_Edit_Reason' ;
			$name0 = __('Remove Edit Reason', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '8. '.esc_html($name0) ?>
			</th>
			<td colspan='2'>
				<label class="description"><?php esc_html_e( 'Remove \'Optional reason for editing\' box', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
<!-- 9. -->		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Click to activate', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		<?php
		
			$name = ('Show_editors') ;
			$name1 = __('9. Show editors', 'bbp-style-pack') ;
			$area1='activate' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : 0) ;
		?>
			
		
		<tr>	
			<th>
				<?php echo esc_html($name1) ; ?> 
			</th>
			<td colspan = 2>	
						<?php echo '<img src="' . esc_url(plugins_url( 'images/editors.JPG',dirname(__FILE__))  ) . '" > '; ?>
			</td>
		</tr>
			<td>
			</td>
			<td colspan = '2'>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="0" class="code"  ' . checked( 0,$value1, false ) . ' />' ;
				esc_html_e ('Text Editor Only' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="1" class="code"  ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Visual Editor Only' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="2" class="code"  ' . checked( 2,$value1, false ) . ' />' ;
				esc_html_e ('Show Both Visual and Text Editors' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="3" class="code"  ' . checked( 3,$value1, false ) . ' />' ;
				esc_html_e ('Full Visual Editor Only' , 'bbp-style-pack' ) ;?>
				<p/>
				<?php
				echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="radio" value="4" class="code"  ' . checked( 4,$value1, false ) . ' />' ;
				esc_html_e ('Show Both Full Visual and Text Editors' , 'bbp-style-pack' ) ;?>
																	
			</td>		
		</tr>
		
		<tr>
		<td>
			</td>
			<td colspan = '2'>
			<?php
			
				esc_html_e ('If you wish to use the Gutenberg Editor, please see this thread for if and how to do this:' , 'bbp-style-pack' ) ;?>
				<br/>
				<a href="https://wordpress.org/support/topic/how-to-enable-gutenberg-blocks/">https://wordpress.org/support/topic/how-to-enable-gutenberg-blocks/</a>
			</td>
			</tr>
		
		
<!--- 10. -->	
		
			
		<tr valign="top">
			<?php
			$name='topic_posting_rules' ;
			$name0 = __('Topic Posting Rules', 'bbp-style-pack') ;
			$name1 = __('Activate For Topics', 'bbp-style-pack') ;
			$name2 = __('Activate For Replies', 'bbp-style-pack') ;
			$area1='activate_for_topics';
			$area2='activate_for_replies';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			?>
			<th>
				<?php echo '10. '.esc_html($name0) ?>
			</th>
			<td colspan='2'>
				<label class="description"><?php esc_html_e( 'You can add some \'posting rules\' before the title on topics and/or replies ', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e ('Click to activate for Topics', 'bbp-style-pack') ; ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<?php
				$name1 = __('Topic Rules text', 'bbp-style-pack') ;
				$area1 = 'topic_rules_text' ;
				$item1="bsp_style_settings_form[".$area1."]" ;
				$value1 = (!empty($bsp_style_settings_form[$area1]) ? $bsp_style_settings_form[$area1]  : '') ;
				$value1 = trim ($value1) ;
				echo '<b>'.$name1.'</b><br>' ;
				esc_html_e( 'Use &lt;p&gt; to create paragraphs', 'bbp-style-pack' ) ; ?>
			</td>
			<td colspan=2>			
				<?php 
				echo '<textarea id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" rows="10">'.esc_html( $value1 ).'</textarea>'; ?>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '<input name="'.esc_html($item2).'" id="'.esc_html($item2).'" type="checkbox" value="1" class="code" ' . checked( 1,$value2, false ) . ' />' ;
				esc_html_e ('Click to activate for Replies', 'bbp-style-pack') ; ?>
			</td>
		</tr>


		
		<tr>
			<td>
			<?php 
				$name1 = __('Reply Rules text', 'bbp-style-pack') ;
				$area1 = 'reply_rules_text' ;
				$item1="bsp_style_settings_form[".$area1."]" ;
				$value1 = (!empty($bsp_style_settings_form[$area1]) ? $bsp_style_settings_form[$area1]  : '') ;
				$value1 = trim ($value1) ;
				echo '<b>'.$name1.'</b><br>' ;
				esc_html_e('Use &lt;p&gt; to create paragraphs', 'bbp-style-pack' ) ; ?>
			</td>
			<td colspan=2>			
				<?php
				echo '<textarea id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" rows="10">'.esc_html( $value1 ).'</textarea>'; ?>
			</td>
		</tr>
		
		<tr>
			<?php 
			$name = 'topic_posting_rules' ;
			$name1 = __('Size', 'bbp-style-pack') ;
			$name2 = __('Color', 'bbp-style-pack') ;
			$name3 = __('Font', 'bbp-style-pack') ;
			$name4 = __('Style', 'bbp-style-pack') ;
			$area1='Size' ;
			$area2='Color' ;
			$area3='Font' ;
			$area4='Style';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$item3="bsp_style_settings_form[".$name.$area3."]" ;
			$item4="bsp_style_settings_form[".$name.$area4."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_style_settings_form[$name.$area3]) ? $bsp_style_settings_form[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_style_settings_form[$name.$area4]) ? $bsp_style_settings_form[$name.$area4]  : '') ;
			?>
			
			<td>
			</td>
			<td>
				<?php echo esc_html($name1) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Default 12px - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
			
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name2) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="bsp-color-picker" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name3) ; ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item3).'" class="large-text" name="'.esc_html($item3).'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Enter Font eg Arial - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo esc_html($name4) ; ?>
			</td>
			<td>
				<select name="<?php echo esc_html($item4) ; ?>">
					<?php echo '<option value="'.esc_html( $value4).'">'.esc_html( $value4 ) ; ?> 
					<option value="Normal">Normal</option>
					<option value="Italic">Italic</option>
					<option value="Bold">Bold</option>
					<option value="Bold and Italic">Bold and Italic</option>
				</select>
			</td>
		</tr>
			
		<tr valign='top'>
			<?php 
			$name1 = __('Background Color', 'bbp-style-pack') ;
			$area1='Background Color' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<td>
			</td>
			<td>
				<?php echo esc_html($name1) ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="bsp-color-picker" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr valign='top'>
			<?php 
			$name1 = __('Border Color', 'bbp-style-pack') ;
			$area1='border_color' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<td>
			</td>
			<td>
				<?php echo esc_html($name1) ; ?> 
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="bsp-color-picker" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php esc_html_e( 'Click to set color - You can select from palette or enter hex value - see help for further info', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
<!--- 11. -->			
			
			<?php 
			$name = __('placeholder_', 'bbp-style-pack') ;
			$name1 = __('Topic Placeholder Text', 'bbp-style-pack') ;
			$name2 = __('Reply Placeholder Text', 'bbp-style-pack') ;
			$area1='topic' ;
			$area2='reply' ;
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1] : '');
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2] : '');
			?>
		<tr>
			<th>
				<?php echo '11. ' ;
					esc_html_e ('Placeholder NOTE: Placeholder will only work for text editor - the visual editor does not have that functionality', 'bbp-style-pack' ); ?>
				
			</th>
				
			<td>
				<?php echo esc_html($name1) ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'Placeholder text is text that suggests what the content should be e.g. \'make sure to include your name\'', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		<tr>
			<td>
			
			</td>
			
			<td>
				<?php echo esc_html($name2) ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'Placeholder text is text that suggests what the content should be e.g. \'make sure to include your name\'', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
</table>
<table class="form-table">	
		
<!-- 12. -->		
		<tr valign="top">
			<?php
			$name='html' ;
			$name0 = __('Remove HTML text', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '12. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( ' Remove \'Your account has the ability to post unrestricted HTML content\'', 'bbp-style-pack' ); ?>
			</td>
		</tr>
<!-- 13. -->		
		<tr valign="top">
			<?php
			$name='redirect_topic' ;
			$name0 = __('Redirect topic to forum list', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '13. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( ' After submission redirect user to the forum list instead of the topic ', 'bbp-style-pack' ); ?>
			</td>
		</tr>
<!-- 14. -->	
		<tr valign="top">
			<?php
			$name='redirect_reply' ;
			$name0 = __('Redirect reply to forum list', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '14. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( ' After submission redirect user to the forum list instead of the topic ', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		
<!-- 15. -->	
		<tr valign="top">
			<?php
			$name='nologin' ;
			$name0 = __('Do not show login if user not logged in', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '15. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'If the user is not logged in, version 2.6.x shows a bbpress login. Activate if you do not want this to show', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		
<!-- 16. -->	
		<tr valign="top">
			<?php
			$name='errormsg' ;
			$name0 = __('Add error messages to top of display', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '16. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				echo $name1; ?>
				<br/>
				<?php esc_html_e( 'bbpress displays error messages within the form.','bbp-style-pack' ); ?>
				<br/> 
				<?php esc_html_e( ' However users are taken to the top of the page on submission, so do not see the messages unless they scroll down.','bbp-style-pack' ); ?>
				<br/> 				
				<?php esc_html_e( 'Activating this also adds messages to the top of the display', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>	
			<?php echo '<img src="' . esc_url(plugins_url( 'images/reply-form-error.JPG',dirname(__FILE__) ) ) . '" width="500" > '; ?>
			</td>
		</tr>
<!-- 17. -->	
		<tr>
		<tr valign="top">
			<?php
			$name='errormsg' ;
			$name0 = __('Show message link', 'bbp-style-pack') ;
			$name1 = __('ActivateLink', 'bbp-style-pack') ;
			$area1='ActivateLink';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '17. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'Click to show a link to the form', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		<tr>
		<?php
			$name='errormsg' ;
			$name0 = __('Show message link', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$name2 = __('Message', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			$area2='Message';
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			?>
			<td>
				<?php echo esc_html($name2) ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"' ; ?> 
				<label class="description"><?php esc_html_e( 'Default : Click here to correct', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
<!-- 18. -->	
		<tr>
		<tr valign="top">
			<?php
			$name='disallowed' ;
			$name0 = __('Change disallowed content error message', 'bbp-style-pack') ;
			$name1 = __('ActivateLink', 'bbp-style-pack') ;
			$area1='ActivateLink';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '18. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'Activate', 'bbp-style-pack' ); ?>
				<br/>
				<?php esc_html_e( 'You can prevent use of particular words or links in titles or content by adding these to the dashboard>settings>discussion>Disallowed Comment Keys section.', 'bbp-style-pack' ); ?>
				<br/>
				<?php esc_html_e( 'If you do this, then a topic/reply with disallowed words will get a message saying "Your topic cannot be created at this time" which is not very helpful!', 'bbp-style-pack' ); ?>
				<br/>
				<?php esc_html_e( 'You can amend this message below', 'bbp-style-pack' ); ?>
				
				
			</td>
		</tr>
		<tr>
		<?php 
			$name='disallowed' ;
			$name2 = __('Message', 'bbp-style-pack') ;
			$area2='Message';
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			?>
			<td>
				<?php echo esc_html($name2) ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"' ; ?> 
				<label class="description"><?php esc_html_e( 'Default : Your submission contains disallowed words or links', 'bbp-style-pack' ); ?></label><br/>
				</td>
		</tr>
		
<!-- 19. -->	
		<tr>
		<tr valign="top">
			<?php
			$name0 = __('Limit Topic Tags to a list', 'bbp-style-pack') ;
			$name='topic_tag' ;
			$area1='_list';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '19. '.esc_html($name0) ?>
			</th>
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'Click to limit', 'bbp-style-pack' ); ?>
				<br/>
				<?php esc_html_e( 'This will limit users to the list of topic tags set up in Dashboard>topics>topic tags and prevent new topic tags being created.', 'bbp-style-pack' ); ?>
			</td>
		<tr>
		
<!-- 20. -->	
		<tr valign="top">
			<?php
			$name='no_anon_name' ;
			$name0 = __('Do not show name field on form for non logged in users', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '20. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'If the user is not logged in, and you have enabled \'Anonymous\' posting, then activating this will hide the name field on topic/reply forms', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		<tr>
		<?php
			$name2 = 'Name' ;
			$area2='name';
			$item2="bsp_style_settings_form[".$name.$area2."]" ;
			$value2 = (!empty($bsp_style_settings_form[$name.$area2]) ? $bsp_style_settings_form[$name.$area2]  : '') ;
			?>
			<td>
				<?php echo esc_html($name2) ?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"' ; ?> 
				<label class="description"><?php esc_html_e( 'Default: Anonymous - If you hide this field, you will need to assign a name to show for all anonymous users eg Anonymous, Anon, Guest etc.', 'bbp-style-pack' ); ?></label><br/>
				</td>
			</tr>
		
<!-- 21. -->	
		<tr valign="top">
			<?php
			$name='no_anon_email' ;
			$name0 = __('Do not show email field on form for non logged in users', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '21. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'If the user is not logged in, and you have enabled \'Anonymous\' posting, then activating this will hide the email field on topic/reply forms', 'bbp-style-pack' ); ?>
			</td>
		</tr>
		
<!-- 22. -->	
		<tr valign="top">
			<?php
			$name='no_anon_website' ;
			$name0 = __('Do not show website field on form for non logged in users', 'bbp-style-pack') ;
			$name1 = __('Activate', 'bbp-style-pack') ;
			$area1='Activate';
			$item1="bsp_style_settings_form[".$name.$area1."]" ;
			$value1 = (!empty($bsp_style_settings_form[$name.$area1]) ? $bsp_style_settings_form[$name.$area1]  : '') ;
			?>
			<th>
				<?php echo '22. '.esc_html($name0) ?>
			</th>
			
			
			<td colspan=2>
				<?php echo '<input name="'.esc_html($item1).'" id="'.esc_html($item1).'" type="checkbox" value="1" class="code" ' . checked( 1,$value1, false ) . ' />' ;
				esc_html_e( 'If the user is not logged in, and you have enabled \'Anonymous\' posting, then activating this will hide the website field on topic/reply forms', 'bbp-style-pack' ); ?>
			</td>
		</tr>

				
	
		
	</table>
	<!-- save the options -->
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
		</p>
</form>
	
<?php
}
