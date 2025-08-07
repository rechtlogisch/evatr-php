<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Rechtlogisch\Evatr\DTO\RequestDto;
use Rechtlogisch\Evatr\DTO\ResultDto;
use RuntimeException;

class Evatr
{
    public const BASE_URL = 'https://api.evatr.vies.bzst.de/app/v1';

    public const URL_VALIDATION = self::BASE_URL.'/abfrage';

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

    public function setHttpClient(Client $client): self
    {
        if (($_ENV['APP_ENV'] ?? 'production') !== 'testing') {
            throw new RuntimeException('Setting a custom HTTP client is only allowed in a testing environment.');
        }

        $this->client = $client;

        return $this;
    }
}
