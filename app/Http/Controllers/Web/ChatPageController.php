<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatPageController extends Controller
{
    protected function currentUserId(): ?string
    {
        return optional(Auth::user())->id ?? session('user_id');
    }

    public function index()
    {
        $uid = $this->currentUserId();
        // load the conversation owned by this user (if any) and its messages
        $conversation = null;
        $messages = collect();
        $conversations = DB::table('chat_conversations')->orderByDesc('created_at')->limit(10)->get();
        if ($uid) {
            $conversation = DB::table('chat_conversations')->where('user_id', $uid)->first();
            if ($conversation) {
                $messages = DB::table('chat_messages')->where('conversation_id', $conversation->id)->orderBy('created_at')->get();
            }
        }

        return view('chat.index', compact('conversations','messages','uid','conversation'));
    }

    public function store(Request $request)
    {
        $uid = $this->currentUserId();
        $data = $request->validate([
            'conversation_id' => 'required|string',
            'content' => 'required|string',
        ]);
        $now = now();
        $msgId = (string) Str::uuid();
        DB::table('chat_messages')->insert([
            'id' => $msgId,
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $uid,
            'content' => $data['content'],
            'is_read' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ensure participant row exists for sender
        try {
            DB::table('chat_participants')->updateOrInsert(
                ['conversation_id' => $data['conversation_id'], 'user_id' => $uid],
                ['updated_at' => $now]
            );
            // increment unread_count for other participants
            DB::table('chat_participants')->where('conversation_id', $data['conversation_id'])->where('user_id', '!=', $uid)->increment('unread_count', 1);
        } catch (\Exception $e) {
            // ignore participant failures
        }

        // update conversation last message timestamp
        DB::table('chat_conversations')->where('id', $data['conversation_id'])->update(['last_message_at' => $now, 'updated_at' => $now]);

        // return JSON when requested via AJAX, otherwise redirect back
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message_id' => $msgId]);
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(['content' => 'required|string']);
        DB::table('chat_messages')->where('id',$id)->update($data + ['updated_at'=>now()]);
        return back();
    }

    public function destroy($id)
    {
        DB::table('chat_messages')->where('id',$id)->delete();
        return back();
    }
}
