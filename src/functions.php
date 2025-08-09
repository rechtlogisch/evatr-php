<?php

declare(strict_types=1);

use Rechtlogisch\Evatr\DTO\RequestDto;
use Rechtlogisch\Evatr\DTO\ResultDto;
use Rechtlogisch\Evatr\Evatr;

function checkVatId(string $vatIdOwn, string $vatIdForeign, bool $includeRaw = false): ResultDto
{
    $evatr = new Evatr(
        vatIdOwn: $vatIdOwn,
        vatIdForeign: $vatIdForeign
    );

    if ($includeRaw === true) {
        $evatr->includeRaw();
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    return $evatr->check();
}

function confirmVatId(
    string $vatIdOwn,
    string $vatIdForeign,
    ?string $company,
    ?string $street,
    ?string $zip,
    ?string $location,
    bool $includeRaw = false
): ResultDto {
    $request = new RequestDto(
        vatIdOwn: $vatIdOwn,
        vatIdForeign: $vatIdForeign,
        company: $company,
        street: $street,
        zip: $zip,
        location: $location,
    );

    $evatr = new Evatr($request);

    if ($includeRaw === true) {
        $evatr->includeRaw();
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    return $evatr->check();
}
