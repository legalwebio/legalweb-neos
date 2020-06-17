<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Eel;

use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

class ServicesHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var GdprToolsService
     */
    protected $gdprToolsService;

    /**
     * @return string
     */
    public function getImprint(): string
    {
        return $this->gdprToolsService->getImprint();
    }

    /**
     * @return string
     */
    public function getDataProtectionStatement(): string
    {
        return $this->gdprToolsService->getDataProtectionStatement();
    }

    /**
     * @return string
     */
    public function getContractTerms(): string
    {
        return $this->gdprToolsService->getContractTerms();
    }

    /**
     * @return DataProtectionPopup
     */
    public function getDataProtectionPopup(): DataProtectionPopup
    {
        return $this->gdprToolsService->getDataProtectionPopup();
    }

    /**
     * @inheritDoc
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }
}
