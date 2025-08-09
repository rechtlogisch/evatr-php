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
     * @param array{
     *     vatIdOwn?: string,
     *     vatIdForeign?: string,
     *     company?: string,
     *     street?: string,
     *     zip?: string,
     *     location?: string,
     *     0: RequestDto,
     * } $input
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

        $this->request = (count($input) === 1 && $input[0] instanceof RequestDto)
            ? $input[0]
            : new RequestDto(...$input);
    }

    // @TODO: handle exceptions and errors properly

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function check(): ResultDto
    {
        $response = $this->client->post(self::URL_VALIDATION, [
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

    public function setHttpClient(Client $client): Client|self
    {
        if (($_ENV['APP_ENV'] ?? 'production') !== 'testing') {
            throw new RuntimeException('Setting a custom HTTP client is only allowed in a testing environment.');
        }

        $this->client = $client;

        return $this;
    }

    private static function decideHttpClient(?Client $client = null): Client
    {
        if (($_ENV['APP_ENV'] ?? 'production') !== 'testing' && $client !== null) {
            throw new RuntimeException('Setting a custom HTTP client is only allowed in a testing environment.');
        }

        return $client ?? new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * Retrieve status messages from the API.
     *
     * @return list<StatusMessage>
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public static function getStatusMessages(?Client $testClient = null): array
    {
        $client = self::decideHttpClient($testClient);

        $response = $client->get(self::URL_STATUS_MESSAGES);
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $messages = [];
        foreach ($data as $entry) {
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
     * @throws GuzzleException
     * @throws JsonException
     */
    public static function checkAvailability(bool $onlyNotAvailable = false, ?Client $testClient = null): array
    {
        $client = self::decideHttpClient($testClient);

        $response = $client->get(self::URL_EU_MEMBER_STATES);
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $map = [];
        foreach ($data as $entry) {
            $map[$entry['alpha2']] = (bool) ($entry['verfuegbar'] ?? false);
        }

        if ($onlyNotAvailable === true) {
            return array_filter($map, static fn (bool $available): bool => $available === false);
        }

        return $map;
    }
}
