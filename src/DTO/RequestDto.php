<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\DTO;

final class RequestDto
{
    private bool $includeRaw = false;

    public function __construct(
        public ?string $vatIdOwn = null,
        public ?string $vatIdForeign = null,
        public ?string $company = null,
        public ?string $street = null,
        public ?string $zip = null,
        public ?string $location = null,
    ) {}

    /**
     * @return $this
     */
    public function setIncludeRaw(bool $value = true): self
    {
        $this->includeRaw = $value;

        return $this;
    }

    public function getIncludeRaw(): bool
    {
        return $this->includeRaw;
    }

    public function getVatIdOwn(): string
    {
        return $this->vatIdOwn;
    }

    public function getVatIdForeign(): string
    {
        return $this->vatIdForeign;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $array = [
            'anfragendeUstid' => $this->vatIdOwn,
            'angefragteUstid' => $this->vatIdForeign,
            'firmenname' => $this->company,
            'strasse' => $this->street,
            'plz' => $this->zip,
            'ort' => $this->location,
        ];

        // Remove null values
        return array_filter($array, static fn (?string $value) => $value !== null);
    }
}
