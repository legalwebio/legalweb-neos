<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Controller;

use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Error\Messages\Message;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Exception\StopActionException;
use Neos\Neos\Controller\Module\AbstractModuleController;

class LegalWebModuleController extends AbstractModuleController
{
    /**
     * @Flow\Inject
     * @var DatasetUpdateService
     */
    protected $datasetUpdateService;

    /**
     * @Flow\Inject
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @Flow\Inject
     * @var GdprToolsService
     */
    protected $gdprToolsService;

    /**
     * @Flow\Inject
     * @var LegalWebLoggerInterface
     */
    protected $legalWebLogger;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('messages', $this->gdprToolsService->getMessages());
        $this->view->assign('latestDataset', $this->datasetRepository->getLatest());
    }

    /**
     * @return void
     * @throws StopActionException
     */
    public function updateAction()
    {
        try {
            $this->datasetUpdateService->update(true);
            $this->addFlashMessage('updateSucceeded', '', Message::SEVERITY_OK);
        } catch (UpdateFailedException $e) {
            $this->legalWebLogger->error(
                'Dataset update error (backend): ' . $e->getMessage(),
                ['exception' => $e]
            );
            $this->addFlashMessage('updateFailed', '', Message::SEVERITY_ERROR);
        }
        $this->redirect('index');
    }
}
