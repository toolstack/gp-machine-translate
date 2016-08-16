$gp.machine_translate = function( $ ) { return {
	current: null,
	init: function( table ) {
		$gp.init();
		$gp.machine_translate.table = table;
		$gp.machine_translate.install_hooks();
	},
	install_hooks: function() {
		$( $gp.machine_translate.table ).on( 'click', 'a.gp_machine_translate', $gp.machine_translate.hooks.machine_translate )
	},
	machine_translate: function( link ) {
		original_text = link.parents( '.textareas' ).siblings( '.original' ).text();
		if( !original_text ) {
			original_text = link.parents( '.textareas' ).siblings( 'p:last' ).children( '.original' ).html();
		}
		
		if( !original_text ) {
			return;
		}

		$gp.notices.notice( 'Translating via Machine Translate&hellip;' );

		var data = {
			'action': 'gp_machine_translate',
			'query': '',
			'locale': gp_machine_translate.locale,
			'original': original_text,
		};

		jQuery.ajax( { 
						url: gp_machine_translate.ajaxurl,
						type: 'post',
						data: data,
						datatype: 'json',
		})
			.always( function( result ) {
				if( ! result.error && result.data.translatedText != '' ) {
					link.parent( 'p' ).siblings( 'textarea' ).html( result.data.translatedText ).focus();
					$gp.notices.success( 'Translated!' );
				} else {
					$gp.notices.error( 'Error in translating via Google Translate: ' + result.error.message + ': ' + result.error.reason );
					link.parent( 'p' ).siblings( 'textarea' ).focus();
				}
		});
	},
	hooks: {
		machine_translate: function() {
			$gp.machine_translate.machine_translate( $( this ) );
			return false;
		}
	}
}}(jQuery);

jQuery( function( $ ) {
	$gp.machine_translate.init( $( '#translations' ) );
});
