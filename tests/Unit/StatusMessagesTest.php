<?php

use Rechtlogisch\Evatr\StatusMessages;

it('returns German messages by default', function () {
    unset($_ENV['EVATR_LANG']);
    $msg = StatusMessages::messageFor('evatr-0000');
    expect($msg)->toBe('Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.');
});

it('returns English messages when EVATR_LANG is set to en', function () {
    $prev = $_ENV['EVATR_LANG'] ?? null;
    $_ENV['EVATR_LANG'] = 'en';
    $msg = StatusMessages::messageFor('evatr-0000');
    expect($msg)->toBe('The foreign VAT-ID is valid at the time of the request.');
    if ($prev === null) {
        unset($_ENV['EVATR_LANG']);
    } else {
        $_ENV['EVATR_LANG'] = $prev;
    }
});

it('returns http code for known status', function () {
    unset($_ENV['EVATR_LANG']);
    $code = StatusMessages::httpCodeFor('evatr-0000');
    expect($code)->toBe(200);
});

it('returns nulls for unknown status', function () {
    unset($_ENV['EVATR_LANG']);
    expect(StatusMessages::forCode('evatr-9999'))->toBeNull()
        ->and(StatusMessages::httpCodeFor('evatr-9999'))->toBeNull()
        ->and(StatusMessages::messageFor('evatr-9999'))->toBeNull();
});
