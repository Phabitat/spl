<?php

namespace Spl\Constant;

use ReflectionClass;
use Spl\Traits\InstantlessTrait;

abstract class AbstractConstant
{
    use InstantlessTrait;

    /**
     * Cached class constants.
     *
     * @var array
     */
    protected static $constants = [];

    /**
     * Cached class constant values.
     *
     * @var array
     */
    protected static $values = [];

    /**
     * Cached class constant values (combined).
     *
     * @var array
     */
    protected static $combinedValues = [];

    /**
     * Returns all defined constants.
     *
     * @return array
     */
    public static function getConstants()
    {
        isset(self::$constants[$class = static::class]) || self::$constants[$class] = (new ReflectionClass($class))->getConstants();
        return self::$constants[$class];
    }

    /**
     * Returns all values of defined constants, if combined flag is set true the returned values array will be combined.
     *
     * @param bool $combined
     * @return array
     */
    public static function getValues($combined = false)
    {
        if (!isset(self::$values[$class = static::class], self::$combinedValues[$class])) {
            foreach (self::getConstants() as $constant => $value) {
                self::$values[$class][$constant]      = $value;
                self::$combinedValues[$class][$value] = $value;
            }
        }

        return $combined ? self::$combinedValues[$class] : self::$values[$class];
    }

    /**
     * Checks whether the specified value is present within defined constant values.
     *
     * @param $value
     * @returns bool
     */
    public static function contains($value)
    {
        return isset(self::getValues(true)[$value]);
    }
}