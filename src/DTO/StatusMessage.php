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
        $rawCategory = isset($data['kategorie']) && is_string($data['kategorie']) ? $data['kategorie'] : null;

        // Normalize category to English invariant values: Result, Error, Hint
        $category = match ($rawCategory) {
            'Ergebnis', 'Result' => 'Result',
            'Fehler', 'Error' => 'Error',
            'Hinweis', 'Notice', 'Hint' => 'Hint',
            default => null,
        };

        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
        $http = isset($data['httpcode']) && is_int($data['httpcode']) ? $data['httpcode'] : null;
        $field = isset($data['feld']) && is_string($data['feld']) ? $data['feld'] : null;
        $message = isset($data['meldung']) && is_string($data['meldung']) ? $data['meldung'] : '';

        return new self(
            status: $status,
            category: $category,
            http: $http,
            field: $field,
            message: $message,
        );
    }
}
