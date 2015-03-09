<?php

namespace Spl\Factory;

interface FactoryInterface
{

    /**
     * Constructs the object based on the provided factory configuration.
     *
     * @return mixed
     */
    function construct();

    /**
     * Resets factory to the initial state removing the previously changed construction settings.
     *
     * @return $this
     */
    function reset();
}