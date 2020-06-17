<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Repository;

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
    /**
     * @return Dataset|null
     */
    public function getLatest(): ?Dataset
    {
        $latest = $this
            ->createQuery()
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

    /**
     * @param string $json
     * @return Dataset
     */
    public function create(string $json): Dataset
    {
        $entity = new Dataset($json);
        try {
            $this->add($entity);
        } catch (IllegalObjectTypeException $e) {
            // @ignoreException
            // Adding a Dataset entity to the Dataset repository cannot cause an IllegalObjectTypeException.
        }
        return $entity;
    }
}
