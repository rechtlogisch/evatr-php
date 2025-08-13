<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\DTO\StatusMessage;
use Rechtlogisch\Evatr\Evatr;

beforeEach(function () {
    $_ENV['APP_ENV'] = 'testing';
});

it('getStatusMessages skips entries without status key', function () {
    $apiResponse = json_encode([
        [
            'status' => 'evatr-0000',
            'kategorie' => 'Ergebnis',
            'httpcode' => 200,
            'feld' => null,
            'meldung' => 'OK',
        ],
        [
            // missing 'status' should be skipped silently
            'kategorie' => 'Fehler',
            'httpcode' => 400,
            'feld' => null,
            'meldung' => 'Invalid',
        ],
    ], JSON_THROW_ON_ERROR);

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_STATUS_MESSAGES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    $messages = Evatr::getStatusMessages($mock);

    expect($messages)->toBeArray()->toHaveCount(1)
        ->and(array_keys($messages))->toBe(['evatr-0000'])
        ->and($messages['evatr-0000'])->toBeInstanceOf(StatusMessage::class);
});

it('checkAvailability skips entries without valid alpha2', function () {
    $apiResponse = json_encode([
        ['alpha2' => 'DE', 'name' => 'Germany', 'verfuegbar' => true],
        ['alpha2' => 123, 'name' => 'Broken', 'verfuegbar' => false], // invalid type, should be skipped
        ['name' => 'Poland', 'verfuegbar' => true], // missing alpha2, should be skipped
        ['alpha2' => 'AT', 'name' => 'Austria', 'verfuegbar' => false],
    ], JSON_THROW_ON_ERROR);

    $mock = Mockery::mock(Client::class);
    $mock->shouldReceive('get')
        ->once()
        ->with(Evatr::URL_EU_MEMBER_STATES)
        ->andReturn(new Response(200, ['Content-Type' => 'application/json'], $apiResponse));

    $states = Evatr::checkAvailability(false, $mock);

    expect($states)->toBeArray()->toHaveCount(2)
        ->and(array_keys($states))->toEqualCanonicalizing(['DE', 'AT'])
        ->and($states['DE'])->toBeTrue()
        ->and($states['AT'])->toBeFalse();
});
