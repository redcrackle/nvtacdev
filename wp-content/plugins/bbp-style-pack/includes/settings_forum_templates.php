<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_forum_templates() {
	global $bsp_templates ;
	?>
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'templates', 'templates-nonce' ) ?>
	<?php settings_fields( 'bsp_templates' );
        bsp_clear_cache();
	?>	
	
	<table class="form-table">
		<tr valign="top">
			<th colspan="2">
				<h3>
					<?php esc_html_e ('Forum Templates' , 'bbp-style-pack' ) ; ?>
				</h3>
		</tr>
	</table>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>		
	<table class="form-table">
		<?php //set up variables
		$name =  'bsp_templates[template]' ;
		$item = (!empty($bsp_templates['template']) ? $bsp_templates['template'] : 0);
		?>
<!--choose template 0 ---------------------------------------------------------------------->
		<tr>
			<td colspan='3' >
				<h4>
					<span style="color:blue">
						<?php esc_html_e('Default Forum template', 'bbp-style-pack' ) ; ?>
					</span>
				</h4>
			</td>
		</tr>

		<tr>
			<td colspan='3'>
				<?php esc_html_e ('This is the default template in bbpress' , 'bbp-style-pack' ) ; ?> 
			</td>
		</tr>
		<tr>
			<td>
				<?php
				echo '<input name="'.esc_html($name).'" id="'.esc_html($item).'" type="radio" value="0" class="code"  ' . checked( 0,$item, false ) . ' />' ;
				esc_html_e ('Click to select' , 'bbp-style-pack' ) ;?>
				<br>
					<label class="description">
						<i>
						<?php esc_html_e( '(You can set the sub forum display in the forum display tab)' , 'bbp-style-pack' ); ?>
						</i>
					</label>
			</td>
			
			<td width="30%">
				<?php echo '<img src="' . esc_url(plugins_url( 'images/extras1.JPG',dirname(__FILE__) ) ) . '"  > '; ?>
			</td>

			<td width="30%">
				<?php esc_html_e ('or' , 'bbp-style-pack' ) ; ?>
				<?php echo '<img src="' . esc_url(plugins_url( 'images/forum2.JPG',dirname(__FILE__))  ) . '" > '; ?>
			</td>
		</tr>
	</table>

<!--choose template 1---------------------------------------------------------------------->
	<table>
		<tr>
			<td colspan='3'>
				<h4>
					<span style="color:blue">
						<?php esc_html_e('Alternate Forum template 1', 'bbp-style-pack' ) ; ?>
					</span>
				</h4>		
			</td>
		</tr>
		
		<tr>
			<td>
				<?php esc_html_e ('This alternate version lists the main forums in seperate sections.  Each Section has a forum of type \'Category\' and the forums in that section then have that category as their parent' , 'bbp-style-pack' ) ; ?>
			</td>
			<td><?php echo '<img src="' . esc_url(plugins_url( 'images/extras3.JPG',dirname(__FILE__) ) ) . '" width=600px> '; ?></td>
		</tr>
	</table>
	
	<table>
		
		<tr>
			<td>
				<?php
				echo '<input name="'.esc_html($name).'" id="'.esc_html($item).'" type="radio" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
				esc_html_e ('Click to select' , 'bbp-style-pack' ) ;
				?>
			</td>
			<td width=100px></td>
			
			<td><?php echo '<img src="' . esc_url(plugins_url( 'images/extras2.JPG',dirname(__FILE__))  ) . '"  > '; ?></td>
			
		</tr>
	</table>


<table>
<tr>
			<th colspan="2" style="vertical-align:top">
				<?php esc_html_e ('Template Priority' , 'bbp-style-pack' ) ; ?>
			</th>
			
			<td>
			</td>
			<td>
			<?php
			$name = 'template_priority' ; ;
			$item1="bsp_templates[".$name."]" ;
			$value1 = (!empty($bsp_templates[$name] ) ? $bsp_templates[$name]  : '') ;
			//echo $name1 ; 
			?>
			</td>
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="small-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br/>' ; ?> 
				<label class="description"><b><?php esc_html_e( 'In most cases the templates in this plugin will work.  Only enter a value here if the template does not work.', 'bbp-style-pack' ); ?></b></label><br/>
				<label class="description"><?php esc_html_e( 'In some cases, your theme or other plugins can also change templates and register different templates. ' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'If this is the case, then you may have to choose between the theme/plugin template and this plugin\'s templates' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'To get this plugin\'s templates to load at the right point, you may need to change its priority.' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'As your theme and/or other plugins can register any number as priority, you may need to enter a number higher or lower than this.' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'First try entering 2, then try 20 - one is lower then the default priorities of 6 and 12, the other higher.' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'If that does not work, try 1 then try 100.' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'Then in turn try all the numbers between 2 and 20 - if say your theme loads some at one value and another plugin at another value, and you need a value in between!' , 'bbp-style-pack' ); ?></label><br/>
				<label class="description"><?php esc_html_e( 'Finally try 1000.  If that does not work, then I have no good suggestions!' , 'bbp-style-pack' ); ?></label><br/>
			
				
				
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
