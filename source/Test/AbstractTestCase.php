<?php

namespace Spl\Test;

use Exception;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * @param callable $callback
     * @param string $unexpectedException
     */
    public static function assertNoException(callable $callback, $unexpectedException = null)
    {
        isset($unexpectedException) || $unexpectedException = Exception::class;

        if (!class_exists($unexpectedException) || interface_exists($unexpectedException)) {
            self::fail("The \"{$unexpectedException}\" exception does not exist.");
        }

        try {
            $callback();
        } catch (Exception $actualException) {
            self::assertNotInstanceOf($unexpectedException, $actualException);
            return;
        }
    }

    /**
     * @param callable $callback
     * @param string $expectedException
     * @param callable $assert
     */
    public static function assertException(callable $callback, $expectedException = null, callable $assert = null)
    {
        isset($expectedException) || $expectedException = Exception::class;

        if (!class_exists($expectedException) || interface_exists($expectedException)) {
            self::fail("The \"{$expectedException}\" exception does not exist.");
        }

        try {
            $callback();
        } catch (Exception $actualException) {
            isset($expectedException) && self::assertInstanceOf($expectedException, $actualException);
            isset($assert) && $assert($actualException);

            return;
        }

        self::fail("Failed asserting that \"{$expectedException}\" exception was thrown.");
    }

    /**
     * @param mixed $object
     * @param string $method
     * @return ReflectionMethod
     */
    protected static function getAccessibleMethod($object, $method)
    {
        $reflection = new ReflectionClass(is_string($object) ? $object : get_class($object));
        $method     = $reflection->getMethod($method);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * @param mixed $object
     * @param string $method
     * @param array ...$arguments
     * @return mixed
     */
    protected static function invokeMethod($object, $method, ...$arguments)
    {
        return self::getAccessibleMethod($object, $method)->invoke($object, ...$arguments);
    }

    /**
     * @param mixed $object
     * @param string $property
     * @return ReflectionProperty
     */
    protected static function getAccessibleProperty($object, $property)
    {
        $reflection = new ReflectionClass(is_string($object) ? $object : get_class($object));
        $property   = $reflection->getProperty($property);

        $property->setAccessible(true);

        return $property;
    }

    /**
     * @param mixed $object
     * @param string $property
     * @return mixed
     */
    protected static function getPropertyValue($object, $property)
    {
        return self::getAccessibleProperty($object, $property)->getValue($object);
    }

    /**
     * @param mixed $object
     * @param string $property
     * @param mixed $value
     */
    protected static function setPropertyValue($object, $property, $value)
    {
        self::getAccessibleProperty($object, $property)->setValue($object, $value);
    }
}