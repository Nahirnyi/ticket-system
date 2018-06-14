<?php

namespace Tests;
use Exception;
use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\Assert;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data',function ($key) {
            return  $this->original->getData()[$key];
        });

        TestResponse::macro('assertViewIs', function ($name) {
           Assert::assertEquals($name, $this->original->name());
        });


    }

    protected function disableExceptionHandler()
    {
        app()->instance(\App\Exceptions\Handler::class, new class extends \App\Exceptions\Handler{
            public function render($request, Exception $exception)
            {
                throw $exception;
            }
        });
    }
}
