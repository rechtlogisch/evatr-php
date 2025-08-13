<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\Evatr;
use Rechtlogisch\Evatr\Exception\ErrorResponse;

beforeEach(function () {
    $_ENV['APP_ENV'] = 'testing';
});

it('check throws ErrorResponse on invalid JSON', function () {
    $evatr = new Evatr(vatIdOwn: 'DE123456789', vatIdForeign: 'ATU12345678');

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('post')
        ->once()
        ->with(Evatr::URL_VALIDATION, [
            'json' => [
                'anfragendeUstid' => 'DE123456789',
                'angefragteUstid' => 'ATU12345678',
            ],
        ])
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], 'not-json'));

    $evatr->setHttpClient($mock);

    try {
        $evatr->check();
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(200)
            ->and($e->getError())->toBe('Invalid JSON response')
            ->and($e->getRaw())->toBe('not-json')
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_VALIDATION)
            ->toHaveKey('errorType', 'invalid_json');
    }
});

it('check throws ErrorResponse when status key is missing', function () {
    $evatr = new Evatr(vatIdOwn: 'DE123456789', vatIdForeign: 'ATU12345678');

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('post')
        ->once()
        ->with(Evatr::URL_VALIDATION, [
            'json' => [
                'anfragendeUstid' => 'DE123456789',
                'angefragteUstid' => 'ATU12345678',
            ],
        ])
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], '{}'));

    $evatr->setHttpClient($mock);

    try {
        $evatr->check();
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(200)
            ->and($e->getError())->toBe('Unexpected response format: missing status')
            ->and($e->getRaw())->toBe('{}')
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_VALIDATION)
            ->toHaveKey('errorType', 'unexpected_response');
    }
});

it('check throws ErrorResponse on network exception', function () {
    $evatr = new Evatr(vatIdOwn: 'DE123456789', vatIdForeign: 'ATU12345678');

    $mock = Mockery::mock(Client::class);
    $exception = new class('Network failed') extends Exception implements GuzzleException {};
    $mock->shouldReceive('post')
        ->once()
        ->with(Evatr::URL_VALIDATION, [
            'json' => [
                'anfragendeUstid' => 'DE123456789',
                'angefragteUstid' => 'ATU12345678',
            ],
        ])
        ->andThrow($exception);

    $evatr->setHttpClient($mock);

    try {
        $evatr->check();
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(0)
            ->and($e->getError())->toBe('Network failed')
            ->and($e->getRaw())->toBeNull()
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_VALIDATION)
            ->toHaveKey('errorType', 'network');
    }
});

it('getStatusMessages throws ErrorResponse on invalid JSON', function () {
    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_STATUS_MESSAGES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], 'oops'));

    try {
        Evatr::getStatusMessages($mock);
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(200)
            ->and($e->getError())->toBe('Invalid JSON response')
            ->and($e->getRaw())->toBe('oops')
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_STATUS_MESSAGES)
            ->toHaveKey('errorType', 'invalid_json');
    }
});

it('getStatusMessages throws ErrorResponse on network exception', function () {
    $mock = Mockery::mock(Client::class);
    $exception = new class('Network down') extends Exception implements GuzzleException {};
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_STATUS_MESSAGES)
        ->andThrow($exception);

    try {
        Evatr::getStatusMessages($mock);
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(0)
            ->and($e->getError())->toBe('Network down')
            ->and($e->getRaw())->toBeNull()
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_STATUS_MESSAGES)
            ->toHaveKey('errorType', 'network');
    }
});

it('getAvailability throws ErrorResponse on invalid JSON', function () {
    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], 'oops'));

    try {
        Evatr::getAvailability(false, $mock);
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(200)
            ->and($e->getError())->toBe('Invalid JSON response')
            ->and($e->getRaw())->toBe('oops')
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_EU_MEMBER_STATES)
            ->toHaveKey('errorType', 'invalid_json');
    }
});

it('getAvailability throws ErrorResponse on network exception', function () {
    $mock = Mockery::mock(Client::class);
    $exception = new class('Timeout') extends Exception implements GuzzleException {};
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andThrow($exception);

    try {
        Evatr::getAvailability(false, $mock);
        expect()->fail('Expected ErrorResponse to be thrown');
    } catch (ErrorResponse $e) {
        expect($e->getHttpCode())->toBe(0)
            ->and($e->getError())->toBe('Timeout')
            ->and($e->getRaw())->toBeNull()
            ->and($e->getMeta())
            ->toHaveKey('endpoint', Evatr::URL_EU_MEMBER_STATES)
            ->toHaveKey('errorType', 'network');
    }
});
