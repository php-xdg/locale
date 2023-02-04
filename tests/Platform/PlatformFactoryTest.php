<?php declare(strict_types=1);

namespace Xdg\Locale\Tests\Platform;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;
use Xdg\Locale\Platform\PlatformFactory;
use Xdg\Locale\Platform\PlatformInterface;
use Xdg\Locale\Platform\WindowsPlatform;

final class PlatformFactoryTest extends TestCase
{
    public function testDefaultPlatform(): void
    {
        Assert::assertInstanceOf(PlatformInterface::class, PlatformFactory::default());
    }

    #[RequiresOperatingSystemFamily('Windows')]
    public function testItIsWindowsUnderWindows(): void
    {
        Assert::assertInstanceOf(WindowsPlatform::class, PlatformFactory::default());
    }
}
