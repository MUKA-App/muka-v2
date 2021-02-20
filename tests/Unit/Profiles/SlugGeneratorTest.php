<?php

namespace Tests\Unit\Profiles;

use App\Profiles\SlugGenerator;
use Tests\TestCase;

class SlugGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_generate_slug_for_name()
    {
        $name = "Thomas the Tank Engine";

        $slug = SlugGenerator::generate($name);

        $this->assertMatchesRegularExpression('/thomas-the-tank-engine-[\w]{6}/', $slug);
    }


    /**
     * @test
     */
    public function it_should_correctly_reduce_white_spaces()
    {
        $name = "Thomas                    the Tank Engine";

        $slug = SlugGenerator::generate($name);

        $this->assertMatchesRegularExpression('/thomas-the-tank-engine-[\w]{6}/', $slug);
    }

    /**
     * @test
     */
    public function it_should_strip_punctuation()
    {
        $name = "\"Create\".!?[] Reach,;:— 'Inspire{}'()";

        $slug = SlugGenerator::generate($name);

        $this->assertMatchesRegularExpression('/create-reach-inspire-[\w]{6}/', $slug);
    }

    /**
     * @test
     */
    public function it_should_strip_non_ascii_characters()
    {
        $name = "הפודקאסט" . " " . "Omri Casspi Podcast. הפודקאסט של עומרי כס";

        $slug = SlugGenerator::generate($name);

        $this->assertMatchesRegularExpression('/omri-casspi-podcast-[\w]{6}/', $slug);
    }


    /**
     * @test
     */
    public function it_should_strip_non_ascii_characters_and_only_return_hash_number()
    {
        $name = "הפודקאסט";

        $slug = SlugGenerator::generate($name);

        $this->assertMatchesRegularExpression('/[\w]{6}/', $slug);
    }


    /**
     * @test
     */
    public function it_should_generate_different_slugs_for_the_same_name()
    {
        $name = "John Smith";

        $slugs = [];

        for ($i = 0; $i < 100; $i++) {
            $slugs[] = SlugGenerator::generate($name);
        }

        $this->assertTrue(count($slugs) === count(array_unique($slugs)));
    }
}
