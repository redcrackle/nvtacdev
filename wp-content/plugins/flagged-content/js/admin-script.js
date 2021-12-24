jQuery(document).ready(function( $ ) {

	/**
	 * Settings init
	 */
	$( '.flagcontent-admin-settings-wrapper section:not(:first)' ).hide();

	/**
	 * Settings tabs
	 */
	$( '.flagcontent-admin-tabs' ).on( 'click', '.nav-tab-wrapper a', function()
    {
		$( this ).addClass( 'nav-tab-active' );

		// Remove active class from all tab links except the one clicked on
		$( '.nav-tab-wrapper a' ).not( this ).removeClass( 'nav-tab-active' );
		
		$( 'section' ).hide();
		$( '.flagcontent-admin-settings-wrapper section' ).eq( $( this ).index() ).show();
		
		$( this ).blur(); // Removes blue outline/box-shadow from the tab (a:focus)

		return false;
	});

	/**
	 * Settings - Hide / show fields based on selections
	 */
	function check_fields( element )
    {
        var field_name = $( element ).attr( 'name' ).match(/\[(.*)\]/);
        field_name = field_name[1];

		var field_value;

        if ( $( element ).is( 'input[type="radio"]:checked' ) ) {
            field_value = $( element ).val();
        }
        else if ( $( element ).is( 'input[type="checkbox"]' ) && $( element ).is( ':checked' ) ) {
            field_value = 1;
        }
        else if ( $( element ).is( 'input[type="checkbox"]' ) && ! ( $( element ).is( ':checked' ) ) ) {
        	field_value = 0;
		}
		else if ( $( element ).is( 'select' ) ) {
            field_value = $( element ).val();
		}
        else {
            return;
        }

        if ( field_name == 'reason' && field_value == 'no_display' ) {
            $( '[name*="[reason_choose]"]' ).closest( 'tr' ).hide();
        }
        else if ( field_name == 'reason' && field_value != 'no_display' ) {
            $( '[name*="[reason_choose]"]' ).closest( 'tr' ).show();
		}
	}

	// Settings hide / show - On change
	$( '.flagcontent-admin-settings-main input[type="checkbox"], ' +
		'.flagcontent-admin-settings-main input[type="radio"], ' +
		'.flagcontent-admin-settings-main select' ).change( function () {
		check_fields( $( this ) );
 	});

	// Settings hide / show - Init
    $( '.flagcontent-admin-settings-main input[type="checkbox"], ' +
        '.flagcontent-admin-settings-main input[type="radio"], ' +
        '.flagcontent-admin-settings-main select' ).each( function () {
        check_fields( $( this ) );
    });

	/**
	 * Admin flags page
	 * */
    $( '.flagcontent-delete-all-flag-link' ).click( function() {
        var post_type = $( this ).attr( 'data-flagcontent-post-type' );
        return window.confirm( "Please confirm you want to delete all flags for this " + post_type + "." );
    });
});