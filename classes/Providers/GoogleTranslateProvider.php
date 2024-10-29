<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use WP_Error;

class GoogleTranslateProvider extends AbstractProvider
{
    public const IDENTIFIER = 'Google Translate';

    protected const LOCALE_MAPPING = [
        'af' => 'af',
        'ar' => 'ar',
        'az' => 'az',
        'be' => 'be',
        'bg' => 'bg',
        'bn_bd' => 'bn',
        'bs' => 'bs',
        'ca' => 'ca',
        'cs' => 'cs',
        'cy' => 'cy',
        'da' => 'da',
        'de' => 'de',
        'el' => 'el',
        'en' => 'en',
        'en_au' => 'en',
        'en_ca' => 'en',
        'en_gb' => 'en',
        'en_nz' => 'en',
        'en_za' => 'en',
        'eo' => 'eo',
        'es' => 'es',
        'es_ar' => 'es',
        'es_cl' => 'es',
        'es_co' => 'es',
        'es_gt' => 'es',
        'es_mx' => 'es',
        'es_pe' => 'es',
        'es_pr' => 'es',
        'es_ve' => 'es',
        'et' => 'et',
        'eu' => 'eu',
        'fa' => 'fa',
        'fa_af' => 'fa',
        'fi' => 'fi',
        'fr' => 'fr',
        'ga' => 'ga',
        'gd' => 'gd',
        'gl' => 'gl',
        'gu' => 'gu',
        'ha' => 'ha',
        'he' => 'iw',
        'hi' => 'hi',
        'hr' => 'hr',
        'hu' => 'hu',
        'hy' => 'hy',
        'id' => 'id',
        'is' => 'is',
        'it' => 'it',
        'ja' => 'ja',
        'jv' => 'jw',
        'ka' => 'ka',
        'kk' => 'kk',
        'km' => 'km',
        'kn' => 'kn',
        'ko' => 'ko',
        'la' => 'la',
        'lo' => 'lo',
        'lt' => 'lt',
        'lv' => 'lv',
        'mg' => 'mg',
        'mk' => 'mk',
        'ml' => 'ml',
        'mn' => 'mn',
        'mr' => 'mr',
        'mri' => 'mi',
        'ms' => 'ms',
        'my' => 'my',
        'nb' => 'no',
        'ne' => 'ne',
        'nl' => 'nl',
        'nl_be' => 'nl',
        'nn' => 'no',
        'no' => 'no',
        'pa' => 'pa',
        'pl' => 'pl',
        'pt' => 'pt-PT',
        'pt_br' => 'pt-BR',
        'ro' => 'ro',
        'ru' => 'ru',
        'si' => 'si',
        'sk' => 'sk',
        'sl' => 'sl',
        'so' => 'so',
        'sq' => 'sq',
        'sr' => 'sr',
        'su' => 'su',
        'sv' => 'sv',
        'sw' => 'sw',
        'ta' => 'ta',
        'ta_lk' => 'ta',
        'te' => 'te',
        'tg' => 'tg',
        'th' => 'th',
        'tl' => 'tl',
        'tr' => 'tr',
        'uk' => 'uk',
        'ur' => 'ur',
        'uz' => 'uz',
        'vi' => 'vi',
        'yi' => 'yi',
        'yor' => 'yo',
        'zh_cn' => 'zh-CN',
        'zh_tw' => 'zh-TW',
    ];

    protected const NAME = 'Google Translate';

    protected const REQUIRES_AUTH_CLIENT_ID = false;

    protected const REQUIRES_AUTH_KEY = true;

    /**
     * @return array|WP_Error
     */
    public function batchTranslate(string $locale, array $strings)
    {
        $isValid = $this->validateTranslationArguments($locale, $strings);

        if ($isValid !== null) {
            return $isValid;
        }

        // This is the URL of the Google API.
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $this->getAuthKey() . '&source=en&target=' . urlencode($this->getLocales()[$locale]);

        // Loop through the stings and add them to the $url as a query string.
        foreach ($strings as $string) {
            $url .= '&q=' . urlencode($string);
        }

        // If we just have a single string, add an extra q= to the end so Google things we're doing multiple strings.
        if (count($strings) == 1) {
            $url .= '&q=';
        }

        // Get the response from Google.
        $response = wp_remote_get($url);

        // Did we get an error?
        if (is_wp_error($response)) {
            return $response;
        }

        // Decode the response from Google.
        $json = json_decode(wp_remote_retrieve_body($response));

        // If something went wrong with the response from Google, throw an error.
        if (!$json) {
            return new WP_Error('gp_machine_translate', 'Error decoding JSON from Google Translate.');
        }

        if (isset($json->error)) {
            return new WP_Error('gp_machine_translate', sprintf('Error auto-translating: %1$s', $json->error->errors[0]->message));
        }

        // Setup an temporary array to use to process the response.
        $translations = [];

        // If the translations have been return as a single entry, make it an array so it's easier to process later.
        if (!is_array($json->data->translations)) {
            $json->data->translations = [$json->data->translations];
        }

        // Merge the originals and translations arrays.
        $items = gp_array_zip($strings, $json->data->translations);

        // If there are no items, throw an error.
        if (!$items) {
            return new WP_Error('gp_machine_translate', 'Error merging arrays');
        }

        // Loop through the items and clean up the responses.
        foreach ($items as $item) {
            list($string, $translation) = $item;

            $translations[] = $this->normalizePlaceholders($translation->translatedText);
        }

        // Return the results.
        return $translations;
    }
}
