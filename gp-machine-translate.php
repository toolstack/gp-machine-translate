<?php

declare(strict_types=1);
/*
 * Plugin Name: GP Machine Translate
 * Plugin URI: http://glot-o-matic.com/gp-machine-translate
 * Description: Machine Translate plugin for GlotPress.
 * Version: 1.2
 * Author: Greg Ross
 * Author URI: http://toolstack.com
 * Tags: glotpress, glotpress plugin, translate, google, bing, yandex, microsoft
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gp-machine-translate
 * Requires PHP: 7.4
 */

use GpMachineTranslate\Providers\AbstractProvider;
use GpMachineTranslate\Providers\ProviderManager;
use GpMachineTranslate\Template;

class GP_Machine_Translate
{
    public $id = 'gp-machine-translate';

    private $languageCodeIsSupportedByProvider = false;

    private AbstractProvider $provider;

    private array $providerList;

    private ProviderManager $providerManager;

    private array $providersDisplayName;

    private string $selectedProviderIdentifier;

    private Template $template;

    private $version = '1.2';

    public function __construct()
    {
        $this->template = new Template();
    }

    // This function displays the admin settings page in WordPress.
    public function adminPage()
    {
        // If the current user can't manage options, display a message and return immediately.
        if (!current_user_can('manage_options')) {
            _e('You do not have permissions to this page!', 'gp-machine-translate');

            return;
        }

        // If the user has saved the settings, commit them to the database.
        if (array_key_exists('save_gp_machine_translate', $_POST)) {
            // Flush the global key, in case the user is removing the API key.
            $authKey = '';

            // If the API key value is being saved, store it in the global key setting.
            if (array_key_exists('gp_machine_translate_key', $_POST)) {
                // Make sure to sanitize the data before saving it.
                $authKey = sanitize_text_field($_POST['gp_machine_translate_key']);
            }

            // Flush the global client id, in case the user is removing the API client id.
            $authClientId = '';

            // If the client ID value is being saved, store it in the global key setting.
            if (array_key_exists('gp_machine_translate_client_id', $_POST)) {
                // Make sure to sanitize the data before saving it.
                $authClientId = sanitize_text_field($_POST['gp_machine_translate_client_id']);
            }

            $provider = $_POST['gp_machine_translate_provider'];

            if ($provider != __('*Select*', 'gp-machine-translate') && in_array($provider, $this->providerList, true)) {
                update_option('gp_machine_translate_provider', $provider);
                $this->selectedProviderIdentifier = $provider;
            }

            // Update the option in the database.
            update_option('gp_machine_translate_key', $authKey);

            // Update the client ID
            update_option('gp_machine_translate_client_id', $authClientId);

            $this->provider = $this->providerManager->updateOrCreateProviderInstance(
                $this->selectedProviderIdentifier,
                $authClientId,
                $authKey,
            );
        }

        echo $this->template->render(
            'admin-settings',
            [
                'provider' => $this->provider,
                'providerList' => $this->providerManager->getProviderIdentifiers(),
            ],
        );
    }

    // This function is here as placeholder to support adding the bulk translate option to the router.
    // Without this placeholder there is a fatal error generated.
    public function after_request()
    {
    }

    public function batchTranslate($locale, $strings)
    {
        if (is_object($locale)) {
            $locale = $locale->slug;
        }

        return $this->provider->batchTranslate($locale, $strings);
    }

    // This function is here as placeholder to support adding the bulk translate option to the router.
    // Without this placeholder there is a fatal error generated.
    public function before_request()
    {
    }

    // This function handles the actual bulk translate as passed in by the router for the projects menu.
    public function bulkTranslate($projectPath)
    {
        // First let's ensure we have decoded the project path for use later.
        $projectPath = urldecode($projectPath);

        // Get the URL to the project for use later.
        $url = gp_url_project($projectPath);

        // If we don't have rights, just redirect back to the project.
        if (!GP::$permission->user_can(wp_get_current_user(), 'write', 'project')) {
            wp_redirect($url);
        }

        // Create a project class to use to get the project object.
        $project_class = new GP_Project();

        // Get the project object from the project path that was passed in.
        $project_obj = $project_class->by_path($projectPath);

        // Get the translations sets from the project ID.
        $translation_sets = GP::$translation_set->by_project_id($project_obj->id);

        // Since there might be a lot of translations to process in a batch, let's setup some time limits
        // to make sure we don't give a white screen of death to the user.
        $time_start = microtime(true);
        $max_exec_time = ini_get('max_execution_time') * 0.7;

        // Loop through all the sets.
        foreach ($translation_sets as $set) {
            // Check to see how our time is doing, if we're over out time limit, stop processing.
            if (microtime(true) - $time_start > $max_exec_time) {
                gp_notice_set(__('Not all strings translated as we ran out of execution time!', 'gp-machine-translate'));

                break;
            }

            // Get the locale we're working with.
            $locale = GP_Locales::by_slug($set->locale);

            // If the current translation provider doesn't support this locale, skip it.
            if (!array_key_exists($locale->slug, $this->provider->getLocales())) {
                continue;
            }
            // Create a template array to pass in to the worker function at the end of the loop.
            $bulk = ['action' => 'gp_machine_translate', 'priority' => 0, 'row-ids' => []];

            // Create a new GP_Translation object to use.
            $translation = new GP_Translation();

            // Get the strings for the current translation.
            $strings = $translation->for_translation($project_obj, $set, 'no-limit', ['status' => 'untranslated']);

            // Add the strings to the $bulk template we setup earlier.
            foreach ($strings as $string) {
                $bulk['row-ids'][] .= $string->row_id;
            }

            // If we don't have any strings to translate, don't bother calling the translation function.
            if (count($bulk['row-ids']) > 0) {
                // Do the actual bulk translation.
                $this->gpTranslationSetBulkActionPost($project_obj, $locale, $set, $bulk);
            }
        }

        // Redirect back to the project home.
        wp_redirect($url);
    }

    // Generate the HTML when a user profile is edited.  Note the $user parameter is a full user object for this function.
    public function editUserProfile($user)
    {
        // Get the current user client id from the WordPress options table.
        $userAuthClientId = get_user_meta($user->ID, 'gp_machine_translate_client_id', true);

        // Get the current user key from the WordPress options table.
        $userAuthKey = get_user_meta($user->ID, 'gp_machine_translate_key', true);

        // If the user cannot edit their profile, then don't show the settings.
        if (!current_user_can('edit_user', $user->ID)) {
            return;
        }

        echo $this->template->render(
            'profile-settings',
            [
                'provider' => $this->provider,
                'userAuthClientId' => $userAuthClientId,
                'userAuthKey' => $userAuthKey,
            ],
        );
    }

    // Once a user profile has been edited, this function saves the settings to the WordPress options table.
    public function editUserProfileUpdate($user)
    {
        // Since the profile and user edit code is identical, just call the profile update code.
        $this->personalOptionsUpdate($user);
    }

    // This function adds the "Machine Translate" to the individual translation items.
    public function gpEntryActions($actions)
    {
        // Make sure we are currently on a supported locale.
        if ($this->languageCodeIsSupportedByProvider) {
            $actions[] = '<a href="#" class="gp_machine_translate" tabindex="-1">' . __('Machine Translate', 'gp-machine-translate') . '</a> (' . $this->providersDisplayName[$this->provider::IDENTIFIER] . ')';
        }

        return $actions;
    }

    // This function adds the "Machine Translate" option to the projects menu.
    public function gpProjectActions($actions, $project)
    {
        $actions[] .= gp_link_get(gp_url('bulk-translate/' . $project->slug), __('Machine Translate', 'gp-machine-translate') . ' (' . $this->providersDisplayName[$this->provider::IDENTIFIER] . ')');

        return $actions;
    }

    // This function adds the "Machine Translate" to the bulk actions dropdown in the translation set list.
    public function gpTranslationSetBulkAction()
    {
        // Make sure we are currently on a supported locale.
        if ($this->languageCodeIsSupportedByProvider) {
            echo '<option value="gp_machine_translate">' . __('Machine Translate', 'gp-machine-translate') . ' (' . strip_tags($this->providersDisplayName[$this->provider::IDENTIFIER]) . ')' . '</option>';
        }
    }

    // This function handles the actual bulk translation as passed in by the translation set list.
    public function gpTranslationSetBulkActionPost($project, $locale, $translation_set, $bulk)
    {
        // If we're not doing a bulk translation, just return.
        if ($bulk['action'] != 'gp_machine_translate') {
            return;
        }

        // Setup some variables to be used during the translation.
        $provider_errors = 0;
        $insert_errors = 0;
        $ok = 0;
        $skipped = 0;

        $singulars = [];
        $original_ids = [];

        // Loop through each of the passed in strings and translate them.
        foreach ($bulk['row-ids'] as $row_id) {
            // Split the $row_id by '-' and get the first one (which will be the id of the original).
            $original_id = gp_array_get(explode('-', $row_id), 0);
            // Get the original based on the above id.
            $original = GP::$original->get($original_id);

            // If there is no original or it's a plural, skip it.
            if (!$original || $original->plural) {
                ++$skipped;

                continue;
            }

            // Add the original to the queue to translate.
            $singulars[] = $original->singular;
            $original_ids[] = $original_id;
        }

        // Translate all the originals that we found.
        $results = $this->batchTranslate($locale, $singulars);

        // Did we get an error?
        if (is_wp_error($results)) {
            error_log(print_r($results, true));
            gp_notice_set($results->get_error_message(), 'error');

            return;
        }

        // Merge the results back in to the original id's and singulars, this will create an array like ($items = array( array( id, single, result), array( id, single, result), ... ).
        $items = gp_array_zip($original_ids, $singulars, $results);

        // If we have no items, something went wrong and stop processing.
        if (!$items) {
            return;
        }

        // Loop through the items and store them in the database.
        foreach ($items as $item) {
            // Break up the item back in to individual components.
            list($original_id, $singular, $translation) = $item;

            // Did we get an error?
            if (is_wp_error($translation)) {
                ++$provider_errors;
                error_log($translation->get_error_message());

                continue;
            }

            // Build a data array to store
            $data = compact('original_id');
            $data['user_id'] = get_current_user_id();
            $data['translation_set_id'] = $translation_set->id;
            $data['translation_0'] = $translation;
            $data['status'] = 'fuzzy';
            $data['warnings'] = GP::$translation_warnings->check($singular, null, [$translation], $locale);

            // Insert the item in to the database.
            $inserted = GP::$translation->create($data);
            $inserted ? $ok++ : $insert_errors++;
        }

        // Did we get an error?  If so let's let the user know about them.
        if ($provider_errors > 0 || $insert_errors > 0) {
            // Create a message array to use later.
            $message = [];

            // Did we have any strings translated successfully?
            if ($ok) {
                $message[] = sprintf(__('Added: %d.', 'gp-machine-translate'), $ok);
            }

            // Did we have any provider errors.
            if ($provider_errors) {
                $message[] = sprintf(__('Error from %s: %d.', 'gp-machine-translate'), $this->selectedProviderIdentifier, $provider_errors);
            }

            // Did we have any errors when we saved everything to the database?
            if ($insert_errors) {
                $message[] = sprintf(__('Error adding: %d.', 'gp-machine-translate'), $insert_errors);
            }

            // Did we skip any items?
            if ($skipped) {
                $message[] = sprintf(__('Skipped: %d.', 'gp-machine-translate'), $skipped);
            }

            // Create a message string and add it to the GlotPress notices.
            gp_notice_set(implode('', $message), 'error');
        } else {
            // If we didn't get any errors, then we just need to let the user know how many translations were added.
            gp_notice_set(sprintf(__('%d fuzzy translation from Machine Translate were added.', 'gp-machine-translate'), $ok));
        }
    }

    // Once a profile has been updated, this function saves the settings to the WordPress options table.
    public function personalOptionsUpdate($user_id)
    {
        // If the user cannot edit their profile, then don't save the settings
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        // Unlike the profile edit function, we only get the user id passed in as a parameter.
        update_user_meta($user_id, 'gp_machine_translate_key', sanitize_text_field($_POST['gp_machine_translate_user_key']));
        update_user_meta($user_id, 'gp_machine_translate_client_id', sanitize_text_field($_POST['gp_machine_translate_user_client_id']));
    }

    public function register()
    {
        // Load the plugin's translated strings.
        load_plugin_textdomain('gp-machine-translate');

        // Handle the WordPress user profile items
        add_action('show_user_profile', [$this, 'showUserProfile'], 10, 1);
        add_action('edit_user_profile', [$this, 'editUserProfile'], 10, 1);
        add_action('personal_options_update', [$this, 'personalOptionsUpdate'], 10, 1);
        add_action('edit_user_profile_update', [$this, 'editUserProfileUpdate'], 10, 1);

        // Add the admin page to the WordPress settings menu.
        add_action('admin_menu', [$this, 'registerAdminMenu'], 10, 1);

        if (get_option('gp_machine_translate_version', '0.7') != $this->version) {
            $this->upgrade();
        }

        $authClientId = get_option('gp_machine_translate_client_id', null);
        $authKey = get_option('gp_machine_translate_key', null);

        // Check to see if there is a user currently logged in.
        if (is_user_logged_in()) {
            // If someone is logged in, get their user object.
            $user = wp_get_current_user();

            // Load the user translate key from the WordPress user meta table, using the currently logged in user id.
            $userAuthClientId = get_user_meta($user->ID, 'gp_machine_translate_client_id', true);
            $userAuthKey = get_user_meta($user->ID, 'gp_machine_translate_key', true);

            // If there is a user key, override the global key.
            if ($userAuthKey) {
                $authClientId = $userAuthClientId;
                $authKey = $userAuthKey;
            }
        }

        $this->providerManager = new ProviderManager($authClientId, $authKey);
        $this->selectedProviderIdentifier = get_option('gp_machine_translate_provider', 'DeepL');
        $this->providerList = $this->providerManager->getProviderIdentifiers();
        $this->providersDisplayName = $this->providerManager->getProvidersDisplayName();
        $this->provider = $this->providerManager->getOrCreateProviderInstance($this->selectedProviderIdentifier);

        // If the provider is not set up, the system will not proceed.
        if (!$this->provider->isSetUp()) {
            return;
        }

        wp_register_script('gp-machine-translate-js', plugins_url('gp-machine-translate.js', __FILE__), ['jquery', 'editor', 'gp-common']);

        // If the user has write permissions to the projects, add the bulk translate option to the projects menu.
        if (GP::$permission->user_can(wp_get_current_user(), 'write', 'project')) {
            add_action('gp_project_actions', [$this, 'gpProjectActions'], 10, 2);
        }

        // Add the actions to handle adding the translate menu to the various parts of GlotPress.
        add_action('gp_pre_tmpl_load', [$this, 'registerJavaScript'], 10, 2);
        add_filter('gp_entry_actions', [$this, 'gpEntryActions'], 10, 1);
        add_action('gp_translation_set_bulk_action', [$this, 'gpTranslationSetBulkAction'], 10, 1);
        add_action('gp_translation_set_bulk_action_post', [$this, 'gpTranslationSetBulkActionPost'], 10, 4);

        // We can't use the filter in the defaults route code because plugins don't load until after
        // it has already run, so instead add the routes directly to the global GP_Router object.
        GP::$router->add('/bulk-translate/(.+?)', [$this, 'bulkTranslate'], 'get');
        GP::$router->add('/bulk-translate/(.+?)', [$this, 'bulkTranslate'], 'post');
    }

    // This function adds the admin settings page to WordPress.
    public function registerAdminMenu()
    {
        add_options_page(
            __('GP Machine Translate', 'gp-machine-translate'),
            __('GP Machine Translate', 'gp-machine-translate'),
            'manage_options',
            basename(__FILE__),
            [$this, 'adminPage'],
        );
    }

    // This function loads the javascript when required.
    public function registerJavaScript($template, $args)
    {
        // If we don't have a translation key, just return without doing anything.
        if (!isset($this->provider) || $this->provider->isSetUp() === false) {
            return;
        }

        // If we're not on the translation template, just return without doing anything.
        if ($template != 'translations') {
            return;
        }

        // If the current locale isn't supported by the translation provider, just return without doing anything.
        if (!array_key_exists($args['locale']->slug, $this->provider->getLocales())) {
            return;
        }

        // Create options for the localization script.
        $options = [
            'locale' => $this->provider->getLocales()[$args['locale']->slug],
            'ajaxurl' => admin_url('admin-ajax.php'),
        ];

        // Set the current Google code to the locale we're dealing with.
        $this->languageCodeIsSupportedByProvider = isset($this->provider->getLocales()[$args['locale']->slug]);

        // Enqueue the translation JavaScript and translate it.
        gp_enqueue_script('gp-machine-translate-js');
        wp_localize_script('gp-machine-translate-js', 'gp_machine_translate', $options);
    }

    // Generate the HTML when a user views their profile.
    public function showUserProfile($user)
    {
        // Show and edit are virtually identical, so just call the edit function.
        $this->editUserProfile($user);
    }

    private function upgrade()
    {
        global $wpdb;

        // If the old google key exists, update it to the new option name and remove it.
        // On the next upgrade this code will not run.
        // To be removed in a future version once we're well past version 0.7.
        if (get_option('gp_google_translate_key', false) !== false) {
            // Rename the global translation key name.
            update_option('gp_machine_translate_key', get_option('gp_google_translate_key', false));
            delete_option('gp_google_translate_key');
        }

        // Rename the per use translation key name.  We can't do this in the "if" above as the global key
        // may be set to blank but user keys may still exist, so we have to do this on each upgrade.
        // To be removed in a future version once we're well past version 0.7.
        $wpdb->query("UPDATE {$wpdb->usermeta} SET `meta_key`='gp_machine_translate_key' WHERE `meta_key`='gp_google_translate_key';");

        // Update the version option to the current version so we don't run the upgrade process again.
        update_option('gp_machine_translate_version', $this->version);

        // If trasltr is enabled, disable it as it is no longer supported.
        if (get_option('gp_machine_translate_provider') == 'transltr.org') {
            update_option('gp_machine_translate_provider', '');
        }
    }
}

// Add an action to WordPress's init hook to setup the plugin. Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action('gp_init', 'gp_machine_translate_init');

require_once rtrim(plugin_dir_path(__FILE__), '/') . '/vendor/autoload.php';

require_once rtrim(plugin_dir_path(__FILE__), '/') . '/ajax.php';

// This function creates the plugin.
function gp_machine_translate_init()
{
    global $gp_machine_translate;

    $gp_machine_translate = new GP_Machine_Translate();
    $gp_machine_translate->register();
}
