<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(Request $request, string $id): RedirectResponse
    {
        // Scoped to the current user's notifications - cannot touch anyone else's
        $request->user()->notifications()->whereKey($id)->first()?->markAsRead();

        return back();
    }
}
