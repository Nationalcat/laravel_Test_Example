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
    protected function initMockery($class) : \Mockery\MockInterface
    {
        $mockClass = \Mockery::mock($class);
        $this->app->instance($class, $mockClass);
        return $mockClass;
    }

    /**
     * 會把類別屬性設為 NULL
     *
     * @param $class
     *
     * @return \Mockery\MockInterface
     */
    protected function initPartialMockery($class) : \Mockery\MockInterface
    {
        $mockClass = \Mockery::mock($class)->makePartial();
        $this->app->instance($class, $mockClass);
        return $mockClass;
    }

    /**
     * 參考
     * https://github.com/sebastianbergmann/phpunit/issues/1646
     * 注意，該方法無法覆寫 private 的屬性
     *
     * @param $object
     * @param $attributeName
     * @param $value
     * @param  null  $class
     *
     * @throws \ReflectionException
     */
    protected function setObjectAttribute($object, string $propertyName, $value) : void
    {
        $reflectionClass    = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
