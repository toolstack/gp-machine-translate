## 0.7
* Release date: January 6, 2016
* Documentation update.

## 0.6
* Release date: January 6, 2016
* Move the WP profile and settings hooks to before we check for the Google API key, otherwise you can never add one.
* Replace gp_redirect() with wp_redirect().
* Fixed incorrect function name wp_get_current_user_id() to be get_current_user_id();
* Added check for no strings to translate and return a better error than a Google API error.
* Updated the error id in WP_Error() call.Removed unused code from plugin.

## 0.5
* Release date: December 14, 2015
* Initial release.
