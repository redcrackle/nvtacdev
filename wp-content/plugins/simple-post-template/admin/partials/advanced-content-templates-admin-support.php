<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://objectiv.co
 * @since      1.0.0
 *
 * @package    Advanced_Content_Templates
 * @subpackage Advanced_Content_Templates/admin/partials
 */

$post_type_objects      = $this->plugin->get_post_types();
$templates              = $this->get_templates();
$act_post_type_settings = $this->plugin->get_setting( 'act_post_type_settings' );
?>
<div class="wrap">
	<?php global $wp_tabbed_navigation; ?>
	<?php $wp_tabbed_navigation->display_tabs(); ?>

	<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
	<script type="text/javascript">window.Beacon('init', '29bc4b4f-f4de-4783-a22d-872b46f7e379')</script>

	<h3><?php esc_html_e( 'Need help?', 'simple-content-templates' ); ?></h3>
	<p> <?php esc_html_e( 'The free version of this plugin doesn\'t include support. If you need support or additional features/functionality (Custom Post Types, Custom Fields, Tags, Categories, Featured Images) please consider updating to the pro version of the plugin.', 'simple-content-templates' ); ?></p>
	<a class="button-primary" target="_blank" href="https://www.advancedcontenttemplates.com?utm_medium=UpgradeTab&utm_content=<?php echo urlencode( 'Get Advanced Content Templates Now!' ); ?>">Get Advanced Content Templates Now!</a>
</div>
