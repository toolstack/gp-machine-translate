<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use WP_Error;

final class YandexTranslateProvider extends AbstractProvider
{
    public const IDENTIFIER = 'Yandex.Translate';

    protected const LOCALE_MAPPING = [
        'be' => 'be',
        'ca' => 'ca',
        'cs' => 'cs',
        'da' => 'da',
        'de' => 'de',
        'el' => 'el',
        'es' => 'es',
        'et' => 'et',
        'fi' => 'fi',
        'fr' => 'fr',
        'hu' => 'hu',
        'it' => 'it',
        'lt' => 'lt',
        'lv' => 'lv',
        'mk' => 'mk',
        'nl' => 'nl',
        'no' => 'no',
        'pt' => 'pt',
        'ru' => 'ru',
        'sk' => 'sk',
        'sl' => 'sl',
        'sv' => 'sv',
        'tr' => 'tr',
        'uk' => 'uk',
    ];

    protected const NAME = '<a href="http://translate.yandex.com/" target="_blank">Powered by Yandex.Translate</a>';

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

        // This is the URL of the Yandex API.
        $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $this->getAuthKey() . '&lang=en-' . urlencode($this->getLocales()[$locale]);

        // Loop through the stings and add them to the $url as a query string.
        foreach ($strings as $string) {
            $url .= '&text=' . urlencode($string);
        }

        // Get the response from Yandex.
        $response = wp_remote_get($url);

        // Did we get an error?
        if (is_wp_error($response)) {
            return $response;
        }

        // Decode the response from Yandex.
        $json = json_decode(wp_remote_retrieve_body($response));

        // If something went wrong with the response from Yandex, throw an error.
        if (!$json) {
            return new WP_Error('gp_machine_translate', 'Error decoding JSON from Yandex Translate.');
        }

        if (isset($json->error)) {
            return new WP_Error('gp_machine_translate', sprintf('Error auto-translating: %1$s', $json->error->errors[0]->message));
        }

        // Setup an temporary array to use to process the response.
        $translations = [];

        // If the translations have been return as a single entry, make it an array so it's easier to process later.
        if (!is_array($json->text)) {
            $json->text = [$json->text];
        }

        // Merge the originals and translations arrays.
        $items = gp_array_zip($strings, $json->text);

        // If there are no items, throw an error.
        if (!$items) {
            return new WP_Error('gp_machine_translate', 'Error merging arrays');
        }

        // Loop through the items and clean up the responses.
        foreach ($items as $item) {
            list($string, $translation) = $item;

            $translations[] = $this->normalizePlaceholders($translation);
        }

        // Return the results.
        return $translations;
    }
}
