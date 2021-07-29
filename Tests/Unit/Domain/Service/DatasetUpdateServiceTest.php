<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Domain\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Domain\Service\ClientFactory;
use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Domain\Service\DatasetValidationService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\Tests\Unit\TestCase;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Fusion\Core\Cache\ContentCache;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class DatasetUpdateServiceTest extends TestCase
{
    /**
     * @var DatasetUpdateService
     */
    private $datasetUpdateService;

    /**
     * @var MockObject
     */
    protected $datasetRepository;

    /**
     * @var MockObject
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
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->datasetRepository = $this->getMockBuilder(DatasetRepository::class)->getMock();

        $this->guzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $this->guzzleClient->method('request')->willReturn(new Response());

        $configuration = new Configuration('default', '', '', '', '', [], '');
        $configurationService = $this->getMockBuilder(ConfigurationService::class)->getMock();
        $configurationService->method('getConfigurations')->willReturn([$configuration]);

        $this->oldDataset = new Dataset($configuration, '');
        static::inject(
            $this->oldDataset,
            'creationDateTime',
            (new \DateTimeImmutable())->setTimestamp(time() - 7 * 24 * 60 * 60 - 1)
        );

        $this->youngDataset = new Dataset($configuration, '');
        static::inject(
            $this->youngDataset,
            'creationDateTime',
            new \DateTimeImmutable()
        );

        $datasetValidationService = $this->getMockBuilder(DatasetValidationService::class)->getMock();
        $datasetValidationService->method('validate')->willReturn([]);

        $clientFactory = $this->getMockBuilder(ClientFactory::class)->getMock();
        $clientFactory->method('getClient')->willReturn($this->guzzleClient);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $persistenceManager = $this->getMockBuilder(PersistenceManagerInterface::class)->getMock();
        $contentCache = $this->getMockBuilder(ContentCache::class)->getMock();

        $this->datasetUpdateService = new DatasetUpdateService();
        static::inject($this->datasetUpdateService, 'datasetRepository', $this->datasetRepository);
        static::inject($this->datasetUpdateService, 'configurationService', $configurationService);
        static::inject($this->datasetUpdateService, 'persistenceManager', $persistenceManager);
        static::inject($this->datasetUpdateService, 'logger', $logger);
        static::inject($this->datasetUpdateService, 'datasetValidationService', $datasetValidationService);
        static::inject($this->datasetUpdateService, 'contentCache', $contentCache);
        static::inject($this->datasetUpdateService, 'clientFactory', $clientFactory);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceDoesNotUpdateYoungDataset(): void
    {
        $this->guzzleClient->expects(static::never())->method('request');
        $this->datasetRepository->expects(static::never())->method('create');
        $this->datasetRepository->expects(static::once())->method('getLatest')->willReturn($this->youngDataset);

        $this->datasetUpdateService->update(false);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceUpdatesOldDataset(): void
    {
        $this->guzzleClient->expects(static::once())->method('request');
        $this->datasetRepository->expects(static::once())->method('create');
        $this->datasetRepository->expects(static::once())->method('getLatest')->willReturn($this->oldDataset);

        $this->datasetUpdateService->update(false);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithoutForceUpdatesWhenNoDatasets(): void
    {
        $this->guzzleClient->expects(static::once())->method('request');
        $this->datasetRepository->expects(static::once())->method('create');
        $this->datasetRepository->expects(static::once())->method('getLatest')->willReturn(null);

        $this->datasetUpdateService->update(false);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithForceUpdatesWithoutCheckingLatest(): void
    {
        $this->guzzleClient->expects(static::once())->method('request');
        $this->datasetRepository->expects(static::once())->method('create');
        $this->datasetRepository->expects(static::never())->method('getLatest');

        $this->datasetUpdateService->update(true);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateDoesNotCreateDatabaseRowWhenRequestFails(): void
    {
        $guzzleException = $this->getMockBuilder(GuzzleException::class)->getMock();
        $this->guzzleClient->expects(static::once())->method('request')->willThrowException($guzzleException);
        $this->datasetRepository->expects(static::never())->method('create');
        static::expectException(UpdateFailedException::class);

        $this->datasetUpdateService->update(true);
    }
}
