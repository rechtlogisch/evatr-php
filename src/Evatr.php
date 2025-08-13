<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Rechtlogisch\Evatr\DTO\RequestDto;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\DTO\StatusMessage;
use Rechtlogisch\Evatr\Enum\HttpMethod;
use Rechtlogisch\Evatr\Exception\ErrorResponse;
use Rechtlogisch\Evatr\Exception\InputError;

class Evatr
{
    public const BASE_URL = 'https://api.evatr.vies.bzst.de/app/v1';

    public const URL_VALIDATION = self::BASE_URL.'/abfrage';

    public const URL_STATUS_MESSAGES = self::BASE_URL.'/info/statusmeldungen';

    public const URL_EU_MEMBER_STATES = self::BASE_URL.'/info/eu_mitgliedstaaten';

    private Client $client;

    private RequestDto $request;

    /**
     * @param  mixed  ...$input
     */
    public function __construct(
        ...$input
    ) {
        if (file_exists(($pathBase = dirname(__DIR__)).DIRECTORY_SEPARATOR.'.env')) {
            (Dotenv::createImmutable($pathBase))->safeLoad();
        }

        $this->client = new Client([
            'http_errors' => false,
        ]);

        if (count($input) === 1 && $input[0] instanceof RequestDto) {
            $this->request = $input[0];
        } else {
            /** @var array<int|string, mixed> $args */
            $args = $input;
            $this->request = new RequestDto(
                vatIdOwn: isset($args['vatIdOwn']) && is_string($args['vatIdOwn']) ? $args['vatIdOwn'] : null,
                vatIdForeign: isset($args['vatIdForeign']) && is_string($args['vatIdForeign']) ? $args['vatIdForeign'] : null,
                company: isset($args['company']) && is_string($args['company']) ? $args['company'] : null,
                street: isset($args['street']) && is_string($args['street']) ? $args['street'] : null,
                zip: isset($args['zip']) && is_string($args['zip']) ? $args['zip'] : null,
                location: isset($args['location']) && is_string($args['location']) ? $args['location'] : null,
            );
        }
    }

    /**
     * Perform a validation check against the eVatR API.
     *
     * @throws ErrorResponse on transport/JSON/response-format failures
     */
    public function check(): ResultDto
    {
        [
            'response' => $response,
        ] = self::requestJson($this->client, HttpMethod::POST, self::URL_VALIDATION, [
            'json' => $this->request->toArray(),
        ]);

        return new ResultDto(
            $this->request->getVatIdOwn(),
            $this->request->getVatIdForeign(),
            $response,
            $this->request->getIncludeRaw(),
        );
    }

    public function includeRaw(bool $value = true): self
    {
        $this->request->setIncludeRaw($value);

        return $this;
    }

    /**
     * @throws InputError
     */
    public function setHttpClient(Client $client): self
    {
        if (($_ENV['APP_ENV'] ?? 'production') !== 'testing') {
            throw new InputError('Setting a custom HTTP client is only allowed in a testing environment.');
        }

        $this->client = $client;

        return $this;
    }

    /**
     * @throws InputError
     */
    private static function decideHttpClient(?Client $client = null): Client
    {
        if (($_ENV['APP_ENV'] ?? 'production') !== 'testing' && $client !== null) {
            throw new InputError('Setting a custom HTTP client is only allowed in a testing environment.');
        }

        return $client ?? new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * Execute an HTTP request and decode its JSON body, wrapping errors in ErrorResponse.
     *
     * @param  array<string,mixed>  $options
     * @return array{response: ResponseInterface, data: array<mixed>}
     *
     * @throws ErrorResponse
     */
    private static function requestJson(Client $client, HttpMethod $method, string $endpoint, array $options = []): array
    {
        try {
            $response = match ($method) {
                HttpMethod::GET => $client->get($endpoint),
                HttpMethod::POST => $client->post($endpoint, $options),
            };

            if ($method === HttpMethod::GET) {
                $body = $response->getBody()->getContents();
                $response->getBody()->rewind();

                try {
                    /** @var array<mixed> $data */
                    $data = ! empty($body) ? json_decode($body, true, 512, JSON_THROW_ON_ERROR) : [];
                } catch (JsonException $e) {
                    throw new ErrorResponse(
                        httpCode: $response->getStatusCode(),
                        error: 'Invalid JSON response',
                        exception: $e,
                        raw: $body,
                        meta: [
                            'endpoint' => $endpoint,
                            'errorType' => 'invalid_json',
                            'exception' => $e->getMessage(),
                        ]
                    );
                }
            }

            return [
                'response' => $response,
                'data' => $data ?? [],
            ];
        } catch (GuzzleException $e) {
            throw new ErrorResponse(
                httpCode: 0,
                error: $e->getMessage(),
                exception: $e,
                raw: null,
                meta: [
                    'endpoint' => $endpoint,
                    'errorType' => 'network',
                ]
            );
        }
    }

    /**
     * Retrieve status messages from the API.
     *
     * @return array<string, StatusMessage>
     *
     * @throws ErrorResponse on transport/JSON failures
     */
    public static function getStatusMessages(?Client $testClient = null): array
    {
        $client = self::decideHttpClient($testClient);
        /** @var array<int, array<string,mixed>> $data */
        ['data' => $data] = self::requestJson($client, HttpMethod::GET, self::URL_STATUS_MESSAGES);

        $messages = [];
        foreach ($data as $entry) {
            if (! isset($entry['status']) || ! is_string($entry['status'])) {
                continue;
            }
            $messages[$entry['status']] = StatusMessage::fromArray($entry);
        }

        return $messages;
    }

    /**
     * Retrieve availability of EU member states from the API.
     *
     * @param  bool  $onlyNotAvailable  When true, returns only entries where availability is false.
     * @param  Client|null  $testClient  Optional client (allowed only in testing env)
     * @return array<string,bool> Map of country code (alpha2) => availability
     *
     * @throws ErrorResponse on transport/JSON failures
     */
    public static function getAvailability(bool $onlyNotAvailable = false, ?Client $testClient = null): array
    {
        $client = self::decideHttpClient($testClient);
        /** @var array<int, array<string,mixed>> $data */
        ['data' => $data] = self::requestJson($client, HttpMethod::GET, self::URL_EU_MEMBER_STATES);

        $map = [];
        foreach ($data as $entry) {
            if (! isset($entry['alpha2']) || ! is_string($entry['alpha2'])) {
                continue;
            }
            $map[$entry['alpha2']] = isset($entry['verfuegbar']) && $entry['verfuegbar'] === true;
        }

        if ($onlyNotAvailable === true) {
            return array_filter($map, static fn (bool $available): bool => $available === false);
        }

        return $map;
    }
}
