<?php

use Rechtlogisch\Evatr\DTO\ResultDto;

beforeEach(function () {
    $_ENV['APP_ENV'] = 'testing';
});

it('checkVatId function works with basic parameters', function () {
    // Since we can't easily mock the function's internal Evatr creation,
    // we'll test the function logic by ensuring it returns a ResultDto
    $result = checkVatId('DE123456789', 'ATU12345678');

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('checkVatId function works with includeRaw parameter', function () {
    $result = checkVatId('DE123456789', 'ATU12345678', true);

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('checkVatId function works with includeRaw false', function () {
    $result = checkVatId('DE123456789', 'ATU12345678', false);

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('confirmVatId function works with all parameters', function () {
    $result = confirmVatId(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Test Company',
        street: 'Test Street 123',
        zip: '12345',
        location: 'Test City'
    );

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('confirmVatId function works with null optional parameters', function () {
    $result = confirmVatId(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: null,
        street: null,
        zip: null,
        location: null
    );

    expect($result)->toBeInstanceOf(ResultDto::class);
});

it('confirmVatId function works with includeRaw parameter', function () {
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
});

it('confirmVatId function works with includeRaw false', function () {
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
});

it('confirmVatId function works with mixed null and non-null parameters', function () {
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
});
