jQuery(document).ready(function( $ ) {

	/**
	 * Init
	 */
	if ( $( '.flagcontent-form-modal' )[0] ) {

		$( '.flagcontent-reveal-button' ).each( function() {

			$( this ).magnificPopup({
				items: {
					src: '.flagcontent-form-modal',
					type: 'inline'
				},
				removalDelay: 300,
				mainClass: 'flagcontent-mfp-slide-bottom',
				midClick: true,
				fixedContentPos: false,
				fixedBgPos: true,
				overflowY: 'auto',
				closeBtnInside: true,
				preloader: false
			});
		});
	}

	/**
	 * Listeners
	 */

	$( ".flagcontent-button" ).mouseup( function(){
    	$( this ).blur();
	});

	$( '.flagcontent-submit-button' ).on( 'click', function( e )
    {
		if ( validate_the_form() ) {
			send_ajax_form();
		}
	});

	/**
	 * Functions
	 */

	function validate_the_form()
    {

		var validation_error_count = 0;

		var $alert_box 					= $( '.flagcontent-alert-box' );
		var alert_require_text 			= $alert_box.text();
		var $all_labels 				= $( 'label' );
		var $reason_dropdown 			= $( 'select[name=flagcontent_reason][required]' );
		var $reason_dropdown_selected 	= $( 'select[name=flagcontent_reason][required] option:selected' );
		var $reason_dropdown_label 	    = $( 'label[for=flagcontent_reason]' );
		var $name 						= $( 'input[name=flagcontent_name][required]' );
		var $name_label 				= $( 'label[for=flagcontent_name]' );
		var $email 						= $( 'input[name=flagcontent_email][required]' );
		var $email_label 				= $( 'label[for=flagcontent_email]' );
		var $description 				= $( 'textarea[name=flagcontent_description][required]' );
		var $description_label 			= $( 'label[for=flagcontent_description]' );

		// Reset the validation errors
		$alert_box.removeClass( 'flagcontent-validation-errors-description' ).hide();
		$all_labels.removeClass( 'flagcontent-validation-error' );

		// Check if "Reason" field (dropdown) is required and if a value has not been chosen
		if ( $reason_dropdown.length && $reason_dropdown_selected.index() < 0 ) {
			$reason_dropdown_label.addClass( 'flagcontent-validation-error' );
			validation_error_count++;
		}

		// Check if "Your Name" field is required and if a value has not been entered
		if ( $name.length && ! $name.val().length ) {
			$name_label.addClass( 'flagcontent-validation-error' );
			validation_error_count++;
		}

		// Check if "Your Email" field is required and if a value has not been entered
		if ( $email.length && ! $email.val().length ) {
			$email_label.addClass( 'flagcontent-validation-error' );
			validation_error_count++;
		}

		// Check if "Your Description" field is required and if a value not has been entered
		if ( $description.length && ! $description.val().length ) {
			$description_label.addClass( 'flagcontent-validation-error' );
			validation_error_count++;
		}

		if ( validation_error_count > 0 ) {
			$alert_box
				.addClass( 'flagcontent-validation-errors-description' )
				.text( alert_require_text )
				.show();
		}

		return ! validation_error_count;
	}

	function send_ajax_form()
    {
		var $alert_box 		  	 = $( '.flagcontent-alert-box' );
		var $reason 		  	 = $( 'select[name=flagcontent_reason]' );
		var $name 			  	 = $( 'input[name=flagcontent_name]' );
		var $email 			  	 = $( 'input[name=flagcontent_email]' );
		var $description 	  	 = $( 'textarea[name=flagcontent_description]' );
		var $post_id 		     = $( 'input[name=flagcontent_post_id]' );
		var $sticky_paper 		 = $( 'input[name=flagcontent_sticky_paper]' );
		var $sticky_paper_2 	 = $( 'input[name=flagcontent_sticky_paper_2]' );
		var $submit 			 = $( '.flagcontent-submit-button' );
		var submit_text_saved  	 = $submit.text();
		var $spinner 			 = $( '.flagcontent-submit-spinner' );
        var $reveal              = $( '.flagcontent-reveal-button' );
        var reveal_success_label = $reveal.attr( 'data-flagcontent-success-label' );
		$sticky_paper_2.val(     $sticky_paper.val() );

		var form_data = {
	        action: 					'flagcontent_ajax_handler',
	        nonce: 						flagcontent_ajax_object.nonce,
	        user_id: 					flagcontent_ajax_object.user_id,
            post_id: 				    $post_id.val(),
			flagcontent_name: 			$name.val(),
			flagcontent_email: 			$email.val(),
			flagcontent_reason: 		$reason.val(),
			flagcontent_description: 	$description.val(),
            flagcontent_sticky_paper:   $sticky_paper.val(),
            flagcontent_sticky_paper_2: $sticky_paper_2.val()
	    };

		$submit.text( 'Sending' );
		$submit.prop( 'disabled', true );
		$spinner.css( "visibility", "visible" );

		jQuery.ajax(
		{
     	    type: "post",
     		url:  flagcontent_ajax_object.ajax_url,
     	    data: form_data,
		     
		    success:function( data, textStatus, jqXHR ) {
				
		     	var return_data = data.data;

				 if ( flagcontent_ajax_object.debug_mode == "1" ) {
					 console.log( 'data:' );
					 console.log( data );
					 console.log( 'textStatus: ' );
					 console.log( textStatus );
					 console.log( 'jqXHR: ' );
					 console.log( jqXHR );
					 console.log( 'return_data: ' );
					 console.log( return_data );
				 }

				// Successful ajax call
				if ( data.success ) {

					if ( return_data.message != '' ) {
						$alert_box
							.removeClass( 'flagcontent-validation-errors-description' )
							.addClass( 'flagcontent-alert-box-success' )
							.text( return_data.message )
							.show();
					}
					else {
						$alert_box.hide();
					}

					// $name.prop( 'disabled', true );
					// $email.prop( 'disabled', true );
					// $reason.prop( 'disabled', true );
					// $( 'input[name=flagcontent_reason]' ).prop( 'disabled', true );
					// $description.prop( 'disabled', true );

					$name.replaceWith( function () {
                        return '<div>' + this.value + '</div>';
                    });

                    $email.replaceWith( function () {
                        return '<div>' + this.value + '</div>';
                    });

                    var reason_value = $reason.val();

                    $( 'select[name=flagcontent_reason]' ).replaceWith( function () {
                        return '<div>' + reason_value + '</div>';
                    });

                    $description.replaceWith( function () {
                        return '<div>' + this.value + '</div>';
                    });

					$submit.hide();

					$reveal.text( reveal_success_label );
				}

				else {

					$alert_box
						.removeClass( 'flagcontent-alert-box-success' )
						.addClass( 'flagcontent-validation-errors-description' )
						.text( return_data.message )
						.show();

					$submit.prop('disabled', false);
				}
		     },

            error: function( jqXHR, textStatus, errorThrown ) {
				$submit.prop('disabled', false);
            },

            complete: function( jqXHR, textStatus ) {
				 $submit.text( submit_text_saved );
		         $spinner.hide();
            }
		});
	}


});