<?php

declare(strict_types=1);

use Rechtlogisch\Evatr\Evatr;
use Rechtlogisch\Evatr\StatusMessages;
use Rechtlogisch\Evatr\Util\StatusMessagesComparer;

require __DIR__.'/../vendor/autoload.php';

function println(string $msg): void
{
    fwrite(STDOUT, $msg."\n");
}

function errorln(string $msg): void
{
    fwrite(STDERR, $msg."\n");
}

try {
    $remoteJson = @file_get_contents(Evatr::URL_STATUS_MESSAGES);
    if ($remoteJson === false) {
        throw new RuntimeException('Failed to download remote status messages from '.Evatr::URL_STATUS_MESSAGES);
    }

    /** @var array<int, array<string, mixed>> $remoteData */
    $remoteData = json_decode($remoteJson, true, 512, JSON_THROW_ON_ERROR);

    // Build local map from embedded StatusMessages::MESSAGES_DE
    $localMap = [];
    foreach (StatusMessages::MESSAGES_DE as $code => $data) {
        $localMap[$code] = (string) ($data['message'] ?? '');
    }
    ksort($localMap);

    $remoteMap = StatusMessagesComparer::toGermanMap($remoteData);

    $diff = StatusMessagesComparer::diff($localMap, $remoteMap);

    if ($diff['missing'] === [] && $diff['new'] === [] && $diff['changed'] === []) {
        println('OK: Embedded German status messages match the latest remote version.');
        exit(0);
    }

    errorln('Differences found between embedded StatusMessages::MESSAGES_DE and remote:');

    if ($diff['missing'] !== []) {
        errorln('- Missing in remote (present locally):');
        foreach ($diff['missing'] as $code) {
            errorln("  * {$code}");
        }
    }

    if ($diff['new'] !== []) {
        errorln('- New in remote (not in local):');
        foreach ($diff['new'] as $code) {
            errorln("  * {$code}");
        }
    }

    if ($diff['changed'] !== []) {
        errorln('- Changed messages:');
        foreach ($diff['changed'] as $code => $pair) {
            errorln("  * {$code}");
            errorln('      local : '.str_replace(["\r", "\n"], [' ', ' '], $pair['local']));
            errorln('      remote: '.str_replace(["\r", "\n"], [' ', ' '], $pair['remote']));
        }
    }

    exit(1);
} catch (Throwable $e) {
    errorln('ERROR: '.$e->getMessage());
    exit(1);
}
