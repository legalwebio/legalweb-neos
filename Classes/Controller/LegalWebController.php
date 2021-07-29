<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Controller;

use LegalWeb\GdprTools\Configuration\ConfigurationService;
use LegalWeb\GdprTools\Domain\Service\DatasetUpdateService;
use LegalWeb\GdprTools\Exception\UpdateFailedException;
use LegalWeb\GdprTools\LegalWebLoggerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;

class LegalWebController extends ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @Flow\Inject
     * @var DatasetUpdateService
     */
    protected $datasetUpdateService;

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
     * @param string $token
     */
    public function updateAction(string $token = ''): void
    {
        foreach ($this->configurationService->getConfigurations() as $configuration) {
            if ($token === $configuration->getCallbackToken()) {
                try {
                    $this->datasetUpdateService->update(true);
                    $this->respondWithSuccess([]);
                } catch (UpdateFailedException $e) {
                    $this->legalWebLogger->error(
                        'Dataset update error (frontend): ' . $e->getMessage(),
                        ['exception' => $e]
                    );
                    $this->respondWithError(500, 1592407361953, 'Update failed: ' . $e->getMessage());
                }
                return;
            }
        }
        $this->legalWebLogger->warning(
            'Dataset update frontend request with invalid or missing token',
            ['token' => $token]
        );
        $this->respondWithError(401, 1592407272608, 'Invalid callback token');
    }

    /**
     * @param mixed[] $data
     */
    protected function respondWithSuccess(array $data): void
    {
        $this->response->setStatusCode(200);
        $this->view->assign('value', [
            'data' => $data
        ]);
    }

    /**
     * @param int $statusCode
     * @param int $errorCode
     * @param string $errorMessage
     */
    protected function respondWithError(int $statusCode, int $errorCode, string $errorMessage): void
    {
        $this->response->setStatusCode($statusCode);
        $this->view->assign('value', [
            'errors' => [
                [
                    'code' => $errorCode,
                    'title' => $errorMessage
                ]
            ]
        ]);
    }
}
