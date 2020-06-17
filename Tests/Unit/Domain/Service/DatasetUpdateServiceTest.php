<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Domain\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Domain\Service\DatasetValidationService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Tests\UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DatasetUpdateServiceTest extends UnitTestCase
{
    /**
     * @var DatasetUpdateService
     */
    private $datasetUpdateService;

    /**
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var Dataset
     */
    protected $oldDataset;

    /**
     * @var Dataset
     */
    protected $youngDataset;

    /**
     * @var DatasetValidationService
     */
    protected $datasetValidationService;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->datasetRepository = $this->getMockBuilder(DatasetRepository::class)->getMock();
        $this->configuration = $this->getMockBuilder(Configuration::class)->getMock();
        $this->persistenceManager = $this->getMockBuilder(PersistenceManagerInterface::class)->getMock();

        $stream = $this->getMockBuilder(StreamInterface::class)->getMock();
        $stream->method('__toString')->willReturn('');

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getBody')->willReturn($stream);

        $this->guzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $this->guzzleClient->method('request')->willReturn($response);

        $this->oldDataset = $this->getMockBuilder(Dataset::class)->getMock();
        $this->oldDataset->method('getCreationDateTime')->willReturn((new \DateTimeImmutable())->setTimestamp(time() - 7 * 24 * 60 * 60 - 1));

        $this->youngDataset = $this->getMockBuilder(Dataset::class)->getMock();
        $this->youngDataset->method('getCreationDateTime')->willReturn((new \DateTimeImmutable()));

        $this->datasetValidationService = $this->getMockBuilder(DatasetValidationService::class)->getMock();
        $this->datasetValidationService->method('validate')->willReturn([]);

        $this->datasetUpdateService = new DatasetUpdateService();
        $this->inject($this->datasetUpdateService, 'datasetRepository', $this->datasetRepository);
        $this->inject($this->datasetUpdateService, 'configuration', $this->configuration);
        $this->inject($this->datasetUpdateService, 'persistenceManager', $this->persistenceManager);
        $this->inject($this->datasetUpdateService, 'guzzleClient', $this->guzzleClient);
        $this->inject($this->datasetUpdateService, 'datasetValidationService', $this->datasetValidationService);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceDoesNotUpdateYoungDataset(): void
    {
        $this->guzzleClient->expects($this->never())->method('request');
        $this->datasetRepository->expects($this->never())->method('create');
        $this->datasetRepository->expects($this->once())->method('getLatest')->willReturn($this->youngDataset);

        $updated = $this->datasetUpdateService->update(false);

        $this->assertFalse($updated);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceUpdatesOldDataset(): void
    {
        $this->guzzleClient->expects($this->once())->method('request');
        $this->datasetRepository->expects($this->once())->method('create');
        $this->datasetRepository->expects($this->once())->method('getLatest')->willReturn($this->oldDataset);

        $updated = $this->datasetUpdateService->update(false);

        $this->assertTrue($updated);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceUpdatesWhenNoDatasets(): void
    {
        $this->guzzleClient->expects($this->once())->method('request');
        $this->datasetRepository->expects($this->once())->method('create');
        $this->datasetRepository->expects($this->once())->method('getLatest')->willReturn(null);

        $updated = $this->datasetUpdateService->update(false);

        $this->assertTrue($updated);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithForceUpdatesWithoutCheckingLatest(): void
    {
        $this->guzzleClient->expects($this->once())->method('request');
        $this->datasetRepository->expects($this->once())->method('create');
        $this->datasetRepository->expects($this->never())->method('getLatest');

        $updated = $this->datasetUpdateService->update(true);

        $this->assertTrue($updated);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateDoesNotCreateDatabaseRowWhenRequestFails(): void
    {
        $guzzleException = $this->getMockBuilder(GuzzleException::class)->getMock();
        \assert($guzzleException instanceof GuzzleException);
        $this->guzzleClient->expects($this->once())->method('request')->willThrowException($guzzleException);
        $this->datasetRepository->expects($this->never())->method('create');
        $this->expectException(UpdateFailedException::class);

        $this->datasetUpdateService->update(true);
    }
}
