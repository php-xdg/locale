<?php declare(strict_types=1);

namespace Xdg\Locale\Platform;

final class PlatformFactory
{
    private static PlatformInterface $osPlatform;

    public static function default(): PlatformInterface
    {
        return self::$osPlatform ??= match (\PHP_OS_FAMILY) {
            'Windows' => new WindowsPlatform(),
            default => new UnixPlatform(),
        };
    }
}
