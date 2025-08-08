<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Rechtlogisch\Evatr\Evatr;

it('rejects injected client for getStatusMessages outside testing env', function () {
    $prev = $_ENV['APP_ENV'] ?? null;
    unset($_ENV['APP_ENV']); // default to production

    try {
        expect(function () {
            /** @noinspection PhpUnhandledExceptionInspection */
            return Evatr::getStatusMessages(new Client(['http_errors' => false]));
        })->toThrow(RuntimeException::class);
    } finally {
        if ($prev === null) {
            /** @noinspection PhpConditionAlreadyCheckedInspection */
            unset($_ENV['APP_ENV']);
        } else {
            $_ENV['APP_ENV'] = $prev;
        }
    }
});
