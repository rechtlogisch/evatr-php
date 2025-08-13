<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Enum\QualifiedResult;
use Rechtlogisch\Evatr\Enum\Status;
use Rechtlogisch\Evatr\Exception\ErrorResponse;

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

it('sets timestamp to null when missing', function () {
    $responseData = [
        // 'anfrageZeitpunkt' intentionally omitted
        'status' => 'evatr-0000',
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('X', 'Y', $response);

    expect($dto->getTimestamp())->toBeNull()
        ->and($dto->getStatus())->toBe(Status::EVATR_0000)
        ->and($dto->getMessage())->toBe('Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.');
});

it('falls back to plain body for raw when headers cause json_encode to fail', function () {
    // valid body to pass JSON parsing
    $validBody = json_encode([
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 'evatr-0000',
    ], JSON_THROW_ON_ERROR);

    // Prepare mocked response with invalid UTF-8 in headers to trigger json_encode error in setRaw()
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('getContents')->andReturn($validBody);
    $stream->shouldReceive('rewind')->andReturnNull();

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getHeaders')->andReturn(['X-Bad' => ["\xB1\x31"]]);
    $response->shouldReceive('getBody')->andReturn($stream);
    $response->shouldReceive('getStatusCode')->andReturn(200);

    $dto = new ResultDto('A', 'B', $response, includeRaw: true);

    expect($dto->getRaw())->toBe($validBody);
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
    $dto = new ResultDto('X', 'Y', $response, includeRaw: true);

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
    $dto = new ResultDto('X', 'Y', $response, includeRaw: false);

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

it('throws when response body is invalid JSON', function () {
    $response = new Response(200, ['Content-Type' => 'application/json'], 'invalid json');
    new ResultDto('A', 'B', $response);
})->throws(ErrorResponse::class, 'Invalid JSON response');

it('throws when response body is an empty object', function () {
    $response = new Response(200, ['Content-Type' => 'application/json'], '{}');
    new ResultDto('A', 'B', $response);
})->throws(ErrorResponse::class, 'Unexpected response format: missing status');

it('throws when content type is application/json but body is not an array', function () {
    // Body is valid JSON (boolean true) but not an array; should not throw and should result in empty parsed data
    $response = new Response(200, ['Content-Type' => 'application/json'], 'true');
    new ResultDto('A', 'B', $response);
})->throws(ErrorResponse::class, 'Unexpected response format: missing status');

it('handles present but non-string status gracefully', function () {
    $responseData = [
        'anfrageZeitpunkt' => '2023-08-07T12:00:00Z',
        'status' => 1234,
    ];

    $response = new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData, JSON_THROW_ON_ERROR));
    $dto = new ResultDto('X', 'Y', $response);

    expect($dto->getStatus())->toBeNull()
        ->and($dto->getMessage())->toBeNull();
});
