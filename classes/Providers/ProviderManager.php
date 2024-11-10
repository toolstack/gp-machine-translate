<?php

declare(strict_types=1);

namespace GpMachineTranslate\Providers;

use LogicException;

final class ProviderManager
{
    private ?string $authClientId;

    private ?string $authKey;

    private array $instances = [];

    /**
     * @var array|string[]
     */
    private array $registry = [
        DeepLProvider::IDENTIFIER => DeepLProvider::class,
        DeepLProProvider::IDENTIFIER => DeepLProProvider::class,
        GoogleTranslateProvider::IDENTIFIER => GoogleTranslateProvider::class,
        MicrosoftTranslatorProvider::IDENTIFIER => MicrosoftTranslatorProvider::class,
        YandexTranslateProvider::IDENTIFIER => YandexTranslateProvider::class,
    ];

    public function __construct(?string $authClientId, ?string $authKey)
    {
        $this->authClientId = !empty($authClientId) ? $authClientId : null;
        $this->authKey = !empty($authKey) ? $authKey : null;
    }

    public function getOrCreateProviderInstance(string $providerIdentifier): AbstractProvider
    {
        $this->ensureProviderExists($providerIdentifier);

        if (!isset($this->instances[$providerIdentifier])) {
            $this->instances[$providerIdentifier] = new $this->registry[$providerIdentifier]($this->authClientId, $this->authKey);
        }

        return $this->instances[$providerIdentifier];
    }

    /**
     * @return array<int, string>
     */
    public function getProviderIdentifiers(): array
    {
        $providersIdentifiers = [];

        foreach ($this->registry as $providerIdentifier => $providerClass) {
            /** @var \GpMachineTranslate\Providers\ProviderInterface $provider */
            $providerInstance = $this->getOrCreateProviderInstance($providerIdentifier);
            $providersIdentifiers[] = $providerInstance::IDENTIFIER;
        }

        return $providersIdentifiers;
    }

    /**
     * @return array<string, string>
     */
    public function getProvidersDisplayName(): array
    {
        $list = [];

        foreach ($this->registry as $providerIdentifier => $providerClass) {
            /** @var \GpMachineTranslate\Providers\ProviderInterface $provider */
            $providerInstance = $this->getOrCreateProviderInstance($providerIdentifier);
            $list[$providerIdentifier] = $providerInstance->getDisplayName();
        }

        return $list;
    }

    public function updateOrCreateProviderInstance(string $providerIdentifier, string $authClientId, string $authKey): AbstractProvider
    {
        $this->ensureProviderExists($providerIdentifier);

        $this->authClientId = !empty($authClientId) ? $authClientId : null;
        $this->authKey = !empty($authKey) ? $authKey : null;
        $this->instances[$providerIdentifier] = new $this->registry[$providerIdentifier]($this->authClientId, $this->authKey);

        return $this->instances[$providerIdentifier];
    }

    private function ensureProviderExists(string $providerIdentifier)
    {
        if (!isset($this->registry[$providerIdentifier])) {
            throw new LogicException('Provider with identifier ' . $providerIdentifier . ' does not exist.');
        }
    }
}
