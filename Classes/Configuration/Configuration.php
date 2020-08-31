<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Configuration;

use LegalWeb\GdprTools\Exception\InvalidConfigurationException;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class Configuration
{
    private const TOKEN_PLACEHOLDER = '{token}';
    private const TOKEN_REGEX = '/^[a-zA-Z0-9_.-~]+$/';

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
     * @param mixed[] $settings
     * @throws InvalidConfigurationException
     */
    public function injectSettings(array $settings): void
    {
        $this->apiUrl = $this->getStringSetting($settings, 'apiUrl');
        $this->apiKey = $this->getStringSetting($settings, 'apiKey');
        $this->callbackUrl = $this->validateCallbackUrl($this->getStringSetting($settings, 'callbackUrl'));
        $this->callbackToken = $this->validateCallbackToken($this->getStringSetting($settings, 'callbackToken'));
        $this->services = $this->getArraySetting($settings, 'services');
        $this->fallbackLanguage = $this->getStringSetting($settings, 'fallbackLanguage');
    }

    /**
     * @param mixed[] $settings
     * @param string $key
     * @return string
     * @throws InvalidConfigurationException
     */
    private function getStringSetting(array $settings, string $key): string
    {
        if (!isset($settings[$key])) {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must be provided.',
                $key
            ));
        }
        if (!is_string($settings[$key])) {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must be contain a string.',
                $key
            ));
        }
        if ($settings[$key] === '') {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must not be empty.',
                $key
            ));
        }
        return $settings[$key];
    }

    /**
     * @param mixed[] $settings
     * @param string $key
     *
     * @return string[]
     *
     * @throws InvalidConfigurationException
     */
    private function getArraySetting(array $settings, string $key): array
    {
        if (!isset($settings[$key])) {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must be provided.',
                $key
            ));
        }
        if (!is_array($settings[$key])) {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must be contain an array.',
                $key
            ));
        }
        if (count($settings[$key]) === 0) {
            throw new InvalidConfigurationException(sprintf(
                'The %s setting must not be empty.',
                $key
            ));
        }
        foreach ($settings[$key] as $setting) {
            if (!is_string($setting)) {
                throw new InvalidConfigurationException(sprintf(
                    'The %s setting must contain only strings.',
                    $key
                ));
            }
        }
        return $settings[$key];
    }

    /**
     * @param string $callbackUrl
     * @return string
     * @throws InvalidConfigurationException
     */
    private function validateCallbackUrl(string $callbackUrl): string
    {
        if (\mb_strpos($callbackUrl, self::TOKEN_PLACEHOLDER) === false) {
            throw new InvalidConfigurationException(
                'The callbackUrl setting must contain a placeholder for the token'
            );
        }
        return $callbackUrl;
    }

    /**
     * @param string $callbackToken
     * @return string
     * @throws InvalidConfigurationException
     */
    private function validateCallbackToken(string $callbackToken): string
    {
        if (preg_match(self::TOKEN_REGEX, $callbackToken) === false) {
            throw new InvalidConfigurationException(
                'The callbackToken setting must match ' . self::TOKEN_REGEX
            );
        }
        return $callbackToken;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return \str_replace(self::TOKEN_PLACEHOLDER, $this->callbackToken, $this->callbackUrl);
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getFallbackLanguage(): string
    {
        return $this->fallbackLanguage;
    }
}
