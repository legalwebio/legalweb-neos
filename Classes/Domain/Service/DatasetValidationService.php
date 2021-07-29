<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Service;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Exception\InvalidDatasetException;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class DatasetValidationService
{
    /**
     * @param string $json
     * @throws InvalidDatasetException
     */
    public function validate(Configuration $configuration, string $json): void
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new InvalidDatasetException(
                ['Decoded JSON is not an array.'],
                1627559489815
            );
        }
        if (!isset($decoded['services'])) {
            throw new InvalidDatasetException(
                ['Decoded JSON does not contain "services" key.'],
                1627559489895
            );
        }
        if (!isset($decoded['domain'])) {
            throw new InvalidDatasetException(
                ['Decoded JSON does not contain "domain" key.'],
                1627559489994
            );
        }
        if (!isset($decoded['domain']['domain_id'])) {
            throw new InvalidDatasetException(
                ['Decoded JSON does not contain "domain_id" key in "domain" key.'],
                1627559490099
            );
        }
        $errors = [];
        foreach ($configuration->getServices() as $service) {
            if (!isset($decoded['services'][$service])) {
                $errors[] = 'The service "' . $service . '" is configured but missing from the API response.';
            }
        }
        if (count($errors) > 0) {
            throw new InvalidDatasetException($errors, 1627559490206);
        }
    }
}
