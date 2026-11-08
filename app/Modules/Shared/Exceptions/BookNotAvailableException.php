<?php

namespace Modules\Shared\Exceptions;

use Exception;

class BookNotAvailableException extends Exception
{
    public function __construct(
        protected string $bookTitle,
        protected int $available,
        protected int $requested
    ) {
        parent::__construct(
            "Buku '{$bookTitle}' tidak tersedia dalam jumlah yang diminta. " .
            "Stok: {$available}, diminta: {$requested}."
        );
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getRequested(): int
    {
        return $this->requested;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'book_not_available',
            ], 422);
        }

        return back()->withInput()->withErrors(['stock' => $this->getMessage()]);
    }
}
