<?php

use Rechtlogisch\Evatr\Enum\Status;
use Rechtlogisch\Evatr\Enum\Variables;
use Rechtlogisch\Evatr\Evatr;

beforeAll(function () {
    (Dotenv\Dotenv::createImmutable(dirname(__DIR__)))->safeLoad();
});

it('sends a request', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
    ))->check();

    expectOk($result);
})->group('external');

it('sends a request for qualified confirmation', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Musterhaus GmbH & Co KG',
        location: 'musterort',
        street: 'Musterstrasse 22',
        zip: '12345',
    ))->check();

    expectConfirmationOk($result);
})->group('external');

it('sends a request with own data', function () {
    hasVatId(Variables::VATID_OWN);
    hasVatId(Variables::VATID_FOREIGN);

    $result = (new Evatr(
        vatIdOwn: $_ENV[Variables::VATID_OWN->name],
        vatIdForeign: $_ENV[Variables::VATID_FOREIGN->name],
    ))->check();

    expectOk($result);
})->group('external');

it('handles 400', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789', // dummy
        vatIdForeign: 'ATU12345679', // dummy
    ))->check();

    expectResult($result, 400, Status::EVATR_0004);
})->group('external', 'error-handling');

it('sends a request to check a German VAT-ID, and the specific own VAT-ID is not allowed to check German VAT-IDs', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789', // dummy
        vatIdForeign: 'DE987654321', // dummy
    ))->check();

    expectResult($result, 403, Status::EVATR_0006);
})->group('external', 'error-handling');

it('returns error when non-German VAT-ID is used as own', function () {
    $result = (new Evatr(
        vatIdOwn: 'LU98765432', // dummy
        vatIdForeign: 'LU98765432', // dummy
    ))->check();

    expectResult($result, 400, Status::EVATR_0004);
})->group('external', 'error-handling');

it('checks with helper function', function () {
    hasVatId(Variables::VATID_OWN);
    hasVatId(Variables::VATID_FOREIGN);
    $result = checkVatId(
        vatIdOwn: $_ENV[Variables::VATID_OWN->name],
        vatIdForeign: $_ENV[Variables::VATID_FOREIGN->name],
    );

    expectOk($result);
})->group('external');

it('sends a request and includes raw response when requested', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Musterhaus GmbH & Co KG',
        location: 'musterort',
        street: 'Musterstrasse 22',
        zip: '12345',
    ))->includeRaw()->check();

    $raw = $result->getRaw();
    expect($raw)->toBeString()
        ->not()->toBeNull()
        ->not()->toBeEmpty();

    $rawData = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    expect($rawData)->toHaveKey('headers')
        ->and($rawData)->toHaveKey('data');
})->group('external');

it('sends a qualified request with includeRaw and validates raw structure with patterns', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Musterhaus GmbH & Co KG',
        location: 'musterort',
        street: 'Musterstrasse 22',
        zip: '12345',
    ))->includeRaw()->check();

    $raw = $result->getRaw();
    expect($raw)->toBeString()->not()->toBeEmpty();

    $rawData = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    expect($rawData['headers'])->toBeArray();
    $headers = $rawData['headers'];
    // Ensure all expected headers from the example are present
    expect($headers)->toHaveKeys([
        'Access-Control-Allow-Headers',
        'Access-Control-Allow-Methods',
        'Access-Control-Allow-Origin',
        'Content-Type',
        'Date',
        'Referrer-Policy',
        'Server',
        'Set-Cookie',
        'Strict-Transport-Security',
        'Transfer-Encoding',
        'Vary',
        'X-Content-Type-Options',
        'X-Frame-Options',
    ])
        ->and($headers['Date'])
        ->toBeString()
        ->toMatch('/^[A-Z][a-z]{2}, \d{2} [A-Z][a-z]{2} \d{4} \d{2}:\d{2}:\d{2} GMT$/')
        ->and($headers['Server'])
        ->toBeString()
        ->toMatch('/^.+$/')
        ->toBe('Apache')
        ->and($headers['Set-Cookie'])
        ->toBeString()
        ->toMatch('/^AL_BALANCE-S=([^;]+); Path=\/; Secure; HttpOnly; SameSite=None$/')
        ->and($headers['Access-Control-Allow-Origin'])
        ->toBeString()
        ->toBe('*')
        ->and($headers['Vary'])
        ->toBeString()
        ->toBe('Origin,Access-Control-Request-Method,Access-Control-Request-Headers')
        ->and($headers['Access-Control-Allow-Headers'])
        ->toBeString()
        ->toBe('*')
        ->and($headers['Access-Control-Allow-Methods'])
        ->toBeString()
        ->toBe('GET,POST,OPTIONS')
        ->and($headers['X-Frame-Options'])
        ->toBeString()
        ->toBe('SAMEORIGIN')
        ->and($headers['Strict-Transport-Security'])
        ->toBeString()
        ->toMatch('/^max-age=\d+/')
        ->toBe('max-age=31536000')
        ->and($headers['X-Content-Type-Options'])
        ->toBeString()
        ->toBe('nosniff')
        ->and($headers['Referrer-Policy'])
        ->toBeString()
        ->toBe('same-origin')
        ->and($headers['Transfer-Encoding'])
        ->toBeString()
        ->toBe('chunked')
        ->and($headers['Content-Type'])
        ->toBeString()
        ->toBe('application/json; charset=UTF-8');

    $data = json_decode($rawData['data'], true, 512, JSON_THROW_ON_ERROR);
    expect($data)->toBeArray()
        ->toHaveKeys([
            'id',
            'anfrageZeitpunkt',
            'status',
            'ergFirmenname',
            'ergStrasse',
            'ergPlz',
            'ergOrt',
        ])
        ->and($data['id'])
        ->toBeString()
        ->toMatch('/^[a-f0-9]{16}$/')
        ->and($data['anfrageZeitpunkt'])
        ->toBeString()
        ->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{9}\+0[1|2]:00$/')
        ->and($data['status'])
        ->toBeString()
        ->toMatch('/^evatr-\d{4}$/i');

    foreach (['ergFirmenname', 'ergStrasse', 'ergPlz', 'ergOrt'] as $field) {
        expect($data[$field])
            ->toBeString()
            ->toMatch('/^[ABCD]$/');
    }

    foreach (['gueltigAb', 'gueltigBis'] as $field) {
        if (array_key_exists($field, $data) && $data[$field] !== null) {
            expect($data[$field])
                ->toBeString()
                ->toMatch('/^\d{4}-\d{2}-\d{2}$/');
        }
    }
})->group('external');

it('raw response contains only expected keys for qualified includeRaw', function () {
    $result = (new Evatr(
        vatIdOwn: 'DE123456789',
        vatIdForeign: 'ATU12345678',
        company: 'Musterhaus GmbH & Co KG',
        location: 'musterort',
        street: 'Musterstrasse 22',
        zip: '12345',
    ))->includeRaw()->check();

    $raw = $result->getRaw();
    expect($raw)->toBeString()->not()->toBeEmpty();

    $rawData = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    expect(array_keys($rawData))->toEqualCanonicalizing(['headers', 'data']);

    // Headers keys must match the example exactly
    $expectedHeaderKeys = [
        'Access-Control-Allow-Headers',
        'Access-Control-Allow-Methods',
        'Access-Control-Allow-Origin',
        'Content-Type',
        'Date',
        'Referrer-Policy',
        'Server',
        'Set-Cookie',
        'Strict-Transport-Security',
        'Transfer-Encoding',
        'Vary',
        'X-Content-Type-Options',
        'X-Frame-Options',
    ];
    expect(array_keys($rawData['headers']))->toEqualCanonicalizing($expectedHeaderKeys);

    $data = json_decode($rawData['data'], true, 512, JSON_THROW_ON_ERROR);
    $expectedDataKeys = [
        'id',
        'anfrageZeitpunkt',
        'status',
        'ergFirmenname',
        'ergStrasse',
        'ergPlz',
        'ergOrt',
    ];
    expect(array_keys($data))->toEqualCanonicalizing($expectedDataKeys);
})->group('external');
