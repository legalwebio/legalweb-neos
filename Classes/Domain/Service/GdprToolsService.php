<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use Neos\Flow\Annotations as Flow;

class GdprToolsService
{
    /**
     * @Flow\Inject
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @return string
     */
    public function getImprint(): string
    {
        $services = $this->getServices();
        return $services['imprint']['de'];
    }

    /**
     * @return string
     */
    public function getDataProtectionStatement(): string
    {
        $services = $this->getServices();
        return $services['dpstatement']['de'];
    }

    /**
     * @return string
     */
    public function getContractTerms(): string
    {
        $services = $this->getServices();
        return $services['contractterms']['de'];
    }

    /**
     * @return DataProtectionPopup
     */
    public function getDataProtectionPopup(): DataProtectionPopup
    {
        $services = $this->getServices();
        return new DataProtectionPopup(
            $services['dppopup']['de'],
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
     * @return array<string, mixed>
     */
    private function getServices(): array
    {
        return $this->getDatasetData()['services'] ?? [];
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
