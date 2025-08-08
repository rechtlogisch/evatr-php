<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\Evatr;

it('checks a vat-id', function (string $vatIdOwn, string $vatIdForeign) {
    $prev = $_ENV['APP_ENV'] ?? null;
    $_ENV['APP_ENV'] = 'testing';

    try {
        $mock = Mockery::mock(Client::class);

        $mock->shouldReceive('post')
            ->once()
            ->with(Evatr::URL_VALIDATION, [
                'json' => [
                    'anfragendeUstid' => $vatIdOwn,
                    'angefragteUstid' => $vatIdForeign,
                ],
            ])
            ->andReturn(
                new Response(200, ['Content-Type' => 'application/json'], fixture('response-simple-ok.json'))
            );

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = (new Evatr(
            vatIdOwn: $vatIdOwn,
            vatIdForeign: $vatIdForeign
        ))
            ->setHttpClient($mock)
            ->check();

        expectOk($result);
    } finally {
        if ($prev === null) {
            unset($_ENV['APP_ENV']);
        } else {
            $_ENV['APP_ENV'] = $prev;
        }
    }
})->with('vatids');
