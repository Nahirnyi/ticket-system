<?php

namespace Tests\Unit\Listeners;

use App\Events\ConcertAdded;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ConcertFactory;
use App\Jobs\ProcessPosterImage;

class SchedulePosterImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_queues_a_job_to_proccess_a_poster_image_if_a_poster_image_is_present()
    {
        Queue::fake();
        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/example.png',
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });
    }

    /** @test */
    public function a_job_is_not_queued_if_a_poster_image_is_not_present()
    {
        $this->disableExceptionHandling();
        Queue::fake();
        $concert = \ConcertFactory::createUnpublished([
            'poster_image_path' => null,
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
    }
}
