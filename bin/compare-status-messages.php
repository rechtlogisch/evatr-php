<?php

declare(strict_types=1);

use Rechtlogisch\Evatr\Evatr;
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
    $localPath = __DIR__.'/../docs/statusmeldungen.json';
    if (! is_file($localPath)) {
        throw new RuntimeException("Local file not found: {$localPath}");
    }

    $localJson = file_get_contents($localPath);
    if ($localJson === false) {
        throw new RuntimeException('Failed to read local statusmeldungen.json');
    }

    $remoteJson = @file_get_contents(Evatr::URL_STATUS_MESSAGES);
    if ($remoteJson === false) {
        throw new RuntimeException('Failed to download remote status messages from '.Evatr::URL_STATUS_MESSAGES);
    }

    /** @var array<int, array<string, mixed>> $localData */
    $localData = json_decode($localJson, true, 512, JSON_THROW_ON_ERROR);
    /** @var array<int, array<string, mixed>> $remoteData */
    $remoteData = json_decode($remoteJson, true, 512, JSON_THROW_ON_ERROR);

    $localMap = StatusMessagesComparer::toGermanMap($localData);
    $remoteMap = StatusMessagesComparer::toGermanMap($remoteData);

    $diff = StatusMessagesComparer::diff($localMap, $remoteMap);

    if ($diff['missing'] === [] && $diff['new'] === [] && $diff['changed'] === []) {
        println('OK: German status messages match the latest remote version.');
        exit(0);
    }

    errorln('Differences found between local docs/statusmeldungen.json and remote:');

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
