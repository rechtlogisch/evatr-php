<?php

use Rechtlogisch\Evatr\DTO\StatusMessage;

it('creates StatusMessage from array with all fields', function () {
    $data = [
        'status' => 'evatr-0000',
        'kategorie' => 'Ergebnis',
        'httpcode' => 200,
        'feld' => null,
        'meldung' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.',
    ];

    $dto = StatusMessage::fromArray($data);

    expect($dto->status)->toBe('evatr-0000')
        ->and($dto->category)->toBe('Result')
        ->and($dto->http)->toBe(200)
        ->and($dto->field)->toBeNull()
        ->and($dto->message)->toBe('Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.');
});

it('creates StatusMessage from array with minimal fields', function () {
    $data = [
        'status' => 'evatr-0007',
        'meldung' => 'Fehlerhafter Aufruf.',
    ];

    $dto = StatusMessage::fromArray($data);

    expect($dto->status)->toBe('evatr-0007')
        ->and($dto->category)->toBeNull()
        ->and($dto->http)->toBeNull()
        ->and($dto->field)->toBeNull()
        ->and($dto->message)->toBe('Fehlerhafter Aufruf.');
});
