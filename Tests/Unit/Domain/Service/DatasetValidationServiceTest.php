<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Domain\Service;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Service\DatasetValidationService;
use LegalWeb\GdprTools\Exception\InvalidDatasetException;
use LegalWeb\GdprTools\Tests\Unit\TestCase;

class DatasetValidationServiceTest extends TestCase
{
    /**
     * @var DatasetValidationService
     */
    protected $datasetValidationService;

    protected function setUp(): void
    {
        $this->datasetValidationService = new DatasetValidationService();
    }

    /**
     * @throws \Exception
     */
    public function testValidData(): void
    {
        static::expectNotToPerformAssertions();

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            json_encode([
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
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function testInvalidJson(): void
    {
        static::expectException(InvalidDatasetException::class);
        static::expectExceptionMessage(
            'Invalid dataset: Decoded JSON is not an array.'
        );

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            '[}'
        );
    }

    /**
     * @throws \Exception
     */
    public function testMissingDomain(): void
    {
        static::expectException(InvalidDatasetException::class);
        static::expectExceptionMessage(
            'Invalid dataset: Decoded JSON does not contain "domain" key.'
        );

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            json_encode([
                'services' => [
                    'imprint' => '',
                    'dpstatement' => '',
                    'contractterms' => '',
                    'dppopup' => '',
                    'dppopupconfig' => '',
                    'dppopupcss' => '',
                    'dppopupjs' => '',
                ],
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws \Exception
     */
    public function testMissingDomainId(): void
    {
        static::expectException(InvalidDatasetException::class);
        static::expectExceptionMessage(
            'Invalid dataset: Decoded JSON does not contain "domain_id" key in "domain" key.'
        );

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            json_encode([
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
            ], JSON_THROW_ON_ERROR)
        );

    }

    /**
     * @throws \Exception
     */
    public function testMissingServices(): void
    {
        static::expectException(InvalidDatasetException::class);
        static::expectExceptionMessage(
            'Invalid dataset: Decoded JSON does not contain "services" key.'
        );

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            json_encode([
                'domain' => [
                    'domain_id' => '1234'
                ]
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws \Exception
     */
    public function testMissingService(): void
    {
        static::expectException(InvalidDatasetException::class);
        static::expectExceptionMessage(
            'Invalid dataset: The service "imprint" is configured but missing from the API response.'
        );

        $this->datasetValidationService->validate(
            new Configuration('', '', '', '', '', ['imprint'], ''),
            json_encode([
                'domain' => [
                    'domain_id' => '1234'
                ],
                'services' => [
                    'contractterms',
                ]
            ], JSON_THROW_ON_ERROR)
        );
    }
}
