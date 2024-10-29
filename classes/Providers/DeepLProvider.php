<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use WP_Error;

class DeepLProvider extends AbstractProvider
{
    public const API_URL = 'https://api.deepl.com';

    public const API_URL_FREE = 'https://api-free.deepl.com';

    public const ENDPOINT_GLOSSARIES = '/v2/glossaries';

    public const ENDPOINT_TRANSLATE = '/v2/translate';

    public const IDENTIFIER = 'DeepL';

    protected const LOCALE_MAPPING = [
        'ar' => 'ar',
        'bg' => 'bg',
        'cs' => 'cs',
        'da' => 'da',
        'de' => 'de',
        'el' => 'el',
        'en' => 'en_us',
        'en_gb' => 'en_gb',
        'es' => 'es',
        'et' => 'et',
        'fi' => 'fi',
        'fr' => 'fr',
        'hu' => 'hu',
        'it' => 'it',
        'id' => 'id',
        'ja' => 'ja',
        'ko' => 'ko',
        'lt' => 'lt',
        'lv' => 'lv',
        'nb' => 'nb',
        'no' => 'nb',
        'nl' => 'nl',
        'pl' => 'pl',
        'pt' => 'pt-pt',
        'pt_br' => 'pt-br',
        'ro' => 'ro',
        'ru' => 'ru',
        'sk' => 'sk',
        'sl' => 'sl',
        'sv' => 'sv',
        'tr' => 'tr',
        'uk' => 'uk',
        'zh_cn' => 'zh',
    ];

    protected const NAME = 'DeepL';

    protected const REQUIRES_AUTH_CLIENT_ID = false;

    protected const REQUIRES_AUTH_KEY = false;

    private const SPECIAL_CHARACTERS = [
        'original' => [
            ' & ',
            '»',
            '&raquo;',
        ],
        'replacement' => [
            ' <mask-amp> ',
            '<mask-raquo>',
            '<mask-raquo>',
        ],
    ];

    /**
     * @return array|WP_Error
     */
    public function batchTranslate(string $locale, array $strings)
    {
        $isValid = $this->validateTranslationArguments($locale, $strings);

        if ($isValid !== null) {
            return $isValid;
        }

        // If we have too many strings, throw an error.
        if (count($strings) > 50) {
            return new WP_Error('gp_machine_translate', 'Only 50 strings allowed.');
        }

        $targetLanguage = $this->getLocales()[$locale];
        $requestHeaders = [
            'Content-Type' => 'application/json',
        ];
        $requestBody = [
            'source_lang' => 'en',
            'target_lang' => $targetLanguage,
            'tag_handling' => 'xml',
            'text' => [],
        ];
        $apiUrl = self::API_URL_FREE;

        if ($this->authKey !== null) {
            $apiUrl = self::API_URL;
            $requestHeaders['Authorization'] = 'DeepL-Auth-Key ' . $this->authKey;
            $glossaryId = $this->getGlossaryId($targetLanguage);

            if ($glossaryId) {
                $requestBody['glossary_id'] = $glossaryId;
            }
        }

        foreach ($strings as $string) {
            $requestBody['text'][] = $this->escapeSpecialCharacters($string);
        }

        $response = wp_remote_post(
            $apiUrl . self::ENDPOINT_TRANSLATE,
            [
                'method' => 'POST',
                'headers' => $requestHeaders,
                'body' => json_encode($requestBody),
            ],
        );

        // Did we get an error?
        if (is_wp_error($response)) {
            return $response;
        }

        // Decode the response from DeepL.
        $json = json_decode(
            wp_remote_retrieve_body($response),
        );

        // If something went wrong with the response from DeepL, throw an error.
        if (!$json || !isset($json->translations)) {
            return new WP_Error('gp_machine_translate', 'Error decoding JSON from DeepL Translate.');
        }

        if (isset($json->error)) {
            return new WP_Error('gp_machine_translate', sprintf('Error auto-translating: %1$s', $json->error->errors[0]->message));
        }

        // Setup an temporary array to use to process the response.
        $translations = [];
        $translatedStrings = array_column($json->translations, 'text');

        // Merge the originals and translations arrays.
        $items = gp_array_zip($strings, $translatedStrings);

        // If there are no items, throw an error.
        if (!$items) {
            return new WP_Error('gp_machine_translate', 'Error merging arrays');
        }

        // Loop through the items and clean up the responses.
        foreach ($items as $item) {
            list($string, $translation) = $item;

            $translations[] = $this->unescapeSpecialCharacters(
                $this->normalizePlaceholders($translation),
            );
        }

        return $translations;
    }

    private function escapeSpecialCharacters(string $text): string
    {
        return str_replace(self::SPECIAL_CHARACTERS['original'], self::SPECIAL_CHARACTERS['replacement'], $text);
    }

    private function getGlossaries(): ?array
    {
        $glossaries = null;

        if ($this->authKey === null) {
            return $glossaries;
        }

        $response = wp_remote_get(
            self::API_URL . self::ENDPOINT_GLOSSARIES,
            [
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $this->authKey,
                ],
            ],
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $jsonResponse = json_decode(
            wp_remote_retrieve_body($response),
        );

        foreach ($jsonResponse->glossaries as $glossary) {
            if (isset($glossary->glossary_id, $glossary->target_lang)) {
                $glossaries[] = $glossary;
            }
        }

        return $glossaries;
    }

    private function getGlossaryId(string $targetLanguage): ?string
    {
        $glossaryId = null;
        $glossaries = $this->getGlossaries();

        if ($glossaries === null) {
            return null;
        }

        foreach ($glossaries as $glossary) {
            if (isset($glossary->glossary_id, $glossary->target_lang) && $glossary->target_lang === $targetLanguage) {
                $glossaryId = $glossary->glossary_id;

                break;
            }
        }

        return is_string($glossaryId) ? $glossaryId : null;
    }

    private function unescapeSpecialCharacters(string $text): string
    {
        return str_replace(self::SPECIAL_CHARACTERS['replacement'], self::SPECIAL_CHARACTERS['original'], $text);
    }
}
