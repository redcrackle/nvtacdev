<?php
if ( ! defined( 'WPINC' ) ) { die; }

/**
 *      Prepares the settings page (seofferedfields and register)
 * 		Renders the settings page
 */
class Flagged_Content_Admin_Settings_Page
{
	private $plugin;
	private $settings;
    private $option;
    private $option_group = 'flagcontent_forms_group';
    private $option_name  = 'flaggedcontent_settings';
    private $page_slug    = 'flagged_content_settings_page';

	public function __construct( $plugin )
    {
		$this->plugin = $plugin;
		$this->settings = $this->plugin->get_settings();
        $this->option = get_option( 'flaggedcontent_settings' );

        add_action( 'admin_init', array( $this, 'admin_settings_init' ) );
	}

    /**
     * Callback function - Prepares the form and fields for the page through the wordpress settings api
     *
     * @action admin_init
     */
    public function admin_settings_init()
    {
        // add_settings_field( $id, $title, $callback, $page, $section, $args);
        add_settings_field( 'reveal_label',         'Text on button',                  array( $this, 'field_reveal_label' ),         $this->page_slug, 'reveal_section' );
        add_settings_field( 'reveal_success_label', 'Success text',                    array( $this, 'field_reveal_success_label' ), $this->page_slug, 'reveal_section' );
        add_settings_field( 'reveal_style',         'Flag button style',               array( $this, 'field_reveal_style' ),         $this->page_slug, 'reveal_section' );
        add_settings_field( 'reveal_location',      'Placement',                       array( $this, 'field_reveal_location' ),      $this->page_slug, 'reveal_section' );
        add_settings_field( 'reveal_align',         'Alignment',                       array( $this, 'field_reveal_align' ),         $this->page_slug, 'reveal_section' );
        add_settings_field( 'content',              'Content',                         array( $this, 'field_content' ),              $this->page_slug, 'reveal_section' );
        add_settings_field( 'name',                 '<em>Name</em> field',             array( $this, 'field_name' ),                 $this->page_slug, 'form_section' );
        add_settings_field( 'email',                '<em>E-mail</em> field',           array( $this, 'field_email' ),                $this->page_slug, 'form_section' );
        add_settings_field( 'reason',               '<em>Reason</em> field',           array( $this, 'field_reason' ),               $this->page_slug, 'form_section' );
        add_settings_field( 'reason_choose',        '',	                               array( $this, 'field_reason_choose' ),        $this->page_slug, 'form_section' );
        add_settings_field( 'description',          '<em>Description</em> field',      array( $this, 'field_description' ),          $this->page_slug, 'form_section' );
        add_settings_field( 'submit_label',         'Text on button',                  array( $this, 'field_submit_label' ),         $this->page_slug, 'form_section' );
        add_settings_field( 'submit_style',         'Submit button style',             array( $this, 'field_submit_style' ),         $this->page_slug, 'form_section' );
        add_settings_field( 'message_instructions', 'Instructions',                    array( $this, 'field_message_instructions' ), $this->page_slug, 'form_section' );
        add_settings_field( 'message_success',      'Success',                         array( $this, 'field_message_success' ),      $this->page_slug, 'form_section' );
        add_settings_field( 'email_enabled',        'Enable email notifications?',     array( $this, 'field_email_enabled' ),        $this->page_slug, 'email_section' );
        add_settings_field( 'permission_flag_view', 'View flags',                      array( $this, 'field_permission_flag_view' ), $this->page_slug, 'security_section' );
        add_settings_field( 'permission_flag_edit', 'Edit flags',                      array( $this, 'field_permission_flag_edit' ), $this->page_slug, 'security_section' );
        add_settings_field( 'honeypot',             'Honeypot',                        array( $this, 'field_honeypot' ),             $this->page_slug, 'security_section' );
        add_settings_field( 'save_ip_address',      'Save flag submitters IP address', array( $this, 'field_save_ip_address' ),      $this->page_slug, 'security_section' );

		// add_settings_section( $id, $title, $callback, $page );
        add_settings_section( 'reveal_section',   '', array( $this, 'reveal_section_display' ),   $this->page_slug );
        add_settings_section( 'form_section',     '', array( $this, 'form_section_display' ),     $this->page_slug );
        add_settings_section( 'email_section',    '', array( $this, 'email_section_display' ),    $this->page_slug );
		add_settings_section( 'security_section', '', array( $this, 'security_section_display' ), $this->page_slug );
		add_settings_section( 'pro_section', 	  '', array( $this, 'pro_section_display' ), 	  $this->page_slug );

		//register_setting( $option_group, $option_name, $callback );
		register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_values' ) );
	}


    /**
     * Render fields: Callback functions to render the fields.
     */
    public function field_reveal_label()
    {
        echo "<input type='text' name='{$this->option_name}[reveal_label]' value='{$this->option['reveal_label']}' />";
    }


    public function field_reveal_success_label()
    {
        echo "<input type='text' name='{$this->option_name}[reveal_success_label]' value='{$this->option['reveal_success_label']}' />";
        echo "<p class='description'>Text shown on the button after the form has been successfully submitted</p>";
    }


    public function field_reveal_style()
    {
        echo "<select name='{$this->option_name}[reveal_style]'>";
        echo "<option value='theme;theme' "  . selected( $this->option['reveal_style'], 'theme;theme', false )  . ">Default theme style and default color</option>";
        echo "<option value='theme;black' "  . selected( $this->option['reveal_style'], 'theme;black', false )  . ">Default theme style - Black</option>";
        echo "<option value='theme;gray' "   . selected( $this->option['reveal_style'], 'theme;gray', false )   . ">Default theme style - Gray</option>";
        echo "<option value='theme;green' "  . selected( $this->option['reveal_style'], 'theme;green', false )  . ">Default theme style - Green</option>";
        echo "<option value='theme;red' "    . selected( $this->option['reveal_style'], 'theme;red', false )    . ">Default theme style - Red</option>";
        echo "</select>";

        echo $this->add_sub_heading( 'Location' );
    }


    public function field_reveal_location()
    {
        echo "<select name='{$this->option_name}[reveal_location]'>";
        echo "<option value='content_before' " . selected( $this->option['reveal_location'], 'content_before', false ) . ">Before the content</option>";
        echo "<option value='content_after' "  . selected( $this->option['reveal_location'], 'content_after', false )  . ">After the content</option>";
        echo "</select>";

        echo "<p class='description'>Choose if the button is displayed before or after the content</p>";
    }


    public function field_reveal_align()
    {
        echo "<select name='{$this->option_name}[reveal_align]'>";
        echo "<option value='left' "   . selected( $this->option['reveal_align'], 'left', false )   . ">Left</option>";
        echo "<option value='center' " . selected( $this->option['reveal_align'], 'center', false ) . ">Center</option>";
        echo "<option value='right' "  . selected( $this->option['reveal_align'], 'right', false )  . ">Right</option>";
        echo "</select>";

        echo "<p class='description'>How the flag button should be aligned within the container</p>";
    }


    public function field_content()
    {
        echo "<input type='radio' id='flagcontent_radio_content_post' name='{$this->option_name}[content]' value='post' " . checked( $this->option['content'], 'post', false ) . " />";
        echo "<label for='flagcontent_radio_content_post'>Posts</label><br>";

        echo "<input type='radio' id='flagcontent_radio_content_page' name='{$this->option_name}[content]' value='page' " . checked( $this->option['content'], 'page', false ) . " />";
        echo "<label for='flagcontent_radio_content_page'>Pages</label><br>";

        echo "<input type='radio' id='flagcontent_radio_content_post_and_page' name='{$this->option_name}[content]' value='post_and_page' " . checked( $this->option['content'], 'post_and_page', false ) . " />";
        echo "<label for='flagcontent_radio_content_post_and_page'>Posts and pages</label>";

        echo "<p class='description'>Where the flag button should appear. The content that can be flagged.</p>";
    }


    public function field_name()
    {
        echo "<input type='radio' id='flagcontent_radio_name_optional' name='{$this->option_name}[name]' value='optional' " . checked( $this->option['name'], 'optional', false ) . " />";
        echo "<label for='flagcontent_radio_name_optional'>Optional - User does not need to enter a name</label><br>";

        echo "<input type='radio' id='flagcontent_radio_name_required' name='{$this->option_name}[name]' value='required' " . checked( $this->option['name'], 'required', false ) . " />";
        echo "<label for='flagcontent_radio_name_required'>Required - User must enter a name to submit the form</label><br>";

        echo "<input type='radio' id='flagcontent_radio_name_no_display' name='{$this->option_name}[name]' value='no_display' " . checked( $this->option['name'], 'no_display', false ) . " />";
        echo "<label for='flagcontent_radio_name_no_display'>Do not display the name field</label>";
    }


    public function field_email()
    {
        echo "<input type='radio' id='flagcontent_radio_email_optional' name='{$this->option_name}[email]' value='optional' " . checked( $this->option['email'], 'optional', false ) . " />";
        echo "<label for='flagcontent_radio_email_optional'>Optional - User does not need to enter an e-mail</label><br>";

        echo "<input type='radio' id='flagcontent_radio_email_required' name='{$this->option_name}[email]' value='required' " . checked( $this->option['email'], 'required', false ) . " />";
        echo "<label for='flagcontent_radio_email_required'>Required - User must enter a valid e-mail</label><br>";

        echo "<input type='radio' id='flagcontent_radio_email_no_display' name='{$this->option_name}[email]' value='no_display' " . checked( $this->option['email'], 'no_display', false ) . " />";
        echo "<label for='flagcontent_radio_email_no_display'>Do not display the e-mail field</label>";
    }


    public function field_reason()
    {
        echo "<input type='radio' id='flagcontent_radio_reason_optional' name='{$this->option_name}[reason]' value='optional' " . checked( $this->option['reason'], 'optional', false ) . " />";
        echo "<label for='flagcontent_radio_reason_optional'>Optional - User does not need to select a reason</label><br>";

        echo "<input type='radio' id='flagcontent_radio_reason_required' name='{$this->option_name}[reason]' value='required' " . checked( $this->option['reason'], 'required', false ) . " />";
        echo "<label for='flagcontent_radio_reason_required'>Required - User must select a reason to submit the form</label><br>";

        echo "<input type='radio' id='flagcontent_radio_reason_no_display' name='{$this->option_name}[reason]' value='no_display' " . checked( $this->option['reason'], 'no_display', false ) . " />";
        echo "<label for='flagcontent_radio_reason_no_display'>Do not display the reason field</label>";
    }


    public function field_reason_choose()
    {
        echo "<p>Add reasons for users to choose from, 1 per line</p>";
        echo "<textarea name='{$this->option_name}[reason_choose]' />{$this->option['reason_choose']}</textarea>";
        echo "<p class='description'>The reason field will not be shown if this is left empty. There will be no reasons for the user to select from</p>";
    }


    public function field_description()
    {
        echo "<input type='radio' id='flagcontent_radio_description_optional' name='{$this->option_name}[description]' value='optional' " . checked( $this->option['description'], 'optional', false ) . " />";
        echo "<label for='flagcontent_radio_description_optional'>Optional - User does not need to add a description</label><br>";

        echo "<input type='radio' id='flagcontent_radio_description_required' name='{$this->option_name}[description]' value='required' " . checked( $this->option['description'], 'required', false ) . " />";
        echo "<label for='flagcontent_radio_description_required'>Required - User must add a description to submit the form</label><br>";

        echo "<input type='radio' id='flagcontent_radio_description_no_display' name='{$this->option_name}[description]' value='no_display' " . checked( $this->option['description'], 'no_display', false ) . " />";
        echo "<label for='flagcontent_radio_description_no_display'>Do not display the description field</label>";

        echo $this->add_sub_heading( 'Submit Button' );
    }


    public function field_submit_label()
    {
        echo "<input type='text' name='{$this->option_name}[submit_label]' value='{$this->option['submit_label']}' />";
    }


    public function field_submit_style()
    {
        echo "<select name='{$this->option_name}[submit_style]'>";
        echo "<option value='theme;theme' "  . selected( $this->option['submit_style'], 'theme;theme', false ) . ">Default theme style and default color</option>";
        echo "<option value='theme;black' "  . selected( $this->option['submit_style'], 'theme;black', false )  . ">Default theme style - Black</option>";
        echo "<option value='theme;gray' "   . selected( $this->option['submit_style'], 'theme;gray', false )   . ">Default theme style - Gray</option>";
        echo "<option value='theme;green' "  . selected( $this->option['submit_style'], 'theme;green', false )  . ">Default theme style - Green</option>";
        echo "<option value='theme;red' "    . selected( $this->option['submit_style'], 'theme;red', false )    . ">Default theme style - Red</option>";
        echo "</select>";

        echo $this->add_sub_heading( 'Form Messages' );
    }


    public function field_message_instructions()
    {
        echo "<input type='text' name='{$this->option_name}[message_instructions]' value='{$this->option['message_instructions']}' />";
        echo "<p class='description'>Add wording to the top of the form</p>";
    }


    public function field_message_success()
    {
        echo "<input type='text' name='{$this->option_name}[message_success]' value='{$this->option['message_success']}' />";
        echo "<p class='description'>Shown after user successfully flags an item</p>";
    }


    public function field_email_enabled()
    {
        echo "<input type='checkbox' id='{$this->option_name}[email_enabled]' name='{$this->option_name}[email_enabled]' value='1' " . checked( $this->option['email_enabled'], 1, false ) . " />";
        echo "<label for='{$this->option_name}[email_enabled]'>Send email notifications</label>";
        echo $this->add_sub_heading( 'Email Preview' );

        echo '<br /><p><strong>Subject</strong><br>' . get_bloginfo( 'name' ) . ' Content flagged</p><br />';
        echo '<p><strong>Message</strong><br> 
                A user has flagged content on ' . get_bloginfo( 'name' ) . '.<br/>
                Post title: [post_title]<br/>
                Post link: [post_link]<br/>
                Flag reason: [flag_reason]<br/>
                Flag description: [flag_description]</p><br />';

        echo '<p class="description">Preview of the email that will be sent to the blog administrator (' . get_option( "admin_email" ) . ') when a post or page is flagged.</p>';
    }


    public function field_permission_flag_view()
    {
        echo "<select name='{$this->option_name}[permission_flag_view]'>";
        echo "<option value='manage_options' "       . selected( $this->option['permission_flag_view'], 'manage_options', false )    . ">Administrator</option>";
        echo "<option value='edit_others_posts' "    . selected( $this->option['permission_flag_view'], 'edit_others_posts', false ) . ">Editor</option>";
        echo "<option value='publish_posts' "        . selected( $this->option['permission_flag_view'], 'publish_posts', false )     . ">Author</option>";
        echo "<option value='edit_posts' "           . selected( $this->option['permission_flag_view'], 'edit_posts', false )        . ">Contributor</option>";
        echo "<option value='read' "                 . selected( $this->option['permission_flag_view'], 'read', false )              . ">Subscriber</option>";
        echo "</select>";
        echo "<p class='description'>Minimum role needed to view the list of submitted flags within the admin section.</p>";
    }


    public function field_permission_flag_edit()
    {
        echo "<select name='{$this->option_name}[permission_flag_edit]'>";
        echo "<option value='manage_options' "       . selected( $this->option['permission_flag_edit'], 'manage_options', false )    . ">Administrator</option>";
        echo "<option value='edit_others_posts' "    . selected( $this->option['permission_flag_edit'], 'edit_others_posts', false ) . ">Editor</option>";
        echo "<option value='publish_posts' "        . selected( $this->option['permission_flag_edit'], 'publish_posts', false )     . ">Author</option>";
        echo "<option value='edit_posts' "           . selected( $this->option['permission_flag_edit'], 'edit_posts', false )        . ">Contributor</option>";
        echo "<option value='read' "                 . selected( $this->option['permission_flag_edit'], 'read', false )              . ">Subscriber</option>";
        echo "</select>";
        echo "<p class='description'>Minimum role needed to edit submitted flags such as changing a flag's status or deleting it.</p>";
        echo $this->add_sub_heading( 'Spam' );
    }


    public function field_honeypot()
    {
        echo "<input type='checkbox' id='{$this->option_name}[honeypot]' name='{$this->option_name}[honeypot]' value='1' " . checked( $this->option['honeypot'], 1, false ) . " />";
        echo "<label for='{$this->option_name}[honeypot]'>Enable</label>";
        echo "<p class='description'>Spam protection to fight bots. Please note that some bots may still get past the defenses and submit the form.</p>";
        echo $this->add_sub_heading( 'Other' );
    }


    public function field_save_ip_address()
    {
        echo "<input type='checkbox' id='{$this->option_name}[save_ip_address]' name='{$this->option_name}[save_ip_address]' value='1' " . checked( $this->option['save_ip_address'], 1, false ) . " />";
        echo "<label for='{$this->option_name}[save_ip_address]'>" . 'Enable - Save the IP address of the flag submitter.' . "</label><br>";
        echo "<p class='description'>" . 'If enabled, the IP address of the flag submitter will be stored and made visible in the flags section.' . "</p>";
    }


    /**
     * Section display functions: Callback functions to display the sections. Each section is under its own tab.
     */
    public function reveal_section_display()
    {
        echo '<section id="reveal_section">';
        echo '<h2>Flag Button Settings</h2>';
        echo '<p>The flag button is automatically injected within post and/or pages. It is clicked to show the form.</p><br>';
        echo '<h3>Labels and Style</h3>';
    }

    public function form_section_display()
    {
        echo '</section>';
        echo '<section id="form_section">';
        echo '<h2>Form Settings</h2>';
        echo '<p>The form is used by visitors to submit flags. The form appears within a modal (aka lightbox, pop-up) after the user clicks the "Flag Button" for a particular piece of content.</p><br>';
        echo '<h3>Fields</h3>';
    }

    public function security_section_display()
    {
        echo '</section>';
        echo '<section id="Security">';
        echo '<h2>Security Settings</h2>';
        echo "<p>Grant or restrict access to the plugin's functionality in the admin backend.</p><br>";
        echo '<h3>Permissions</h3>';
    }

    public function email_section_display()
    {
        echo '</section>';
        echo '<section id="email_section">';
        echo '<h2>Email Settings</h2>';
        echo '<p>Email notifications can be sent to site users when a flag is submitted.</p>';
    }

    public function pro_section_display()
    {
		$link = 'https://codecanyon.net/item/flagged-content-let-visitors-report-and-flag-posts-comments-and-more-to-admin-wordpress-plugin/19748662';

        echo '</section>';
		echo '<section id="Pro" class="flagcontent-admin-pro">';
		echo '<h2>Pro Version Information</h2>';
        echo '<img src="' . plugins_url( '../images/flagged-content-1.jpg', __FILE__ ) . '">';
        echo "<p>The <a href='{$link}' target='_blank'>pro version</a> of Flagged Content offers many additional features on top of those offered here.</p>";
        echo '<h3>Visitors can flag more types of content</h3>
                <ul>
                    <li>WordPress comments</li>
                    <li>Custom post types</li>
                    <li>bbPress topics and replies</li>
                    <li>WooCommerce products and reviews</li>
                    <li>GeoDirectory places and reviews</li>
                </ul>';

        echo '<h3>Create and customize many different types of forms</h3>';
        echo '<h4>Tailor each form to the content that can be flagged</h4>';
        echo '<ul>
                    <li>Setup different form fields, messages and styles for different content</li>
                    <li>Easily activate and deactivate the forms</li>
                    <li>Restrict content from being flagged only to logged in users versus any visitors</li>
                    <li>Spam - Additional defenses against spam submissions</li>
              </ul>';

        echo '<h3>More form styling options</h3>
                <ul>
                    <li>Easily add icons to the buttons</li>
                    <li>Choose additional styles and custom colors for the buttons</li>
                    <li>Change additional labels</li>
                    <li>Customize additional messages shown when validation fails</li>
                </ul>';

        echo '<h3>Email - Additional options</h3>
                <ul>
                    <li>Choose additional users for emails to be sent to</li>
                    <li>Customize the email subject and message</li>
                    <li>Limit how often emails are sent</li>
                </ul>';

        echo '<h3>Flag page - More options</h3>
                <ul>
                    <li>Flag statuses - Save flags as completed for future reference</li>
                    <li>Sort flags submitted by an IP address</li>
                    <li>Organize flags by content</li>
                </ul>';

        echo "<h3><a href='{$link}' target='_blank'>Learn more or download the pro version</a></h3>";
    }


    private function add_sub_heading( $heading )
    {
        return sprintf( '</td></tr><tr><th><h3>%s</h3></th><td>', $heading );
    }

    /**
     * Callback function: sanitizes the values before saving to the options table in the database.
     *
     * Sanitization examples:
     * - If checkbox has not been selected, then store 0 in the db. Otherwise the value
     * is deleted in the db and causes a PHP warning upon showing the page again.
     * - Trim is used to remove extraneous \n and whitespaces from text input and textarea.
     *
     * @param $value
     * @return mixed
     */
    public function sanitize_values( $value )
    {
		$value['permission_flag_view']  = isset( $value['permission_flag_view'] ) ? $value['permission_flag_view'] : 'manage_options';
		$value['permission_flag_edit']  = isset( $value['permission_flag_edit'] ) ? $value['permission_flag_edit'] : 'manage_options';
		$value['honeypot']              = isset( $value['honeypot'] )             ? $value['honeypot']             : 0;
        $value['content']               = isset( $value['content'] )              ? $value['content'] : 'post';
        $value['reveal_label']          = trim( $value['reveal_label'] );
        $value['reveal_success_label']  = trim( $value['reveal_success_label'] );
        $value['reveal_style']          = isset( $value['reveal_style'] )         ? $value['reveal_style'] : 'theme;theme';
        $value['reveal_location']       = isset( $value['reveal_location'] )      ? $value['reveal_location'] : 'content_before';
        $value['reveal_align']          = isset( $value['reveal_align'] )         ? $value['reveal_align'] : 'left';
        $value['name']                  = isset( $value['name'] )                 ? $value['name']        : 'optional';
        $value['email']                 = isset( $value['email'] )                ? $value['email']       : 'optional';
        $value['reason']                = isset( $value['reason'] )               ? $value['reason']      : 'required';
        $value['reason_choose']         = trim( $value['reason_choose'] );
        $value['reason_choose']         = preg_replace( '/^\s+/m', '', $value['reason_choose'] ); // Removes multiple newlines in a row
        $value['description']           = isset( $value['description'] )          ? $value['description'] : 'optional';
        $value['submit_label']          = trim( $value['submit_label'] );
        $value['submit_style']          = isset( $value['submit_style'] )         ? $value['submit_style'] : 'theme;theme';
        $value['message_instructions'] 	= trim( $value['message_instructions'] );
        $value['message_success'] 	    = trim( $value['message_success'] );
        $value['email_enabled']         = isset( $value['email_enabled'] )        ? $value['email_enabled'] : 0;
        $value['save_ip_address']       = isset( $value['save_ip_address'] )      ? $value['save_ip_address'] : 0;

		return $value;
	}


	public function display_page()
    {
		echo '<div class="wrap">';

			$title = esc_html( get_admin_page_title() );
			echo "<h1>$title</h1>";

			settings_errors();

			echo '<div class="flagcontent-admin-settings-main">';

				echo '<div class="flagcontent-admin-tabs">';
					echo '<h2 class="nav-tab-wrapper">';
                        echo '<a href="#" class="nav-tab nav-tab-active"><span class="dashicons dashicons-flag"></span> Flag Button</a>';
                        echo '<a href="#" class="nav-tab"><span class="dashicons dashicons-feedback"></span> Form</a>';
                        echo '<a href="#" class="nav-tab"><span class="dashicons dashicons-email-alt"></span> Email</a>';
						echo '<a href="#" class="nav-tab"><span class="dashicons dashicons-lock"></span> Security</a>';
						echo '<a href="#" class="nav-tab"><span class="dashicons dashicons-shield-alt"></span> Pro</a>';
					echo '</h2>';
				echo '</div>';

				echo '<div class="flagcontent-admin-settings-wrapper">';
					echo '<form method="post" action="options.php">';

                        /* 'option_group' must match 'option_group' from register_setting call */
						settings_fields( $this->option_group );
                        do_settings_sections( $this->page_slug );

						echo '</section>';

						submit_button( 'Save All Changes' );

					echo '</form>';


                    if ( Flagged_Content::DEBUG )
                    {
                        echo 'Settings array:<pre>';
                        print_r ( $this->settings );
                        echo '</pre>';
                    }

				echo '</div>';
			echo '</div>';

            echo '<div class="flagcontent-admin-settings-support">';
                echo '<h2>Support us</h2>';
                echo '<p>Please consider supporting us</p>';
                echo '<p><span class="dashicons dashicons-shield-alt"></span> <a href="https://codecanyon.net/item/flagged-content-let-visitors-report-and-flag-posts-comments-and-more-to-admin-wordpress-plugin/19748662" target="_blank">Get</a> the Pro version</p>';
                echo '<p><span class="dashicons dashicons-star-filled"></span> <a href="https://wordpress.org/support/plugin/flagged-content/reviews/?filter=5" target="_blank">Rate</a> on WordPress</p>';
            echo '</div>';

		echo '</div>';
	}
}