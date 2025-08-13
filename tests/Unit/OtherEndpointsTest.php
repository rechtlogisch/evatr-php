<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\DTO\StatusMessage;
use Rechtlogisch\Evatr\Evatr;

it('can fetch status messages', function () {
    $_ENV['APP_ENV'] = 'testing';
    $apiResponse = file_get_contents(dirname(__DIR__, 2).'/docs/statusmeldungen.json');

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_STATUS_MESSAGES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    $messages = Evatr::getStatusMessages($mock);

    expect($messages)->toBeArray()->not->toBeEmpty();
    $firstKey = array_keys($messages)[0];
    expect($firstKey)->toBeString()->toBe('evatr-0000');
    $firstValue = array_values($messages)[0];
    expect($firstValue)->toBeInstanceOf(StatusMessage::class);
});

it('can fetch EU member states availability as map', function () {
    $_ENV['APP_ENV'] = 'testing';
    $apiResponse = json_encode([
        ['alpha2' => 'DE', 'name' => 'Germany', 'verfuegbar' => true],
        ['alpha2' => 'AT', 'name' => 'Austria', 'verfuegbar' => false],
    ], JSON_THROW_ON_ERROR);

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    $states = Evatr::checkAvailability(false, $mock);

    expect($states)->toBeArray()->toHaveCount(2)
        ->and($states['DE'])->toBeTrue()
        ->and($states['AT'])->toBeFalse();
});

it('can fetch only not available EU member states', function () {
    $_ENV['APP_ENV'] = 'testing';
    $apiResponse = json_encode([
        ['alpha2' => 'DE', 'name' => 'Germany', 'verfuegbar' => true],
        ['alpha2' => 'AT', 'name' => 'Austria', 'verfuegbar' => false],
        ['alpha2' => 'FR', 'name' => 'France', 'verfuegbar' => false],
    ], JSON_THROW_ON_ERROR);

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    $states = Evatr::checkAvailability(true, $mock);

    expect($states)->toBeArray()->toHaveCount(2)
        ->and(array_keys($states))->toEqualCanonicalizing(['AT', 'FR'])
        ->and($states['AT'])->toBeFalse()
        ->and($states['FR'])->toBeFalse();
});
