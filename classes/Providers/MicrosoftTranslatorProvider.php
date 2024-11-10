<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use MicrosoftTranslator\Translate;
use WP_Error;

class MicrosoftTranslatorProvider extends AbstractProvider
{
    public const IDENTIFIER = 'Microsoft Translator';

    protected const LOCALE_MAPPING = [
        'af' => 'af',
        'ar' => 'ar',
        'bg' => 'bg',
        'bs' => 'bs-Latn',
        'ca' => 'ca',
        'cs' => 'cs',
        'cy' => 'cy',
        'da' => 'da',
        'de' => 'de',
        'el' => 'el',
        'es' => 'es',
        'et' => 'et',
        'fa' => 'fa',
        'fi' => 'fi',
        'fr' => 'fr',
        'he' => 'iw',
        'hi' => 'hi',
        'hr' => 'hr',
        'ht' => 'ht',
        'hu' => 'hu',
        'id' => 'id',
        'it' => 'it',
        'ja' => 'ja',
        'ko' => 'ko',
        'lt' => 'lt',
        'lv' => 'lv',
        'ms' => 'ms',
        'mt' => 'mt',
        'mww' => 'mww',
        'nl' => 'nl',
        'no' => 'no',
        'otq' => 'otq',
        'pl' => 'pl',
        'pt' => 'pt-PT',
        'ro' => 'ro',
        'ru' => 'ru',
        'sk' => 'sk',
        'sl' => 'sl',
        'sr' => 'sr-Cyrl',
        'sv' => 'sv',
        'sw' => 'sw',
        'th' => 'th',
        'tlh' => 'tlh',
        'tr' => 'tr',
        'uk' => 'uk',
        'ur' => 'ur',
        'vi' => 'vi',
        'yua' => 'yua',
        'zh_cn' => 'zh-CHS',
    ];

    protected const NAME = 'Microsoft Translator';

    protected const REQUIRES_AUTH_CLIENT_ID = true;

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

        $config = [
            'clientID' => $this->getAuthClientId(),
            'clientSecret' => $this->getAuthKey(),
        ];
        $t = new Translate($config);

        return $t->translate($strings, $this->getLocales()[$locale], 'en');
    }
}
