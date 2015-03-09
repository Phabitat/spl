<?php

namespace Spl\Traits;

trait InstanceTrait
{

    /**
     * Returns the new instance of the class.
     *
     * @param array ...$arguments
     * @return $this
     */
    public static function instance(...$arguments)
    {
        return new static(...$arguments);
    }
}