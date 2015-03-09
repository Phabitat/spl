<?php

namespace Spl\Traits;

/**
 * Blocks class instantiation.
 */
trait InstantlessTrait
{
    final private function __construct()
    {
    }

    final private function __clone()
    {
    }
}