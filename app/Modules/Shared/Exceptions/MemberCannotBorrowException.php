<?php

namespace Modules\Shared\Exceptions;

use Exception;

class MemberCannotBorrowException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'member_cannot_borrow',
            ], 422);
        }

        return back()->withInput()->withErrors(['member' => $this->getMessage()]);
    }
}
