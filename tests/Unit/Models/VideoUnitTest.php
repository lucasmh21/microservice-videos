<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use PHPUnit\Framework\TestCase;

class VideoUnitTest extends TestCase
{

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }


    public function testFillable()
    {
        $fillable = [
            'description',
            'title',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'video_file',
            'thumb_file',
            'banner_file',
            'trailer_file'
        ];
        $this->assertEqualsCanonicalizing($this->video->getFillable(), $fillable);
        $this->assertCount(sizeof($fillable),$this->video->getFillable());
    }

    /** @test */
    public function testCasts()
    {
        $casts = [
            'opened' => 'boolean',
            'id' => 'string'
        ];

        $this->assertEqualsCanonicalizing($this->video->getCasts(), $casts);
        $this->assertCount(sizeof($casts),$this->video->getCasts());
    }

    /** @test */
    public function testDates()
    {
        $dates = [
            'created_at',
            'deleted_at',
            'updated_at'
        ];

        $this->assertEqualsCanonicalizing($dates, $this->video->getDates());
        $this->assertCount(sizeof($dates), $this->video->getDates());
    }
}
