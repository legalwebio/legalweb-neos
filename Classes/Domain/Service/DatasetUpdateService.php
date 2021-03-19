<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Fusion\Core\Cache\ContentCache;

class DatasetUpdateService
{
    /**
     * @Flow\Inject
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @Flow\Inject
     * @var Configuration
     */
    protected $configuration;

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
     * @param bool $force
     * @return bool
     * @throws UpdateFailedException
     */
    public function update(bool $force): bool
    {
        if (!$force && !$this->isUpdateNeeded()) {
            $this->logger->info('Skipping unneeded dataset update without force');
            return false;
        }

        try {
            $this->logger->info('Fetching new dataset', ['force' => $force]);
            $response = $this->fetchDataset();
            $this->logger->info('Received new dataset');
            $validationErrors = $this->datasetValidationService->validate($response);
            if (count($validationErrors) === 0) {
                $this->storeResponse($response);
                $this->logger->info('Stored new dataset');
                $this->contentCache->flushByTag('LegalWeb-DataProtectionPopup-Cache-EntryTag');
                $this->logger->info('Flushed popup cache');
                return true;
            }
            throw new UpdateFailedException(
                'Received invalid dataset: ' . implode('. ', $validationErrors),
                1592817786605,
            );
        } catch (GuzzleException $e) {
            throw new UpdateFailedException(
                'The legal web API request failed',
                1592407065953,
                $e
            );
        }
    }

    /**
     * @return bool
     */
    protected function isUpdateNeeded(): bool
    {
        $latest = $this->datasetRepository->getLatest();
        $oneWeekAgo = time() - 7 * 24 * 60 * 60;
        return !$latest instanceof Dataset || $latest->getCreationDateTime()->getTimestamp() < $oneWeekAgo;
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    protected function fetchDataset(): string
    {
        $client = new Client();
        return $client->request(
            'POST',
            $this->configuration->getApiUrl(),
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Guid' => $this->configuration->getApiKey(),
                    'Callback' => $this->configuration->getCallbackUrl(),
                ],
            ]
        )->getBody()->__toString();
    }

    /**
     * @param string $json
     */
    protected function storeResponse(string $json): void
    {
        $this->datasetRepository->create($json);
        $this->persistenceManager->persistAll();
    }
}
