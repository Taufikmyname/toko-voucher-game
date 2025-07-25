<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inbox; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PromoNotificationController extends Controller
{
    public function create()
    {
        return view('admin.promo-notifications.create');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $users = User::where('is_admin', false)->get();
        
        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada pengguna untuk dikirimi notifikasi.');
        }

        // Buat pesan di inbox untuk setiap user
        foreach ($users as $user) {
            Inbox::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'body' => $request->body,
                'type' => 'promo',
            ]);
        }

        // Kirim notifikasi FCM ke user yang memiliki token
        $usersWithToken = $users->whereNotNull('fcm_token')->pluck('fcm_token')->all();
        if (!empty($usersWithToken)) {
            $messaging = app('firebase.messaging');
            $notification = Notification::create($request->title, $request->body);
            $message = CloudMessage::new()->withNotification($notification);
            $messaging->sendMulticast($message, $usersWithToken);
        }

        return back()->with('success', "Notifikasi promo berhasil dikirim ke " . $users->count() . " pengguna.");
    }
}