#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * @todo codepages https://learn.microsoft.com/en-us/windows/win32/intl/code-page-identifiers
 */

const LOCALES_URL = 'https://learn.microsoft.com/en-us/openspecs/windows_protocols/ms-lcid/a9eac961-e77d-41a6-90a5-ce1a8b0cdb9c';
const LANG_STRINGS_URL = 'https://learn.microsoft.com/en-us/cpp/c-runtime-library/language-strings';

const TEMPLATE = <<<'PHP'
<?php declare(strict_types=1);

namespace Xdg\Locale\Platform\Windows;

/**
 * This file has been automatically generated. Do not edit.
 * @internal
 */
final class Aliases
{
    /**
     * @link %s
     */
    public const LANGUAGE_STRINGS = [
%s    
    ];

    /**
     * @link %s
     */
    public const LOCALES = [
%s
    ];
}
PHP;


$languageStrings = parseLanguageStrings(LANG_STRINGS_URL);
$locales = parseLocales(LOCALES_URL);
$code = sprintf(
    TEMPLATE,
    LANG_STRINGS_URL,
    serializeArray($languageStrings, 2),
    LOCALES_URL,
    serializeArray($locales, 2),
);

file_put_contents(
    __DIR__ . '/../src/Platform/Windows/Aliases.php',
    $code,
);


function parseLocales(string $url): array
{
    $doc = loadHtmlPage($url);
    $xpath = new \DOMXPath($doc);
    $table = findTableByFirstColumnHeader($xpath, 'Language');
    if (!$table) {
        echo 'Could not find language table.', "\n";
        exit(2);
    }

    $data = [];
    foreach ($xpath->query('./tbody/tr', $table) as $row) {
        $language = trim($xpath->query('./td[1]', $row)->item(0)->textContent);
        $region = trim($xpath->query('./td[2]', $row)->item(0)->textContent);
        $tag = trim($xpath->query('./td[4]', $row)->item(0)->textContent);
        if ($language === 'Pseudo Language') {
            continue;
        }
        $key = $language . ($region ? "_{$region}" : '');
        $data[$key] = $tag;
    }

    return $data;
}

function parseLanguageStrings(string $url): array
{
    $doc = loadHtmlPage($url);
    $xpath = new \DOMXPath($doc);
    $table = findTableByFirstColumnHeader($xpath, 'Language string');
    if (!$table) {
        echo 'Could not find language strings table.', "\n";
        exit(2);
    }

    $data = [];
    foreach ($xpath->query('./tbody/tr', $table) as $row) {
        $key = trim($row->firstElementChild->textContent);
        $value = trim($row->lastElementChild->textContent);
        $data[$key] = $value;
    }

    return $data;
}

function serializeArray(array $data, int $indentLevel): string
{
    $entries = [];
    $indent = str_repeat('    ', $indentLevel);
    foreach ($data as $key => $value) {
        $entries[] = sprintf("%s%s => %s,", $indent, var_export($key, true), var_export($value, true));
    }

    return implode("\n", $entries);
}

function findTableByFirstColumnHeader(\DOMXPath $xp, string $headerValue): ?\DOMElement
{
    $q = sprintf(
        '//table[normalize-space(./thead/tr[1]/th[1]) = "%s"]',
        $headerValue,
    );

    return $xp->query($q)->item(0);
}

function loadHtmlPage(string $url): \DOMDocument
{
    $html = file_get_contents($url);
    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html, \LIBXML_NOBLANKS|\LIBXML_COMPACT);
    libxml_use_internal_errors(false);

    return $doc;
}
