<?php

namespace Tests;
use Exception;
use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Illuminate\Database\Eloquent\Collection;

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

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contained the specified value");
        });

        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection did not contain the specified value");
        });

        Collection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items));
            $this->zip($items)->each(function ($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
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
