<?php declare(strict_types=1);

namespace Xdg\Locale\Tests\Platform;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Xdg\Locale\Exception\InvalidLocaleException;
use Xdg\Locale\Locale;
use Xdg\Locale\Platform\PlatformInterface;

abstract class PlatformTestCase extends TestCase
{
    abstract protected static function createPlatform(): PlatformInterface;

    #[DataProvider('parseProvider')]
    final public function testParse(string $input, Locale $expected): void
    {
        Assert::assertEquals($expected, static::createPlatform()->parse($input));
    }

    abstract public static function parseProvider(): iterable;

    protected static function posixLanguageTagProvider(): iterable
    {
        $tests = [
            'll' => ['ll', null, null, null],
            'll@bar' => ['ll', null, null, 'bar'],
            'll_CC' => ['ll', 'CC', null, null],
            'll.foo' => ['ll', null, 'foo', null],
            'll.foo@bar' => ['ll', null, 'foo', 'bar'],
            'll_CC@bar' => ['ll', 'CC', null, 'bar'],
            'll_CC.foo' => ['ll', 'CC', 'foo', null],
            'll_CC.foo@bar' => ['ll', 'CC', 'foo', 'bar'],
        ];
        foreach ($tests as $input => $args) {
            yield $input => [$input, Locale::new(...$args)];
        }
    }

    protected static function ietfLanguageTagProvider(): iterable
    {
        $tests = [
            'll-CC' => ['ll', 'CC'],
            'll-CC@foo' => ['ll', 'CC', null, 'foo'],
            'fr-Latn-BE' => ['fr', 'BE', null, 'latin'],
            'fr-fr-latin' => ['fr', 'FR', null, 'latin'],
        ];
        foreach ($tests as $input => $args) {
            yield $input => [$input, Locale::new(...$args)];
        }
    }

    #[DataProvider('invalidLocaleStringProvider')]
    final public function testInvalidLocaleString(string $input): void
    {
        $this->expectException(InvalidLocaleException::class);
        static::createPlatform()->parse($input);
    }

    public static function invalidLocaleStringProvider(): iterable
    {
        yield ['_.@'];
        yield ['---'];
        yield ['-Latn'];
        yield [' '];
    }
}
