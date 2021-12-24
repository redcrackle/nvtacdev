<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

		</div><!-- .site-content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Primary Menu', 'twentysixteen' ); ?>">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'menu_class'     => 'primary-menu',
							)
						);
					?>
				</nav><!-- .main-navigation -->
			<?php endif; ?>

			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentysixteen' ); ?>">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'social',
								'menu_class'     => 'social-links-menu',
								'depth'          => 1,
								'link_before'    => '<span class="screen-reader-text">',
								'link_after'     => '</span>',
							)
						);
					?>
				</nav><!-- .social-navigation -->
			<?php endif; ?>

<!--			<div class="site-info">
				<?php
					/**
					 * Fires before the twentysixteen footer text for footer customization.
					 *
					 * @since Twenty Sixteen 1.0
					 */
					do_action( 'twentysixteen_credits' );
				?>
				<span class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
				<?php
				if ( function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
				}
				?>
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentysixteen' ) ); ?>" class="imprint">
					<?php printf( __( 'Proudly powered by %s', 'twentysixteen' ), 'WordPress' ); ?>
				</a>
			</div>-->
<div class="nvtacfoot">
<p class="fthead">© National Veterans' Technical Assistance Center - All rights reserved </p>

<p class="fttxt">This workforce product was funded by a contract with the U.S. Department of Labor’s Veterans’ Employment and Training Service (DOL-VETS) and copyrighted by the National Veterans’ Technical Assistance Center (NVTAC). It 
does not necessarily reflect the official position of the U.S. Department of Labor. The U.S. Department of Labor makes no guarantees, warranties, or assurances of any kind, express or implied, with respect to such information, 
including any information on linked sites and including, but not limited to, accuracy of the information or its completeness, timeliness, usefulness, adequacy, continued availability, or ownership. This product is copyrighted 
by the institution that created it. Internal use by an organization and/or personal use by an individual for non-commercial purposes is permissible. All other uses require the prior authorization of the copyright owner. </p>
</div>
<div class="ftthumbs">
<a href="https://www.dol.gov" target="blank"><img class="ftlogo" src="https://nvtac.org/wp-content/uploads/2019/08/NVTAC_footer-logos__DoL.png" alt="DOL Logo" title="DOL Logo" class=""></a>
<a href="http://dol.gov/vets" target="blank"><img class="ftlogo" src="https://nvtac.org/wp-content/uploads/2019/08/NVTAC_footer-logos__DoL-Vets.png" alt="DOL Vets Logo" title="DOL Vets Logo"></a> 
<a href="http://www.nchv.org" target="blank"><img class="ftlogo" src="https://nvtac.org/wp-content/uploads/2019/08/NVTAC_footer-logos__NCHV.png" draggable="false" alt="NCHV Logo"  title="NCHV Logo" class=""></a> 
<a href="https://www.atlasresearch.us/" target="blank"><img class="ftlogo" src="https://nvtac.org/wp-content/uploads/2019/08/NVTAC_footer-logos_Atlas.png" draggable="false" alt="Atlas Logo" title="Atlas Logo"></a> 
<a href="http://www.manhattanstrategy.com" target="blank"><img class="ftlogo" src="https://nvtac.org/wp-content/uploads/2019/08/NVTAC_footer-logos__MSG.png" draggable="false" alt="Manhattan Strategy Group Logo" title="Manhattan Strategy Group Logo"></a>	
</div>



<!-- .site-info -->
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
