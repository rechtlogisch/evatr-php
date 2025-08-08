<?php

use Rechtlogisch\Evatr\Enum\Status;

it('has all status cases defined', function () {
    $expectedCases = [
        'EVATR_0000', 'EVATR_0001', 'EVATR_0002', 'EVATR_0003', 'EVATR_0004',
        'EVATR_0005', 'EVATR_0006', 'EVATR_0007', 'EVATR_0008', 'EVATR_0011',
        'EVATR_0012', 'EVATR_0013', 'EVATR_1001', 'EVATR_1002', 'EVATR_1003',
        'EVATR_1004', 'EVATR_2001', 'EVATR_2002', 'EVATR_2003', 'EVATR_2004',
        'EVATR_2005', 'EVATR_2006', 'EVATR_2007', 'EVATR_2008', 'EVATR_2011',
        'EVATR_3011',
    ];

    $actualCases = array_map(fn ($case) => $case->name, Status::cases());

    expect($actualCases)->toEqual($expectedCases);
});

it('returns correct values for all status cases', function () {
    expect(Status::EVATR_0000->value)->toBe('evatr-0000')
        ->and(Status::EVATR_0001->value)->toBe('evatr-0001')
        ->and(Status::EVATR_0002->value)->toBe('evatr-0002')
        ->and(Status::EVATR_0003->value)->toBe('evatr-0003')
        ->and(Status::EVATR_0004->value)->toBe('evatr-0004')
        ->and(Status::EVATR_0005->value)->toBe('evatr-0005')
        ->and(Status::EVATR_0006->value)->toBe('evatr-0006')
        ->and(Status::EVATR_0007->value)->toBe('evatr-0007')
        ->and(Status::EVATR_0008->value)->toBe('evatr-0008')
        ->and(Status::EVATR_0011->value)->toBe('evatr-0011')
        ->and(Status::EVATR_0012->value)->toBe('evatr-0012')
        ->and(Status::EVATR_0013->value)->toBe('evatr-0013')
        ->and(Status::EVATR_1001->value)->toBe('evatr-1001')
        ->and(Status::EVATR_1002->value)->toBe('evatr-1002')
        ->and(Status::EVATR_1003->value)->toBe('evatr-1003')
        ->and(Status::EVATR_1004->value)->toBe('evatr-1004')
        ->and(Status::EVATR_2001->value)->toBe('evatr-2001')
        ->and(Status::EVATR_2002->value)->toBe('evatr-2002')
        ->and(Status::EVATR_2003->value)->toBe('evatr-2003')
        ->and(Status::EVATR_2004->value)->toBe('evatr-2004')
        ->and(Status::EVATR_2005->value)->toBe('evatr-2005')
        ->and(Status::EVATR_2006->value)->toBe('evatr-2006')
        ->and(Status::EVATR_2007->value)->toBe('evatr-2007')
        ->and(Status::EVATR_2008->value)->toBe('evatr-2008')
        ->and(Status::EVATR_2011->value)->toBe('evatr-2011')
        ->and(Status::EVATR_3011->value)->toBe('evatr-3011');
});

it('checks descriptions against statusmeldungen.json', function () {
    // Read the statusmeldungen.json file
    $statusmeldungenPath = dirname(__DIR__, 2).'/docs/statusmeldungen.json';
    $statusmeldungenContent = file_get_contents($statusmeldungenPath);
    /** @noinspection PhpUnhandledExceptionInspection */
    $statusmeldungen = json_decode($statusmeldungenContent, true, 512, JSON_THROW_ON_ERROR);

    // Create a lookup array for status messages
    $statusMessages = [];
    foreach ($statusmeldungen as $statusInfo) {
        $statusMessages[$statusInfo['status']] = $statusInfo['meldung'];
    }

    // Test each status case against the JSON data
    foreach (Status::cases() as $statusCase) {
        $statusValue = $statusCase->value;

        // Check if the status exists in the JSON
        expect($statusMessages)->toHaveKey(
            $statusValue,
            $statusCase->description(),
            "Status {$statusValue} not found in statusmeldungen.json"
        );
    }

    // Verify we have all statuses from JSON in our enum
    foreach ($statusMessages as $statusValue => $message) {
        $enumExists = false;
        foreach (Status::cases() as $statusCase) {
            if ($statusCase->value === $statusValue) {
                $enumExists = true;
                break;
            }
        }
        expect($enumExists)->toBeTrue("Status {$statusValue} from JSON not found in Status enum");
    }
})->group('manual');

it('can be created from string values', function () {
    expect(Status::from('evatr-0000'))->toBe(Status::EVATR_0000)
        ->and(Status::from('evatr-0001'))->toBe(Status::EVATR_0001)
        ->and(Status::from('evatr-2006'))->toBe(Status::EVATR_2006)
        ->and(Status::from('evatr-3011'))->toBe(Status::EVATR_3011);
});
