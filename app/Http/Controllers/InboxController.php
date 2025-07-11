<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua pesan
        $messages = $user->inboxes()->latest()->paginate(15);
        
        // Tandai semua pesan yang belum dibaca sebagai sudah dibaca
        $user->inboxes()->where('is_read', false)->update(['is_read' => true]);

        return view('pages.inbox.index', compact('messages'));
    }
}
