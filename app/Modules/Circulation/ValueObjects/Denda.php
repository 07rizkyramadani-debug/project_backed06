<?php

namespace Modules\Circulation\ValueObjects;

use DateTimeInterface;
use InvalidArgumentException;
use Modules\Shared\ValueObjects\Money;

/**
 * Value Object Denda: nominal denda dihitung otomatis dari
 * keterlambatan (hari) x tarif per hari.
 */
class Denda
{
    public const TARIF_PER_HARI = 1000.0;

    private int $daysLate;
    private float $ratePerDay;

    public function __construct(int $daysLate, float $ratePerDay = self::TARIF_PER_HARI)
    {
        if ($daysLate < 0) {
            throw new InvalidArgumentException('Jumlah hari terlambat tidak boleh negatif');
        }
        if ($ratePerDay < 0) {
            throw new InvalidArgumentException('Tarif denda tidak boleh negatif');
        }

        $this->daysLate = $daysLate;
        $this->ratePerDay = $ratePerDay;
    }

    /** Hitung denda dari tanggal jatuh tempo dan tanggal pengembalian. */
    public static function hitung(
        DateTimeInterface $dueAt,
        DateTimeInterface $returnedAt,
        float $ratePerDay = self::TARIF_PER_HARI
    ): self {
        $seconds = $returnedAt->getTimestamp() - $dueAt->getTimestamp();
        $days = $seconds <= 0 ? 0 : (int) ceil($seconds / 86400);

        return new self($days, $ratePerDay);
    }

    public static function none(): self
    {
        return new self(0);
    }

    public function getDaysLate(): int
    {
        return $this->daysLate;
    }

    public function getRatePerDay(): float
    {
        return $this->ratePerDay;
    }

    public function getAmount(): float
    {
        return $this->daysLate * $this->ratePerDay;
    }

    public function isLate(): bool
    {
        return $this->daysLate > 0;
    }

    public function toMoney(): Money
    {
        return new Money($this->getAmount());
    }

    public function __toString(): string
    {
        return 'Rp ' . number_format($this->getAmount(), 0, ',', '.')
            . " ({$this->daysLate} hari x Rp " . number_format($this->ratePerDay, 0, ',', '.') . ')';
    }
}


