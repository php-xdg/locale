<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

use Xdg\Locale\Exception\InvalidLocaleException;
use Xdg\Locale\Locale;
use Xdg\Locale\Platform\Windows\Aliases;

final class WindowsPlatform extends AbstractPlatform
{
    private const WIN32_LOCALE_RX = <<<'REGEXP'
    /
        ^
        (?<language> [^_]+ )
        (?:
            _ (?<territory> .+? )
            (?: \. (?<encoding> utf-?8 | [oa]cp | \d+ ) )?
        )?
        $
    /Six
    REGEXP;

    public function parse(string $value): Locale
    {
        if ($tag = Aliases::LANGUAGE_STRINGS[$value] ?? Aliases::LOCALES[$value] ?? null) {
            return Locale::new(...$this->parseIetfLanguageTag($tag));
        }

        if (preg_match(self::WIN32_LOCALE_RX, $value, $matches, \PREG_UNMATCHED_AS_NULL)) {
            ['language' => $lang, 'territory' => $territory, 'encoding' => $encoding] = $matches;
            if ($tag = match ($territory) {
                null => Aliases::LOCALES[$lang] ?? null,
                default => Aliases::LOCALES["{$lang}_{$territory}"] ?? Aliases::LOCALES[$lang] ?? null,
            }) {
                $result = [
                    ...$this->parseIetfLanguageTag($tag),
                    'encoding' => $encoding,
                ];
                return new Locale(...$result);
            }
        }

        if ($result = Posix::parseLanguageTag($value)) {
            return new Locale(...$result);
        }

        if ($result = $this->parseIetfLanguageTag($value)) {
            return Locale::new(...$result);
        }

        throw new InvalidLocaleException(sprintf(
            'Invalid locale: %s',
            $value,
        ));
    }
}
