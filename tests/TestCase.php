<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param $class
     *
     * @return \Mockery\MockInterface
     */
    protected function initMockery($class)
    {
        $mockClass = \Mockery::mock($class);
        $this->app->instance($class, $mockClass);
        return $mockClass;
    }
}
