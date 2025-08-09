<?php

declare(strict_types=1);

use Rechtlogisch\Evatr\DTO\EUMemberState;

it('creates EUMemberState from array with values', function () {
    $dto = EUMemberState::fromArray([
        'alpha2' => 'DE',
        'verfuegbar' => true,
    ]);

    expect($dto)->toBeInstanceOf(EUMemberState::class)
        ->and($dto->code)->toBe('DE')
        ->and($dto->available)->toBeTrue();
});

it('creates EUMemberState from array with defaults when keys missing', function () {
    $dto = EUMemberState::fromArray([]);

    expect($dto->code)->toBe('')
        ->and($dto->available)->toBeFalse();
});
