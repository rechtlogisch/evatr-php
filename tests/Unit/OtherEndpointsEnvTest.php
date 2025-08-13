<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Rechtlogisch\Evatr\Evatr;
use Rechtlogisch\Evatr\Exception\InputError;

it('rejects injected client for getStatusMessages outside testing env', function () {
    unset($_ENV['APP_ENV']);
    Evatr::getStatusMessages(new Client(['http_errors' => false]));
})->throws(InputError::class, 'Setting a custom HTTP client is only allowed in a testing environment.');
