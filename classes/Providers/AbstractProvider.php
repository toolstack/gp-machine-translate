<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use GpMachineTranslate\Providers\Traits\Normalizer;
use WP_Error;

abstract class AbstractProvider implements ProviderInterface
{
    use Normalizer;

    protected ?string $authClientId = null;

    protected ?string $authKey = null;

    public function __construct(?string $authClientId, ?string $authKey)
    {
        $this->authClientId = $authClientId;
        $this->authKey = $authKey;
    }

    public function getAuthClientId(): ?string
    {
        return $this->authClientId;
    }

    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    public function getDisplayName(): string
    {
        return static::NAME;
    }

    public function getLocales(): array
    {
        return static::LOCALE_MAPPING;
    }

    /**
     * Checks if the setup requirements for authentication are met.
     *
     * The method verifies if the authentication key and client ID are set
     * when they are required. If either of these required values are missing,
     * the setup is considered incomplete.
     *
     * @return bool true if the setup requirements are met; false otherwise
     */
    public function isSetUp(): bool
    {
        if ($this->requiresAuthKey() && $this->authKey === null) {
            return false;
        }

        return !($this->requiresAuthClientId() && $this->authClientId === null);
    }

    public function requiresAuthClientId(): bool
    {
        return static::REQUIRES_AUTH_CLIENT_ID;
    }

    public function requiresAuthKey(): bool
    {
        return static::REQUIRES_AUTH_KEY;
    }

    public function validateTranslationArguments(string $locale, array $strings): ?WP_Error
    {
        // If we don't have a supported translation code, throw an error.
        if (!array_key_exists($locale, $this->getLocales())) {
            return new WP_Error('gp_machine_translate', sprintf("The locale %s isn't supported by %s.", $locale, $this->getDisplayName()));
        }

        // If we don't have any strings, throw an error.
        if (count($strings) == 0) {
            return new WP_Error('gp_machine_translate', 'No strings found to translate.');
        }

        return null;
    }
}
