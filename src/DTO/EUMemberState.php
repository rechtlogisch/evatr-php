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
        $code = isset($data['alpha2']) && is_string($data['alpha2']) ? $data['alpha2'] : '';
        $available = isset($data['verfuegbar']) && $data['verfuegbar'] === true;

        return new self(
            code: $code,
            available: $available,
        );
    }
}
