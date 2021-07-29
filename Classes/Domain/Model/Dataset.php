<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use LegalWeb\GdprTools\Configuration\Configuration;
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
     * @ORM\Column(type="string")
     * @var string
     */
    protected $configurationKey;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $fallbackLanguage;

    /**
     * @var \DateTimeInterface
     */
    protected $creationDateTime;

    public function __construct(Configuration $configuration, string $json)
    {
        $this->json = $json;
        $this->configurationKey = $configuration->getKey();
        $this->fallbackLanguage = $configuration->getFallbackLanguage();
        try {
            $this->creationDateTime = new \DateTimeImmutable();
        } catch (\Exception $e) {
            // @ignoreException
        }
    }

    public function getPersistenceObjectIdentifier(): string
    {
        return $this->Persistence_Object_Identifier;
    }

    public function getDomainId(): string
    {
        $decoded = \json_decode($this->getJson(), true);
        if (is_array($decoded)) {
            return strval($decoded['domain']['domain_id']);
        }
        return '';
    }

    public function getJson(): string
    {
        return $this->json;
    }

    public function getCreationDateTime(): \DateTimeInterface
    {
        return $this->creationDateTime;
    }

    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }

    public function getImprint(string $language = null): ?string
    {
        return $this->getService('imprint', $this->fallbackLanguage, $language);
    }

    public function getDataProtectionStatement(string $language = null): ?string
    {
        return $this->getService('dpstatement', $this->fallbackLanguage, $language);
    }

    public function getContractTerms(string $language = null): ?string
    {
        return $this->getService('contractterms', $this->fallbackLanguage, $language);
    }

    public function getDataProtectionPopup(string $language = null): ?DataProtectionPopup
    {
        $services = $this->getServices();
        $html = $this->getService('dppopup', $this->fallbackLanguage, $language);
        $config = $services['dppopupconfig'] ?? null;
        $css = $services['dppopupcss'] ?? null;
        $js = $services['dppopupjs'] ?? null;

        if (is_string($html) && is_array($config) && is_string($css) && is_string($js)) {
            return new DataProtectionPopup($html, $config, $css, $js);
        }

        return null;
    }

    /**
     * @return mixed[]
     */
    private function getServices(): array
    {
        $decoded = \json_decode($this->json, true);
        if (is_array($decoded) && isset($decoded['services']) && is_array($decoded['services'])) {
            return $decoded['services'];
        }
        return [];
    }

    private function getService(string $service, string $fallbackLanguage, string $language = null): ?string
    {
        $services = $this->getServices();
        $service = isset($services[$service]) && is_array($services[$service]) ? $services[$service] : [];
        if (is_string($language) && isset($service[$language]) && is_string($service[$language])) {
            return $service[$language];
        }
        if (isset($service[$fallbackLanguage]) && is_string($service[$fallbackLanguage])) {
            return $service[$fallbackLanguage];
        }
        return null;
    }
}
