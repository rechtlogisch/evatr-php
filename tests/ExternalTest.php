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
