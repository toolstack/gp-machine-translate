## 1.0
* Release date: November 15, 2016
* Added timeout for batch translations to help prevent the white screen of death.
* Added translation domain and translation support.
* Fixed support for translation engines that don't need a key.

## 0.9.5
* Release date: October 28, 2016
* Multiple fixes related to proper detection of support locales and bulk translation.

## 0.9
* Release date: October 21, 2016
* Fix Google Translate not recognizing supported locales.

## 0.8
* Release date: September 1, 2016
* Extract all Google logic from the code to allow for multiple translation services to be supported.
* Added Microsoft Translator.
* Added Yandex.Translate.
* Added transltr.org.

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
