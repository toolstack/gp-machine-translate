# GP Machine Translate #
**Contributors:** [gregross](https://profiles.wordpress.org/gregross/)  
**Donate link:** http://toolstack.com/donate  
**Plugin URI:** http://glot-o-matic.com/gp-machine-translate  
**Author URI:** http://toolstack.com  
**Tags:** glotpress, glotpress plugin, translate, google, bing, yandex, microsoft, transltr, deepl  
**Requires at least:** 4.4  
**Tested up to:** 6.4  
**Stable tag:** 2.0  
**License:** GPLv2  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  
**Requires PHP:** 7.4  

A machine translate plugin for GlotPress as a WordPress plugin.

## Description ##

A machine translate plugin for [GlotPress as a WordPress plugin](https://github.com/GlotPress/GlotPress).

Four machine translation providers are supported:

* DeepL Free (500k character free limit per month with account, but no cost)
* DeepL Pro (pay per character, Pro account required)
* Google Translate (pay per character)
* Microsoft Translator (free tier available)
* Yandex.Translate (free but requires Yandex account)

Note: This plugin assumes the source language is English as support for automated translation from other source languages is limited.

### Configuration ###

Once you have installed GP Machine Translate, go to your WordPress admin screen and select "Settings > GP Machine Translate".

You will have four fields to configure:

	1. Translation Provider
	2. Display extra info
	3. Global API Key
	4. Client ID

You can select from providers with some requiring the additional fields to be filled in.

### DeepL Free ###

DeepL has a free tier that allows you to access the API for 500k characters per month.  Additional characters require a DeepL API Pro (aka paid) account.

* Login/signup [DeepL API](https://www.deepl.com/)
* Go to your account and scroll down to [Authentication Key for DeepL API](https://www.deepl.com/pro-account/summary)
* Copy the `Authentication Key` and put it into `Global API Key` of GP Machine Translate.

Note also that DeepL allows for a maximum of 50 strings to be translated at once, so keep that in mind when doing bulk translations and only selected at most 50 strings.

### DeepL Pro ###

DeepL has a Pro tier that allows you to access the API on a pay per character basis.

* Login/signup [DeepL API](https://www.deepl.com/)
* Go to your account and scroll down to [Authentication Key for DeepL API](https://www.deepl.com/pro-account/summary)
* Copy the `Authentication Key` and put it into `Global API Key` of GP Machine Translate.

Note also that DeepL allows for a maximum of 50 strings to be translated at once, so keep that in mind when doing bulk translations and only selected at most 50 strings.

### Google ###

Google Translate requires an API key to function, to do this you must register with Google and provide a payment method.

* Login/signup your [Google developers console](http://console.developer.google.com)
* Select "APIs & auth"->Credentials.
* Create a new "Public API access" key.

The public access key is what you will use to configure GP Machine Translate with, either for all users or a specific user.

### Microsoft Translator ###

Microsoft Translator requires an API key to function, to do this you must register with Microsoft.  Microsoft does have a free tier for translation, limited to 2 million characters a month so you do not need to provide payment details for this tier.

Microsoft has a walk through on how to subscribe to the Translator service here:

	https://www.microsoft.com/en-us/translator/getstarted.aspx

You will need both the client secret and client id for it to function with GP Machine Translate.

### transltr.org ###

This service no longer exists and has been removed from the plugin.

### Yandex.Translate ###

Yandex.Translate is a free service, however you must sign up to their service and adhere to their terms of service.  This includes providing a link back to the service for translated text.

One other thing to note with Yandex.Translate is that when you sign up, you get a Yandex e-mail address and other services they provide and there is no option to opt out of them.

To get an API key, follow the instructions here:

	https://tech.yandex.com/translate/

[Powered by Yandex.Translate](http://translate.yandex.com/).

### Setting the API key ###

To set the API key for all users, go to the WordPress Dashboard, then Settings, then "GP Machine Translate" and set the API key (and Client ID if required).

To set if for a specific user, go to the users profile and scroll down to the "GP Machine Translate" section and set the API key (and Client ID if required).

Note, if both a global and user API key are set, the user API key will override the global API key.

### Supported Languages by Provider ###

| DeepL                 | DeepL Pro             | Google Translate      | Microsoft Translator  | Yandex.Translate      |
|-----------------------|-----------------------|-----------------------|-----------------------|-----------------------|
| Arabic                | Arabic                | Afrikaans             | Afrikaans             | Catalan               |
| Bulgarian             | Bulgarian             | Albanian              | Arabic                | Czech                 |
| Czech                 | Czech                 | Arabic                | Bosnian               | Danish                |
| Danish                | Danish                | Armenian              | Bulgarian             | Dutch                 |
| Dutch                 | Dutch                 | Azerbaijani           | Catalan               | Estonian              |
| English               | English               | Basque                | Croatian              | Finnish               |
| Estonian              | Estonian              | Bosnian               | Czech                 | French (France)       |
| Finnish               | Finnish               | Bulgarian             | Danish                | German                |
| French (France)       | French (France)       | Catalan               | Dutch                 | Greek                 |
| German                | German                | Croatian              | Estonian              | Hungarian             |
| Greek                 | Greek                 | Czech                 | Finnish               | Italian               |
| Hungarian             | Hungarian             | Danish                | French (France)       | Latvian               |
| Indonesian            | Indonesian            | Dutch                 | German                | Lithuanian            |
| Italian               | Italian               | English               | Greek                 | Macedonian            |
| Japanese              | Japanese              | Esperanto             | Hebrew                | Norwegian             |
| Korean                | Korean                | Estonian              | Hindi                 | Portuguese (Portugal) |
| Latvian               | Latvian               | Finnish               | Hungarian             | Russian               |
| Lithuanian            | Lithuanian            | French (France)       | Indonesian            | Slovak                |
| Norwegian             | Norwegian             | Galician              | Italian               | Slovenian             |
| Norwegian (Bokmål)    | Norwegian (Bokmål)    | Georgian              | Japanese              | Spanish (Spain)       |
| Polish                | Polish                | German                | Klingon               | Swedish               |
| Portuguese (Portugal) | Portuguese (Portugal) | Greek                 | Korean                | Turkish               |
| Romanian              | Romanian              | Gujarati              | Latvian               | Ukrainian             |
| Russian               | Russian               | Hausa (Arabic)        | Lithuanian            |                       |
| Slovak                | Slovak                | Hebrew                | Malay                 |                       |
| Slovenian             | Slovenian             | Hindi                 | Norwegian             |                       |
| Spanish (Spain)       | Spanish (Spain)       | Hungarian             | Persian               |                       |
| Swedish               | Swedish               | Icelandic             | Polish                |                       |
| Turkish               | Turkish               | Indonesian            | Portuguese (Portugal) |                       |
| Ukrainian             | Ukrainian             | Irish                 | Romanian              |                       |
|                       |                       | Italian               | Russian               |                       |
|                       |                       | Japanese              | Serbian               |                       |
|                       |                       | Javanese              | Slovak                |                       |
|                       |                       | Kannada               | Slovenian             |                       |
|                       |                       | Kazakh                | Spanish (Spain)       |                       |
|                       |                       | Khmer                 | Swahili               |                       |
|                       |                       | Korean                | Swedish               |                       |
|                       |                       | Lao                   | Thai                  |                       |
|                       |                       | Latin                 | Turkish               |                       |
|                       |                       | Latvian               | Ukrainian             |                       |
|                       |                       | Lithuanian            | Urdu                  |                       |
|                       |                       | Macedonian            | Vietnamese            |                       |
|                       |                       | Malagasy              | Welsh                 |                       |
|                       |                       | Malay                 |                       |                       |
|                       |                       | Malayalam             |                       |                       |
|                       |                       | Maori                 |                       |                       |
|                       |                       | Marathi               |                       |                       |
|                       |                       | Mongolian             |                       |                       |
|                       |                       | Nepali                |                       |                       |
|                       |                       | Norwegian             |                       |                       |
|                       |                       | Norwegian (Bokmål)    |                       |                       |
|                       |                       | Norwegian (Nynorsk)   |                       |                       |
|                       |                       | Panjabi (India)       |                       |                       |
|                       |                       | Persian               |                       |                       |
|                       |                       | Polish                |                       |                       |
|                       |                       | Portuguese (Portugal) |                       |                       |
|                       |                       | Romanian              |                       |                       |
|                       |                       | Russian               |                       |                       |
|                       |                       | Scottish Gaelic       |                       |                       |
|                       |                       | Serbian               |                       |                       |
|                       |                       | Sinhala               |                       |                       |
|                       |                       | Slovak                |                       |                       |
|                       |                       | Slovenian             |                       |                       |
|                       |                       | Somali                |                       |                       |
|                       |                       | Spanish (Spain)       |                       |                       |
|                       |                       | Sundanese             |                       |                       |
|                       |                       | Swahili               |                       |                       |
|                       |                       | Swedish               |                       |                       |
|                       |                       | Tagalog               |                       |                       |
|                       |                       | Tajik                 |                       |                       |
|                       |                       | Tamil                 |                       |                       |
|                       |                       | Telugu                |                       |                       |
|                       |                       | Thai                  |                       |                       |
|                       |                       | Turkish               |                       |                       |
|                       |                       | Ukrainian             |                       |                       |
|                       |                       | Urdu                  |                       |                       |
|                       |                       | Uzbek                 |                       |                       |
|                       |                       | Vietnamese            |                       |                       |
|                       |                       | Welsh                 |                       |                       |
|                       |                       | Yiddish               |                       |                       |
|                       |                       | Yoruba                |                       |                       |

## Changelog ##
### 2.0 ###
* Release date: November 10, 2024
* Major rewrite of translation provider code provided by @BenBornschein, thanks!
* Added option to display a supported/not supported message to the project locales list (disabled by default, go to settings to enable).

### 1.2 ###
* Release date: March 27, 2024
* Fixed GlotPress 3+ compatibility
* Removed defunct transltr.org
* Updated DeepL details and functionality

### 1.1 ###
* Release date: January 16, 2022
* Added DeepL support, thanks @Borlabs-Ben.

### 1.0 ###
* Release date: November 15, 2016
* Added timeout for batch translations to help prevent the white screen of death.
* Added translation domain and translation support.
* Fixed support for translation engines that don't need a key.

### 0.9.5 ###
* Release date: October 28, 2016
* Multiple fixes related to proper detection of support locales and bulk translation.

### 0.9 ###
* Release date: October 21, 2016
* Fix Google Translate not recognizing supported locales.

### 0.8 ###
* Release date: September 1, 2016
* Extract all Google logic from the code to allow for multiple translation services to be supported.
* Added Microsoft Translator.
* Added Yandex.Translate.
* Added transltr.org.

### 0.7 ###
* Release date: January 6, 2016
* Documentation update.

### 0.6 ###
* Release date: January 6, 2016
* Move the WP profile and settings hooks to before we check for the Google API key, otherwise you can never add one.
* Replace gp_redirect() with wp_redirect().
* Fixed incorrect function name wp_get_current_user_id() to be get_current_user_id();
* Added check for no strings to translate and return a better error than a Google API error.
* Updated the error id in WP_Error() call.Removed unused code from plugin.

### 0.5 ###
* Release date: December 14, 2015
* Initial release.
