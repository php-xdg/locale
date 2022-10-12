<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * @link https://www.rfc-editor.org/rfc/rfc5646.html
     */
    protected function parseIetfLanguageTag(string $tag, bool $canonicalize = true): ?array
    {
        if ($canonicalize && !($tag = locale_canonicalize($tag))) {
            return null;
        }
        if (!$m = locale_parse($tag)) {
            return null;
        }
        if ($lang = $m['language'] ?? null) {
            if ($script = $m['script'] ?? null) {
                $script = Bcp47::scriptToUnixModifier($script);
            }
            if ($variant = $m['variant0'] ?? null) {
                $variant = strtolower($variant);
            }
            return [
                'language' => $lang,
                'territory' => $m['region'] ?? null,
                'encoding' => null,
                'modifier' => $script ?? $variant,
            ];
        }

        return null;
    }
}
