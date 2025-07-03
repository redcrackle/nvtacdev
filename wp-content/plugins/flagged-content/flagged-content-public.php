<?php
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Handles all public page requests
 */
class Flagged_Content_Public
{
	private $plugin;
    private $settings;
    private $post_id;
    private $post_type;

    /**
     * Adds init() to the wp hook, which fires late enough for is_single, is_page, is_singular, etc. to be used. Otherwise,
     * these functions are not available at the time of this plugin's loading.
     * @param Flagged_Content $plugin
     */
    public function __construct( $plugin )
    {
        $this->plugin = $plugin;
        add_action( 'wp', array( $this, 'init' ) );
	}

    /**
     * Callback function: sets the settings, enqueue's public scripts and adds the flagging forms to various content types.
     * @action wp ( this->__construct() )
     */
	public function init()
    {
		// Do not load on index/archive pages
		if ( ( ! is_single() and ! is_page() ) or is_front_page() ) {
			return;
		}

        global $post;
        $this->post_id   = $post->ID;
        $this->settings  = $this->plugin->get_settings();

        // Currently on a builtin post and set to show on post
        if ( is_singular( 'post' ) && ( $this->settings['content'] == 'post' || $this->settings['content'] == 'post_and_page' ) ) {
            $this->post_type = 'post';
        }
        // Currently on a builtin page and set to show on page
        elseif ( is_singular( 'page' ) && ( $this->settings['content'] == 'page' || $this->settings['content'] == 'post_and_page' ) ) {
            $this->post_type = 'page';
        }
        // Unsupported post type or post/page did not match content setting
        else {
            return;
        }

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_files' )     );
        add_filter( 'the_content',        array( $this, 'add_to_post'          ), 10 );
	}

    /**
     * Callback function: Enqueues scripts and styles on public pages.
     * @action wp_enqueue_scripts ( this->init() )
     */
	public function enqueue_public_files()
    {
		wp_enqueue_style(  'flagcontent-public-style',       plugins_url('css/public-styles.css', __FILE__), array(),           Flagged_Content::VERSION );
		wp_enqueue_script( 'flagcontent-ajax-public-script', plugins_url( 'js/public-script.js', __FILE__ ), array( 'jquery' ), Flagged_Content::VERSION );

		wp_localize_script(
		    'flagcontent-ajax-public-script',
            'flagcontent_ajax_object',
			array( 
				'ajax_url' 	 => admin_url( 'admin-ajax.php' ),
				'nonce' 	 => wp_create_nonce( 'flagcontent_form_nonce' ),
				'user_id' 	 => get_current_user_id(),
                'debug_mode' => Flagged_Content::DEBUG
			)
		);

        if ( ! wp_script_is ( 'magnific-popup', 'registered' ) && ! wp_script_is ( 'jquery.magnific-popup.min.js', 'enqueued' ) && ! wp_script_is ( 'jquery.magnific-popup.js', 'enqueued' ) ) {
            wp_enqueue_script( 'flagcontent-magpop-script', plugins_url( 'js/jquery.magnific-popup.min.js', __FILE__ ), array( 'jquery' ), Flagged_Content::VERSION );
        }

        if ( ! wp_style_is ( 'magnific-popup', 'registered' ) && ! wp_style_is ( 'magnific-popup.min.css', 'enqueued' ) && ! wp_style_is ( 'magnific-popup.css', 'enqueued' ) ) {
            wp_enqueue_style( 'flagcontent-magpop-style', plugins_url( 'css/magnific-popup.css', __FILE__ ), array(), Flagged_Content::VERSION );
        }
	}

    /**
     * Callback function outputs form to the post
     *
     * @filter the_content ( this->init() )
     * @param $content
     * @return string
     */
    public function add_to_post( $content )
    {
        if ( ! in_the_loop() ) {
            return $content;
        }

        // Debug Settings Array
        if ( Flagged_Content::DEBUG ) {
            $this->display_debug();
        }

        $output = $this->get_output();

        // Add form before the content "content_before" or after "content_after"
        if ( $this->settings['reveal_location'] == "content_before" ) {
            return $output . $content;
        }
        else {
            return $content . $output;
        }
	}

    /**
     * Assembles the html for the form to be added into the modal
     */
    private function get_output()
    {
        $output_instructions = '';
        $output_name_email_container_open = '';
        $output_name_email_container_close = '';
        $output_name = '';
        $output_email = '';
        $output_reason = '';
        $output_description = '';
        $output_hidden = '';
        $output_reveal = '';

        $output_alert = '<div class="flagcontent-alert-box flagcontent-validation-errors-description">Please complete the required fields.</div>';

        if ( ! empty( $this->settings['message_instructions'] ) ) {
            $output_instructions = '<div class="flagcontent-instructions">' . esc_html( $this->settings['message_instructions'] ). '</div>';
        }

        if ( $this->settings['name'] != 'no_display' || $this->settings['email'] != 'no_display' )
        {
            $output_name_email_container_open .= '<div class="flagcontent-name-email-container">';

            if ( is_user_logged_in() )
            {
                $readonly = 'readonly';
                $current_user = wp_get_current_user();
            }
            else
            {
                $readonly = '';
            }
        }

        if ( $this->settings['name'] != 'no_display' )
        {
            $user_name          = is_user_logged_in() ? $current_user->user_login : '';
            $required_wording 	= $this->settings['name'] == 'required' ? ' <small>(required)</small>': '';
            $required_attribute = $this->settings['name'] == 'required' ? 'required ' : '';

            $output_name .= '<div class="flagcontent-name-field">';
            $output_name .= "<label for='flagcontent_name'>Your Name{$required_wording}</label><br>";
            $output_name .= "<input type='text' name='flagcontent_name' id='flagcontent_name' value='{$user_name}' {$required_attribute} {$readonly}>";
            $output_name .= '</div>';
        }

        if ( $this->settings['email'] != 'no_display' )
        {
            $user_email         = is_user_logged_in() ? $current_user->user_email : '';
            $required_wording 	= $this->settings['email'] == 'required' ? ' <small>(required)</small>': '';
            $required_attribute = $this->settings['email'] == 'required' ? 'required ' : '';

            $output_email .= '<div class="flagcontent-email-field">';
            $output_email .= "<label for='flagcontent_email'>Email{$required_wording}</label><br>";
            $output_email .= "<input type='text' name='flagcontent_email' id='flagcontent_email' value='{$user_email}' {$required_attribute} {$readonly}>";
            $output_email .= '</div>';
        }

        if ( $this->settings['name'] != 'no_display' || $this->settings['email'] != 'no_display' )
        {
            $output_name_email_container_close .= '</div>';
            $output_name_email_container_close .= '<div class="flagcontent-clear-floated-fields"></div>';
        }

        if ( $this->settings['reason'] != 'no_display' && ! empty( $this->settings['reason_choose'] ) )
        {
            $required_wording   = $this->settings['reason'] == 'required' ? ' <small>(required)</small>': '';
            $required_attribute = $this->settings['reason'] == 'required' ? 'required ' : '';

            $output_reason .= '<div class="flagcontent-reason-container">';
            $output_reason .= "<label for='flagcontent_reason'>Reason{$required_wording}</label><br />";
            $output_reason .= "<select name='flagcontent_reason' id='flagcontent_reason' {$required_attribute}>";

            foreach ( $this->settings['reason_choose'] as $reason ) {
                $output_reason .= sprintf( '<option value="%s">%s</option>', esc_attr( $reason ), esc_html( $reason ) );
            }

            $output_reason .= '</select></div>';
        }

        if ( $this->settings['description'] != 'no_display' )
        {
            $required_wording 	= $this->settings['description'] == 'required' ? ' <small>(required)</small>': '';
            $required_attribute = $this->settings['description'] == 'required' ? 'required ' : '';

            $output_description .= '<div class="flagcontent-description-container">';
            $output_description .= "<label for='flagcontent_description'>Description{$required_wording}</label><br />";
            $output_description .= "<textarea name='flagcontent_description' id='flagcontent_description' {$required_attribute}></textarea>";
            $output_description .= '</div>';
        }

        $output_hidden .= "<input type='hidden' name='flagcontent_post_id' value='{$this->post_id}'>";

        // Spam Settings - Honeypot
        if ( $this->settings['honeypot'] )
        {
            $output_hidden .= '<input type="hidden" name="flagcontent_sticky_paper" value="' . rand(5000, 6000) . '">';
            $output_hidden .= '<input type="hidden" name="flagcontent_sticky_paper_2" value="1">';
        }

        /*
         * reveal_style has string data that needs to be exploded (e.g. theme;red )
         * $reveal['style'] = theme
         * $reveal['color'] = red
         */
        $reveal               = array_combine( array( 'style', 'color' ), explode( ';', $this->settings['reveal_style'] ) );
        $reveal_classes       = "flagcontent-button flagcontent-reveal-button flagcontent-button-style-{$reveal['style']} flagcontent-button-color-{$reveal['color']}";
        $reveal_label         = $this->settings['reveal_label']  == '' ? 'Flag' : $this->settings['reveal_label'];
        $reveal_success_label = $this->settings['reveal_success_label'] == '' ? 'Flagged' : $this->settings['reveal_success_label'];

        $output_reveal .= sprintf(
            '<button type="button" class="%1$s" data-flagcontent-success-label="%2$s">%3$s</button>',
            /* class=                       */    esc_attr( $reveal_classes ),        /* %1$s */
            /* data-flagcontent-success-label= */ esc_attr( $reveal_success_label ),  /* %2$s */
            /* <> </>                       */    esc_html( $reveal_label )           /* %3$s */
        );

        /*
         * submit_style has string data that needs to be exploded (e.g. theme;red )
         * $submit['style'] = theme
         * $submit['color'] = red
         */
        $submit         = array_combine( array( 'style', 'color' ), explode( ';', $this->settings['submit_style'] ) );
        $submit_classes = "flagcontent-button flagcontent-submit-button flagcontent-button-style-{$submit['style']} flagcontent-button-color-{$submit['color']}";
        $submit_label   = $this->settings['submit_label'] == '' ? 'Submit' : $this->settings['submit_label'];

        $output_submit  = '<div class="flagcontent-submit-container">';
        $output_submit .= sprintf(
            '<button type="button" class="%1$s">%2$s</button>',
            /* class= */ esc_attr( $submit_classes ),  /* %1$s */
            /* <> </> */ esc_html( $submit_label )     /* %2$s */
        );

        $output_submit .= '<img src="' . plugins_url('images/loading.gif', __FILE__) . '" class="flagcontent-submit-spinner">';
        $output_submit .= '</div>';

        $alignment = isset( $this->settings['reveal_align'] ) && ! empty( $this->settings['reveal_align'] ) ? "style='text-align:{$this->settings['reveal_align']}'" : '';

        return
            "<div class='flagcontent-form-container' {$alignment}>" .
                '<div>' .
                    $output_reveal .
                '</div>' .

                "<div class='flagcontent-form flagcontent-form-modal flagcontent-zoom-anim-dialog mfp-hide'>" .
                    "<form>" .
                        '<div class="flagcontent-form-inside">' .

                            $output_alert .

                            '<div class="flagcontent-form-fields">' .

                                $output_instructions .

                                $output_name_email_container_open .

                                $output_name .
                                $output_email .

                                $output_name_email_container_close .

                                $output_reason .
                                $output_description .
                                $output_hidden .
                                $output_submit .

                            '</div>' .
                        '</div>' .
                    '</form>' .
                '</div>' .
            '</div>';
    }

    /**
     * Function for debugging purposes.
     */
    private function display_debug()
    {
        echo '<pre><h3>Debug:</h3>';
        global $post;
        echo '<br>post->ID: '                  . $post->ID;
        echo '<br>get_the_ID(): '              . get_the_ID();
        echo '<br>this->post_id: '             . $this->post_id;
        echo '<br>get_post_type( post->ID ): ' . get_post_type( $post->ID );
        echo '<br>this->post_type: '           . $this->post_type;
        echo '<br>is_singular: '               . is_singular();
        echo '<br>is_single: '                 . is_single();
        echo '<br>is_page: '                   . is_page();
        echo '<br>in_the_loop: '               . in_the_loop() . '<br>';
        echo '<br>this->settings: '            . print_r( $this->settings );
        echo '</pre>';
        return ;
    }
}
