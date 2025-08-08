<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\DTO\EUMemberState;
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

    /** @noinspection PhpUnhandledExceptionInspection */
    $messages = Evatr::getStatusMessages($mock);

    expect($messages)->toBeArray()->not->toBeEmpty()
        ->and($messages[0])->toBeInstanceOf(StatusMessage::class);
});

it('can fetch EU member states availability', function () {
    $_ENV['APP_ENV'] = 'testing';
    /** @noinspection PhpUnhandledExceptionInspection */
    $apiResponse = json_encode([
        ['alpha2' => 'DE', 'name' => 'Germany', 'verfuegbar' => true],
        ['alpha2' => 'AT', 'name' => 'Austria', 'verfuegbar' => false],
    ], JSON_THROW_ON_ERROR);

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    /** @noinspection PhpUnhandledExceptionInspection */
    $states = Evatr::checkAvailability($mock);

    expect($states)->toBeArray()->toHaveCount(2)
        ->and($states[0])->toBeInstanceOf(EUMemberState::class)
        ->and($states[0]->code)->toBe('DE')
        ->and($states[0]->available)->toBeTrue();
});
