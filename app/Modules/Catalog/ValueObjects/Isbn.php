<?php

namespace Modules\Catalog\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object ISBN (ISBN-10 atau ISBN-13) dengan validasi format dan check digit.
 */
class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = self::normalize($value);

        if (!self::isValid($normalized)) {
            throw new InvalidArgumentException("ISBN tidak valid: {$value}");
        }

        $this->value = $normalized;
    }

    /** Hapus spasi dan tanda hubung, huruf X dijadikan kapital. */
    public static function normalize(string $value): string
    {
        return strtoupper(preg_replace('/[\s\-]/', '', $value));
    }

    public static function isValid(string $value): bool
    {
        $value = self::normalize($value);

        return match (strlen($value)) {
            10 => self::isValidIsbn10($value),
            13 => self::isValidIsbn13($value),
            default => false,
        };
    }

    private static function isValidIsbn10(string $isbn): bool
    {
        if (!preg_match('/^\d{9}[\dX]$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $digit = $isbn[$i] === 'X' ? 10 : (int) $isbn[$i];
            $sum += $digit * (10 - $i);
        }

        return $sum % 11 === 0;
    }

    private static function isValidIsbn13(string $isbn): bool
    {
        if (!preg_match('/^\d{13}$/', $isbn)) {
            return false;
        }

        return self::checkDigit13(substr($isbn, 0, 12)) === (int) $isbn[12];
    }

    /** Hitung check digit ISBN-13 dari 12 digit pertama. */
    public static function checkDigit13(string $first12): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $first12[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isIsbn13(): bool
    {
        return strlen($this->value) === 13;
    }

    /** Format tampilan sederhana */
    public function format(): string
    {
        return $this->isIsbn13()
            ? substr($this->value, 0, 3) . '-' . substr($this->value, 3)
            : $this->value;
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}



