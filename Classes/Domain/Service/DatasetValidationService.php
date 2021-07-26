<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use LegalWeb\GdprTools\Configuration\Configuration;
use Neos\Flow\Annotations as Flow;

class DatasetValidationService
{
    /**
     * @Flow\Inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param string $json
     * @return string[]
     */
    public function validate(string $json): array
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return ['Decoded JSON is not an array'];
        }
        if (!isset($decoded['services'])) {
            return ['Decoded JSON does not contain "services" key'];
        }
        if (!isset($decoded['domain'])) {
            return ['Decoded JSON does not contain "domain" key'];
        }
        if (!isset($decoded['domain']['domain_id'])) {
            return ['Decoded JSON does not contain "domain_id" key in "domain" key'];
        }
        $errors = [];
        foreach ($this->configuration->getServices() as $service) {
            if (!isset($decoded['services'][$service])) {
                $errors[] = 'The service "' . $service . '" is configured but missing from the API response';
            }
        }
        return $errors;
    }
}
