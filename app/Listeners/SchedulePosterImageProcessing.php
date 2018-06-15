<?php

namespace App\Listeners;

use App\Events\ConcertAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\ProcessPosterImage;

class SchedulePosterImageProcessing
{
    /**
     * Handle the event.
     *
     * @param  ConcertAdded  $event
     * @return void
     */
    public function handle(ConcertAdded $event)
    {
        if ($event->concert->hasPoster()){
            ProcessPosterImage::dispatch();
        }

    }
}
