<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rechtlogisch\Evatr\Evatr;

it('checks a vat-id', function (string $vatIdOwn, string $vatIdForeign) {
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

    $result = (new Evatr(
        vatIdOwn: $vatIdOwn,
        vatIdForeign: $vatIdForeign
    ))
        ->setHttpClient($mock)
        ->check();

    expectOk($result);
})->with('vatids');
