<?php declare(strict_types=1);

namespace Xdg\Locale;

use Xdg\Locale\Exception\InvalidLocaleException;
use Xdg\Locale\Platform\PlatformFactory;
use Xdg\Locale\Platform\Posix;

final class Locale implements \Stringable
{
    public readonly string $value;
    private readonly int $mask;
    private array $variants;

    // Masks for components of locale spec.
    // The ordering here is from least significant to most significant
    private const ENCODING = 0x01;
    private const TERRITORY = 0x02;
    private const MODIFIER = 0x04;

    /**
     * Constructs a new (possibly invalid) Locale object.
     * Use {@see self::new()} to ensure a valid locale.
     *
     * @internal
     */
    public function __construct(
        public readonly string $language,
        public readonly ?string $territory,
        public readonly ?string $encoding,
        public readonly ?string $modifier,
    ) {
        $this->mask = 0
            | ($this->encoding ? self::ENCODING : 0)
            | ($this->territory ? self::TERRITORY : 0)
            | ($this->modifier ? self::MODIFIER : 0);
        $this->value = $this->language
            . ($this->territory ? "_{$this->territory}" : '')
            . ($this->encoding ? ".{$this->encoding}" : '')
            . ($this->modifier ? "@{$this->modifier}" : '');
    }

    /**
     * Constructs a new valid Locale object.
     */
    public static function new(
        string $language,
        ?string $territory = null,
        ?string $encoding = null,
        ?string $modifier = null,
    ): self {
        return (new self($language, $territory ?: null, $encoding ?: null, $modifier ?: null))->validate();
    }

    public static function of(self|string $locale): self
    {
        return match (true) {
            \is_string($locale) => PlatformFactory::default()->parse($locale),
            default => $locale,
        };
    }

    /**
     * @return string[]
     */
    public function getVariants(): array
    {
        return $this->variants ??= $this->computeVariants();
    }

    public function matches(self|string $other): bool
    {
        $value = \is_string($other) ? $other : $other->value;
        if ($this->value === $value) {
            return true;
        }
        $lookup = array_flip($this->getVariants());
        return isset($lookup[$value]);
    }

    /**
     * @param string[] $candidates
     */
    public function select(array $candidates): ?string
    {
        if (array_is_list($candidates)) {
            $candidates = array_flip($candidates);
        }

        foreach ($this->getVariants() as $variant) {
            if (isset($candidates[$variant])) {
                return $variant;
            }
        }

        return null;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function computeVariants(): array
    {
        $mask = $this->mask;
        $variants = [];
        for ($j = 0; $j <= $mask; $j++) {
            $i = $mask - $j;
            if (($i & ~$mask) === 0) {
                $variants[] = $this->language
                    . (($i & self::TERRITORY) ? "_{$this->territory}" : '')
                    . (($i & self::ENCODING) ? ".{$this->encoding}" : '')
                    . (($i & self::MODIFIER) ? "@{$this->modifier}" : '');
            }
        }

        return $variants;
    }

    private function validate(): self
    {
        if (!Posix::isValidLanguageTag($this->value)) {
            throw new InvalidLocaleException("Invalid locale: '{$this->value}'");
        }

        return $this;
    }
}
