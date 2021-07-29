<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Eel;

use LegalWeb\GdprTools\Domain\Model\DataProtectionPopup;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\NodeAggregate\NodeName;
use Neos\ContentRepository\Domain\Projection\Content\TraversableNodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

class ServicesHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var GdprToolsService
     */
    protected $gdprToolsService;

    public function getImprint(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getImprint(
            $this->getSiteRootNodeNameFromNode($node),
            $this->getLanguageFromNode($node)
        );
    }

    public function getDataProtectionStatement(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getDataProtectionStatement(
            $this->getSiteRootNodeNameFromNode($node),
            $this->getLanguageFromNode($node)
        );
    }

    public function getContractTerms(NodeInterface $node = null): string
    {
        return $this->gdprToolsService->getContractTerms(
            $this->getSiteRootNodeNameFromNode($node),
            $this->getLanguageFromNode($node)
        );
    }

    public function getDataProtectionPopup(NodeInterface $node = null): DataProtectionPopup
    {
        return $this->gdprToolsService->getDataProtectionPopup(
            $this->getSiteRootNodeNameFromNode($node),
            $this->getLanguageFromNode($node)
        );
    }

    /**
     * @inheritDoc
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }

    protected function getSiteRootNodeNameFromNode(NodeInterface $node = null): string
    {
        if ($node instanceof TraversableNodeInterface) {
            $siteNodeName = $node->findNodePath()->getParts()[1] ?? null;
            if ($siteNodeName instanceof NodeName) {
                return $siteNodeName->__toString();
            }
        }
        return '';
    }

    protected function getLanguageFromNode(NodeInterface $node = null): ?string
    {
        if ($node instanceof NodeInterface) {
            $dimensions = $node->getContext()->getTargetDimensions();
            return $dimensions['language'] ?? null;
        }
        return null;
    }
}
