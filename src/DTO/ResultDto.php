<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\DTO;

use Psr\Http\Message\ResponseInterface;
use Rechtlogisch\Evatr\Enum\QualifiedResult;
use Rechtlogisch\Evatr\Enum\Status;
use Throwable;

final class ResultDto
{
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
        private readonly ?ResponseInterface $response = null,
        bool $includeRaw = false,
    ) {
        $body = $this->response?->getBody()->getContents();
        // @TODO: handle exceptions and errors properly
        $data = $body ? json_decode($body, true, 512, JSON_THROW_ON_ERROR) : [];

        $this->timestamp = $data['anfrageZeitpunkt'] ?? null;
        $this->status = isset($data['status']) ? Status::from($data['status']) : null;
        if (isset($this->status)) {
            $this->message = $this->status->description();
        }
        $this->company = isset($data['ergFirmenname']) ? QualifiedResult::from($data['ergFirmenname']) : null;
        $this->street = isset($data['ergStrasse']) ? QualifiedResult::from($data['ergStrasse']) : null;
        $this->zip = isset($data['ergPlz']) ? QualifiedResult::from($data['ergPlz']) : null;
        $this->location = isset($data['ergOrt']) ? QualifiedResult::from($data['ergOrt']) : null;
        $this->dateFrom = $data['gueltigAb'] ?? null;
        $this->dateTill = $data['gueltigBis'] ?? null;

        $this->setHttpStatusCode($this->response?->getStatusCode());

        if ($includeRaw) {
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
            // @codeCoverageIgnoreStart
            // @TODO: add tests where: 1. body is html, 2. body is empty
        } catch (Throwable) {
            $this->raw = $body;
        }
        // @codeCoverageIgnoreEnd
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

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getMessage(): ?Status
    {
        return $this->status;
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
