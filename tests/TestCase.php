<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public-test', [
            'url' => env('APP_URL') . '/uploads',
        ]);

        Queue::fake();
        Mail::fake();
        Notification::fake();
    }

    protected function fakeEventFacade()
    {
        // Fake events, but allow UUIDs to be gen
        $initialDispatcher = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($initialDispatcher);
    }

    /**
     * Returns valid dataProvider from given array
     * @param array $data
     * @return array
     */
    protected function arrayToDataProvider(array $data): array
    {
        return array_map(function ($v) {
            return [$v];
        }, $data);
    }
}
