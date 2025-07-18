<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_help() {
?>
			
        <table class="form-table">
			<tbody>		
                <tr valign="top">
                        <th colspan="2">		
                                <h3>
                                        <?php esc_html_e ('Help' , 'bbp-style-pack' ) ; ?>
                                </h3>
                        </th>
                </tr>
                <tr>
					<td>				
                        <h4>
                                <span style="color:blue"><?php esc_html_e('Colors', 'bbp-style-pack' ) ; ?></span>
                        </h4>
                        <p>
                        <?php
                        //show style image
                                 echo '<img src="' . esc_url(plugins_url( 'images/color.JPG',dirname(__FILE__)  )) . '" > '; 
                        ?>
                        </p>
                        <p>
                                <?php esc_html_e("Colors are entered by clicking the color you want, or adding a 'hex number' that defines the color eg '#ff0000'", 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e("Quite often you will want to use a color in your theme, but how do you know what color that is ? There are several products that will tell you what color any part of your screen is.   ", 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('I use Color Cop see <a href = "http://colorcop.net/screenshots/" target="_blank"> http://colorcop.net/screenshots/</a>', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('See', 'bbp-style-pack'); ?>
                                <a href = "http://www.w3schools.com/html/html_colornames.asp" target="_blank"> www.w3schools.com/html/html_colornames.asp</a>
                                <?php esc_html_e('for further info on colors', 'bbp-style-pack'); ?>
                        </p>
                        <h4>
                                <span style="color:blue"><?php esc_html_e('Font', 'bbp-style-pack' ) ; ?></span>
                        </h4>
                        <p>
                                <?php esc_html_e("Fonts can be single  eg 'Arial' or or a font family such as 'arial, Times, serif'", 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('Where a font has more than one word such as Times New Roman, the it must be entered with double quotes eg "Times New Roman"', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('See', 'bbp-style-pack'); ?>
                                <a href = "http://www.w3schools.com/css/css_font.asp" target="_blank"> www.w3schools.com/css/css_font.asp</a>
                                <?php esc_html_e('for further info', 'bbp-style-pack'); ?>
                        </p>
                        <h4>
                                <span style="color:blue"><?php esc_html_e('Font Size', 'bbp-style-pack' ) ; ?></span>
                        </h4>
                        <p>
                                <?php esc_html_e("Fonts can be specified as absolute-size eg 'medium', relative size eg 'larger', length eg '12px' or '0.8em' or as a percentage eg 80%", 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('A good explanation  is contained in', 'bbp-style-pack'); ?>
                                <a href = "https://developer.mozilla.org/en-US/docs/Web/CSS/font-size" target="_blank"> https://developer.mozilla.org/en-US/docs/Web/CSS/font-size</a>
                        </p>
                        <p> 
                                <?php esc_html_e("bbpress uses px eg '12px', but the choice is yours !", 'bbp-style-pack'); ?>
                        </p>
                        <h4>
                                <span style="color:blue"><?php esc_html_e('Border style', 'bbp-style-pack' ) ; ?></span>
                        </h4>
                        <p>
                                <?php esc_html_e('Borders can be specified as solid, dashed, dotted, groove, ridge, inset & outset', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('Common values are solid, dashed, dotted', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('Values groove, ridge, inset & outset need a border wider than 1px (5px is a good start point) for the effect to work', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('See', 'bbp-style-pack'); ?>
                                <a href = "http://www.w3schools.com/css/css_border.asp" target="_blank"> www.w3schools.com/css/css_border.asp</a> 
                                <?php esc_html_e('for further info', 'bbp-style-pack'); ?>
                        </p>
                        <h4>
                                <span style="color:blue"><?php esc_html_e('If you need further help', 'bbp-style-pack' ) ; ?></span>
                        </h4>
                        <p>
                                <?php esc_html_e('Please log a topic at', 'bbp-style-pack'); ?>
								<a href = "https://wordpress.org/support/plugin/bbp-style-pack" target="blank">
								<?php esc_html_e('plugin support', 'bbp-style-pack'); ?>
								</a>
								<?php esc_html_e('- you will need to create an account and scroll to the bottom of the page to find the new topic form', 'bbp-style-pack'); ?>
                         </p>
				</td>
                </tr>
				</tbody>
        </table>
<?php
}
