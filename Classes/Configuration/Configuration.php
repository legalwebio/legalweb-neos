<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Configuration;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class Configuration
{
    public const TOKEN_PLACEHOLDER = '{token}';
    public const TOKEN_REGEX = '/^[a-zA-Z0-9_.-~]+$/';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $callbackUrl;

    /**
     * @var string
     */
    private $callbackToken;

    /**
     * @var string[]
     */
    private $services;

    /**
     * @var string
     */
    private $fallbackLanguage;

    /**
     * @param string $key
     * @param string $apiUrl
     * @param string $apiKey
     * @param string $callbackUrl
     * @param string $callbackToken
     * @param string[] $services
     * @param string $fallbackLanguage
     */
    public function __construct(
        string $key,
        string $apiUrl,
        string $apiKey,
        string $callbackUrl,
        string $callbackToken,
        array $services,
        string $fallbackLanguage
    ) {
        $this->key = $key;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->callbackUrl = $callbackUrl;
        $this->callbackToken = $callbackToken;
        $this->services = $services;
        $this->fallbackLanguage = $fallbackLanguage;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getCallbackUrl(): string
    {
        return \str_replace(self::TOKEN_PLACEHOLDER, $this->callbackToken, $this->callbackUrl);
    }

    public function getCallbackToken(): string
    {
        return $this->callbackToken;
    }

    /**
     * @return string[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function getFallbackLanguage(): string
    {
        return $this->fallbackLanguage;
    }

    public function isDefault(): bool
    {
        return $this->getKey() === 'default';
    }

    public function isForSite(string $siteRootNodeName): bool
    {
        return $this->getKey() === $siteRootNodeName;
    }
}
