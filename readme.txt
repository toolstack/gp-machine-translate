=== GP Machine Translate ===
Contributors: GregRoss
Donate link: http://toolstack.com/donate
Plugin URI: http://glot-o-matic.com/gp-machine-translate
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin, translate, google, bing, yandex, microsoft, transltr, deepl
Requires at least: 4.4
Tested up to: 6.4
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A machine translate plugin for GlotPress as a WordPress plugin.

== Description ==

A machine translate plugin for [GlotPress as a WordPress plugin](https://github.com/GlotPress/GlotPress).

Four machine translation providers are supported (though only one at a time):

* DeepL (pay per character, Pro account required)
* Google Translate (pay per character)
* Microsoft Translator (free tier available)
* Yandex.Translate (free but requires Yandex account)

Note: This plugin assumes the source language is English as support for automated translation from other source languages is limited.

= Configuration =

Once you have installed GP Machine Translate, go to your WordPress admin screen and select "Settings > GP Machine Translate".

You will have three Fields to configure:

	1. Translation Provider
	2. Global API Key
	3. Client ID

You can select from five providers with some requiring the additional fields to be filled in.

= DeepL =

DeepL has a free tier that allows you to access the API for 500k characters per month.  Additional characters require a DeepL API Pro (aka paid) account.

* Login/signup [DeepL API](https://www.deepl.com/)
* Go to your account and scroll down to [Authentication Key for DeepL API](https://www.deepl.com/pro-account/summary)
* Copy the `Authentication Key` and put it into `Global API Key` of GP Machine Translate.

Note also that DeepL allows for a maximum of 50 strings to be translated at once, so keep that in mind when doing bulk translations and only selected at most 50 strings.

= Google =

Google Translate requires an API key to function, to do this you must register with Google and provide a payment method.

* Login/signup your [Google developers console](http://console.developer.google.com)
* Select "APIs & auth"->Credentials.
* Create a new "Public API access" key.

The public access key is what you will use to configure GP Machine Translate with, either for all users or a specific user.

= Microsoft Translator =

Microsoft Translator requires an API key to function, to do this you must register with Microsoft.  Microsoft does have a free tier for translation, limited to 2 million characters a month so you do not need to provide payment details for this tier.

Microsoft has a walk through on how to subscribe to the Translator service here:

	https://www.microsoft.com/en-us/translator/getstarted.aspx

You will need both the client secret and client id for it to function with GP Machine Translate.

= transltr.org =

This service no longer exists and has been removed from the plugin.

= Yandex.Translate =

Yandex.Translate is a free service, however you must sign up to their service and adhere to their terms of service.  This includes providing a link back to the service for translated text.

One other thing to note with Yandex.Translate is that when you sign up, you get a Yandex e-mail address and other services they provide and there is no option to opt out of them.

To get an API key, follow the instructions here:

	https://tech.yandex.com/translate/

[Powered by Yandex.Translate](http://translate.yandex.com/).

= Setting the API key =

To set the API key for all users, go to the WordPress Dashboard, then Settings, then "GP Machine Translate" and set the API key (and Client ID if required).

To set if for a specific user, go to the users profile and scroll down to the "GP Machine Translate" section and set the API key (and Client ID if required).

Note, if both a global and user API key are set, the user API key will override the global API key.

= Supported Languages by Provider =

| DeepL                 | Google Translate      | Microsoft Translator  | Yandex.Translate      |
|-----------------------|-----------------------|-----------------------|-----------------------|
| Arabic                | Afrikaans             | Afrikaans             | Catalan               |
| Bulgarian             | Albanian              | Arabic                | Czech                 |
| Czech                 | Arabic                | Bosnian               | Danish                |
| Danish                | Armenian              | Bulgarian             | Dutch                 |
| Dutch                 | Azerbaijani           | Catalan               | Estonian              |
| English               | Basque                | Croatian              | Finnish               |
| Estonian              | Bosnian               | Czech                 | French (France)       |
| Finnish               | Bulgarian             | Danish                | German                |
| French (France)       | Catalan               | Dutch                 | Greek                 |
| German                | Croatian              | Estonian              | Hungarian             |
| Greek                 | Czech                 | Finnish               | Italian               |
| Hungarian             | Danish                | French (France)       | Latvian               |
| Indonesian            | Dutch                 | German                | Lithuanian            |
| Italian               | English               | Greek                 | Macedonian            |
| Japanese              | Esperanto             | Hebrew                | Norwegian             |
| Korean                | Estonian              | Hindi                 | Portuguese (Portugal) |
| Latvian               | Finnish               | Hungarian             | Russian               |
| Lithuanian            | French (France)       | Indonesian            | Slovak                |
| Norwegian (Bokmål)    | Galician              | Italian               | Slovenian             |
| Polish                | Georgian              | Japanese              | Spanish (Spain)       |
| Portuguese (Portugal) | German                | Klingon               | Swedish               |
| Romanian              | Greek                 | Korean                | Turkish               |
| Russian               | Gujarati              | Latvian               | Ukrainian             |
| Slovak                | Hausa (Arabic)        | Lithuanian            |                       |
| Slovenian             | Hebrew                | Malay                 |                       |
| Spanish (Spain)       | Hindi                 | Norwegian             |                       |
| Swedish               | Hungarian             | Persian               |                       |
| Turkish               | Icelandic             | Polish                |                       |
| Ukrainian             | Indonesian            | Portuguese (Portugal) |                       |
|                       | Irish                 | Romanian              |                       |
|                       | Italian               | Russian               |                       |
|                       | Japanese              | Serbian               |                       |
|                       | Javanese              | Slovak                |                       |
|                       | Kannada               | Slovenian             |                       |
|                       | Kazakh                | Spanish (Spain)       |                       |
|                       | Khmer                 | Swahili               |                       |
|                       | Korean                | Swedish               |                       |
|                       | Lao                   | Thai                  |                       |
|                       | Latin                 | Turkish               |                       |
|                       | Latvian               | Ukrainian             |                       |
|                       | Lithuanian            | Urdu                  |                       |
|                       | Macedonian            | Vietnamese            |                       |
|                       | Malagasy              | Welsh                 |                       |
|                       | Malay                 |                       |                       |
|                       | Malayalam             |                       |                       |
|                       | Maori                 |                       |                       |
|                       | Marathi               |                       |                       |
|                       | Mongolian             |                       |                       |
|                       | Nepali                |                       |                       |
|                       | Norwegian             |                       |                       |
|                       | Norwegian (Bokmål)    |                       |                       |
|                       | Norwegian (Nynorsk)   |                       |                       |
|                       | Panjabi (India)       |                       |                       |
|                       | Persian               |                       |                       |
|                       | Polish                |                       |                       |
|                       | Portuguese (Portugal) |                       |                       |
|                       | Romanian              |                       |                       |
|                       | Russian               |                       |                       |
|                       | Scottish Gaelic       |                       |                       |
|                       | Serbian               |                       |                       |
|                       | Sinhala               |                       |                       |
|                       | Slovak                |                       |                       |
|                       | Slovenian             |                       |                       |
|                       | Somali                |                       |                       |
|                       | Spanish (Spain)       |                       |                       |
|                       | Sundanese             |                       |                       |
|                       | Swahili               |                       |                       |
|                       | Swedish               |                       |                       |
|                       | Tagalog               |                       |                       |
|                       | Tajik                 |                       |                       |
|                       | Tamil                 |                       |                       |
|                       | Telugu                |                       |                       |
|                       | Thai                  |                       |                       |
|                       | Turkish               |                       |                       |
|                       | Ukrainian             |                       |                       |
|                       | Urdu                  |                       |                       |
|                       | Uzbek                 |                       |                       |
|                       | Vietnamese            |                       |                       |
|                       | Welsh                 |                       |                       |
|                       | Yiddish               |                       |                       |
|                       | Yoruba                |                       |                       |
|-----------------------|-----------------------|-----------------------|-----------------------|
