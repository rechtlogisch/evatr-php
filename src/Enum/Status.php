<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\Enum;

enum Status: string
{
    case EVATR_0000 = 'evatr-0000';
    case EVATR_0001 = 'evatr-0001';
    case EVATR_0002 = 'evatr-0002';
    case EVATR_0003 = 'evatr-0003';
    case EVATR_0004 = 'evatr-0004';
    case EVATR_0005 = 'evatr-0005';
    case EVATR_0006 = 'evatr-0006';
    case EVATR_0007 = 'evatr-0007';
    case EVATR_0008 = 'evatr-0008';
    case EVATR_0011 = 'evatr-0011';
    case EVATR_0012 = 'evatr-0012';
    case EVATR_0013 = 'evatr-0013';
    case EVATR_1001 = 'evatr-1001';
    case EVATR_1002 = 'evatr-1002';
    case EVATR_1003 = 'evatr-1003';
    case EVATR_1004 = 'evatr-1004';
    case EVATR_2001 = 'evatr-2001';
    case EVATR_2002 = 'evatr-2002';
    case EVATR_2003 = 'evatr-2003';
    case EVATR_2004 = 'evatr-2004';
    case EVATR_2005 = 'evatr-2005';
    case EVATR_2006 = 'evatr-2006';
    case EVATR_2007 = 'evatr-2007';
    case EVATR_2008 = 'evatr-2008';
    case EVATR_2011 = 'evatr-2011';
    case EVATR_3011 = 'evatr-3011';

    public function description(): string
    {
        // @TODO: EN descriptions
        return match ($this) {
            self::EVATR_0000 => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig.',
            self::EVATR_0001 => 'Bitte bestätigen Sie den Datenschutzhinweis.',
            self::EVATR_0002 => 'Mindestens eins der Pflichtfelder ist nicht besetzt.',
            self::EVATR_0003 => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig. Mindestens eines der Pflichtfelder für eine qualifizierte Bestätigungsanfrage ist nicht besetzt.',
            self::EVATR_0004 => 'Die anfragende DE Ust-IdNr. ist syntaktisch falsch. Sie passt nicht in das deutsche Erzeugungsschema.',
            self::EVATR_0005 => 'Die angegebene angefragte Ust-IdNr. ist syntaktisch falsch.',
            self::EVATR_0006 => 'Die anfragende DE USt-IdNr. ist nicht berechtigt eine DE Ust-IdNr. anzufragen.',
            self::EVATR_0007 => 'Fehlerhafter Aufruf.',
            self::EVATR_0008 => 'Die maximale Anzahl von qualifizierten Bestätigungsabfragen für diese Session wurde erreicht. Bitte starten Sie erneut mit einer einfachen Bestätigungsabfrage.',
            self::EVATR_0011 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_0012 => 'Die angefrage USt-IdNr. ist syntaktisch falsch. Sie passt nicht in das Erzeugungsschema.',
            self::EVATR_0013 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_1001 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_1002 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_1003 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_1004 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_2001 => 'Die angefragte USt-IdNr. ist zum Anfragezeitpunkt nicht vergeben.',
            self::EVATR_2002 => 'Die angefragte USt-IdNr. ist zum Anfragezeitpunkt nicht gültig. Sie ist erst gültig ab dem Datum im Feld gueltigAb.',
            self::EVATR_2003 => 'Das angegebene Länderkennzeichen der angefragten USt-IdNr. ist nicht gültig.',
            self::EVATR_2004 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_2005 => 'Die angegebene eigene DE Ust-IdNr. ist zum Anfragezeitpunkt nicht gültig.',
            self::EVATR_2006 => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt nicht gültig. Sie war gültig im Zeitraum, der durch die Werte in den Feldern gueltigAb und gueltigBis beschrieben ist.',
            self::EVATR_2007 => 'Bei der Verarbeitung der Daten aus dem angefragten EU-Mitgliedstaat ist ein Fehler aufgetreten. Ihre Anfrage kann deshalb nicht bearbeitet werden.',
            self::EVATR_2008 => 'Die angefragte Ust-IdNr. ist zum Anfragezeitpunkt gültig. Für die qualifizierte Bestätigungsanfrage liegt einer Besonderheit vor. Für Rückfragen wenden Sie sich an das BZSt.',
            self::EVATR_2011 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
            self::EVATR_3011 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.',
        };
    }
}
