<?php
/*
Plugin Name: GlotPress Google Translate
Plugin URI: http://glotpress.org/
Description: Google Translate plugin for GlotPress.
Version: 0.1
Author: GlotPress
Author URI: http://glotpress.org
Tags: glotpress, glotpress plugin, translate, google 
License: GPLv2 or later
*/

class GP_Google_Translate {
	public $id = 'google-translate';

	private $key;
	private $google_code = false;

	public function __construct() {
		$this->key = get_option('gp_google_translate_key');
		
		if (GP::$user->logged_in()) {
			$user_obj = GP::$user->current();
			
			$user_key = get_option('gp_google_translate_key_' . strtolower($user_obj->user_login));
			if( $user_key ) { $this->key = $user_key; }
		}

		if( false === $this->key ) { return; }
		
		if( GP::$user->current()->can( 'write', 'project' ) ) {
			add_action( 'gp_project_actions', array( $this, 'gp_project_actions'), 10, 2 );
		}
		
		add_action( 'pre_tmpl_load', array( $this, 'pre_tmpl_load'), 10, 2);
		add_filter( 'gp_entry_actions', array( $this, 'gp_entry_actions' ), 10, 1 );
		add_action( 'gp_translation_set_bulk_action', array( $this, 'gp_translation_set_bulk_action'), 10, 1); 
		add_action( 'gp_translation_set_bulk_action_post', array( $this, 'gp_translation_set_bulk_action_post'), 10, 4);

		// Handle the user profile items
		add_action( 'show_user_profile', array( $this, 'show_user_profile' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ), 10, 1 );
		add_action( 'personal_options_update', array( $this, 'personal_options_update' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ), 10, 1 );
		
		// Add the admin page to the settings menu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10, 1 );

		// We can't use the filter in the defaults route code because plugins don't load until after
		// it has already run, so instead add the routes directly to the global GP_Router object.
		GP::$router->add( "/bulk-translate/(.+?)", array( $this, 'bulk_translate' ), 'get' );
		GP::$router->add( "/bulk-translate/(.+?)", array( $this, 'bulk_translate' ), 'post' );
	}
	
	public function show_user_profile( $user ) {
		$this->edit_user_profile( $user );
	}
	
	public function edit_user_profile( $user ) {
		$user_key = get_option( 'gp_google_translate_key_' . $user->user_login );
		
		// If the user cannot edit their profile, then don't show the settings
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
?>
	<h3 id=\"gp-google-translate\"><?php _e('GlotPress Google Translate'); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="gp_google_translate_user_key"><?php _e('User Google API Key');?></label></th>
			<td>
			<input type="text" id="gp_google_translate_user_key" name="gp_google_translate_user_key" size="40" value="<?php echo htmlentities($user_key);?>">
			<p class="description"><?php _e('Enter the Google API key for this user.');?></p>
			</td>
		</tr>
	</table>
<?php		
		
	}
	
	public function personal_options_update( $user_id ) {
		// If the user cannot edit their profile, then don't save the settings
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		
		$user = get_user_by( 'id', $user_id );
		update_option( 'gp_google_translate_key_' . $user->user_login,  sanitize_text_field( $_POST['gp_google_translate_user_key'] ) );
	}
	
	public function edit_user_profile_update( $user ) {
		$this->personal_options_update( $user );
	}
	
	public function gp_project_actions( $actions, $project ) {
		$actions[] .= gp_link_get( gp_url( 'bulk-translate/' . $project->slug), __('Bulk Google Translate') );
		
		return $actions;
	}
	
	public function before_request() {
	}
	
	public function bulk_translate( $project_path ) {
		$project_path = urldecode( $project_path );
		$url = gp_url_project( $project_path );

		// If we don't have rights, just redirect back to the project.
		if( !GP::$user->current()->can( 'write', 'project' ) ) {
			gp_redirect( $url );
		}

		// Create a project class to use to get the project object.
		$project_class = new GP_Project;
		
		// Get the project object from the project path that was passed in.
		$project_obj = $project_class->by_path( $project_path );
		
		// Get the translations sets from the project ID.
		$translation_sets = GP::$translation_set->by_project_id( $project_obj->id );

		// Loop through all the sets.
		foreach( $translation_sets as $set ) {
			//Array ( [action] => gtranslate [priority] => 0 [redirect_to] => http://localhost/wp40/gp/projects/sample/bg/my [row-ids] => Array ( [0] => 1 [1] => 2 ) ) 
			$bulk = array( 'action' => 'gtranslate', 'priority' => 0, 'row-ids' => array() );
			
			$translation = new GP_Translation;
			
			$strings = $translation->for_translation( $project_obj, $set, null, array( 'status' => 'untranslated') );

			foreach( $strings as $string ) {
				$bulk['row-ids'][] .= $string->row_id;
			}
			
			$locale = GP_Locales::by_slug( $set->locale );
			
			$this->gp_translation_set_bulk_action_post( $project_obj, $locale, $set, $bulk );
		}

		$url = gp_url_project( $project_path );
		gp_redirect( $url );
	}

	public function after_request() {
	}
	
	public function pre_tmpl_load( $template, $args ) {
		if (GP::$user->logged_in()) {
			$user_obj = GP::$user->current();
			
			$user = strtoupper($user_obj->user_login);

			$user_key = get_option('gp_google_translate_key_'.$user);
			if( $user_key ) { $this->key = $user_key; }
		}

		if( ! $this->key ) {
			return;
		}
		
		if ( 'translations' != $template ) {
			return;
		}

		if ( ! $args['locale']->google_code ) {
			return;
		}

		$url = gp_url_public_root();

		if ( is_ssl() ) {
			$url = gp_url_ssl( $url );
		}

		$options = array(
			'key'    => $this->key,
			'locale' => $args['locale']->google_code
		);
		
		$this->google_code = $args['locale']->google_code;

		wp_enqueue_script( 'gp-google-translate', plugins_url( 'gp-google-translate.js', __FILE__ ), array( 'jquery', 'editor' ) );
		wp_localize_script( 'gp-google-translate', 'gp_google_translate', $options );

	}

	public function gp_entry_actions( $actions ) {
		if ( $this->google_code ) {
			$actions[] = '<a href="#" class="gtranslate" tabindex="-1">' . __('Translation from Google') . '</a>';
		}

		return $actions;
	}


	public function gp_translation_set_bulk_action() {
		if ( $this->google_code ) {
			echo '<option value="gtranslate">' . __('Translate via Google') . '</option>';
		}
	}

	public function gp_translation_set_bulk_action_post( $project, $locale, $translation_set, $bulk ) {
		if ( 'gtranslate' != $bulk['action'] ) {
			return;
		}

		if (GP::$user->logged_in() && ! $this->key) {
			$user_obj = GP::$user->current();
			
			$user = strtoupper($user_obj->user_login);

			$user_key = gp_const_get('GP_GOOGLE_TRANSLATE_KEY_'.$user);
			if( $user_key ) { $this->key = $user_key; }
		}

		if( ! $this->key ) {
			return;
		}
		
		$google_errors = 0;
		$insert_errors = 0;
		$ok      = 0;
		$skipped = 0;

		$singulars = array();
		$original_ids = array();

		foreach ( $bulk['row-ids'] as $row_id ) {
			if ( gp_in( '-', $row_id ) ) {
				$skipped++;
				continue;
			}

			$original_id = gp_array_get( explode( '-', $row_id ), 0 );
			$original    = GP::$original->get( $original_id );

			if ( ! $original || $original->plural ) {
				$skipped++;
				continue;
			}

			$singulars[] = $original->singular;
			$original_ids[] = $original_id;
		}

		$results = $this->google_translate_batch( $locale, $singulars );

		if ( is_wp_error( $results ) ) {
			error_log( print_r( $results, true ) );
			gp_notice_set( $results->get_error_message(), 'error' );
			return;

		}

		$items = gp_array_zip( $original_ids, $singulars, $results );

		if ( ! $items ) {
			return;
		}

		foreach ( $items as $item ) {
			list( $original_id, $singular, $translation ) = $item;

			if ( is_wp_error( $translation ) ) {
				$google_errors++;
				error_log( $translation->get_error_message() );
				continue;
			}

			$data = compact( 'original_id' );
			$data['user_id'] = GP::$user->current()->id;
			$data['translation_set_id'] = $translation_set->id;
			$data['translation_0'] = $translation;
			$data['status'] = 'fuzzy';
			$data['warnings'] = GP::$translation_warnings->check( $singular, null, array( $translation ), $locale );

			$inserted = GP::$translation->create( $data );
			$inserted? $ok++ : $insert_errors++;
		}

		if ( $google_errors > 0 || $insert_errors > 0 ) {
			$message = array();

			if ( $ok ) {
				$message[] = sprintf( __('Added: %d.' ), $ok );
			}

			if ( $google_errors ) {
				$message[] = sprintf( __('Error from Google Translate: %d.' ), $google_errors );
			}

			if ( $insert_errors ) {
				$message[] = sprintf( __('Error adding: %d.' ), $insert_errors );
			}

			if ( $skipped ) {
				$message[] = sprintf( __('Skipped: %d.' ), $skipped );
			}

			gp_notice_set( implode( '', $message ), 'error' );
		}
		else {
			gp_notice_set( sprintf( __('%d fuzzy translation from Google Translate were added.' ), $ok ) );
		}
	}

	public function google_translate_batch( $locale, $strings ) {
		if ( ! $locale->google_code ) {
			return new WP_Error( 'google_translate', sprintf( "The locale %s isn't supported by Google Translate.", $locale->slug ) );
		}

		$url = 'https://www.googleapis.com/language/translate/v2?key=' . $this->key . '&source=en&target=' . urlencode( $locale->google_code );

		foreach ( $strings as $string ) {
			$url .= '&q=' . urlencode( $string );
		}

		if ( count( $strings ) == 1 ) {
			$url .= '&q=';
		}

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$json = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! $json ) {
			return new WP_Error( 'google_translate', 'Error decoding JSON from Google Translate.' );
		}

		if ( isset( $json->error ) ) {
			return new WP_Error( 'google_translate', sprintf( 'Error auto-translating: %1$s', $json->error->errors[0]->message ) );
		}

		$translations = array();

		if ( ! is_array( $json->data->translations ) ) {
			$json->data->translations = array( $json->data->translations );
		}

		$items = gp_array_zip( $strings, $json->data->translations );

		if ( ! $items ) {
			return new WP_Error( 'google_translate', 'Error merging arrays' );
		}

		foreach ( $items as $item ) {
			list( $string, $translation ) = $item;

			$translations[] = $this->google_translate_fix( $translation->translatedText );
		}

		return $translations;
	}

	public function google_translate_fix( $string ) {
		$string = preg_replace_callback( '/% (s|d)/i', function ($m) { return '"%".strtolower($m[1])'; }, $string );
		$string = preg_replace_callback( '/% (\d+) \$ (s|d)/i', function ($m) { return '"%".$m[1]."\\$".strtolower($m[2])'; }, $string );
		return $string;
	}
	
	public function admin_menu() {
		add_options_page( __('GlotPress Google Translate'), __('GlotPress Google Translate'), 'manage_options', basename( __FILE__ ), array( $this, 'admin_page' ) );
	}
	
	public function admin_page() {
		if( ! current_user_can( 'manage_options' ) ) { _e('You do not have permissions to this page!'); return; }
		
		if( array_key_exists( 'save_gp_google_transalate', $_POST ) ) {
			$this->key = '';
			
			if( array_key_exists( 'gp_google_translate_key', $_POST ) ) {
				$this->key = sanitize_text_field( $_POST['gp_google_translate_key'] );
			}	
				
			update_option( 'gp_google_translate_key', $this->key );
		}

		
	?>	
<div class="wrap">
	<h2><?php _e('GlotPress Google Translate Settings');?></h2>

	<form method="post" action="options-general.php?page=gp-google-translate.php" >	
		<table class="form-table">
			<tr>
				<th><label for="gp_google_translate_key"><?php _e('Global Google API Key');?></label></th>
				<td>
				<input type="text" id="gp_google_translate_key" name="gp_google_translate_key" size="40" value="<?php echo htmlentities($this->key);?>">
				<p class="description"><?php _e('Enter the Google API key for all users (leave blank to disable, per user API keys will still function).');?></p>
				</td>
			</tr>
		</table>
		
		<?php submit_button( __('Save'), 'primary', 'save_gp_google_transalate' ); ?>
		
	</form>
	
</div>
<?php		
	}
}

// may need to use plugins_loaded.
add_action( 'init', 'gp_google_translate_init' );

function gp_google_translate_init() {
	GLOBAL $gp_google_translate;
	
	$gp_google_translate = new GP_Google_Translate;
	
}