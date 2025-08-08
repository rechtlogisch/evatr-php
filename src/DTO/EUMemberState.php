<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\DTO;

final readonly class EUMemberState
{
    public function __construct(
        public string $code,
        public bool $available,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['alpha2'] ?? ''),
            available: (bool) ($data['verfuegbar'] ?? false),
        );
    }
}
