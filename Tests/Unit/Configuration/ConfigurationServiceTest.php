<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Configuration;

use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Exception\InvalidConfigurationException;
use LegalWeb\GdprTools\Tests\Unit\TestCase;

class ConfigurationServiceTest extends TestCase
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    protected function setUp(): void
    {
        $this->configurationService = new ConfigurationService();
    }

    /**
     * @throws \Exception
     */
    public function testValid(): void
    {
        static::inject($this->configurationService, 'settings', $this->validData());
        $configuration = $this->configurationService->getConfigurations()[0];

        static::assertEquals(
            'https://www.example.com',
            $configuration->getApiUrl()
        );
        static::assertEquals(
            '7f4d86bb-4d44-4285-864f-65fca6e6b2cd',
            $configuration->getApiKey()
        );
        static::assertEquals(
            'https://www.example.com?token=CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
            $configuration->getCallbackUrl()
        );
        static::assertEquals(
            'CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
            $configuration->getCallbackToken()
        );
        static::assertEquals(
            ['imprint'],
            $configuration->getServices()
        );
        static::assertEquals(
            'de',
            $configuration->getFallbackLanguage()
        );
        static::assertEquals(
            'default',
            $configuration->getKey()
        );
    }

    /**
     * @dataProvider invalidDataProvider
     * @param array<string, string> $settings
     * @throws \Exception
     */
    public function testInvalid(array $settings): void
    {
        static::inject($this->configurationService, 'settings', $settings);

        $this->expectException(InvalidConfigurationException::class);
        $this->configurationService->getConfigurations();
    }

    /**
     * @return array<string, mixed[]>
     * @throws \Exception
     */
    public function invalidDataProvider(): array
    {
        return [
            'missing apiUrl' => [[$this->invalidData(['apiUrl'], [])]],
            'missing apiKey' => [[$this->invalidData(['apiKey'], [])]],
            'missing callbackUrl' => [[$this->invalidData(['callbackUrl'], [])]],
            'missing callbackToken' => [[$this->invalidData(['callbackToken'], [])]],
            'missing fallbackLanguage' => [[$this->invalidData(['fallbackLanguage'], [])]],
            'empty apiUrl' => [[$this->invalidData([], ['apiUrl' => ''])]],
            'empty apiKey' => [[$this->invalidData([], ['apiKey' => ''])]],
            'empty callbackUrl' => [[$this->invalidData([], ['callbackUrl' => ''])]],
            'empty callbackToken' => [[$this->invalidData([], ['callbackToken' => ''])]],
            'empty fallbackLanguage' => [[$this->invalidData([], ['fallbackLanguage' => ''])]],
            'non-string apiUrl' => [[$this->invalidData([], ['apiUrl' => 1])]],
            'non-string apiKey' => [[$this->invalidData([], ['apiKey' => false])]],
            'non-string callbackUrl' => [[$this->invalidData([], ['callbackUrl' => new \DateTimeImmutable()])]],
            'non-string callbackToken' => [[$this->invalidData([], ['callbackToken' => null])]],
            'non-string fallbackLanguage' => [[$this->invalidData([], ['fallbackLanguage' => 42])]],
            'missing token placeholder in callbackUrl' => [[$this->invalidData([], ['callbackUrl' => 'https://www.example.com/'])]],
            'invalid character in callbackToken' => [[$this->invalidData([], ['callbackToken' => 'in!valid'])]],
            'missing services' => [[$this->invalidData(['services'], [])]],
            'empty services' => [[$this->invalidData([], ['services' => []])]],
            'services not array' => [[$this->invalidData([], ['services' => ''])]],
            'services contains non-string' => [[$this->invalidData([], ['services' => [1]])]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function validData(): array
    {
        return [
            'default' => [
                'apiUrl' => 'https://www.example.com',
                'apiKey' => '7f4d86bb-4d44-4285-864f-65fca6e6b2cd',
                'callbackUrl' => 'https://www.example.com?token={token}',
                'callbackToken' => 'CT74.TSzcQWiVXZen2TP0eDf9_ByWT1vDT~sTD6wh5fSG-mrjQaljCH5yMxWFzo',
                'services' => ['imprint'],
                'fallbackLanguage' => 'de'
            ],
        ];
    }

    /**
     * @param mixed[] $unsetKeys
     * @param mixed[] $overrides
     * @return mixed[]
     */
    private function invalidData(array $unsetKeys, array $overrides): array
    {
        $result = $this->validData()['default'];
        foreach ($unsetKeys as $key) {
            unset($result[$key]);
        }
        return ['default' => array_merge($result, $overrides)];
    }
}
