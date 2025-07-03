=== Flagged Content ===
Contributors: divspark
Tags: flag, flagging, report, moderation, problems, issues, spam
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows visitors to flag posts and pages.


== Description ==

Allow visitors to report posts and pages if there is an issue or to provide feedback. A flagging button is automatically injected within the post and page content. Clicking to flag a post or page will present the visitor with a popup modal (lightbox) form. You can determine what information the user must provide in order to submit the flag.

= Form must be completed to submit flag =
Require a form must be completed before the visitor can flag a post or page. Customize the buttons, messages and fields within the form to match your site. Visitors will use this form to flag posts and pages.

= Features =
* Display fields within the form (or do not display them) and make them optional or required.
* Customize a reason users must select in order to submit a flag.
* All flags are organized within the "flags page".
* The post or page flagged is displayed next to the flag and can be easily viewed or edited.
* Enable emails to be sent when a post or page has been flagged.

= Pro Version =
The pro version of the plugin offers many additional features.
* A wide variety of content can be flagged including WordPress comments, custom post types, bbPress topics and replies, WooCommerce products and more.
* Different forms can be used for different types of content, e.g. require a description to flag comments, but make a description optional to flag products.
* Different buttons and labels can be used for different content.
* And more

Please [visit the pro plugin page](https://codecanyon.net/item/flagged-content-let-visitors-report-and-flag-posts-comments-and-more-to-admin-wordpress-plugin/19748662) to upgrade or see the benefits for upgrading.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. The plugin settings can be accessed through the admin sidebar. Use the `Flagged->Settings` link to configure the plugin.


== Frequently Asked Questions ==

= What post types are supported? =

Post and pages are supported. One, or both, can be set to be flagged in the settings page.

= How do I change the location of the flag button? =

The flag button will appear before, or after, the content depending on the placement setting. Alignment (left, middle and right) can also be edited within the settings page. To alter the location within the post/page beyond this will require you to write custom code (jQuery/css) to move the button.

= What happens to flags when a post is deleted/removed? =

Flags for a post and page are automatically removed when that post or page is permanently deleted. If the post or page is just in the trash then the flag will still be available.


== Screenshots ==

1. Button automatically added above the post content
2. Another theme with the button automatically added below the post content
3. Clicking the button reveals a popup form which the visitor completes to submit a flag
4. All flags are organized within a custom screen
5. Numerous settings for style and behavior


== Changelog ==

= 1.0.2 =
* Added option to enable or disable capturing the ip address
* Styling fix on the form, added a class to the form instructions
* Other code changes

= 1.0.1 =
* Bug fixes

= 1.0.0 =
* Release
