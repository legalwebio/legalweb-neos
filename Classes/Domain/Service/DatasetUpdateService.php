<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use GuzzleHttp\Exception\GuzzleException;
use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Fusion\Core\Cache\ContentCache;

/**
 * @Flow\Scope("singleton")
 */
class DatasetUpdateService
{
    /**
     * @Flow\Inject
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @Flow\Inject
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var LegalWebLoggerInterface
     */
    protected $logger;

    /**
     * @Flow\Inject
     * @var DatasetValidationService
     */
    protected $datasetValidationService;

    /**
     * @Flow\Inject
     * @var ContentCache
     */
    protected $contentCache;

    /**
     * @Flow\Inject
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @param bool $force
     * @throws UpdateFailedException
     */
    public function update(bool $force): void
    {
        $configurations = $this->configurationService->getConfigurations();
        if (count($configurations) === 0) {
            throw new UpdateFailedException(
                'No configurations',
                1627556442106,
            );
        }

        foreach ($configurations as $configuration) {
            $this->updateSite($configuration, $force);
        }
    }

    /**
     * @param Configuration $configuration
     * @param bool $force
     * @throws UpdateFailedException
     */
    protected function updateSite(Configuration $configuration, bool $force): void
    {
        if (!$force && !$this->isUpdateNeeded($configuration)) {
            $this->logger->info(
                'Skipping unneeded dataset update without force',
                ['configurationKey' => $configuration->getKey()]
            );
            return;
        }

        $response = $this->fetchDataset($configuration);
        $this->logger->info('Received new dataset', ['configurationKey' => $configuration->getKey()]);

        $this->datasetValidationService->validate($configuration, $response);

        $this->storeResponse($configuration, $response);
        $this->logger->info('Stored new dataset');

        $this->contentCache->flushByTag('LegalWeb-DataProtectionPopup-Cache-EntryTag');
        $this->logger->info('Flushed popup cache');
    }

    protected function isUpdateNeeded(Configuration $configuration): bool
    {
        $latest = $this->datasetRepository->getLatest($configuration);
        $oneWeekAgo = time() - 7 * 24 * 60 * 60;
        return !$latest instanceof Dataset || $latest->getCreationDateTime()->getTimestamp() < $oneWeekAgo;
    }

    /**
     * @param Configuration $configuration
     * @return string
     * @throws UpdateFailedException
     */
    protected function fetchDataset(Configuration $configuration): string
    {
        try {
            $this->logger->info('Fetching new dataset', ['configurationKey' => $configuration->getKey()]);
            return $this->clientFactory->getClient()->request(
                'POST',
                $configuration->getApiUrl(),
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Guid' => $configuration->getApiKey(),
                        'Callback' => $configuration->getCallbackUrl(),
                    ],
                ]
            )->getBody()->__toString();
        } catch (GuzzleException $e) {
            throw new UpdateFailedException(
                'The legal web API request failed',
                1592407065953,
                $e
            );
        }
    }

    protected function storeResponse(Configuration $config, string $json): void
    {
        $this->datasetRepository->create($config, $json);
        $this->persistenceManager->persistAll();
    }
}
