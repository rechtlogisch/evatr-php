<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\Exception;

use RuntimeException;
use Throwable;

final class ErrorResponse extends RuntimeException
{
    public function __construct(
        private readonly int $httpCode,
        private readonly string $error,
        private readonly Throwable $exception,
        private readonly ?string $raw = null,
        /** @var array<string,mixed> */
        private readonly array $meta = [],
    ) {
        parent::__construct($error, $httpCode);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getRaw(): ?string
    {
        return $this->raw;
    }

    /**
     * @return array<string,mixed>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @return array{httpCode: int, error: string, raw: ?string, meta:array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'httpCode' => $this->httpCode,
            'error' => $this->error,
            'raw' => $this->raw,
            'meta' => $this->meta,
        ];
    }
}
