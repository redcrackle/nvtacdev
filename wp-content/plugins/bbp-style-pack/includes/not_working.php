<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_not_working() {
 ?>

<h3>
	<?php esc_html_e('It\'s not working !!', 'bbp-style-pack'); ?>
</h3>

<p><?php esc_html_e('It may be as simple as closing and restarting your browser, so try this first !!', 'bbp-style-pack'); ?>
</p>

<p><?php esc_html_e('If that doesn\'t work...', 'bbp-style-pack'); ?>
</p>

<h4><?php esc_html_e('Background', 'bbp-style-pack'); ?>
</h4>

<p><?php esc_html_e('Style pack works with many/most sites, but it can fail due to a myriad of reasons, including (but no means limited to) site permissions, php versions, other plugins and most often site themes where the theme author has altered bbpress files.', 'bbp-style-pack'); ?>
</p>

<p><?php esc_html_e('This is no-ones fault - whilst I can control how bbpress performs, there are many thousands of plugins and themes, all of which may be trying to amend the same stuff as I am, and many host poviders who will have differing but equally valid permission and code versions.', 'bbp-style-pack'); ?></p>

<h4><?php esc_html_e('Problem finding', 'bbp-style-pack'); ?>
</h4>

<p><?php esc_html_e('1. "caching" software that speeds up the download of your site, but may not recognise and immediately make changes from my plugin', 'bbp-style-pack'); ?>
</p>
<p><?php esc_html_e('Do you know if you are using caching software?  If so then try refreshing, purging or deactivating to see if this fixes.', 'bbp-style-pack'); ?>
	</p>
	
<p><?php esc_html_e('If you still have the issue, we need to work out which parts are working and which not', 'bbp-style-pack'); ?>
</p>

<p><?php esc_html_e('We need to look at 2 areas - files and css', 'bbp-style-pack'); ?>
</p>

<h4><?php esc_html_e('2. Files', 'bbp-style-pack'); ?>
</h4>

<p><?php esc_html_e('If your theme author has changed bbpress files in his theme, this will be for valid reason, which may relate to either style or functionality', 'bbp-style-pack'); ?>
</p>

<?php 

$dir = get_stylesheet_directory();

$dir =$dir.'/bbpress/' ;

	if (is_dir($dir)){
		
	  if ($dh = opendir($dir)){
		 echo '<p> <span style = "color : white ; background-color : #FFC200;">' ;
		esc_html_e( 'The following bbpress files are held a bbpress folder in your theme' , 'bbp-style-pack' ) ;
		echo '</span></p>' ;	
		 
		while (($file = readdir($dh)) !== false){
			if ($file != '.' && $file != '..' ) {
				echo esc_html($file) . '<br>';
			}
		}
		closedir($dh);
		echo '<p> <span style = "color : white ; background-color : #FFC200;">' ;
		esc_html_e(' This may or may not be affecting the working of my plugin - contact my' , 'bbp-style-pack' ) ;
		echo ' <a href ="https://wordpress.org/support/plugin/bbp-style-pack" target="_blank"> ' ;
		esc_html_e ( 'support site' , 'bbp-style-pack' ) ;
		echo '</a> if you need further help  </span></p>' ;	
		 
	  }
	}

else {
	echo '<p> <span style = "color : white ; background-color : green;">' ;
	esc_html_e ('No bbpress files have been changed in the theme' , 'bbp-style-pack' ) ;
	echo '</span></p>' ;	
	}

$dir2 = get_template_directory(); 
$dir2 =$dir2.'/bbpress/' ;

if ($dir != $dir2 ) {
	
	if (is_dir($dir2)){
		echo '<p> <span style = "color : white ; background-color : amber;">' ;
		esc_html_e ('The following bbpress files are held a bbpress folder in your parent theme' , 'bbp-style-pack') ;
		echo '</span></p>' ;
	  if ($dh = opendir($dir2)){
		while (($file = readdir($dh)) !== false){
		if ($file != '.' && $file != '..' ) {
			echo esc_html($file) . '<br>';
		}
		}
		closedir($dh);
		echo '<p> <span style = "color : white ; background-color : #FFC200;">' ;
		esc_html_e(' This may or may not be affecting the working of my plugin - contact my' , 'bbp-style-pack' ) ;
		echo ' <a href ="https://wordpress.org/support/plugin/bbp-style-pack" target="_blank"> ' ;
		esc_html_e ( 'support site' , 'bbp-style-pack' ) ;
		echo '</a> if you need further help  </span></p>' ;		
	 
	  }
	}

	else {
	echo '<p> <span style = "color : white ; background-color : green;">' ;
	esc_html_e( 'No bbpress files have been changed in the parent theme' , 'bbp-style-pack') ;
	echo '</span></p>' ;	
	}

}



?>


<h4><?php esc_html_e('3. CSS', 'bbp-style-pack'); ?>
</h4>

<p><?php esc_html_e('If css had loaded then the text below will have a green background, if it has not loaded this will be a red background', 'bbp-style-pack'); ?>
</p>
<style>
.not-working  {
	color : white ;
    background-color: red;
} 
</style>
<p><span class="not-working working "><?php esc_html_e('This sentance should have a green background', 'bbp-style-pack'); ?>
</span></p>

<p><?php esc_html_e('If the background is red, then try amending the "css location" tab above', 'bbp-style-pack'); ?>
<table>
<tr>
<td></td>
<td>
	<?php esc_html_e('check the activate box and set the location to ', 'bbp-style-pack'); ?>
</td>
</tr>
<tr>
<td></td>
<td></td><td>
	<?php esc_html_e('wp-content/uploads/', 'bbp-style-pack'); ?>
</td>
</tr>
<tr>
<td></td>
<td>
	<?php esc_html_e('and save these changes', 'bbp-style-pack'); ?>
</td>
</tr>
</table>
		
<p><?php esc_html_e('and then come back and re-check this area.', 'bbp-style-pack'); ?>
</p>

<?php
}
?>