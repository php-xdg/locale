<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

use Xdg\Locale\Exception\InvalidLocaleException;
use Xdg\Locale\Locale;

final class UnixPlatform extends AbstractPlatform
{
    public function parse(string $value): Locale
    {
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
