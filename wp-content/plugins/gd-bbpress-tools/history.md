# GD bbPress Tools

## Changelog

### Version: 3.3 (2022.12.03)
* **new** plugin is tested with WordPress 6.1
* **edit** settings page with use of escaping for attributes
* **edit** some aspects for the PHP core code style
* **edit** some system requirements

### Version: 3.2.1 (2022.05.16)
* **new** plugin is tested with WordPress 6.0

### Version: 3.2 - 2021.10.05
* **edit** improvements to the plugin readme file
* **edit** few more minor updates
* **fix** issue with the hidden signature form

### Version: 3.1 - 2020.07.23
* **edit** improvements to the plugin readme file
* **edit** few more minor updates

### Version: 3.0.1 - 2020.05.13
* **edit** Improvements to the plugin readme file
* **fix** small issue with the new private prefix tweak
* **fix** issue with the wrong include path

### Version: 3.0 - 2020.05.09
* **new** Tweak to remove forum and topic Private prefix
* **new** Tweak to load search form on top of all forums
* **new** Tweak to load search form on top of all topics
* **new** reorganized plugin code and the way it is loaded and run
* **new** rewritten loading of JavaScript and CSS files
* **new** fully reorganized CSS now written using SCSS
* **new** loading JavaScript and CSS minified or normal
* **del** WordPress 4.4 shortcodes update
* **del** some outdated and unused code
* **fix** issues when saving some plugin settings

### Version: 2.2 - 2019.09.02
* **del** all outdated translations
* **fix** a minor sanitation issues related to shortcodes

### Version: 2.1 - 2019.03.10
* **new** option to change allowed HTML tags and attributes for topics and replies
* **edit** readme.txt file and included FAQ list

### Version: 2.0.2 - 2018.08.22
* **edit** changed default BBCode option to load on bbPress pages only to enabled
* **edit** BBCodes information for potential issue with BuddyPress
* **edit** readme.txt file and included FAQ list
* **edit** minor core code changes and improvements

### Version: 2.0.1 - 2018.07.27
* **edit** readme file with new FAQ entries
* **edit** plugin admin code to remove FAQ link

### Version: 2.0 - 2018.07.25
* **new** interface for the plugin settings panel
* **new** panel with settings for Tweaks
* **new** tweak: show lead topic
* **new** tweak: disable breadcrumbs
* **new** tweak: topic tags in reply for topic author only
* **new** topics view: Topics Freshness
* **edit** toolbar icon to use bbPress dashicon
* **edit** WordPress minimal requirement to 4.4
* **edit** PHP minimal requirement to 5.5

### Version: 1.9.3 - 2018.02.16
* **edit** function for universal rendering of BBCodes
* **edit** various URL's included in plugin and readme file

### Version: 1.9.2 - 2017.10.26
* **new** function to access BBCodes handler functions
* **edit** trim quote content before adding to editor
* **edit** several BBCodes handler functions
* **fix** topic related issue with the quote BBCode
* **fix** minor issue with the loading of Views module

### Version: 1.9.1 - 2017.09.20
* **edit** WordPress minimal requirement to 4.2
* **edit** several broken URL's
* **edit** and improved readme file

### Version: 1.9 - 2016.09.24
* **edit** sanitation of the plugin settings on save
* **edit** PHP minimal requirement to 5.3
* **edit** WordPress minimal requirement to 4.0
* **edit** several broken URL's
* **edit** several missing translation strings
* **edit** signature editors display filters

### Version: 1.8 - 2015.12.10
* **new** auto update signature for shorthand BBCodes
* **new** update tool for WordPress 4.4 shortcodes changes
* **new** few missing translation strings
* **edit** list of BBCodes to remove shorthand notation
* **edit** loading of text domain for centralized translations loading
* **fix** adding quote BBCode using shorthand notation
* **fix** list of BBCodes in some cases missing quotes

### Version: 1.7.1
* **edit** several Dev4Press links
* **fix** XSS security vulnerability with unsanitized input

### Version: 1.7
* **new** option to enable DIV tag in the content
* **new** check if user can set unfiltered HTML for signatures
* **new** option to allow mixing HTML and BBCode in signatures
* **edit** Improved signature editing process loading and display
* **fix** display of HTML signatures to non logged users
* **fix** editing signatures on admin profile page breaks HTML
* **fix** warning when saving signature in some cases
* **fix** BuddyPress profile edit shows wrong signature
* **fix** quote problem caused by filtered DIV tags
* **fix** order of the quote content wrapper filters

### Version: 1.6
* **new** smilies parsing for user signature
* **del** support for bbPress 2.2.x
* **fix** some quote issues with BR tags
* **fix** quote not working with WordPress 3.9

### Version: 1.5.1
* **fix** signatures not working with bbPress 2.4
* **fix** quote not working with bbPress 2.4

### Version: 1.5
* **new** options to disable any of the plugins bbcodes
* **edit** Improved bbcodes: youtube code supports full url
* **edit** Improved bbcodes: vimeo code supports full url
* **del** support for bbPress 2.1.x
* **fix** option for showing and hiding bbCode notice
* **fix** bbCode youtube and vimeo don't work with SSL active

### Version: 1.4
* **edit** Select profile group in BuddyPress for signature editor
* **edit** Changed loading order for bbPress 2.3 compatibility
* **edit** Admin side uses proper enqueue method to load style
* **del** Dropped support for bbPress 2.0
* **del** Dropped support for WordPress 3.2
* **fix** quote not setting proper ID for lead topic display
* **fix** testing for roles allowed for all available tools
* **fix** missing enhanced info when editing signatures
* **fix** missing table cell ending for admin side signature editor

### Version: 1.3.1
* **fix** signature visible to logged in users only
* **fix** detection of bbPress 2.2

### Version: 1.3
* **new**  support for dynamic roles from bbPress 2.2
* **new**  signature edit to BuddyPress profile editor
* **edit** using enqueue scripts and styles to load files on frontend
* **edit** various styling improvements to embedded forms and elements
* **edit** admin menu now uses 'activate_plugins' capability by default
* **del** screenshots removed from plugin and added into assets directory
* **fix** duplicated signature form on profile edit page
* **fix** signature fails to find topic/reply author
* **fix** signature not displayed when using lead topic
* **fix** quote not working when using lead topic
* **fix** quote in some cases quote link is missing
* **fix** bbcodes not applied when displaying lead topic

### Version: 1.2.9
* **fix** another important quote problem with JavaScript

### Version: 1.2.8
* **fix** quote not working with HTML editor with fancy editor
* **fix** scroll in JavaScript for quote with IE7 and IE8
* **fix** JavaScript use of trim function with IE7 and IE8
* **fix** problem with quote that breaks the oEmbed

### Version: 1.2.7
* **new** BuddyPress with site wide bbPress supported
* **new** support for signature editing with admin side profile editor
* **new** expanded list of FAQ entries
* **new** panel for upgrade to GD bbPress Toolbox
* **new** few missing translation strings
* **new** German Translation
* **edit** change to generating some links in toolbar menu
* **fix** quote element that can include attachments also
* **fix** quote option displayed when not allowed

### Version: 1.2.6
* **fix** toolbar menu when there are no forums to show

### Version: 1.2.5
* **new** Added Serbian translation
* **edit** check if bbPress is activated before loading code

### Version: 1.2.4
* **fix** toolbar integration bug causing posts edit problems

### Version: 1.2.3
* **edit** Improvements to shared functions loading
* **edit** Improvements to loading of plugin modules

### Version: 1.2.2
* **fix** search topics view for bbPress 2.0.2

### Version: 1.2.1
* **edit** readme.txt with more information
* **fix** broken links in the context help
* **fix** invalid display of user signatures

### Version: 1.2.0
* **new** user signature with BBCode and HTML support
* **new** use of capabilities for all plugin features
* **new** support for setting up additional custom views
* **new** BBCodes: Vimeo, Image, Font Size, Font Color
* **new** basic support for topics search results view
* **new** allows use of the WordPress rich editor for quoting
* **new** allows to quote only selected portion of the text
* **edit** when you click quote button, page will scroll to the form
* **edit** Improvements for the bbPress 2.1 compatibility

### Version: 1.1.0
* **new** BBCodes shortcodes support
* **new** quote reply or topic support
* **new** file with shared functions
* **new** plugin features organized into mods

### Version: 1.0.0
* First official release