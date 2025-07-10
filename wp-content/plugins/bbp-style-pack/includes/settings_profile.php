<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//profile settings page

function bsp_profile_settings() {
	global $bsp_profile ;
	?>
	<form method="post" action="options.php">
	<?php 
        wp_nonce_field( 'profile', 'profile-nonce' );
	settings_fields( 'bsp_profile' ); 
        bsp_clear_cache();
        ?>
            
        <?php 
        $item =  'bsp_profile[profile]' ;
        $item1 = (!empty($bsp_profile['profile']) ? $bsp_profile['profile'] : 0);
        ?>
            
	<h3> <?php esc_html_e ('Profile Settings' , 'bbp-style-pack' ) ; ?>	</h3>
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">
					
		<tr valign="top">
			<th colspan="2">
				
				
					<p>						
						1. <?php esc_html_e ('Turn off Profiles', 'bbp-style-pack' ) ; ?>
					</p>
					<p>
						<?php esc_html_e ('You can choose to allow all users to see profiles, or only show profiles to logged in users, or turn off all profiles.' , 'bbp-style-pack' ) ; ?> 
					</p>
					<p>
						<?php esc_html_e ('NOTE: Keymaster role will always be able to see all profiles' , 'bbp-style-pack' ) ; ?> 
					</p>

			</th>
		</tr>
		
<!--show all Profile ---------------------------------------------------------------------->			
		<tr>
			<td>
			</td>
			
			<td>
				<?php
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item1).'" type="radio" value="0" class="code" ' . checked( 0,$item1, false ) . ' /> ';
				esc_html_e ('Show Profiles to everyone' , 'bbp-style-pack' ) ; ?>
				<br>
				<label class="description">
					<i>
					<?php esc_html_e( 'This is the default in bbpress.' , 'bbp-style-pack' ); ?>
					</i>
				</label>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td>
				<?php
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item1).'" type="radio" value="1" class="code" ' . checked( 1,$item1, false ) . ' /> ';
				esc_html_e ('Show Profiles only to logged in users' , 'bbp-style-pack' ) ; 
				?>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td>
				<?php
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item1).'" type="radio" value="2" class="code" ' . checked( 2,$item1, false ) . ' /> ';
				esc_html_e ('Show only users own Profile' , 'bbp-style-pack' ) ;
				?>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td>
				<?php
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item1).'" type="radio" value="3" class="code" ' . checked( 3,$item1, false ) . ' /> ';
				esc_html_e ('Turn off all Profiles' , 'bbp-style-pack' ) ;
				?>
			</td>
		</tr>
	
		<tr>
			<td>
			</td>
			
			<td>
				<?php
				$item =  'bsp_profile[moderator]' ;
				$item1 = (!empty($bsp_profile['moderator']) ? $bsp_profile['moderator'] : '');
				esc_html_e ('If you have selected other than "Show only users own Profile"  or "Turn off all Profiles", then click if you wish moderators to see all profiles' , 'bbp-style-pack' ) ;
				echo '<br/><input name="'.esc_html($item).'" id="'.esc_html($item).'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' /> ';
				esc_html_e ('Allow moderators to see all profiles' , 'bbp-style-pack' ) ;
				?>
			</td>
		</tr>
		<?php
			$area1='profile-redirect' ;
			$item1="bsp_profile[".$area1."]" ;
			$value1 = (!empty($bsp_profile[$area1]) ? $bsp_profile[$area1] : '');
			?>
		<tr>
			<td>
				<?php echo 'Profile redirection. '; ?> 
			</td>
			</tr>
			<tr>
			
			<td colspan=2>
				<?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html ($value1).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'IF you are restricting profile display, anyone unauthorised trying to access a profile directly needs to be sent somewhere.  By default this will be the homepage, but you can choose a different page here, or enter "/404/" to send them to your sites 404 page', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>

	</table>

	<table class="form-table">
		<tr>
			<th>				
				2. 
				<?php esc_html_e ('Gravatars' , 'bbp-style-pack' ) ; ?>
			</th>
		</tr>
	</table>
	<p>
		<?php esc_html_e ('BBpress uses Gravatar for avatars.  Unless you have activated a plugin that allows users to upload avatars, then your users will need user the Gravatar system. ' , 'bbp-style-pack' ) ; ?> 
	</p>
	<p>
		<?php esc_html_e ('This feature allows you to tell them about this when they visit their profile page' , 'bbp-style-pack' ) ; ?>
	</p>

		<?php echo '<img src="' . esc_url(plugins_url( 'images/profile1.JPG',dirname(__FILE__) ) ) . '" width="600" > '; ?>


	<table class="form-table">
			
<!--add gravatar to menu ---------------------------------------------------------------------->			
		<tr>
			<th>
				<?php esc_html_e ("Show a gravatar link on the profile page" , 'bbp-style-pack' ) ; ?>
			</th>
			
			<td colspan="2">
				<?php
				$item =  'bsp_profile[gravatar]' ;
				$item1 = (!empty($bsp_profile['gravatar']) ? $bsp_profile['gravatar'] : '');
				echo '<input name="'.esc_html($item).'" id="'.esc_html($item).'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' /> ';
				esc_html_e ('Click to activate' , 'bbp-style-pack' ) ;
				?>
			</td>
		</tr>

	
<!--Gravatar page ---------------------------------------------------------------------->
		<tr>
			<?php 
			$name = __('Profile', 'bbp-style-pack') ;
			$name1 = __('Gravatar Label', 'bbp-style-pack') ;
			$name2 = __('Gravatar Description', 'bbp-style-pack') ;
			$name3 = __('Gravatar URL', 'bbp-style-pack') ;
			$name4 = __('Gravatar URL Description', 'bbp-style-pack') ;
			$area1='Gravatar Label' ;
			$area2='Item Description' ;
			$area3='Page URL' ;
			$area4='URL Description' ;
			$item1="bsp_profile[".$name.$area1."]" ;
			$item2="bsp_profile[".$name.$area2."]" ;
			$item3="bsp_profile[".$name.$area3."]" ;
			$item4="bsp_profile[".$name.$area4."]" ;
			$value1 = (!empty($bsp_profile[$name.$area1]) ? $bsp_profile[$name.$area1] : '');
			$value2 = (!empty($bsp_profile[$name.$area2]) ? $bsp_profile[$name.$area2] : '');
			$value3 = (!empty($bsp_profile[$name.$area3]) ? $bsp_profile[$name.$area3] : '');
			$value4 = (!empty($bsp_profile[$name.$area4]) ? $bsp_profile[$name.$area4] : '');
			?>
			<td>
			</td>
			
			<td>
				<?php echo '1. '.esc_html($name1) ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item1).'" class="large-text" name="'.esc_html($item1).'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'Enter the label name', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '2. '.esc_html($name2) ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item2).'" class="large-text" name="'.esc_html($item2).'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'Enter any text eg "Manage your profile picture at"', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '3. '.esc_html($name3) ; ?> 
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item3).'" class="large-text" name="'.esc_html($item3).'" type="text" value="'.esc_html ($value3).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'You should create either a wordpress page with gravatar instructions or link to gravatar eg http://www.mysite.com/gravatar-explanation-page or https://en.gravatar.com/', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo '4. '.esc_html($name4) ; ?>
			</td>
			
			<td>
				<?php echo '<input id="'.esc_html($item4).'" class="large-text" name="'.esc_html($item4).'" type="text" value="'.esc_html( $value4 ).'"<br>' ; ?> 
				<label class="description">
					<?php esc_html_e( 'Enter text you want this link to show eg "click here" or "https://en.gravatar.com/"', 'bbp-style-pack' ); ?>
				</label>
				<br/>
			</td>
		</tr>
		
		<tr>
			<th>
				3. <?php esc_html_e ("Amend admin bar profile link", 'bbp-style-pack' ) ; ?>
			</th>
			<td colspan=2>
			<?php esc_html_e ("If users are allowed to see the WordPress admin bar, then you can change this to the bbpress edit profile, replacing the wordpress one", 'bbp-style-pack') ; ?>
			<?php esc_html_e ('see' , 'bbp-style-pack') ; ?>
			<a href="/wp-admin/options-general.php?page=bbp-style-pack&tab=login">
			<?php esc_html_e ('login tab item 6' , 'bbp-style-pack') ; ?>
			</a>
			<br>
			</td>
		</tr>
	</table>
<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	</form>

<?php esc_html_e ("Possible wording that you might use on a wordpress page", 'bbp-style-pack' ) ; ?>
<br> 
	 <?php esc_html_e ("This site uses <em>Gravatar</em> to display avatars. if you already have a <em>Gravatar</em> account with your email for this site, then it will display on your forum posts; it can take a few hours before the link is established the first time.", 'bbp-style-pack' ) ; ?>
<br>
<strong>
	<?php esc_html_e ("What is an Avatar?", 'bbp-style-pack' ) ; ?>
</strong>
<br>
<?php esc_html_e ("When you post, you'll see the default avatar beside your name. You can change this to any image you wish (providing it's decent!).  Many people have a picture of themselves, or their family or anything that represents your ego or character.", 'bbp-style-pack' ) ; ?>
<strong>
<br>
<?php esc_html_e ("Changing from the default avatar.", 'bbp-style-pack' ) ; ?>
</strong>
<br>
<?php esc_html_e ("The site uses Gravatar.  The Gravatar site stores you avatar against your email address.", 'bbp-style-pack' ) ; ?>
<br>
<?php esc_html_e ("This allows you to have a single image that many websites can use without you needing to add it or change it on each site.", 'bbp-style-pack' ) ; ?>
<br>
<?php esc_html_e ("Since this site knows your email address (through your profile), this site can pick up the avatar stored with Gravatar.", 'bbp-style-pack' ) ; ?>
<br>
<?php esc_html_e ('If you are not already signed up with Gravatar, then follow <a href="https://en.gravatar.com/site/signup" target="_blank">this link</a> to register your email address and avatar with them.  This site will then pick up that avatar and display it against your post. It can take a few hours before the link is established the first time.', 'bbp-style-pack' ) ; ?>
<br>
<strong>
<?php esc_html_e ("Confused?", 'bbp-style-pack' ) ; ?>
</strong>
<?php esc_html_e ("Don't worry - if you're happy with the default avatar against your name, you need to do nothing further !", 'bbp-style-pack' ) ; ?>
<br>	

<?php
}
