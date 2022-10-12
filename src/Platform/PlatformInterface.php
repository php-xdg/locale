<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

use Xdg\Locale\Locale;

interface PlatformInterface
{
    public function parse(string $value): Locale;
}
