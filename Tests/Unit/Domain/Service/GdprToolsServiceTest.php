<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit\Domain\Service;

use LegalWeb\GdprTools\Domain\Repository\DatasetRepository;
use LegalWeb\GdprTools\Domain\Service\GdprToolsService;
use Neos\Flow\Tests\UnitTestCase;

class GdprToolsServiceTest extends UnitTestCase
{
    /**
     * @Flow\Inject
     * @var DatasetRepository
     */
    protected $datasetRepository;

    /**
     * @var GdprToolsService()
     */
    protected $gdprToolsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->datasetRepository = $this->getMockBuilder(DatasetRepository::class)->getMock();

        $this->gdprToolsService = new GdprToolsService();
    }

    public function testGetImprint(): void
    {
        $this->markTestSkipped();
    }
}
