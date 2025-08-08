<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\Util;

final class StatusMessagesComparer
{
    /**
     * Build a map of status code => German message from decoded JSON array.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return array<string, string>
     */
    public static function toGermanMap(array $data): array
    {
        $map = [];
        foreach ($data as $item) {
            $status = (string) ($item['status'] ?? '');
            $message = (string) ($item['meldung'] ?? '');
            if ($status !== '') {
                $map[$status] = $message;
            }
        }

        ksort($map);

        return $map;
    }

    /**
     * Compute differences between local and remote maps.
     *
     * @param  array<string, string>  $local
     * @param  array<string, string>  $remote
     * @return array{missing: list<string>, new: list<string>, changed: array<string, array{local: string, remote: string}>}
     */
    public static function diff(array $local, array $remote): array
    {
        $missing = array_values(array_diff(array_keys($local), array_keys($remote)));
        $new = array_values(array_diff(array_keys($remote), array_keys($local)));

        $changed = [];
        foreach ($local as $code => $text) {
            if (array_key_exists($code, $remote) && $remote[$code] !== $text) {
                $changed[$code] = [
                    'local' => $text,
                    'remote' => $remote[$code],
                ];
            }
        }

        sort($missing);
        sort($new);
        ksort($changed);

        return [
            'missing' => $missing,
            'new' => $new,
            'changed' => $changed,
        ];
    }
}
