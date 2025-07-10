=== bbp style pack ===
Contributors: robin-w
Tags: forum, bbpress, bbp, style
Donate link: http://www.rewweb.co.uk/donate
Tested up to: 6.8
Stable tag: 6.3.9
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

For bbPress - Lets you style bbPress, and add display features


== Description  ==
This Plugin lets you style bbPress, and add display features

You can change the forum styling for elements, letting you match (or contrast!) bbPress to your theme

Many features are available at the click of a button, such as creating vertical lists, adding create new topic links, hiding counts and much more.

<ul>
<li>Style font sizes colors etc. in forums and topics</li>
<li>Change forum display layouts</li>
<li>Add or take away forum elements, such as adding descriptions or removing 'this forum contains..'</li>
<li>Change the forum order</li>
<li>Change the freshness display to date and time, or combination date and freshness</li>
<li>Change the breadcrumbs to alter or remove elements, or remove breadcrumbs completely</li>
<li>Add Create new Topic, Subscribe and Profile buttons, making navigation easier</li>
<li>Add login Register and profile to menus</li>
<li>Change forum role names or add role images</li>
<li>Amend subscription email headings and text</li>
<li>Amend the topic list order</li>
<li>Add topic previews to make topic navigation easier</li>
<li>Change how the topic and reply forms display - adding, removing or changing elements</li>
<li>Amend how profiles display and configure who sees them</li>
<li>Amend the search styling</li>
<li>Use additional shortcodes to improve how you display your forums and topics</li>
<li>Add an unread posts section so that users can easily see new topics and replies</li>
<li>Add a quote button to topics and replies</li>
<li>Add moderation tools to allow to to control </li>
<li>Add an unread posts section so that users can easily see new topics and replies</li>
<li>Use additional widgets to better display latest activity, or forum and topic information</li>
<li>Find a list of other useful bbPress related plugins</li>
<li>Let bbpress work with FSE themes
</ul>


== Installation ==
To install this plugin :

1. Go to Dashboard>plugins>add new
2. Search for 'bbp style pack'
3. Click install
4. and then activate
6. go into settings and set up as required.

<strong>Settings</strong>


== Screenshots ==
1. a sample settings page


== Changelog ==

= 6.3.9 =

*minor (but annoying!) bug fix !!

= 6.3.7/6.3.8 =

*a correction to forum lists widget division which was not closed correctly
*bbpress has a bug where the discussion disallowed comment keys set in dashboard>settings> discussion do not work - I've have added a fix for this which is active by default, which can be taken out in the style pack bug fixes tab

= 6.3.6 =

*a minor change to unread posts

= 6.3.5 =

*a fix for an error thrown in some cases if using private sub forums

= 6.3.4 =

*Fix for latest activity block widget not showing author

= 6.3.2/6.3.3 =

*Correction to how blocks are handled in admin to make them work correctly following WP 6.8 release

= 6.3.1 =

*Correction to forum information block to correctly show last activity 


= 6.3.0 =

*On display of the forum/topic/reply admin pages an automatic author filter was being added. This could slow display of these pages on sites with thousands of users, so I have made this an option in dashboard>settings>bbp-style-pack>Dashboard Admin 

= 6.2.9 =

*amended css enqueing so that blank css files are not downloaded

= 6.2.8 =

*further bug fix for 6.2.6

= 6.2.7 =

*bug fix for 6.2.6


= 6.2.6 =

*I've added the ability to list tags on the topics dashboard page - see dashboard>settings>bbp-style-pack>Dashboard Admin

= 6.2.5 =

*I've corrected an error for those wanting to use the bbpress profile in the WordPress Admin Bar.
*Technical change - I've changed some lookup functions to not use 'abspath' in bbp-style-pack.php

= 6.2.4 =
*Fix for settings topics fields error

= 6.2.3 =
*Fix for not displaying 'mark all topics as read' button if users have opted out
*Fix for incorrect shortcode for displaying unread topics


= 6.2.2 =
*minor fix for a bug in 6.2.1 !


= 6.2.1 =
*Some further technical changes which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes


= 6.2.0 =
*Some further technical changes which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.1.8/6.1.9 =
*Some further technical changes which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.1.7 =
*fix issue with reply titles in subscription emails
*fix deprecated cache clearing function for WT3C
*Some further technical changes which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.1.6 =
*fix issue for bock themes not displaying all templates in dashboard>themes>customise
*fix issue for shortcodes not working in block theme footers - I've added an additional option in theme set-up to allow this.

= 6.1.5 =
*correct issue for Astra theme

= 6.1.4 =
* Further improvements for issues with translations following WP 6.7 release.
* I've added an additional shortcode is you are using unread posts, which lets you have say a page showing all unread posts.
* Some further technical changes which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes



= 6.1.3 =
* Change to getting bbpress version in bbp-style-pack.php as old method is causing issues with translations following WP 6.7 release.
* fix to error in unread functions 'Attempt to read property “ID” on null'
* fix to forums and topic index icons in version 6.1.2



= 6.1.2 =
* The second of several technical releases which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.1.1 =
* The first of several technical releases which will help bring this plugin up to date with all the latest WordPress coding standards and PHP changes

= 6.0.9/6.1.0 =
* Fixed a bug if trim revisions was not numeric in bsp_trim_revision_log
* Fixed a css bug for topic/reply display font size

= 6.0.8 =
* I've added the ability to change the 'from name' on subscription emails. See Subscription Emails tab item 1.

= 6.0.7 =
* I've added the ability to customise the 'create new reply' button with forum or topic name. See 'Topic/Reply Display' tab item 25.

= 6.0.6 =
* Following release of bbPress 2.6.11 a modification is needed for those using the [bsp-moderation-pending] shortcode.

= 6.0.5 =
* I've fixed a small bug in the 6.0.4 realted to the new topic fields functionality.

= 6.0.4 =
* I've added the ability to control what additional fields are shown/required for anonymous posting on the topic/reply forms -  See the new 'Topic/Reply form' tab.
* I've fixed a small bug in the 6.0.3 release

= 6.0.3 =
* I've added a new tab, which lets you add additional fields to the topic form, for instance you can ask for 'Make of car' on a car restoration forum -  See the new 'Topic Additional Fields' tab.
* If you post a new topic using the form at the bottom of the topics list and do not complete the required fields, you can be sent to the top of the form without your topic being posted, and with no visible reason. I've added a bug fix which displays these errors at the top of the topics list.  You can exclude this fix in settings>bug fixes if you wish, but if you find issues with it, I'd like to know, so please also post a support thread.


= 6.0.2 =
* I've added a new tab, which lets you decide which columns to show on the forum and topics index pages, and do this differently for mobile if you wish.  See the new 'Column Display' tab.


= 6.0.1 =
* I've added the abilty to bulk move topics between forums in dashboard>topics.  Use the bulk edit feature and you can set the forum against multiple topics 

