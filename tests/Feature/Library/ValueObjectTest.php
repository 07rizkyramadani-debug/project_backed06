<?php

use Database\Factories\BookFactory;
use Modules\Catalog\ValueObjects\Isbn;
use Modules\Circulation\ValueObjects\Denda;

// ---------- ISBN ----------
it('menerima ISBN-13 yang valid dan menormalkan tanda hubung', function () {
    $isbn = new Isbn('978-0-306-40615-7');

    expect($isbn->getValue())->toBe('9780306406157')
        ->and($isbn->isIsbn13())->toBeTrue()
        ->and($isbn->format())->toBe('978-0306406157');
});

it('menerima ISBN-10 yang valid (termasuk digit X)', function () {
    expect(Isbn::isValid('0-306-40615-2'))->toBeTrue()
        ->and(Isbn::isValid('080442957X'))->toBeTrue();
});

it('menolak ISBN dengan check digit salah atau format tidak valid', function () {
    expect(Isbn::isValid('9780306406158'))->toBeFalse()
        ->and(Isbn::isValid('12345'))->toBeFalse()
        ->and(Isbn::isValid('abcdefghijklm'))->toBeFalse();

    new Isbn('9780306406158');
})->throws(InvalidArgumentException::class, 'ISBN tidak valid');

it('ISBN yang dihasilkan factory selalu valid', function () {
    foreach (range(1, 20) as $i) {
        expect(Isbn::isValid(BookFactory::generateIsbn13()))->toBeTrue();
    }
});

// ---------- DENDA ----------
it('denda nol bila dikembalikan tepat waktu', function () {
    $due = now()->addDays(7);

    $denda = Denda::hitung($due, now());

    expect($denda->getAmount())->toBe(0.0)->and($denda->isLate())->toBeFalse();
});

it('denda dihitung otomatis: hari terlambat x tarif per hari', function () {
    $due = now()->subDays(3);

    $denda = Denda::hitung($due, now());

    expect($denda->getDaysLate())->toBe(3)
        ->and($denda->getAmount())->toBe(3000.0)
        ->and($denda->toMoney()->getAmount())->toBe(3000.0);
});

it('terlambat kurang dari sehari tetap dihitung 1 hari', function () {
    expect(Denda::hitung(now()->subHours(2), now())->getDaysLate())->toBe(1);
});

it('denda menolak hari negatif', function () {
    new Denda(-1);
})->throws(InvalidArgumentException::class);
