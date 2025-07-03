<?php
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Class requires user's settings for the plugin and the ajax request form data.
 */
class Flagged_Content_AJAX
{
	private $plugin;
	private $settings;
	private $data;

	private $fail_message_blank;
	private $fail_message_email;
	private $fail_message_generic;

	public function __construct( $plugin )
    {
	    $this->plugin = $plugin;

        add_action( 'wp_ajax_nopriv_flagcontent_ajax_handler', array( $this, 'flagcontent_ajax_handler' ) ); // Visitors
        add_action( 'wp_ajax_flagcontent_ajax_handler', 	   array( $this, 'flagcontent_ajax_handler' ) ); // Logged-in users
	}

    /**
     * Callback function processes ajax data for the plugin.
     *
     * Runs security and validation checks. Sanitizes the data, adds to plugin table in DB and sends out emails (if enabled).
     */
	public function flagcontent_ajax_handler()
    {
		// Quick check to verify nonce. Dies if cannot be verified.
		check_ajax_referer( 'flagcontent_form_nonce', 'nonce' );
       
		// Get and set the user settings for the plugin
		$this->settings = $this->plugin->get_settings();

		// Get and set the form data passed through the ajax request
        $this->data = $this->get_ajax_data();

        // If there is no form for the content type, then fail
        if ( $this->settings === false ) {
            $this->send_failure_message( 'There was an issue submitting your form. Please reload the page and try again.', 'no form: there is no form set for this content type');
        }

        $this->fail_message_blank   = 'Please complete the required fields.';
        $this->fail_message_email   = 'Please enter a valid e-mail address.';
        $this->fail_message_generic = 'There was an error. Please try again.';

		// Verify the spam checks are met
		$this->check_spam_security();

		// Validate incoming content and user data
		$this->validate_content_user_data();
		
		// Validate incoming form field data
		$this->validate_form_fields( 'reason' );
		$this->validate_form_fields( 'name' );
		$this->validate_form_fields( 'email' );
		$this->validate_form_fields( 'description' );

		// Validation is finished, now sanitize the strings (just in case)
		$this->sanitize_form_fields();
	
		global $wpdb;		
		$table_name = $this->plugin->get_table_name();

		// $wpdb->prepare() not needed, insert uses prepare internally
		$wpdb->insert(
			$table_name,
			array(
                'name_entered' 	=> $this->data['name'],
                'email' 		=> $this->data['email'],
				'reason' 		=> $this->data['reason'],
				'description' 	=> $this->data['description'],
				'ip' 			=> $this->data['ip'],
                'user_id' 		=> $this->data['user_id'],
				'date_notified' => current_time('mysql'),
				'post_id' 	    => $this->data['post_id']
            )
		);

		// Email notification (if enabled
		if ( $this->settings['email_enabled'] == TRUE ) {
			$this->send_email();			
		}

		// Return success message to user
		$return_array['message'] = $this->settings['message_success'];
		wp_send_json_success( $return_array );

		// Precaution - Ajax handlers must die when finished.
	    wp_die();
	}

    /**
     * Assembles the data sent through ajax into an array. Cleans the data.
     *
     * @return array
     */
	private function get_ajax_data()
    {
		$data = array(
			'post_id' 	     => $_POST['post_id'],
			'user_id' 		 => $_POST['user_id'],
			'reason'		 => isset( $_POST['flagcontent_reason'])          ? $_POST['flagcontent_reason'] : '',
			'name' 			 => isset( $_POST['flagcontent_name'] )           ? $_POST['flagcontent_name'] : '',
			'email' 		 => isset( $_POST['flagcontent_email'] )          ? $_POST['flagcontent_email'] : '',
			'description'	 => isset( $_POST['flagcontent_description'] )    ? $_POST['flagcontent_description'] : '',
			'sticky_paper'   => isset( $_POST['flagcontent_sticky_paper'] )   ? $_POST['flagcontent_sticky_paper'] : '',
			'sticky_paper_2' => isset( $_POST['flagcontent_sticky_paper_2'] ) ? $_POST['flagcontent_sticky_paper_2'] : ''
		);

		$data['reason'] 	 = trim( stripslashes_deep( $data['reason'] 	 ) );
		$data['name']  		 = trim( stripslashes_deep( $data['name'] 		 ) );
		$data['email'] 		 = trim( stripslashes_deep( $data['email'] 	  	 ) );
		$data['description'] = trim( stripslashes_deep( $data['description'] ) );

		// Add submitter's IP address to the data
        if ( $this->settings['save_ip_address'] )
        {
			// If debug then chance to generate random IP address, otherwise use normal ip
			$ip = ( Flagged_Content::DEBUG && mt_rand( 0, 1 ) ) ? long2ip( mt_rand() ) : $_SERVER['REMOTE_ADDR'];

			// Convert to binary
			$data['ip'] = filter_var( $ip, FILTER_VALIDATE_IP ) ? inet_pton( $ip ) : ''; 
        }
        else
        {
            $data['ip'] = '';
        }

		return $data;
	}

    /**
     * Spam Security
     * - Honeypot
     * - Timestamp Defense
     *
     * If the function finds a validation error, then it will exit and send a failure message to the user. If the function passes validation then
     * nothing is returned. Control reverts back to the handler.
     */
	private function check_spam_security ()
    {
		if ( $this->settings['honeypot'] )
		{
			// Fail if the honeypot value is not set
			if ( ! isset( $this->data['sticky_paper'] ) || ! isset( $this->data['sticky_paper_2'] ) ) {
				$this->send_failure_message( $this->fail_message_generic, 'honeypot: enabled but not set in ajax data');
			} 

			// Fail if the honeypot value is not numeric
			elseif ( ! is_numeric( $this->data['sticky_paper'] ) || ! is_numeric( $this->data['sticky_paper_2'] ) ) {
				$this->send_failure_message( $this->fail_message_generic, 'honeypot: should be numeric but was not in ajax data');
			}

			// Only a certain range of numbers will be generated. Anything outside this range is bad.
			elseif ( $this->data['sticky_paper_2'] < 5000 || $this->data['sticky_paper_2'] > 6000 ) {
				$this->send_failure_message( $this->fail_message_generic, 'honeypot: the guide number was outside the range');
			} 

			// Check if the honeypot hidden field numbers matches the other. Fail if they don't match.
			elseif ( $this->data['sticky_paper_2'] !== $this->data['sticky_paper'] ) {
				$this->send_failure_message( $this->fail_message_generic, 'honeypot: the guide number and stick fields do not match');
			}
		}

		return;
	}

    /**
     * Validation - Check post id, user id, post status and post type.
     *
     * If the function finds a validation error, then it will exit and send a failure message to the user. If the function passes validation then
     * nothing is returned. Control reverts back to the handler.
     */
	private function validate_content_user_data()
    {
	    // post_id - is int
		if ( ! isset( $this->data['post_id'] ) || filter_var( $this->data['post_id'], FILTER_VALIDATE_INT) === FALSE ) {
			$this->send_failure_message( $this->fail_message_generic, 'post_id: should be numeric but was not in ajax data');
		}

		// post_id - greater than zero
        elseif ( (int) $this->data['post_id'] <= 0 ) {
            $this->send_failure_message( $this->fail_message_generic, 'post_id: The post_id received in the ajax data is was int casted to 0 or below');
        }

        // user_id - is int
		elseif ( ! isset( $this->data['user_id'] ) || filter_var( $this->data['user_id'], FILTER_VALIDATE_INT) === FALSE ) {
			$this->send_failure_message( $this->fail_message_generic, 'user_id: should be numeric but was not in ajax data');
		}

        // post_id - verify valid post
        elseif ( get_post_status( (int) $this->data['post_id'] ) === FALSE ) {
            $this->send_failure_message( $this->fail_message_generic, 'post_id: The post_id received in the ajax data does not correspond with an actual post');
        }

        /** Check post type */

        $post_type = (int) get_post_type( $this->data['post_id'] );

        // verify valid post type
		if ( $post_type != 'post' && $post_type != 'page' ) {
			$this->send_failure_message( $this->fail_message_generic, 'post type: invalid post type was received in the ajax data');
		}

        // verify post type matches settings content
        elseif ( $this->settings['content'] != 'post_and_page' && $post_type != $this->settings['content'] ) {
            $this->send_failure_message( $this->fail_message_generic, 'post type: post type of post id received in ajax data does not match settings');
        }

		return;
	}

    /**
     * Sanitize form fields
     */
	private function sanitize_form_fields()
    {
		$this->data['post_id']  	= (int) $this->data['post_id'];
		$this->data['user_id'] 	 	= (int) $this->data['user_id'];
		$this->data['reason'] 	 	= sanitize_text_field( 	$this->data['reason'] );
		$this->data['name']  		= sanitize_text_field( 	$this->data['name'] );
		$this->data['email'] 		= sanitize_email( 		$this->data['email'] );
		$this->data['description'] 	= sanitize_text_field( 	$this->data['description'] );

		return;
	}


	/**
     * Possible validation errors:
	 * - 1 or more fields are not set when they should be
	 * - 1 or more required fields are blank
	 * - E-mail field is invalid
	 *
	 * If the function finds a validation error, then it will exit and send a failure message to the user. If the function passes validation then
     * nothing is returned. Control reverts back to the handler.
    */
	private function validate_form_fields( $field )
    {
	    // Validate forms fields if they set to be displayed. In the case of the reason field, only validate if there are reasons to choose from
		if ( ( $this->settings[ $field ] != 'no_display' && $field != 'reason' ) || ( $field == 'reason' && ! empty( $this->settings['reason_choose'] ) ) )
		{
			if ( ! isset( $this->data[ $field ] ) ) {
				$this->send_failure_message( $this->fail_message_blank, $field . ': is set to be shown but was not set in ajax data');
			}

			elseif ( $this->settings[ $field ] == 'required' && $this->data[ $field ] == '' ) {
				$this->send_failure_message( $this->fail_message_blank, $field . ': is required but blank');
			} 

			elseif ( $field == 'email' && $this->data[ $field ] != '' && filter_var( $this->data[ $field ], FILTER_VALIDATE_EMAIL) === false ) {
				$this->send_failure_message( $this->fail_message_email, $field . ': is not a valid email');
			}
		}

		else
        {
			$this->data[ $field ] = '';
		}

		return;
	}

    /**
     * Returns a failure message (ajax) back to the user's browser.
     *
     * @param string $fail_message - message displayed to the user in the flagging form.
     * @param string $debug_reason - The actual reason for the failure, only displayed if debug mode is turned on.
     */
	private function send_failure_message( $fail_message, $debug_reason )
    {
		$return_array['message'] = $fail_message;

		if ( Flagged_Content::DEBUG ) {
			$return_array['issue'] = $debug_reason;
		}

		wp_send_json_error( $return_array );
	}


    /**
     * Email notification for flag submission. Uses wp_mail to send the e-mail.
     */
	private function send_email()
    {
		// Determine who the email is sent to
        $email_to = array();
        // get_option('admin_email') pulls email from: Settings > General > Email address
        $email_to[] = get_option('admin_email');

		// If there are no recipients for the email then exit
		if ( empty( $email_to ) ) {
		    return;
        }
        // Else, remove duplicate emails and reindex the array
        else {
            $email_to = array_values ( array_unique( $email_to ) );
        }

		// Build email subject and message body
		$email_subject  = get_bloginfo( 'name' ) . ' Content flagged';
		$email_message	= "A user has flagged content on " . get_bloginfo( 'name' ) . "\n";
        $email_message .= "Title: " . get_the_title( $this->data['post_id'] ) . "\n";
		$email_message .= "Link: " . get_permalink( $this->data['post_id'] ) . "\n" ;
		$email_message .= "Flag reason: " . $this->data['reason'] . "\n";
		$email_message .= "Flag description: " . $this->data['description'] . "\n";

		wp_mail( $email_to, $email_subject, $email_message );

		return;
	}
}
