<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

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

    /**
     * @param TestResponse $response Laravel test response
     * @param string $operationPath e.g. /profiles/shows
     * @param string $method e.g. get/post
     */
    protected function assertResponseMatchesApiSpecs(TestResponse $response, string $operationPath, string $method): void
    {
        $validator = (new ValidatorBuilder)
            ->fromYamlFile('./.api/muka-developers.v1.yaml')
            ->getResponseValidator();

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrResponse = $psrHttpFactory->createResponse($response->baseResponse);

        $operation = new OperationAddress($operationPath, $method);

        $validator->validate($operation, $psrResponse);
    }
}
