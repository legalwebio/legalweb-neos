<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;

class GdprToolsService
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
     * @var LegalWebLoggerInterface
     */
    protected $logger;

    /**
     * @param string|null $language
     * @return string
     */
    public function getImprint(string $language = null): string
    {
        return $this->getService('imprint', $language);
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getDataProtectionStatement(string $language = null): string
    {
        return $this->getService('dpstatement', $language);
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getContractTerms(string $language = null): string
    {
        return $this->getService('contractterms', $language);
    }

    /**
     * @param string|null $language
     * @return DataProtectionPopup
     */
    public function getDataProtectionPopup(string $language = null): DataProtectionPopup
    {
        $services = $this->getServices();
        return new DataProtectionPopup(
            $this->getService('dppopup', $language),
            $services['dppopupconfig'],
            $services['dppopupcss'],
            $services['dppopupjs'],
        );
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return [];
    }

    /**
     * @param mixed[] $data
     * @param string|null $language
     * @return string
     */
    private function getByLanguage(array $data, string $language = null): string
    {
        if (is_string($language) && isset($data[$language])) {
            return $data[$language];
        }
        return $data[$this->configuration->getFallbackLanguage()];
    }

    /**
     * @return array<string, mixed>
     */
    private function getServices(): array
    {
        return $this->getDatasetData()['services'] ?? [];
    }

    /**
     * @param string $key
     * @param string|null $language
     * @return string
     */
    private function getService(string $key, string $language = null): string
    {
        $services = $this->getServices();
        if (!isset($services[$key])) {
            $this->logger->error(sprintf('Attempted to load missing service "%s"', $key));
            return '';
        }
        return $this->getByLanguage($services[$key], $language);
    }

    /**
     * @return mixed[]
     */
    private function getDatasetData(): array
    {
        $dataset = $this->datasetRepository->getLatest();
        if (!$dataset instanceof Dataset) {
            return [];
        }
        $decoded = json_decode($dataset->getJson(), true);
        if (is_array($decoded)) {
            return $decoded;
        }
        return [];
    }
}
