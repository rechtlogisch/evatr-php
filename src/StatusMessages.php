<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr;

/**
 * Embedded status messages sourced from docs/statusmeldungen.json.
 * Provides bilingual lookups (German default, English when EVATR_LANG=en).
 */
final class StatusMessages
{
    /**
     * @var array<string, array{category: ?string, httpCode: ?int, field: ?string, message: string}>
     */
    public const MESSAGES_DE = [
        'evatr-0000' => ['category' => 'Ergebnis', 'httpCode' => 200, 'field' => null, 'message' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.'],
        'evatr-0001' => ['category' => 'Hinweis', 'httpCode' => null, 'field' => 'datenschutz', 'message' => 'Bitte bestätigen Sie den Datenschutzhinweis.'],
        'evatr-0002' => ['category' => 'Hinweis', 'httpCode' => 400, 'field' => 'angefragteUstid', 'message' => 'Mindestens eins der Pflichtfelder ist nicht besetzt.'],
        'evatr-0003' => ['category' => 'Hinweis', 'httpCode' => 400, 'field' => 'firmenname,ort', 'message' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig. Mindestens eines der Pflichtfelder für eine qualifizierte Bestätigungsanfrage ist nicht besetzt.'],
        'evatr-0004' => ['category' => 'Fehler', 'httpCode' => 400, 'field' => 'anfragendeUstid', 'message' => 'Die anfragende DE Ust-IdNr. ist syntaktisch falsch. Sie passt nicht in das deutsche Erzeugungsschema.'],
        'evatr-0005' => ['category' => 'Fehler', 'httpCode' => 400, 'field' => 'angefragteUstid', 'message' => 'Die angegebene angefragte Ust-IdNr. ist syntaktisch falsch.'],
        'evatr-0006' => ['category' => 'Hinweis', 'httpCode' => 403, 'field' => 'anfragendeUstid', 'message' => 'Die anfragende DE USt-IdNr. ist nicht berechtigt eine DE Ust-IdNr. anzufragen.'],
        'evatr-0007' => ['category' => 'Hinweis', 'httpCode' => 403, 'field' => null, 'message' => 'Fehlerhafter Aufruf.'],
        'evatr-0008' => ['category' => 'Hinweis', 'httpCode' => 403, 'field' => null, 'message' => 'Die maximale Anzahl von qualifizierten Bestätigungsabfragen für diese Session wurde erreicht. Bitte starten Sie erneut mit einer einfachen Bestätigungsabfrage.'],
        'evatr-0011' => ['category' => 'Fehler', 'httpCode' => 503, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        // typo in message: is "angefrage", should be "angefragte"
        'evatr-0012' => ['category' => 'Fehler', 'httpCode' => 400, 'field' => 'angefragteUstid', 'message' => 'Die angefrage USt-IdNr. ist syntaktisch falsch. Sie passt nicht in das Erzeugungsschema.'],
        'evatr-0013' => ['category' => 'Fehler', 'httpCode' => 503, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-1001' => ['category' => 'Fehler', 'httpCode' => 503, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-1002' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-1003' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-1004' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-2001' => ['category' => 'Hinweis', 'httpCode' => 404, 'field' => 'angefragteUstid', 'message' => 'Die angefragte USt-IdNr. ist zum Anfragezeitpunkt nicht vergeben.'],
        'evatr-2002' => ['category' => 'Hinweis', 'httpCode' => 200, 'field' => 'angefragteUstid', 'message' => 'Die angefragte USt-IdNr. ist zum Anfragezeitpunkt nicht gültig. Sie ist erst gültig ab dem Datum im Feld gueltigAb.'],
        'evatr-2003' => ['category' => 'Fehler', 'httpCode' => 400, 'field' => 'angefragteUstid', 'message' => 'Das angegebene Länderkennzeichen der angefragten USt-IdNr. ist nicht gültig.'],
        'evatr-2004' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-2005' => ['category' => 'Fehler', 'httpCode' => 404, 'field' => 'anfragendeUstid', 'message' => 'Die angegebene eigene DE Ust-IdNr. ist zum Anfragezeitpunkt nicht gültig.'],
        'evatr-2006' => ['category' => 'Hinweis', 'httpCode' => 200, 'field' => 'angefragteUstid', 'message' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt nicht gültig. Sie war gültig im Zeitraum, der durch die Werte in den Feldern gueltigAb und gueltigBis beschrieben ist.'],
        'evatr-2007' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Bei der Verarbeitung der Daten aus dem angefragten EU-Mitgliedstaat ist ein Fehler aufgetreten. Ihre Anfrage kann deshalb nicht bearbeitet werden.'],
        'evatr-2008' => ['category' => 'Hinweis', 'httpCode' => 200, 'field' => null, 'message' => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig. Für die qualifizierte Bestätigungsanfrage liegt einer Besonderheit vor. Für Rückfragen wenden Sie sich an das BZSt.'],
        'evatr-2011' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
        'evatr-3011' => ['category' => 'Fehler', 'httpCode' => 500, 'field' => null, 'message' => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'],
    ];

    /**
     * @var array<string, array{category: ?string, httpCode: ?int, field: ?string, message: string}>
     *
     * Warning: This is an unofficial translation. Use at your own risk.
     */
    public const MESSAGES_EN = [
        'evatr-0000' => ['category' => 'Result', 'httpCode' => 200, 'field' => null, 'message' => 'The foreign VAT-ID is valid at the time of the request.'],
        'evatr-0001' => ['category' => 'Notice', 'httpCode' => null, 'field' => 'privacy', 'message' => 'Please confirm the privacy notice.'],
        'evatr-0002' => ['category' => 'Notice', 'httpCode' => 400, 'field' => 'vatIdForeign', 'message' => 'At least one required field is missing.'],
        'evatr-0003' => ['category' => 'Notice', 'httpCode' => 400, 'field' => 'company,location', 'message' => 'The VAT-ID is valid at the time of the request. At least one of the required fields for a qualified confirmation request is missing.'],
        'evatr-0004' => ['category' => 'Error', 'httpCode' => 400, 'field' => 'vatIdOwn', 'message' => 'The requesting German VAT-ID is syntactically invalid. It does not match the German rules.'],
        'evatr-0005' => ['category' => 'Error', 'httpCode' => 400, 'field' => 'vatIdForeign', 'message' => 'The given foreign VAT-ID is syntactically invalid.'],
        'evatr-0006' => ['category' => 'Notice', 'httpCode' => 403, 'field' => 'vatIdOwn', 'message' => 'The requesting German VAT-ID is not authorized to query a German VAT-ID.'],
        'evatr-0007' => ['category' => 'Notice', 'httpCode' => 403, 'field' => null, 'message' => 'Invalid request.'],
        'evatr-0008' => ['category' => 'Notice', 'httpCode' => 403, 'field' => null, 'message' => 'The maximum number of qualified confirmation requests for this session has been reached. Please start again with a simple confirmation request.'],
        'evatr-0011' => ['category' => 'Error', 'httpCode' => 503, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-0012' => ['category' => 'Error', 'httpCode' => 400, 'field' => 'vatIdForeign', 'message' => 'The foreign VAT-ID is syntactically invalid. It does not match the rules.'],
        'evatr-0013' => ['category' => 'Error', 'httpCode' => 503, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-1001' => ['category' => 'Error', 'httpCode' => 503, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-1002' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-1003' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-1004' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-2001' => ['category' => 'Notice', 'httpCode' => 404, 'field' => 'vatIdForeign', 'message' => 'The foreign VAT-ID is not assigned at the time of the request.'],
        'evatr-2002' => ['category' => 'Notice', 'httpCode' => 200, 'field' => 'vatIdForeign', 'message' => 'The foreign VAT-ID is not valid at the time of the request. It will be valid starting from the date in validFrom.'],
        'evatr-2003' => ['category' => 'Error', 'httpCode' => 400, 'field' => 'vatIdForeign', 'message' => 'The specified country code of the foreign VAT-ID is not valid.'],
        'evatr-2004' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-2005' => ['category' => 'Error', 'httpCode' => 404, 'field' => 'vatIdOwn', 'message' => 'The specified own German VAT-ID is not valid at the time of the request.'],
        'evatr-2006' => ['category' => 'Notice', 'httpCode' => 200, 'field' => 'vatIdForeign', 'message' => 'The foreign VAT-ID is not valid at the time of the request. It was valid during the period described by validFrom and validTill.'],
        'evatr-2007' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'An error occurred while processing data from the requested EU member state. Your request cannot be processed.'],
        'evatr-2008' => ['category' => 'Notice', 'httpCode' => 200, 'field' => null, 'message' => 'The foreign VAT-ID is valid at the time of the request. There is a particular condition for the qualified confirmation request. For inquiries, please contact the BZSt.'],
        'evatr-2011' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
        'evatr-3011' => ['category' => 'Error', 'httpCode' => 500, 'field' => null, 'message' => 'Your request cannot be processed at the moment. Please try again later.'],
    ];

    /**
     * @return array<string, array{category: ?string, httpCode: ?int, field: ?string, message: string}>
     */
    private static function data(): array
    {
        $lang = strtolower((string) ($_ENV['EVATR_LANG'] ?? 'de'));

        return $lang === 'en' ? self::MESSAGES_EN : self::MESSAGES_DE;
    }

    public static function forCode(string $code): ?array
    {
        $data = self::data();

        return $data[$code] ?? null;
    }

    public static function httpCodeFor(string $code): ?int
    {
        return self::forCode($code)['httpCode'] ?? null;
    }

    public static function messageFor(string $code): ?string
    {
        return self::forCode($code)['message'] ?? null;
    }
}
