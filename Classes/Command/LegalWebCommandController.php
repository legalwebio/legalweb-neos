<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Command;

use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class LegalWebCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var DatasetUpdateService
     */
    protected $datasetUpdateService;

    /**
     * @Flow\Inject
     * @var LegalWebLoggerInterface
     */
    protected $legalWebLogger;

    /**
     * @param bool $force
     */
    public function updateCommand(bool $force = false): void
    {
        try {
            $this->datasetUpdateService->update($force);
        } catch (UpdateFailedException $e) {
            $this->legalWebLogger->error(
                'Dataset update error (cli): ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }
}
