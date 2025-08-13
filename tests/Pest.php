<?php

use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Enum\QualifiedResult;
use Rechtlogisch\Evatr\Enum\Status;
use Rechtlogisch\Evatr\Enum\Variables;

// Input helpers
function hasVatId(
    Variables $key,
    bool $throw = true
): bool {
    if (! empty($_ENV[$key->name])) {
        return true;
    }

    if ($throw === true) {
        throw new RuntimeException(ucfirst(strtolower($key->name))." VAT-ID not set in environment file. Please set VATID_{$key->name}. For example in .env");
    }

    return false;
}

// Result helpers
function fixture(
    string $name,
): string {
    $file = __DIR__."/Fixtures/{$name}";
    if (! file_exists($file)) {
        throw new RuntimeException("Fixture file {$file} does not exist.");
    }

    return file_get_contents($file);
}

// Expectation helpers
function expectOk(
    ResultDto $result,
): void {
    expectResult($result);
    expectValidTimestamp($result->getTimestamp());
}

function expectConfirmationOk(
    ResultDto $result,
): void {
    expectOk($result);

    expect($result->getCompany())->toBe(QualifiedResult::A)
        ->and($result->getLocation())->toBe(QualifiedResult::A)
        ->and($result->getStreet())->toBe(QualifiedResult::A)
        ->and($result->getZip())->toBe(QualifiedResult::A);
}

function expectResult(
    ResultDto $result,
    int $httpStatusCode = 200,
    Status $status = Status::EVATR_0000,
): void {
    expect($result)
        ->toBeInstanceOf(ResultDto::class)
        ->and($result->getHttpStatusCode())->toBeInt()->toBe($httpStatusCode)
        ->and($result->getStatus())->toBeInstanceOf(Status::class)->toBe($status);
}

/**
 * @throws Exception
 */
function expectValidTimestamp(
    ?string $timestamp = null,
): void {
    $dt = new DateTimeImmutable($timestamp);
    $berlin = new DateTimeZone('Europe/Berlin');
    $dtBerlin = $dt->setTimezone($berlin);
    $offset = $dtBerlin->format('P');

    expect($timestamp)->toBeString()
        ->not->toBeEmpty()
        // ISO-8601 with time zone (+01:00 CET or +02:00 CEST)
        ->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6,9}\+0(1|2):00$/')
        // Verify valid timestamp and correct time zone
        ->toContain($offset)
        ->and(strtotime($timestamp))->toBeGreaterThan(0);
}
