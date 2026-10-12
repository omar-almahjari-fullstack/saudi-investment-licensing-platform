<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\AdminSentMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'recipient_user_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $sender = Auth::user();

        // basic role check: ensure sender is admin
        if (!$sender || (isset($sender->role) && $sender->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $recipient = User::find($data['recipient_user_id']);
        if (!$recipient) {
            return response()->json(['error' => 'Recipient not found'], 404);
        }

        // Ensure there's a conversation for this user (owner=user)
        $conversation = DB::table('chat_conversations')->where('user_id', $recipient->id)->first();
        if (!$conversation) {
            $convId = (string) Str::uuid();
            DB::table('chat_conversations')->insert([
                'id' => $convId,
                'title' => null,
                'user_id' => $recipient->id,
                'last_message_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $conversation = DB::table('chat_conversations')->where('id', $convId)->first();
        }

        // Insert the chat message
        $msgId = (string) Str::uuid();
        DB::table('chat_messages')->insert([
            'id' => $msgId,
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id ?? null,
            'content' => $data['message'],
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ensure participants exist for admin (sender) and recipient
        try {
            // recipient participant
            DB::table('chat_participants')->updateOrInsert(
                ['conversation_id' => $conversation->id, 'user_id' => $recipient->id],
                ['updated_at' => now()]
            );
            // admin participant
            if ($sender && $sender->id) {
                DB::table('chat_participants')->updateOrInsert(
                    ['conversation_id' => $conversation->id, 'user_id' => $sender->id],
                    ['updated_at' => now()]
                );
            }
            // increment unread_count for recipient
            DB::table('chat_participants')->where(['conversation_id' => $conversation->id, 'user_id' => $recipient->id])->increment('unread_count', 1);
        } catch (\Exception $e) {
            // non-fatal, continue
        }

        // update conversation last message timestamp
        DB::table('chat_conversations')->where('id', $conversation->id)->update(['last_message_at' => now(), 'updated_at' => now()]);

        // send DB notification (for UI badge and email fallback)
        $recipient->notify(new AdminSentMessageNotification($sender, $data['message']));

        return response()->json(['ok' => true, 'message' => 'تم إرسال الرسالة', 'conversation_id' => $conversation->id, 'message_id' => $msgId]);
    }

    /**
     * List conversations for admin view (recent conversations with unread counts and user info)
     */
    public function conversations(Request $request)
    {
        // require auth
        $user = Auth::user();
        if (!$user || (isset($user->role) && $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Return all investors (users with role='investor' or with an investor record),
        // along with any existing conversation + unread count for the current admin.
        $rows = DB::table('users as u')
            ->leftJoin('chat_conversations as c', 'c.user_id', '=', 'u.id')
            ->leftJoin('chat_participants as p', function($join) use ($user){
                $join->on('p.conversation_id', '=', 'c.id')->where('p.user_id', '=', $user->id);
            })
            ->leftJoin('investors as iv', 'iv.user_id', '=', 'u.id')
            ->where(function($q){
                $q->where('u.role', 'investor')->orWhereNotNull('iv.user_id');
            })
            ->select('c.id as conversation_id', 'u.id as user_id', 'u.full_name', 'u.email', 'c.last_message_at', DB::raw('COALESCE(p.unread_count,0) as unread'))
            ->orderByDesc('c.last_message_at')
            ->orderBy('u.full_name')
            ->get();

        return response()->json(['ok' => true, 'conversations' => $rows]);
    }

    /**
     * Return messages for a conversation
     */
    public function messages($conversationId)
    {
        $user = Auth::user();
        if (!$user || (isset($user->role) && $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $msgs = DB::table('chat_messages')->where('conversation_id', $conversationId)->orderBy('created_at')->get();
        return response()->json(['ok' => true, 'messages' => $msgs]);
    }

    /**
     * Mark conversation as read for a user (reset unread_count)
     */
    public function markRead(Request $request, $conversationId)
    {
        $user = Auth::user();
        if (!$user || (isset($user->role) && $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // ensure participant row exists for admin and set unread_count = 0
        try {
            DB::table('chat_participants')->updateOrInsert(
                ['conversation_id' => $conversationId, 'user_id' => $user->id],
                ['unread_count' => 0, 'updated_at' => now()]
            );
        } catch (\Exception $e) {
            // ignore
        }

        return response()->json(['ok' => true]);
    }
}
