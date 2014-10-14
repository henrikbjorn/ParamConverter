<?php

namespace ParamConverter\Tests\EventListener\Fixture;

use ParamConverter\Annotation\ParamConverter;

/**
 * @ParamConverter("test")
 */
class FooControllerParamConverterAtClassAndMethod
{
    /**
     * @ParamConverter("test2")
     */
    public function barAction($test, $test2)
    {
    }
}
