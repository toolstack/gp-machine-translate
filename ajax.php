<?php

// Setup an AJAX action to close the donation nag banner on the overview page.
function gp_machine_translate_action_callback() {
	GLOBAL $gp_machine_translate; 

	if( ! is_set( $gp_machine_translsate ) ) {
		wp_die();
	}

	$translations = array( 'translatedText' => 'text' );
	
	echo json_encode( $translations );
	
	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_gp_machine_translate', 'gp_machine_translate_action_callback' );

