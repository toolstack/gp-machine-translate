<?php

// Setup an AJAX action to close the donation nag banner on the overview page.
function gp_machine_translate_action_callback() {
	GLOBAL $gp_machine_translate; 

	if( ! isset( $gp_machine_translate ) ) {
		wp_send_json( array( 'success' => false, 'message' => 'GlotPress not yet loaded.' ) );
	}

	$locale = $_POST['locale'];
	$strings = array( $_POST['original'] );
	
	$new_string = $gp_machine_translate->translate_batch( $locale, $strings );
	
	$translations = array( 'success' => true, 'data' => array( 'translatedText' => $new_string ) );
	
	wp_send_json( $translations );
}
add_action( 'wp_ajax_gp_machine_translate', 'gp_machine_translate_action_callback' );

