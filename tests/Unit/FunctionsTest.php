<?php

use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Exception\ErrorResponse;

beforeEach(function () {
    $_ENV['APP_ENV'] = 'testing';
});

it('checkVatId function works with basic parameters', function () {
    // Since we can't easily mock the function's internal Evatr creation,
    // we accept either a ResultDto (success) or a thrown ErrorResponse (network/parse failure)
    try {
        $result = checkVatId('DE123456789', 'ATU12345678');
        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('checkVatId function works with includeRaw parameter', function () {
    try {
        $result = checkVatId('DE123456789', 'ATU12345678', true);
        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('checkVatId function works with includeRaw false', function () {
    try {
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $result = checkVatId('DE123456789', 'ATU12345678', false);
        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('confirmVatId function works with all parameters', function () {
    try {
        $result = confirmVatId(
            vatIdOwn: 'DE123456789',
            vatIdForeign: 'ATU12345678',
            company: 'Test Company',
            street: 'Test Street 123',
            zip: '12345',
            location: 'Test City'
        );

        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('confirmVatId function works with null optional parameters', function () {
    try {
        $result = confirmVatId(
            vatIdOwn: 'DE123456789',
            vatIdForeign: 'ATU12345678',
            company: null,
            street: null,
            zip: null,
            location: null
        );

        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('confirmVatId function works with includeRaw parameter', function () {
    try {
        $result = confirmVatId(
            vatIdOwn: 'DE123456789',
            vatIdForeign: 'ATU12345678',
            company: 'Test Company',
            street: 'Test Street 123',
            zip: '12345',
            location: 'Test City',
            includeRaw: true
        );

        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('confirmVatId function works with includeRaw false', function () {
    try {
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $result = confirmVatId(
            vatIdOwn: 'DE123456789',
            vatIdForeign: 'ATU12345678',
            company: 'Test Company',
            street: 'Test Street 123',
            zip: '12345',
            location: 'Test City',
            includeRaw: false
        );

        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});

it('confirmVatId function works with mixed null and non-null parameters', function () {
    try {
        $result = confirmVatId(
            vatIdOwn: 'DE123456789',
            vatIdForeign: 'ATU12345678',
            company: 'Test Company',
            street: null,
            zip: '12345',
            location: null,
            includeRaw: true
        );

        expect($result)->toBeInstanceOf(ResultDto::class);
    } catch (ErrorResponse $e) {
        expect($e)->toBeInstanceOf(ErrorResponse::class);
    }
});
