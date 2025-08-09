<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\DTO;

final readonly class StatusMessage
{
    public function __construct(
        public string $status,
        public ?string $category,
        public ?int $http,
        public ?string $field,
        public string $message,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $rawCategory = isset($data['kategorie']) ? (string) $data['kategorie'] : null;

        // Normalize category to English invariant values: Result, Error, Hint
        $category = match ($rawCategory) {
            'Ergebnis', 'Result' => 'Result',
            'Fehler', 'Error' => 'Error',
            'Hinweis', 'Notice', 'Hint' => 'Hint',
            default => null,
        };

        return new self(
            status: (string) ($data['status'] ?? ''),
            category: $category,
            http: isset($data['httpcode']) ? (int) $data['httpcode'] : null,
            field: isset($data['feld']) ? (string) $data['feld'] : null,
            message: (string) ($data['meldung'] ?? ''),
        );
    }
}
