<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Controller;

use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Error\Messages\Message;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;
use Neos\Flow\Mvc\Exception\StopActionException;
use Neos\Flow\Mvc\View\JsonView;
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
     * @var ConfigurationService
     */
    protected $configurationService;

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
        $datasets = [];
        foreach ($this->configurationService->getConfigurations() as $configuration) {
            $dataset = $this->datasetRepository->getLatest($configuration);
            if (!is_null($dataset)) {
                $datasets[] = $dataset;
            }
        }
        $this->view->assign('datasets', $datasets);
    }

    /**
     * @return void
     * @throws StopActionException
     */
    public function updateAction()
    {
        try {
            $this->datasetUpdateService->update(true);
            $this->addFlashMessage('updateSucceeded');
        } catch (UpdateFailedException $e) {
            $this->legalWebLogger->error(
                'Dataset update error (backend): ' . $e->getMessage(),
                ['exception' => $e]
            );
            $this->addFlashMessage('updateFailed', '', Message::SEVERITY_ERROR);
        }
        $this->redirect('index');
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function showAction(string $id): void
    {
        $this->defaultViewObjectName = JsonView::class;
        $this->view = $this->resolveView();
        $this->view->assign('settings', $this->settings);
        $this->view->setControllerContext($this->controllerContext);
        $this->initializeView($this->view);

        $dataset = $this->datasetRepository->findByIdentifier($id);

        if ($dataset instanceof Dataset) {
            $this->view->assign('value', json_decode($dataset->getJson(), true));
        } else {
            throw new Exception('Dataset not found', 1593445205279);
        }
    }
}
