<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

/**
 * @internal
 */
final class Posix
{
    public const ROOT_ALIASES = [
        'C' => 'C',
        'c' => 'C',
        'POSIX' => 'C',
        'c.ascii' => 'C',
        'c_c' => 'C',
        'c_c.c' => 'C',
    ];

    /**
     * @link https://www.wikidata.org/wiki/Property:P9060
     */
    private const POSIX_LOCALE_RX = <<<'REGEXP'
    /^
        (?<language> [a-z]{2,3} )
        (?: _ (?<territory> [A-Z]{2} | \d{3} | Han[st] ) )?
        (?: \. (?<encoding> [^@]+ ) )?
        (?: @ (?<modifier> .+ ) )?
    $/Sx
    REGEXP;

    public static function parseLanguageTag(string $tag): ?array
    {
        if (preg_match(self::POSIX_LOCALE_RX, $tag, $matches, \PREG_UNMATCHED_AS_NULL)) {
            return array_filter($matches, \is_string(...), \ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public static function isValidLanguageTag(string $tag): bool
    {
        return (bool)preg_match(self::POSIX_LOCALE_RX, $tag);
    }
}
