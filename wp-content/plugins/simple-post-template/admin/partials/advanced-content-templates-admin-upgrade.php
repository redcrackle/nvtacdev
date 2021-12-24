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

$post_type_objects = $this->plugin->get_post_types();
$templates = $this->get_templates();
$act_post_type_settings = $this->plugin->get_setting('act_post_type_settings');
?>
<div class="wrap">
	<?php global $wp_tabbed_navigation; ?>
	<?php $wp_tabbed_navigation->display_tabs(); ?>

	<h2>You're missing out!</h2>
	<p>Advanced Content Templates has tons of bonus features, and it's growing all the time. Join over 300 other pro users and receive:</p>
	<ol>
		<li>Featured Images</li>
		<li>Categories, Tags, and Custom Taxonomies</li>
		<li>Custom Fields</li>
		<li>Custom Post Type Support</li>
		<li>And much more!</li>
	</ol>

	<a class="button-primary" target="_blank" href="https://www.advancedcontenttemplates.com?utm_medium=UpgradeTab&utm_content=<?php echo urlencode("Get Advanced Content Templates Now!"); ?>">Get Advanced Content Templates Now!</a>
</div>
