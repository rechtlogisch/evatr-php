<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Rechtlogisch\Evatr\DTO\RequestDto;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\DTO\StatusMessage;
use Rechtlogisch\Evatr\Exception\ErrorResponse;
use Rechtlogisch\Evatr\Exception\InputError;
use RuntimeException;

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
        try {
            $response = $this->client->post(self::URL_VALIDATION, [
                'json' => $this->request->toArray(),
            ]);

            // Validate JSON and required key without throwing to the outside
            $body = $response->getBody()->getContents();
            $response->getBody()->rewind();
            try {
                /** @var array<string,mixed> $data */
                $data = $body !== '' ? json_decode($body, true, 512, JSON_THROW_ON_ERROR) : [];
            } catch (JsonException $e) {
                throw new ErrorResponse(
                    httpCode: $response->getStatusCode(),
                    error: 'Invalid JSON response',
                    exception: $e,
                    raw: $body,
                    meta: [
                        'endpoint' => self::URL_VALIDATION,
                        'errorType' => 'invalid_json',
                        'exception' => $e->getMessage(),
                    ]
                );
            }

            if (! isset($data['status'])) {
                throw new ErrorResponse(
                    httpCode: $response->getStatusCode(),
                    error: 'Unexpected response format: missing status',
                    exception: new RuntimeException('Unexpected response format: missing status'),
                    raw: $body,
                    meta: [
                        'endpoint' => self::URL_VALIDATION,
                        'errorType' => 'unexpected_response',
                    ]
                );
            }

            return new ResultDto(
                $this->request->getVatIdOwn(),
                $this->request->getVatIdForeign(),
                $response,
                $this->request->getIncludeRaw(),
            );
        } catch (GuzzleException $e) {
            throw new ErrorResponse(
                httpCode: 0,
                error: $e->getMessage(),
                exception: $e,
                raw: null,
                meta: [
                    'endpoint' => self::URL_VALIDATION,
                    'errorType' => 'network',
                ]
            );
        }
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
     * Retrieve status messages from the API.
     *
     * @return array<string, StatusMessage>
     *
     * @throws ErrorResponse on transport/JSON failures
     */
    public static function getStatusMessages(?Client $testClient = null): array
    {
        try {
            $client = self::decideHttpClient($testClient);
            $response = $client->get(self::URL_STATUS_MESSAGES);
            $body = $response->getBody()->getContents();
            $response->getBody()->rewind();
            try {
                /** @var array<int, array<string,mixed>> $data */
                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new ErrorResponse(
                    httpCode: $response->getStatusCode(),
                    error: 'Invalid JSON response',
                    exception: $e,
                    raw: $body,
                    meta: [
                        'endpoint' => self::URL_STATUS_MESSAGES,
                        'errorType' => 'invalid_json',
                        'exception' => $e->getMessage(),
                    ]
                );
            }

            $messages = [];
            foreach ($data as $entry) {
                if (! isset($entry['status']) || ! is_string($entry['status'])) {
                    continue;
                }
                $messages[$entry['status']] = StatusMessage::fromArray($entry);
            }

            return $messages;
        } catch (GuzzleException $e) {
            throw new ErrorResponse(
                httpCode: 0,
                error: $e->getMessage(),
                exception: $e,
                raw: null,
                meta: [
                    'endpoint' => self::URL_STATUS_MESSAGES,
                    'errorType' => 'network',
                ]
            );
        }
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
    public static function checkAvailability(bool $onlyNotAvailable = false, ?Client $testClient = null): array
    {
        try {
            $client = self::decideHttpClient($testClient);
            $response = $client->get(self::URL_EU_MEMBER_STATES);
            $body = $response->getBody()->getContents();
            $response->getBody()->rewind();
            try {
                /** @var array<int, array<string,mixed>> $data */
                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new ErrorResponse(
                    httpCode: $response->getStatusCode(),
                    error: 'Invalid JSON response',
                    exception: $e,
                    raw: $body,
                    meta: [
                        'endpoint' => self::URL_EU_MEMBER_STATES,
                        'errorType' => 'invalid_json',
                        'exception' => $e->getMessage(),
                    ]
                );
            }

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
        } catch (GuzzleException $e) {
            throw new ErrorResponse(
                httpCode: 0,
                error: $e->getMessage(),
                exception: $e,
                raw: null,
                meta: [
                    'endpoint' => self::URL_EU_MEMBER_STATES,
                    'errorType' => 'network',
                ]
            );
        }
    }
}
