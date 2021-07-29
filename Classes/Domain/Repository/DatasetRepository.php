<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Repository;

use LegalWeb\GdprTools\Configuration\Configuration;
use LegalWeb\GdprTools\Domain\Model\Dataset;
use Neos\Flow\Annotations\Scope;
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\Repository;

/**
 * @Scope("singleton")
 */
class DatasetRepository extends Repository
{
    public function getLatest(Configuration $configuration): ?Dataset
    {
        $query = $this->createQuery();
        $latest = $query
            ->matching($query->equals('configurationKey', $configuration->getKey()))
            ->setLimit(1)
            ->setOrderings(['creationDateTime' => QueryInterface::ORDER_DESCENDING])
            ->execute()
            ->getFirst();
        if ($latest instanceof Dataset) {
            return $latest;
        } else {
            return null;
        }
    }

    public function create(Configuration $configuration, string $json): Dataset
    {
        $entity = new Dataset($configuration, $json);
        try {
            $this->add($entity);
        } catch (IllegalObjectTypeException $e) {
            // @ignoreException
            // Adding a Dataset entity to the Dataset repository cannot cause an IllegalObjectTypeException.
        }
        return $entity;
    }
}
