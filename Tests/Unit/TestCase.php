<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Tests\Unit;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     * @throws \ReflectionException
     */
    protected static function inject(object $object, string $propertyName, $value): void
    {
        $prop = new \ReflectionProperty($object, $propertyName);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }
}
