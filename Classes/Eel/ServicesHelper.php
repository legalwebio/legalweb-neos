<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Eel;

use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use Neos\ContentRepository\Domain\Model\NodeInterface;
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
     * @param NodeInterface|null $node
     * @return string
     */
    public function getImprint(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getImprint($this->getLanguageFromNode($node));
    }

    /**
     * @param NodeInterface|null $node
     * @return string
     */
    public function getDataProtectionStatement(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getDataProtectionStatement($this->getLanguageFromNode($node));
    }

    /**
     * @param NodeInterface|null $node
     * @return string
     */
    public function getContractTerms(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getContractTerms($this->getLanguageFromNode($node));
    }

    /**
     * @param NodeInterface|null $node
     * @return DataProtectionPopup
     */
    public function getDataProtectionPopup(NodeInterface $node = null): DataProtectionPopup
    {
        return $this->gdprToolsService->getDataProtectionPopup($this->getLanguageFromNode($node));
    }

    /**
     * @inheritDoc
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }

    /**
     * @param NodeInterface|null $node
     * @return string|null
     */
    protected function getLanguageFromNode(NodeInterface $node = null): ?string
    {
        if ($node instanceof NodeInterface) {
            $dimensions = $node->getContext()->getTargetDimensions();
            return $dimensions['language'] ?? null;
        }
        return null;
    }
}
