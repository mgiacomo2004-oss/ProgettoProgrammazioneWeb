<?php

namespace App\Http\Controllers;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()
            ->user()
            ->notifications()
            ->latest()
            ->get();

        auth()
            ->user()
            ->notifications()
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return view('notifications', [
            'notifications' => $notifications
        ]);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notifica eliminata.');
    }
}