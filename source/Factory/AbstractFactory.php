<?php

namespace Spl\Factory;

use Spl\Traits\InstanceTrait;

abstract class AbstractFactory implements FactoryInterface
{
    use InstanceTrait;

    /**
     * @inheritdoc
     */
    public abstract function construct();

    /**
     * @inheritdoc
     */
    public abstract function reset();
}