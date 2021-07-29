<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class GdprToolsService
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
     * @var LegalWebLoggerInterface
     */
    protected $logger;

    public function getImprint(string $siteRootNodeName, string $language = null): string
    {
        $dataset = $this->getDataset($siteRootNodeName);
        $result = is_null($dataset) ? null : $dataset->getImprint($language);
        if (is_null($result)) {
            $this->logger->error('Attempted to load missing service "imprint"');
            return '';
        }
        return $result;
    }

    public function getDataProtectionStatement(string $siteRootNodeName, string $language = null): string
    {
        $dataset = $this->getDataset($siteRootNodeName);
        $result = is_null($dataset) ? null : $dataset->getDataProtectionStatement($language);
        if (is_null($result)) {
            $this->logger->error('Attempted to load missing service "dpstatement"');
            return '';
        }
        return $result;
    }

    public function getContractTerms(string $siteRootNodeName, string $language = null): string
    {
        $dataset = $this->getDataset($siteRootNodeName);
        $result = is_null($dataset) ? null : $dataset->getContractTerms($language);
        if (is_null($result)) {
            $this->logger->error('Attempted to load missing service "contractterms"');
            return '';
        }
        return $result;
    }

    public function getDataProtectionPopup(string $siteRootNodeName, string $language = null): DataProtectionPopup
    {
        $dataset = $this->getDataset($siteRootNodeName);
        $result = is_null($dataset) ? null : $dataset->getDataProtectionPopup($language);
        if (is_null($result)) {
            $this->logger->error('Attempted to load missing service "dppopup"');
            return new DataProtectionPopup('', [], '', '');
        }
        return $result;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return [];
    }

    private function getDataset(string $siteRootNodeName): ?Dataset
    {
        $configuration = $this->configurationService->getConfiguration($siteRootNodeName);
        if (is_null($configuration)) {
            $this->logger->error(
                'No configuration found',
                ['siteRootNodeName' => $siteRootNodeName]
            );
            return null;
        }

        $dataset = $this->datasetRepository->getLatest($configuration);
        if (is_null($dataset)) {
            $this->logger->error(
                'No dataset found',
                ['siteRootNodeName' => $siteRootNodeName, 'configurationKey' => $configuration->getKey()]
            );
            return null;
        }

        return $dataset;
    }
}
