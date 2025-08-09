<?php

declare(strict_types=1);

use Rechtlogisch\Evatr\Util\StatusMessagesComparer;

it('maps German messages to a status=>message map', function () {
    $data = [
        ['status' => 'evatr-0000', 'meldung' => 'OK'],
        ['status' => 'evatr-0004', 'meldung' => 'Bad request'],
        ['status' => '', 'meldung' => 'ignored'], // invalid, no status
    ];

    $map = StatusMessagesComparer::toGermanMap($data);

    expect($map)->toBe([
        'evatr-0000' => 'OK',
        'evatr-0004' => 'Bad request',
    ]);
});

it('diff returns empty differences for equal maps', function () {
    $local = ['evatr-0000' => 'A', 'evatr-0004' => 'B'];
    $remote = ['evatr-0000' => 'A', 'evatr-0004' => 'B'];

    $diff = StatusMessagesComparer::diff($local, $remote);

    expect($diff['missing'])->toBe([])
        ->and($diff['new'])->toBe([])
        ->and($diff['changed'])->toBe([]);
});

it('diff detects missing and new keys', function () {
    $local = ['evatr-0000' => 'A', 'evatr-0004' => 'B'];
    $remote = ['evatr-0004' => 'B', 'evatr-0006' => 'C'];

    $diff = StatusMessagesComparer::diff($local, $remote);

    expect($diff['missing'])->toBe(['evatr-0000'])
        ->and($diff['new'])->toBe(['evatr-0006'])
        ->and($diff['changed'])->toBe([]);
});

it('diff detects changed messages for shared keys', function () {
    $local = ['evatr-0000' => 'Old'];
    $remote = ['evatr-0000' => 'New'];

    $diff = StatusMessagesComparer::diff($local, $remote);

    expect($diff['missing'])->toBe([])
        ->and($diff['new'])->toBe([])
        ->and($diff['changed'])->toHaveKey('evatr-0000')
        ->and($diff['changed']['evatr-0000']['local'])->toBe('Old')
        ->and($diff['changed']['evatr-0000']['remote'])->toBe('New');
});
