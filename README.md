# GlotPress Google Translate
A Google Translate plugin for [GlotPress as a WordPress plugin](https://github.com/deliciousbrains/GlotPress).

Google translate is a pay service, you must acquire an API key from Google and setup a payment option with them.

Login to your [Google developers console](http://console.developer.google.com) and select "APIs & auth"->Credentials.  Then create a new "Public API access" Key.  Use this key as below to configure access.

Once you have the Google API key, you can set the key for all users or a specific user.

To set it for all users, go to the WordPress Dashboard, then Settings, then "GlotPress Google Translate" and set the API key.

To set if for a specific user, go to the users profile and scroll down to the "GlotPress Google Translate" section and set the API key.

Note, if both a global and user API key are set, the user API key will override the global API key.

# Converstion Notes
Converting from the standalone GlotPress plugin system to the GlotPress as a WordPress plugin is relativly straight forward.

1. Add a WordPress plugin header block to the top of your plugin file.
2. Any add_action() or add_filters() calls you have must be converted to the WordPress add_action()/add_filter() format.
3. Double check your GlotPress action/filters names, these have now been prefixed with 'gp_'.
4. If your using a define() to enable/disable your plugin, remove it, using the activeate/deactivate functionality in WordPress's plugin system is the way to go.
5. If your using warnings/errors, replace them with the appropriate gp_notice_set() call ( gp_notice_set( $string ) for warnings, gp_notice_set( $string, 'error' ) for errors).
6. Replace your initalization of the plugin with a add_action( 'init', 'gp_google_translate_init' ) call.
7. In your init action, make sure GP is loaded before loading your code with a 'if( defined( 'GP_VERSION' ) )' block.
8. Create a readme.

