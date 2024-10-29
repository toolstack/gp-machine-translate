<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use WP_Error;

interface ProviderInterface
{
    /**
     * @return array|WP_Error
     */
    public function batchTranslate(string $locale, array $strings);

    public function getDisplayName(): string;

    /**
     * @return array{wordPressLocale: string, providerLocale: string}
     */
    public function getLocales(): array;

    public function requiresAuthClientId(): bool;

    public function requiresAuthKey(): bool;
}
