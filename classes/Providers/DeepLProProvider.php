<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use WP_Error;

class DeepLProProvider extends DeepLProvider
{
    public const API_URL = 'https://api.deepl.com';

    public const IDENTIFIER = 'DeepL Pro';

    protected const NAME = 'DeepL - Pro';

    /**
     * @return array|WP_Error
     */
    public function batchTranslate(string $locale, array $strings)
    {
        $isValid = $this->validateTranslationArguments($locale, $strings);

        if ($isValid !== null) {
            return $isValid;
        }

        $glossaryId = $this->getGlossaryId($locale);
        $translationData = $this->performTranslationRequest(
            $this->getRequestBody($strings, $locale, $glossaryId),
        );

        if ($translationData instanceof WP_Error) {
            return $translationData;
        }

        return $this->getTranslatedStringsArray($translationData, $strings);
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

    private function getGlossaryId(string $locale): ?string
    {
        $glossaryId = null;
        $glossaries = $this->getGlossaries();
        $targetLanguage = $this->getLocales()[$locale];

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
}
