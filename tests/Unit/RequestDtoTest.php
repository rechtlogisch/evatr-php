<?php

use Rechtlogisch\Evatr\DTO\RequestDto;

it('can be instantiated with all parameters', function () {
    $dto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Test Company',
        street: 'Test Street 123',
        zip: '12345',
        location: 'Test City'
    );

    expect($dto->vatIdOwn)->toBe('DE123456789')
        ->and($dto->vatIdForeign)->toBe('ATU12345678')
        ->and($dto->company)->toBe('Test Company')
        ->and($dto->street)->toBe('Test Street 123')
        ->and($dto->zip)->toBe('12345')
        ->and($dto->location)->toBe('Test City');
});

it('can be instantiated with minimal parameters', function () {
    $dto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    expect($dto->vatIdOwn)->toBe('DE123456789')
        ->and($dto->vatIdForeign)->toBe('ATU12345678')
        ->and($dto->company)->toBeNull()
        ->and($dto->street)->toBeNull()
        ->and($dto->zip)->toBeNull()
        ->and($dto->location)->toBeNull();
});

it('can be instantiated with no parameters', function () {
    $dto = new RequestDto;

    expect($dto->vatIdOwn)->toBeNull()
        ->and($dto->vatIdForeign)->toBeNull()
        ->and($dto->company)->toBeNull()
        ->and($dto->street)->toBeNull()
        ->and($dto->zip)->toBeNull()
        ->and($dto->location)->toBeNull();
});

it('can set and get includeRaw flag', function () {
    $dto = new RequestDto;

    $result = $dto->setIncludeRaw(true);
    expect($result)->toBe($dto); // Should return self for method chaining

    $result = $dto->setIncludeRaw(false);
    expect($result)->toBe($dto);
});

it('converts to array with all fields', function () {
    $dto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Test Company',
        street: 'Test Street 123',
        zip: '12345',
        location: 'Test City'
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'anfragendeUstid' => 'DE123456789',
        'angefragteUstid' => 'ATU12345678',
        'firmenname' => 'Test Company',
        'strasse' => 'Test Street 123',
        'plz' => '12345',
        'ort' => 'Test City',
    ]);
});

it('converts to array with minimal fields', function () {
    $dto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678'
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'anfragendeUstid' => 'DE123456789',
        'angefragteUstid' => 'ATU12345678',
    ]);
});

it('filters out null values in toArray', function () {
    $dto = new RequestDto(
        vatIdOwn: 'DE123456789',
        vatIdForeign: null,
        company: 'Test Company',
        street: null,
        zip: '12345',
        location: null
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'anfragendeUstid' => 'DE123456789',
        'firmenname' => 'Test Company',
        'plz' => '12345',
    ]);
});

it('returns empty array when all fields are null', function () {
    $dto = new RequestDto;
    $array = $dto->toArray();

    expect($array)->toBe([]);
});
