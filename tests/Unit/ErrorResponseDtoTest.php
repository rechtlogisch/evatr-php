<?php

use Rechtlogisch\Evatr\Exception\ErrorResponse;

it('creates ErrorResponse with all fields and toArray works', function () {
    $meta = [
        'endpoint' => 'https://example.test',
        'errorType' => 'invalid_json',
        'extra' => ['a' => 1],
    ];

    $previous = new RuntimeException('previous');

    $err = new ErrorResponse(
        httpCode: 418,
        error: 'I am a teapot',
        exception: $previous,
        raw: '{"oops":true}',
        meta: $meta,
    );

    expect($err->getHttpCode())->toBe(418)
        ->and($err->getError())->toBe('I am a teapot')
        ->and($err->getException())->toBe($previous)
        ->and($err->getRaw())->toBe('{"oops":true}')
        ->and($err->getMeta())->toBe($meta);

    $arr = $err->toArray();
    expect($arr)
        ->toHaveKey('httpCode', 418)
        ->toHaveKey('error', 'I am a teapot')
        ->toHaveKey('raw', '{"oops":true}')
        ->toHaveKey('meta')
        ->and($arr['meta'])
        ->toBe($meta);
});

it('creates ErrorResponse with defaults (null raw, empty meta)', function () {
    $previous = new RuntimeException('network');
    $err = new ErrorResponse(
        httpCode: 0,
        error: 'Network error',
        exception: $previous,
    );

    expect($err->getHttpCode())->toBe(0)
        ->and($err->getError())->toBe('Network error')
        ->and($err->getException())->toBe($previous)
        ->and($err->getRaw())->toBeNull()
        ->and($err->getMeta())->toBeArray()->toBeEmpty();

    $arr = $err->toArray();
    expect($arr)
        ->toHaveKey('httpCode', 0)
        ->toHaveKey('error', 'Network error')
        ->toHaveKey('raw', null)
        ->toHaveKey('meta')
        ->and($arr['meta'])
        ->toBeArray()->toBeEmpty();
});
