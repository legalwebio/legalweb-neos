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
     * @param string|null $language
     * @return string
     */
    public function getImprint(string $language = null): string
    {
        return $this->gdprToolsService->getImprint($language);
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getDataProtectionStatement(string $language = null): string
    {
        return $this->gdprToolsService->getDataProtectionStatement($language);
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getContractTerms(string $language = null): string
    {
        return $this->gdprToolsService->getContractTerms($language);
    }

    /**
     * @param string|null $language
     * @return DataProtectionPopup
     */
    public function getDataProtectionPopup(string $language = null): DataProtectionPopup
    {
        return $this->gdprToolsService->getDataProtectionPopup($language);
    }

    /**
     * @inheritDoc
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }
}
