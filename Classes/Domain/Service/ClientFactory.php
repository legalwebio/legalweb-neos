<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use GuzzleHttp\Client;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class ClientFactory
{
    public function getClient(): Client
    {
        return new Client();
    }
}
