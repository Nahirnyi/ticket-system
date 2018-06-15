<?php

namespace Tests\Unit\Jobs;

use App\Events\ConcertAdded;
use App\Jobs\ProcessPosterImage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ConcertFactory;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'poster/example.png',
            file_get_contents(base_path('tests/__fixtures__/full-size-poster.png'))
        );
        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/example.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $resizedImage = Storage::disk('public')->get('posters/example.png');
        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(600, $width);
        $this->assertEquals(776, $height);

        $resizeImageContents = Storage::disk('public')->get('posters/example.png');
        $controlImageContents = file_get_contents(base_path('tests/__fixtures__/optimized-poster.png'));
        $this->assertEquals($controlImageContents, $resizeImageContents);

    }

    /** @test */
    public function it_opimizes_the_poster_image()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'poster/example.png',
            file_get_contents(base_path('tests/__fixtures__/small-unoptimized-poster.png'))
        );
        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/example.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $optimizedImageSize = Storage::disk('public')->size('posters/example.png');
        $originalSize = filesize(base_path('tests/__fixtures__/small-unoptimized-poster.png'));
        $this->assertLessThan($originalSize, $optimizedImageSize);

        $optimizedImageContents = Storage::disk('public')->get('posters/example.png');
        $controlImageContents = file_get_contents(base_path('tests/__fixtures__/optimized-poster.png'));
        $this->assertEquals($controlImageContents, $optimizedImageContents);
    }
}