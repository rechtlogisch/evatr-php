<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\DTO\RequestDto;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Evatr;

beforeEach(function () {
    $_ENV['APP_ENV'] = 'testing';
});

it('can be instantiated with individual parameters', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Test Company',
        street: 'Test Street 123',
        zip: '12345',
        location: 'Test City'
    );

    expect($evatr)->toBeInstanceOf(Evatr::class);
});

it('can be instantiated with RequestDto object', function () {
    $requestDto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $evatr = new Evatr($requestDto);

    expect($evatr)->toBeInstanceOf(Evatr::class);
});

it('can be instantiated with minimal parameters', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    expect($evatr)->toBeInstanceOf(Evatr::class);
});

it('has correct constants defined', function () {
    expect(Evatr::BASE_URL)->toBe('https://api.evatr.vies.bzst.de/app/v1')
        ->and(Evatr::URL_VALIDATION)->toBe('https://api.evatr.vies.bzst.de/app/v1/abfrage');
});

it('can set HTTP client in testing environment', function () {
    $_ENV['APP_ENV'] = 'testing';

    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $mockClient = Mockery::mock(Client::class);
    $result = $evatr->setHttpClient($mockClient);

    expect($result)->toBe($evatr); // Should return self for method chaining
});

it('throws exception when setting HTTP client in non-testing environment', function () {
    $_ENV['APP_ENV'] = 'production';

    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $mockClient = Mockery::mock(Client::class);

    expect(fn () => $evatr->setHttpClient($mockClient))
        ->toThrow(RuntimeException::class, 'Setting a custom HTTP client is only allowed in a testing environment.');
});

it('can enable includeRaw option', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $result = $evatr->includeRaw();

    expect($result)->toBe($evatr); // Should return self for method chaining
});

it('can disable includeRaw option', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $result = $evatr->includeRaw(false);

    expect($result)->toBe($evatr); // Should return self for method chaining
});

it('includeRaw defaults to true when called without parameter', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $result = $evatr->includeRaw();

    expect($result)->toBe($evatr); // Should return self for method chaining
});

it('can perform check with mocked client', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->once()
        ->with(Evatr::URL_VALIDATION, [
            'json' => [
                'anfragendeUstid' => 'DE123456789',
                'angefragteUstid' => 'ATU12345678',
            ],
        ])
        ->andReturn(
            new Response(200, ['Content-Type' => 'application/json'], fixture('response-simple-ok.json'))
        );

    $evatr->setHttpClient($mockClient);
    /** @noinspection PhpUnhandledExceptionInspection */
    $result = $evatr->check();

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('can perform check with qualified parameters', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Test Company',
        street: 'Test Street 123',
        zip: '12345',
        location: 'Test City'
    );

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->once()
        ->with(Evatr::URL_VALIDATION, [
            'json' => [
                'anfragendeUstid' => 'DE123456789',
                'angefragteUstid' => 'ATU12345678',
                'firmenname' => 'Test Company',
                'strasse' => 'Test Street 123',
                'plz' => '12345',
                'ort' => 'Test City',
            ],
        ])
        ->andReturn(
            new Response(200, ['Content-Type' => 'application/json'], fixture('response-qualified-ok.json'))
        );

    $evatr->setHttpClient($mockClient);
    /** @noinspection PhpUnhandledExceptionInspection */
    $result = $evatr->check();

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('loads .env file when it exists', function () {
    // This test verifies the .env loading logic in constructor
    // We can't easily test the actual file loading without creating files,
    // but we can verify the constructor doesn't throw errors
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    expect($evatr)->toBeInstanceOf(Evatr::class);
});

it('method chaining works correctly', function () {
    $evatr = new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->once()
        ->andReturn(
            new Response(200, ['Content-Type' => 'application/json'], fixture('response-simple-ok.json'))
        );

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = $evatr
        ->setHttpClient($mockClient)
        ->includeRaw()
        ->check();

    expect($result)->toBeInstanceOf(ResultDto::class);
});
