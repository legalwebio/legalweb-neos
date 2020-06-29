<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 * @ORM\Table(name="legalweb_gdprtools_dataset")
 * @property string $Persistence_Object_Identifier
 */
class Dataset
{
    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $json;

    /**
     * @var \DateTimeInterface
     */
    protected $creationDateTime;

    /**
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->json = $json;
        try {
            $this->creationDateTime = new \DateTimeImmutable();
        } catch (\Exception $e) {
            // @ignoreException
        }
    }

    /**
     * @return string
     */
    public function getPersistenceObjectIdentifier(): string
    {
        return $this->Persistence_Object_Identifier;
    }

    /**
     * @return string
     */
    public function getDomainId(): string
    {
        $decoded = json_decode($this->getJson(), true);
        if (is_array($decoded)) {
            return strval($decoded['domain']['domain_id']);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreationDateTime(): \DateTimeInterface
    {
        return $this->creationDateTime;
    }
}
