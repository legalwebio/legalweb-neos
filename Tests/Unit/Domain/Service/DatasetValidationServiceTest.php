<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Domain\Service;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Service\DatasetValidationService;
use Neos\Flow\Tests\UnitTestCase;

class DatasetValidationServiceTest extends UnitTestCase
{
    /**
     * @var DatasetValidationService
     */
    protected $datasetValidationService;

    protected function setUp()
    {
        parent::setUp();

        $this->datasetValidationService = new DatasetValidationService();
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getServices')->willReturn(['imprint']);
        $this->inject($this->datasetValidationService, 'configuration', $configuration);
    }

    public function testValidData(): void
    {
        $errors = $this->datasetValidationService->validate(json_encode([
            'domain' => [
                'domain_id' => '1234'
            ],
            'services' => [
                'imprint' => '',
                'dpstatement' => '',
                'contractterms' => '',
                'dppopup' => '',
                'dppopupconfig' => '',
                'dppopupcss' => '',
                'dppopupjs' => '',
            ],
        ]));

        $this->assertEquals([], $errors);
    }

    public function testInvalidJson(): void
    {
        $errors = $this->datasetValidationService->validate('[}');

        $this->assertEquals(['Decoded JSON is not an array'], $errors);
    }

    public function testMissingDomain(): void
    {
        $errors = $this->datasetValidationService->validate(json_encode([
            'services' => [
                'imprint' => '',
                'dpstatement' => '',
                'contractterms' => '',
                'dppopup' => '',
                'dppopupconfig' => '',
                'dppopupcss' => '',
                'dppopupjs' => '',
            ],
        ]));

        $this->assertEquals(['Decoded JSON does not contain "domain" key'], $errors);
    }

    public function testMissingDomainId(): void
    {
        $errors = $this->datasetValidationService->validate(json_encode([
            'domain' => [],
            'services' => [
                'imprint' => '',
                'dpstatement' => '',
                'contractterms' => '',
                'dppopup' => '',
                'dppopupconfig' => '',
                'dppopupcss' => '',
                'dppopupjs' => '',
            ],
        ]));

        $this->assertEquals(['Decoded JSON does not contain "domain_id" key in "domain" key'], $errors);
    }

    public function testMissingServices(): void
    {
        $errors = $this->datasetValidationService->validate(json_encode([
            'domain' => [
                'domain_id' => '1234'
            ]
        ]));

        $this->assertEquals(['Decoded JSON does not contain "services" key'], $errors);
    }

    public function testMissingService(): void
    {
        $errors = $this->datasetValidationService->validate(json_encode([
            'domain' => [
                'domain_id' => '1234'
            ],
            'services' => [
                // Configuration mock returns ['imprint'] as expected services, so this test should fail because
                // it does not contain all configured services.
                'contractterms',
            ]
        ]));

        $this->assertEquals(['Missing service "imprint"'], $errors);
    }
}
