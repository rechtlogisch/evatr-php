<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Enum\QualifiedResult;
use Rechtlogisch\Evatr\Enum\Status;

it('can be instantiated without response', function () {
    $dto = new ResultDto('A', 'B');

    expect($dto->getHttpStatusCode())->toBeNull()
        ->and($dto->getTimestamp())->toBeNull()
        ->and($dto->getStatus())->toBeNull()
        ->and($dto->getMessage())->toBeNull()
        ->and($dto->getCompany())->toBeNull()
        ->and($dto->getStreet())->toBeNull()
        ->and($dto->getZip())->toBeNull()
        ->and($dto->getLocation())->toBeNull()
        ->and($dto->getDateFrom())->toBeNull()
        ->and($dto->getDateTill())->toBeNull()
        ->and($dto->getRaw())->toBeNull();
});

it('parses response with minimal data', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('A', 'B', $response);

    expect($dto->getHttpStatusCode())->toBe(200)
        ->and($dto->getTimestamp())->toBe('2023-08-07T12:00:00Z')
        ->and($dto->getStatus())->toBe(Status::EVATR_0000)
        ->and($dto->getMessage())->toBe('Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.')
        ->and($dto->getCompany())->toBeNull()
        ->and($dto->getStreet())->toBeNull()
        ->and($dto->getZip())->toBeNull()
        ->and($dto->getLocation())->toBeNull()
        ->and($dto->getDateFrom())->toBeNull()
        ->and($dto->getDateTill())->toBeNull();
});

it('parses response with full qualified data', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
        'ergFirmenname' => 'A',
        'ergStrasse' => 'B',
        'ergPlz' => 'C',
        'ergOrt' => 'D',
        'gueltigAb' => '2020-01-01',
        'gueltigBis' => '2025-12-31',
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('X', 'Y', $response);

    expect($dto->getHttpStatusCode())->toBe(200)
        ->and($dto->getTimestamp())->toBe('2023-08-07T12:00:00Z')
        ->and($dto->getStatus())->toBe(Status::EVATR_0000)
        ->and($dto->getCompany())->toBe(QualifiedResult::A)
        ->and($dto->getStreet())->toBe(QualifiedResult::B)
        ->and($dto->getZip())->toBe(QualifiedResult::C)
        ->and($dto->getLocation())->toBe(QualifiedResult::D)
        ->and($dto->getVatIdOwn())->toBe('X')
        ->and($dto->getVatIdForeign())->toBe('Y')
        ->and($dto->getDateFrom())->toBe('2020-01-01')
        ->and($dto->getDateTill())->toBe('2025-12-31');
});

it('includes raw response when requested', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
    ];

    $response = new Response(
        200,
        ['Content-Type' => 'application/json', 'X-Custom-Header' => 'test'],
        json_encode($responseData, JSON_THROW_ON_ERROR)
    );
    $dto = new ResultDto('X', 'Y', $response, true);

    $raw = $dto->getRaw();
    expect($raw)->not()->toBeNull();

    $rawData = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    expect($rawData)->toHaveKey('headers')
        ->and($rawData)->toHaveKey('data')
        ->and($rawData['headers'])->toHaveKey('Content-Type')
        ->and($rawData['headers'])->toHaveKey('X-Custom-Header');
});

it('does not include raw response when not requested', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('X', 'Y', $response, false);

    expect($dto->getRaw())->toBeNull();
});

it('handles different status codes', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0004',
    ];

    $response = new Response(400, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('A', 'B', $response);

    expect($dto->getHttpStatusCode())->toBe(400)
        ->and($dto->getStatus())->toBe(Status::EVATR_0004);
});

it('converts to array correctly', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
        'ergFirmenname' => 'A',
        'ergStrasse' => 'B',
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('X', 'Y', $response);

    $array = $dto->toArray();

    expect($array)->toBe([
        'timestamp' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
        'message' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.',
        'company' => 'A',
        'street' => 'B',
        'zip' => null,
        'location' => null,
        'httpStatusCode' => 200,
        'vatIdOwn' => 'X',
        'vatIdForeign' => 'Y',
        'dateFrom' => null,
        'dateTill' => null,
        'raw' => null,
    ]);
});

it('handles malformed JSON gracefully', function () {
    $response = new Response(200, ['Content-Type' => 'application/json'], 'invalid json');

    new ResultDto('A', 'B', $response);
})->throws(JsonException::class);

it('handles empty response body', function () {
    $response = new Response(200, ['Content-Type' => 'application/json'], '{}');
    $dto = new ResultDto('A', 'B', $response);

    expect($dto->getHttpStatusCode())->toBe(200)
        ->and($dto->getTimestamp())->toBeNull()
        ->and($dto->getStatus())->toBeNull()
        ->and($dto->getMessage())->toBeNull();
});

it('handles raw response with invalid JSON in setRaw', function () {
    // Create a mocked response that forces json_encode() to throw due to malformed UTF-8
    $invalid = "\xB1\x31"; // invalid UTF-8 (in headers)
    $validBody = '{"ok":true}'; // valid JSON so constructor parsing succeeds

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('getContents')->andReturn($validBody);

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getHeaders')->andReturn(['X-Bad' => [$invalid]]);
    $response->shouldReceive('getBody')->andReturn($stream);
    $response->shouldReceive('getStatusCode')->andReturn(200);

    $dto = new ResultDto('A', 'B', $response, true);

    // When json_encode throws, setRaw() should fall back to the plain body string
    expect($dto->getRaw())->toBe($validBody);
});

it('handles valid JSON that is not an array by resetting to empty data', function () {
    // Body is valid JSON (boolean true) but not an array; should not throw and should result in empty parsed data
    $response = new Response(200, ['Content-Type' => 'application/json'], 'true');
    $dto = new ResultDto('A', 'B', $response);

    expect($dto->getHttpStatusCode())->toBe(200)
        ->and($dto->getTimestamp())->toBeNull()
        ->and($dto->getStatus())->toBeNull()
        ->and($dto->getMessage())->toBeNull()
        ->and($dto->getCompany())->toBeNull()
        ->and($dto->getStreet())->toBeNull()
        ->and($dto->getZip())->toBeNull()
        ->and($dto->getLocation())->toBeNull()
        ->and($dto->getDateFrom())->toBeNull()
        ->and($dto->getDateTill())->toBeNull();
});
