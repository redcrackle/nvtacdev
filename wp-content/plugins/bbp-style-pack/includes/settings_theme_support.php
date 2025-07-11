<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_style_settings_theme_support() {
	global $bsp_theme_check ;
 ?>
			
	<h3>
		<?php esc_html_e ('Theme Support' , 'bbp-style-pack' ) ; ?>
	</h3>
	
	<?php 
/////////////////////  FSE Theme	
	if ($bsp_theme_check == 'block_theme') {
		$theme = wp_get_theme() ;
        ?>

                <p><b>
                        <?php esc_html_e ('You are using the theme ' , 'bbp-style-pack') ;
                        echo $theme ;
                        esc_html_e (', which is a block theme also known as "Full Site Editing" or FSE theme' , 'bbp-style-pack' ) ; ?>
                </b></p>

                <p>
                        <?php 
                        esc_html_e (' This is one of the new "block themes" - this is a new way that WordPress plans to develop themes. ' , 'bbp-style-pack' ) ; ?>
						<?php esc_html_e(' As Wordpress roll out blocks and FSE themes, we developers are needing to learn new techniques and tools.', 'bbp-style-pack'); ?> </p>
                        
						<p> <?php esc_html_e('bbPress authors have stated that they plan to release a version that will work with FSE themes in the future ', 'bbp-style-pack'); ?>
						<?php esc_html_e('but at the moment it needs a bit of help to do so.', 'bbp-style-pack'); ?> </p>
						<p> <?php esc_html_e('So until then you can use the settings below to get bbpress to work with your FSE theme.', 'bbp-style-pack'); ?> </p>
						
                        <p>	<?php esc_html_e('If you just want your forums to display with no other content, then (if your theme does not specifically cater for bbpress) enabling basic support should allow the forums to display.', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('If you just want additional content on the forums page above, below or to the side of the forums, then enabling advanced support should let you do this, BUT read the instructions on how to set this up below.', 'bbp-style-pack'); ?> </p>
                        <p><i>	<?php esc_html_e('In either case, you can set the width of the forum page if you need to.', 'bbp-style-pack'); ?> </i> </p>
						
						<p><b> <?php esc_html_e('Alternately and for closer integration you can use the', 'bbp-style-pack')?>
						<a href= "https://wordpress.org/plugins/enable-bbp-block-theme/">Enable bbPress for Block Themes </a> 
						<?php esc_html_e('plugin, in which case select the "No theme support" option below', 'bbp-style-pack'); ?> </p>
                      

						</b> </p>
						




                <?php global $bsp_style_settings_theme_support ;
                ?>
                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings-theme-support', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_theme_support' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>

                                <table class="form-table">

                                <!-- ACTIVATE  -->	
                <?php $theme_support = (!empty( $bsp_style_settings_theme_support['fse'] ) ?  $bsp_style_settings_theme_support['fse'] : 0); ?>
                <tr valign="top">  
                                <th style="width: 350px">
                                        <?php esc_html_e('No theme support', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse]" id="bsp_style_settings_theme_support[fse]" type="radio" value="0" class="code" ' . checked( 0,$theme_support, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse]">
                                                <?php esc_html_e( 'Do NOT Enable bbp Style Pack theme support', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>


                        </tr>
                        <tr valign="top">  
                                <th style="width: 350px">
                                        <?php esc_html_e('Enable Basic', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse]" id="bsp_style_settings_theme_support[fse]" type="radio" value="1" class="code" ' . checked( 1,$theme_support, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse]">
                                                <?php esc_html_e( 'Add support to just show the forums with no additional content', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
                        </tr>

                        <tr valign="top">  
                                <th style="width: 350px">
                                        <?php esc_html_e('Enable advanced FSE theme support', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 

                                        echo '<input name="bsp_style_settings_theme_support[fse]" id="bsp_style_settings_theme_support[fse]" type="radio" value="2" class="code" ' . checked( 2,$theme_support, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse]">
                                                <?php esc_html_e( 'Add advanced theme suppport - see below for instructions', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
                        </tr>
                        
                <!-- width  ------------------------------------------------------------------->
                        <tr>
                                <?php 
                                $name = __('Forum page width','bbp-style-pack')  ; 
                                $item =  'bsp_style_settings_theme_support[fse_width]' ;
                                $item1 = (!empty($bsp_style_settings_theme_support['fse_width'] ) ? $bsp_style_settings_theme_support['fse_width']  : ''); 
                                ?>
                                <td>
                                <?php echo $name ; ?>
                                </td>
                                <td>
                                        <?php echo '<input id="'.$item.'" class="small-text" name="'.$item.'" type="text" value="'.esc_html( $item1 ).'" /><br>' ; ?> 
                                        <label class="description"><?php esc_html_e( 'Default 75%', 'bbp-style-pack' ); ?></label><br/>
                                </td>
                        </tr>
                        <tr>
                                <?php
                                $name = __('bbPress template page for advanced FSE theme support','bbp-style-pack')  ; 
                                ?>
                                <td>
                                        <?php echo $name ; ?>
                                </td>
                                <td>
                                        <?php 
                                        $item1 = (!empty($bsp_style_settings_theme_support['fse_template_page'] ) ? $bsp_style_settings_theme_support['fse_template_page']  : ''); 
                                        $list = array (
                                        'name' => 'bsp_style_settings_theme_support[fse_template_page]' , 
                                        'id' => 'bsp_style_settings_theme_support[fse_template_page]',
                                        'selected' => $item1,
                                        'show_option_none' => 'no page'
                                        );
                                        ?>

                                        <?php wp_dropdown_pages($list) ; ?>
                                        <label class="description"><?php esc_html_e( 'Select the page to act as a template for forum display - see below for help', 'bbp-style-pack' ); ?></label><br/>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php esc_html_e('Your forums url is ', 'bbp-style-pack'); ?>
                                </td>
                                <td>
                                        <?php echo esc_url(get_site_url()).'/'.bbp_get_root_slug() ; ?>
                                        <br/>
                                        <label class="description">
                                        <?php esc_html_e( 'You can amend the \'', 'bbp-style-pack' ); 
                                        echo bbp_get_root_slug() ;
                                        esc_html_e( '\' name by going to ', 'bbp-style-pack' ); 
                                        echo '<a href="' . esc_url(site_url()) . '/wp-admin/options-general.php?page=bbpress">' ; 
                                        esc_html_e('Dashboard>settings>forums', 'bbp-style-pack');
                                        echo '</a>' ;
                                        esc_html_e(' and changing the forum root slug', 'bbp-style-pack');
                                        ?>
                                        </label>
                                </td>

                        </tr>
						
						                 <!-- ACTIVATE  -->	
                <?php $page_template = (!empty( $bsp_style_settings_theme_support['fse_template_version'] ) ?  $bsp_style_settings_theme_support['fse_template_version'] : 0); ?>
                <tr valign="top">  
                                <th style="width: 350px">
                                        <?php esc_html_e('Page display options', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse_template_version]" id="bsp_style_settings_theme_support[fse_template_version]" type="radio" value="0" class="code" ' . checked( 0,$page_template, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse_template_version]">
                                                <?php esc_html_e( 'For most sites this setting should work ', 'bbp-style-pack' ); ?>
                                        </label>
										<br>
										<?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse_template_version]" id="bsp_style_settings_theme_support[fse_template_version]" type="radio" value="1" class="code" ' . checked( 1,$page_template, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse_template_version]">
                                                <?php esc_html_e( 'However try this if your header does not display correctly eg is out of line or has wrong font sizes etc. ', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>
								
								 <!-- ACTIVATE  -->	
                <?php $footer_template = (!empty( $bsp_style_settings_theme_support['fse_template_footer'] ) ?  $bsp_style_settings_theme_support['fse_template_footer'] : 0); ?>
                <tr valign="top">  
                                <th style="width: 350px">
                                        <?php esc_html_e('Footer options', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse_template_footer]" id="bsp_style_settings_theme_support[fse_template_footer]" type="radio" value="0" class="code" ' . checked( 0,$footer_template, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse_template_footer]">
                                                <?php esc_html_e( 'Standard Footer ', 'bbp-style-pack' ); ?>
                                        </label>
										<br>
										<?php 
                                        echo '<input name="bsp_style_settings_theme_support[fse_template_footer]" id="bsp_style_settings_theme_support[fse_template_footer]" type="radio" value="1" class="code" ' . checked( 1,$footer_template, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_style_settings_theme_support[fse_template_footer]">
                                                <?php esc_html_e( 'If you are using shortcodes in your footer, this option should ensure they display correctly ', 'bbp-style-pack' ); ?>
                                        </label>
                                </td>


                        

                        </table>

                        <!-- save the options -->
                        <p class="submit">
                                <input type="submit" class="button-primary" value="<?php esc_html_e( 'Save', 'bbp-style-pack' ); ?>" />
                        </p>
                </form>


                        <strong>
                        <h2>	<?php esc_html_e('Advanced FSE support', 'bbp-style-pack'); ?> </h2>
                        </strong>
                        <p>	<?php esc_html_e('I have tried to make this as easy as possible, so whilst there may be more sophisticated ways of doing this say via block templates, these require a level of knowledge that you may not have and that I do not know how to use!', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('This method uses a \'dummy\' page to hold the layout.  This page is never accessed by the site users, but bbPress uses it to know where and what to display.', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('So we will create a wordpress page, enter the content we want including a shortcode block with the [bbp-forum-index] shortcode in it, and set the \'bbPress template page\' above to this page, and that is it !', 'bbp-style-pack'); ?> </p>


                        <strong>
                        <h3>	<?php esc_html_e('1. Create dummy page', 'bbp-style-pack'); ?> </h3>
                        </strong>
                        <p>	<?php esc_html_e('In <i> Dashboard>pages>add new </i> create a new page.  You can title this page anything you want - <i> bbpress template </i> is a good name.', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('You cannot call it forums, forum, topic, reply or any slug you have set in <i> dashboard>settings>forums>slugs</i>', 'bbp-style-pack'); ?> </p>

                        <strong>
                        <h3>	<?php esc_html_e('2. Populate the page', 'bbp-style-pack'); ?> </h3>
                        </strong>
                        <p>	<?php esc_html_e('You can design this page to have <i> any </i> content you want, but it must have one block that is a shortcode block, and in this block you will have the [bbp-forum-index] shortcode.', 'bbp-style-pack'); ?> </p>

                        <strong>
                        <h3>	<?php esc_html_e('2a. Forum Patterns', 'bbp-style-pack'); ?> </h3>
                        </strong>
                        <p>	<?php esc_html_e('I have included 2 patterns which make it easy to add a left or right sidebar.  These are populated with the login, latest activity, forum information and single topic information widgets.', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('If you want to try these, then from the blank page:', 'bbp-style-pack'); ?> </p>
                        <ul>
                        <li> <?php esc_html_e('click the + in the top left hand to add a block', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('select patterns (the middle of \'blocks | patterns | Media\')', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('select \'bbp style pack forum patterns\'', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('select left or right sidebar versions', 'bbp-style-pack'); ?> </li>
                        </ul>

                        <p>	<?php esc_html_e('Once added, you can easily change which widgets are displayed, what they display, and change column widths etc.', 'bbp-style-pack'); ?></p>
                        <p>	<?php esc_html_e('See the \'block widgets\' tab for more details on block widgets available within the Style Pack Plugin.', 'bbp-style-pack'); ?></p>

                        <strong>
                        <h3>	<?php esc_html_e('3. Set advanced theme support and set the template page', 'bbp-style-pack'); ?> </h3>
                        </strong>
                        <p>	<?php esc_html_e('In the settings above, click the \'Enable advanced FSE theme support \' selection.', 'bbp-style-pack'); ?> </p>
                        <p>	<?php esc_html_e('In the settings above, set the \'bbPress template page \' to the page you set up in 1. above', 'bbp-style-pack'); ?> </p>

                        <strong>
                        <h3>	<?php esc_html_e('4. Add forums to your menu page if desired', 'bbp-style-pack'); ?> </h3>
                        </strong>
                        <p>	<?php esc_html_e('This may depend on how your theme handles menus - but I suspect will be the same as Wordpress.', 'bbp-style-pack'); ?> </p>
                        <ul>
                        <li> <?php esc_html_e('Go to dashboard>appearance>editor.  This will show you your home page, which typically includes your menus whether in header or footer.', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('You should see in the menus \'Add label\' which is where you add new menu items', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('click <i> add label </i> and type in the name you wish to appear for your forums eg Forums', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('The item control box should appear, allowing you to add a link using the (-) link symbol', 'bbp-style-pack'); ?> </li>
                        <li> <?php esc_html_e('Enter you forums url, which is ', 'bbp-style-pack'); ?> 
                        <?php echo esc_url(get_site_url()).'/'.bbp_get_root_slug() ; ?>
                        </li>
                        <li> <?php esc_html_e('Remember to press the \'enter\' button when creating a link - this is a wordpress bug, not under my control, and clicking the mouse when complete will NOT save the link.', 'bbp-style-pack'); ?> 
                        </ul>

        <?php
        } // end of FSE theme
	

/////////////////////  Astra Theme	

        if ($bsp_theme_check == 'astra') {
                        $theme = wp_get_theme() ;
                        $version = $theme->get( 'Version' ) ;
                        ?>

                <p><b>
                        <?php 
                                esc_html_e ('You are using the ' , 'bbp-style-pack') ;
                                echo $theme ;
                                esc_html_e (' theme (or a child theme of this theme) version ' , 'bbp-style-pack') ;
                                echo $version ;
                                if ($version == '4.0.2')  esc_html_e (' which has issues whereby bbpress Profiles, search and views do not work properly' , 'bbp-style-pack' ) ; 
                                else esc_html_e (' which has an issue whereby bbpress views do not work properly' , 'bbp-style-pack' ) ; 
                                esc_html_e (' - enable support fix to correct this' , 'bbp-style-pack' ) ; 
                        ?>


                </b></p>

                <?php global $bsp_style_settings_theme_support ;
                ?>
                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings-theme-support', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_theme_support' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>

                                <table class="form-table">

                                <!-- ACTIVATE  -->	
                <!-- checkbox to activate  -->
                        <tr valign="top">  
                                 <th style="width: 350px">
                                        <?php esc_html_e('Enable Astra theme support fix', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_theme_support['astra'] ) ?  $bsp_style_settings_theme_support['astra'] : '');
                                        echo '<input name="bsp_style_settings_theme_support[astra]" id="bsp_style_settings_theme_support[astra]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_settings[theme_support]">
                                                <?php esc_html_e( 'Enable Theme Support', 'bbp-style-pack' ); ?>
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
        } // end of ASTRA theme


/////////////////////  Divi Theme	

        if ($bsp_theme_check == 'divi') {
                        $theme = wp_get_theme() ;
                ?>

                <p><b>
                        <?php esc_html_e ('You are using the ' , 'bbp-style-pack') ;
                        echo $theme ;
                        esc_html_e (' theme (or a child theme of this theme) which has an issue whereby bbpress Profiles and bbpress search may not work properly' , 'bbp-style-pack' ) ; ?>
                </b></p>
                <p>
                        <?php esc_html_e ('If you have issues with either of these, please change the following Divi theme settings: ' , 'bbp-style-pack') ; ?>
                </p>
                <p> 1. 
                        <?php esc_html_e ('In Dashboard> Divi> Theme options > Builder > Advanced > Static CSS File Generation > Clear' , 'bbp-style-pack') ; ?>
                </p>
                <p> 2. 
                        <?php esc_html_e ('In Dashboard> Divi> > Theme Options > Performance> Dynamic CSS option> set to Disabled' , 'bbp-style-pack') ; ?>
                </p>
                <p> 3. 
                        <?php esc_html_e ('THEN critically you need to clear any server side caching AND clear your browser cache.' , 'bbp-style-pack') ; ?>
                </p>
                <p> 
                        <?php esc_html_e ('Server side will depend on your host, and/or any caching plugins you have installed' , 'bbp-style-pack') ; ?>
                </p>
                <p> 
                        <?php esc_html_e ('For your browser - see this helpful article <a href="https://kinsta.com/knowledgebase/how-to-clear-browser-cache/" target="_blank">How to clear browser cache</a>' , 'bbp-style-pack') ; ?>
                </p>


        <?php
        }

/////////////////////  Kadence Theme
        
        if ($bsp_theme_check == 'kadence') {
                global $bsp_style_settings_theme_support ;
                        $theme = wp_get_theme() ;
                ?>

                <p><b>
                        <?php esc_html_e ('You are using the ' , 'bbp-style-pack') ;
                        echo $theme ;
                        esc_html_e (' theme (or a child theme of this theme)' , 'bbp-style-pack' ) ; ?>
                </b></p>
                <p>
                        <?php esc_html_e ('This theme has extensive modifications for bbpress, and not all the features of the bbp style pack plugin may work with this theme.' , 'bbp-style-pack') ; ?>
                </p>
                <p> <?php esc_html_e ('Some bbpress styling can be done through the theme in dashboard>appearance>customise>bbpress' , 'bbp-style-pack') ; ?>
                </p>
                <p> <?php esc_html_e ('If you find that the styling features in this plugin do not work, then try enabling the fix below.' , 'bbp-style-pack') ; ?>
                </p>
                <p> <?php esc_html_e ('If you find other things not working, then please advise us via' , 'bbp-style-pack') ; ?>
                        <?php echo '<a href="https://wordpress.org/support/plugin/bbp-style-pack/" target="_blank">' ?>
                        <?php esc_html_e ('bbp Style Pack support' , 'bbp-style-pack') ; ?>
                        </a>
                </p>
                <p>
                        <?php esc_html_e ('We cannot guarantee to fix every issue with this theme, but we will take a look.' , 'bbp-style-pack') ; ?>
                </p>

                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings-theme-support', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_theme_support' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>

                                <table class="form-table">

                                <!-- ACTIVATE  -->	
                <!-- checkbox to activate  -->
                        <tr valign="top">  
                                 <th style="width: 350px">
                                        <?php esc_html_e('Enable Kadence theme styling support fix', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_theme_support['kadence'] ) ?  $bsp_style_settings_theme_support['kadence'] : '');
                                        echo '<input name="bsp_style_settings_theme_support[kadence]" id="bsp_style_settings_theme_support[kadence]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_settings[theme_support]">
                                                <?php esc_html_e( 'Enable Theme Support', 'bbp-style-pack' ); ?>
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
        } // end Kadence Theme
		
/////////////////////  Hello Elementor Theme
        
        if ($bsp_theme_check == 'hello-elementor') {
                global $bsp_style_settings_theme_support ;
                        $theme = wp_get_theme() ;
                ?>

                <p><b>
                        <?php esc_html_e ('You are using the ' , 'bbp-style-pack') ;
                        echo $theme ;
                        esc_html_e (' theme (or a child theme of this theme)' , 'bbp-style-pack' ) ; ?>
                </b></p>
                <p>
                        <?php esc_html_e ('This theme needs a specific bbpress template file.' , 'bbp-style-pack') ; ?>
                </p>
               
                <p> <?php esc_html_e ('If you find other things not working, then please advise us via' , 'bbp-style-pack') ; ?>
                        <?php echo '<a href="https://wordpress.org/support/plugin/bbp-style-pack/" target="_blank">' ?>
                        <?php esc_html_e ('bbp Style Pack support' , 'bbp-style-pack') ; ?>
                        </a>
                </p>
                <p>
                        <?php esc_html_e ('We cannot guarantee to fix every issue with this theme, but we will take a look.' , 'bbp-style-pack') ; ?>
                </p>

                <form method="post" action="options.php">
                <?php wp_nonce_field( 'style-settings-theme-support', 'style-settings-nonce' ) ?>
                <?php settings_fields( 'bsp_style_settings_theme_support' );
                //create a style.css on entry and on saving
                generate_style_css();
                bsp_clear_cache();
                ?>

                                <table class="form-table">

                                <!-- ACTIVATE  -->	
                <!-- checkbox to activate  -->
                        <tr valign="top">  
                                 <th style="width: 350px">
                                        <?php esc_html_e('Enable Hello Elementor theme styling support fix', 'bbp-style-pack'); ?>
                                </th>

                                <td>
                                        <?php 
                                        $item = (!empty( $bsp_style_settings_theme_support['hello_elementor'] ) ?  $bsp_style_settings_theme_support['hello_elementor'] : '');
                                        echo '<input name="bsp_style_settings_theme_support[hello_elementor]" id="bsp_style_settings_theme_support[hello_elementor]" type="checkbox" value="1" class="code" ' . checked( 1,$item, false ) . ' />' ;
                                        ?>
                                        <label class="description" for="bsp_settings[theme_support]">
                                                <?php esc_html_e( 'Enable Theme Support', 'bbp-style-pack' ); ?>
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
        } // end  Hello Elementor
        
} // end function bsp_style_settings_theme_support
