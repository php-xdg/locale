<?php declare(strict_types=1);

namespace Xdg\Locale\Tests\Platform;

use Xdg\Locale\Platform\PlatformInterface;
use Xdg\Locale\Platform\UnixPlatform;

final class UnixPlatformTest extends PlatformTestCase
{
    protected static function createPlatform(): PlatformInterface
    {
        return new UnixPlatform();
    }

    public static function parseProvider(): iterable
    {
        yield from self::posixLanguageTagProvider();
        yield from self::ietfLanguageTagProvider();
    }
}
