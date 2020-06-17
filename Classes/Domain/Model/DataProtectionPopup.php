<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Domain\Model;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class DataProtectionPopup
{
    /**
     * @var string
     */
    private $html;

    /**
     * @var mixed[]
     */
    private $config;

    /**
     * @var string
     */
    private $css;

    /**
     * @var string
     */
    private $js;

    /**
     * @param string $html
     * @param mixed[] $config
     * @param string $css
     * @param string $js
     */
    public function __construct(string $html, array $config, string $css, string $js)
    {
        $this->html = $html;
        $this->config = $config;
        $this->css = $css;
        $this->js = $js;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @return mixed[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getCss(): string
    {
        return $this->css;
    }

    /**
     * @return string
     */
    public function getJs(): string
    {
        return $this->js;
    }
}
