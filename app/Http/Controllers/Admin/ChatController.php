<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $sessions = ChatMessage::query()
            ->selectRaw('session_id, MAX(id) as last_id, COUNT(*) as total, MIN(created_at) as started_at')
            ->groupBy('session_id')
            ->orderByDesc('last_id')
            ->paginate(15);

        $lastMessages = ChatMessage::whereIn('id', $sessions->pluck('last_id'))->get()->keyBy('id');

        $sessionUsers = ChatMessage::query()
            ->whereIn('session_id', $sessions->pluck('session_id'))
            ->whereNotNull('user_id')
            ->get(['session_id', 'user_id'])
            ->unique('session_id')
            ->pluck('user_id', 'session_id');

        $users = User::whereIn('id', $sessionUsers->values()->unique())
            ->get()
            ->keyBy('id');

        $totalSessions = ChatMessage::query()->distinct()->count('session_id');

        return view('admin.chat.index', compact('sessions', 'lastMessages', 'sessionUsers', 'users', 'totalSessions'));
    }

    public function show(string $session)
    {
        $messages = ChatMessage::where('session_id', $session)->orderBy('id')->get();

        abort_if($messages->isEmpty(), 404);

        $userId = $messages->firstWhere('user_id', '!=', null)?->user_id;
        $user = $userId ? User::find($userId) : null;

        return view('admin.chat.show', compact('session', 'messages', 'user'));
    }

    public function reply(Request $request, string $session)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        abort_unless(ChatMessage::where('session_id', $session)->exists(), 404);

        $userId = ChatMessage::where('session_id', $session)->whereNotNull('user_id')->value('user_id');

        ChatMessage::create([
            'session_id' => $session,
            'user_id' => $userId,
            'role' => 'assistant',
            'content' => $data['content'],
            'meta' => ['admin' => true],
        ]);

        return back()->with('success', 'Balasan terkirim ke pembeli.');
    }
}
