<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\DTO;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Rechtlogisch\Evatr\Enum\QualifiedResult;
use Rechtlogisch\Evatr\Enum\Status;
use Rechtlogisch\Evatr\Evatr;
use Rechtlogisch\Evatr\Exception\ErrorResponse;
use RuntimeException;
use Throwable;

final class ResultDto
{
    private ?string $id;

    private ?string $timestamp;

    private ?Status $status;

    private ?string $message = null;

    private ?QualifiedResult $company;

    private ?QualifiedResult $street;

    private ?QualifiedResult $zip;

    private ?QualifiedResult $location;

    private ?string $dateFrom;

    private ?string $dateTill;

    private ?int $httpStatusCode = null;

    private ?string $raw = null;

    public function __construct(
        private readonly string $vatIdOwn,
        private readonly string $vatIdForeign,
        private readonly ResponseInterface $response,
        bool $includeRaw = false,
    ) {
        $body = $this->response->getBody()->getContents();
        $this->response->getBody()->rewind();

        try {
            /** @var array<mixed> $data */
            $data = ! empty($body) ? json_decode($body, true, 512, JSON_THROW_ON_ERROR) : [];
        } catch (JsonException $e) {
            throw new ErrorResponse(
                httpCode: $this->response->getStatusCode(),
                error: 'Invalid JSON response',
                exception: $e,
                raw: $body,
                meta: [
                    'endpoint' => Evatr::URL_VALIDATION,
                    'errorType' => 'invalid_json',
                    'exception' => $e->getMessage(),
                ]
            );
        }

        if (empty($data) || ! isset($data['anfrageZeitpunkt'])) {
            throw new ErrorResponse(
                httpCode: $this->response->getStatusCode(),
                error: 'Unexpected response format: missing anfrageZeitpunkt',
                exception: new RuntimeException('Unexpected response format: missing anfrageZeitpunkt'),
                raw: $this->response->getBody()->getContents(),
                meta: [
                    'endpoint' => Evatr::URL_VALIDATION,
                    'errorType' => 'unexpected_response',
                ]
            );
        }

        if (! isset($data['status'])) {
            throw new ErrorResponse(
                httpCode: $this->response->getStatusCode(),
                error: 'Unexpected response format: missing status',
                exception: new RuntimeException('Unexpected response format: missing status'),
                raw: $this->response->getBody()->getContents(),
                meta: [
                    'endpoint' => Evatr::URL_VALIDATION,
                    'errorType' => 'unexpected_response',
                ]
            );
        }

        $this->id = isset($data['id']) && is_string($data['id'])
            ? $data['id']
            : null;
        $this->timestamp = match (gettype($data['anfrageZeitpunkt'])) {
            'string' => $data['anfrageZeitpunkt'],
            'integer' => (string) $data['anfrageZeitpunkt'],
            default => null,
        };
        $this->status = is_string($data['status'])
            ? Status::from($data['status'])
            : null;
        if (isset($this->status)) {
            $this->message = $this->status->description();
        }
        $this->company = isset($data['ergFirmenname']) && is_string($data['ergFirmenname'])
            ? QualifiedResult::from($data['ergFirmenname'])
            : null;
        $this->street = isset($data['ergStrasse']) && is_string($data['ergStrasse'])
            ? QualifiedResult::from($data['ergStrasse'])
            : null;
        $this->zip = isset($data['ergPlz']) && is_string($data['ergPlz'])
            ? QualifiedResult::from($data['ergPlz'])
            : null;
        $this->location = isset($data['ergOrt']) && is_string($data['ergOrt'])
            ? QualifiedResult::from($data['ergOrt'])
            : null;
        $this->dateFrom = isset($data['gueltigAb']) && is_string($data['gueltigAb']) ? $data['gueltigAb'] : null;
        $this->dateTill = isset($data['gueltigBis']) && is_string($data['gueltigBis']) ? $data['gueltigBis'] : null;

        $this->setHttpStatusCode($this->response->getStatusCode());

        if ($includeRaw === true) {
            $this->setRaw($this->response);
        }
    }

    private function setHttpStatusCode(?int $httpStatusCode = null): void
    {
        if ($httpStatusCode !== null) {
            $this->httpStatusCode = $httpStatusCode;
        }
    }

    private function setRaw(ResponseInterface $response): void
    {
        $headers = array_map(
            static fn (array $values) => implode(', ', $values),
            $response->getHeaders()
        );
        $body = $response->getBody()->getContents();
        try {
            $this->raw = json_encode([
                'headers' => $headers,
                'data' => $body,
            ], JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $this->raw = $body;
        }
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode ?? null;
    }

    public function getVatIdOwn(): string
    {
        return $this->vatIdOwn;
    }

    public function getVatIdForeign(): string
    {
        return $this->vatIdForeign;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCompany(): ?QualifiedResult
    {
        return $this->company;
    }

    public function getStreet(): ?QualifiedResult
    {
        return $this->street;
    }

    public function getZip(): ?QualifiedResult
    {
        return $this->zip;
    }

    public function getLocation(): ?QualifiedResult
    {
        return $this->location;
    }

    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    public function getDateTill(): ?string
    {
        return $this->dateTill;
    }

    public function getRaw(): ?string
    {
        return $this->raw;
    }

    /**
     * Convert the DTO to an array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'status' => $this->status?->value,
            'message' => $this->message ?? null,
            'company' => $this->company?->value,
            'street' => $this->street?->value,
            'zip' => $this->zip?->value,
            'location' => $this->location?->value,
            'httpStatusCode' => $this->httpStatusCode ?? null,
            'vatIdOwn' => $this->vatIdOwn,
            'vatIdForeign' => $this->vatIdForeign,
            'dateFrom' => $this->dateFrom ?? null,
            'dateTill' => $this->dateTill ?? null,
            'raw' => $this->raw ?? null,
        ];
    }
}
