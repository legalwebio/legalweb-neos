<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Configuration;

use LegalWeb\GdprTools\Exception\InvalidConfigurationException;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class ConfigurationService
{
    /**
     * @Flow\InjectConfiguration
     * @var mixed[]
     */
    protected $settings;

    public function getConfiguration(string $siteRootNodeName): ?Configuration
    {
        $default = null;
        foreach ($this->getConfigurations() as $configuration) {
            if ($configuration->isDefault()) {
                $default = $configuration;
            }
            if ($configuration->isForSite($siteRootNodeName)) {
                return $configuration;
            }
        }
        return $default;
    }

    /**
     * @return Configuration[]
     * @throws InvalidConfigurationException
     */
    public function getConfigurations(): array
    {
        $result = [];
        foreach ($this->settings as $key => $config) {
            if (is_array($config)) {
                $result[] = new Configuration(
                    $key,
                    $this->getStringSetting($config, 'apiUrl'),
                    $this->getStringSetting($config, 'apiKey'),
                    $this->validateCallbackUrl($this->getStringSetting($config, 'callbackUrl')),
                    $this->validateCallbackToken($this->getStringSetting($config, 'callbackToken')),
                    $this->getArraySetting($config, 'services'),
                    $this->getStringSetting($config, 'fallbackLanguage'),
                );
            } else {
                throw new InvalidConfigurationException(
                    sprintf('LegalWeb configuration key %s does not contain an array', $key),
                    1627575122400
                );
            }
        }
        if (count($result) === 0) {
            throw new InvalidConfigurationException('LegalWeb configuration is missing', 1627571498580);
        }
        return $result;
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
                'The %s setting must be an array.',
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
                    'The %s setting array must contain only strings.',
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
        if (\mb_strpos($callbackUrl, Configuration::TOKEN_PLACEHOLDER) === false) {
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
        if (preg_match(Configuration::TOKEN_REGEX, $callbackToken) === false) {
            throw new InvalidConfigurationException(
                'The callbackToken setting must match ' . Configuration::TOKEN_REGEX
            );
        }
        return $callbackToken;
    }
}
