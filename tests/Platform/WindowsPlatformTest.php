<?php declare(strict_types=1);

namespace Xdg\Locale\Tests\Platform;

use Xdg\Locale\Locale;
use Xdg\Locale\Platform\PlatformInterface;
use Xdg\Locale\Platform\WindowsPlatform;

final class WindowsPlatformTest extends PlatformTestCase
{
    protected static function createPlatform(): PlatformInterface
    {
        return new WindowsPlatform();
    }

    public static function parseProvider(): iterable
    {
        yield from self::posixLanguageTagProvider();
        yield from self::ietfLanguageTagProvider();

        $tests = [
            'chinese' => ['zh'],
            'chinese-hongkong' => ['zh', 'HK'],
            //
            'Chinese (Traditional)_Hong Kong S.A.R.' => ['zh', 'HK'],
            'Arabic' => ['ar'],
            'Arabic_Palestinian Authority' => ['ar', 'PS'],
            'Arabic_Palestinian Authority.UTF-8' => ['ar', 'PS', 'UTF-8'],
            'French_France.1252' => ['fr', 'FR', '1252'],
        ];
        foreach ($tests as $locale => $args) {
            yield $locale => [$locale, Locale::new(...$args)];
        }
    }
}
