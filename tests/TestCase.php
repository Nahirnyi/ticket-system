<?php

namespace Tests;
use Exception;
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
        //Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
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
