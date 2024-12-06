<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {

        $userId = Auth::user()->id;

        $limit = $request->input('limit', 20); // Default limit of 20
        $before = $request->input('before', now()); // Default to current time

        $messages = Message::where('user_id', $userId)
            ->where('created_at', '<', $before)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        $messages=$messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
                'sender' => $message->sender,
                'created_at' => $message->created_at,
            ];
        });
        //mark all messages read which have been fetched now
        Message::where('user_id', $userId)->update(['status' => 'read']);

        return response()->json(['status' => 'success', 'data' => $messages], 200);
    }
    public function newMessages(){
        $userId = Auth::user()->id;
        $messages = Message::where('user_id', $userId)->where('sender', 'admin')->where('status', 'unread')->get();

        $messages=$messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
                'sender' => $message->sender,
                'created_at' => $message->created_at,
            ];
        });
        //now  update that new messages
        Message::where('user_id', $userId)->where('sender', 'admin')->where('status', 'unread') ->update(['status' => 'read']);
        return response()->json(['status' => 'success', 'data' => $messages], 200);
    }
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $profilePicturePath = null;
        if ($request->hasFile('attachment')) {
            $profilePicture = $request->file('attachment');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('attachments', $fileName, 'public');
        }
        $message = new Message();
        $message->user_id = $userId;
        $message->message = $request->input('message');
        $message->attachment = $profilePicturePath;
        $message->sender = 'user';
        $message->status = 'unread';
        $message->save();

        if ($message) {
            $msg = [
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
            ];
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully', 'data' => $msg], 200);
        } else {
            $msg = [];
            return response()->json(['status' => 'error', 'message' => 'Failed to send message', 'data' => $msg], 500);
        }
    }
    public function sendAdminMessage(Request $request)
    {
        $userId = $request->input('user_id');
        $profilePicturePath = null;
        if ($request->hasFile('attachment')) {
            $profilePicture = $request->file('attachment');
            $fileName = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicturePath = $profilePicture->storeAs('attachments', $fileName, 'public');
        }
        $message = new Message();
        $message->user_id = $userId;
        $message->message = $request->input('message');
        $message->attachment = $profilePicturePath;
        $message->sender = 'admin';
        $message->save();

        if ($message) {
            $msg = [
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
                'created_at'=>$message->created_at->format('h:i A')
            ];
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully', 'data' => $msg], 200);
        } else {
            $msg = [];
            return response()->json(['status' => 'error', 'message' => 'Failed to send message', 'data' => $msg], 500);
        }
    }
    public function getUsers()
{
    $users = User::has('account')->with('account')->get(); // Replace with your user fetching logic
    $data = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->account->firstName . ' ' . $user->account->lastame,
            'profile_picture' => $user->account->profilePicture ? asset('storage/' . $user->account->profilePicture) : null,
        ];
    });
    return response()->json(['status' => 'success', 'data' => $data]);
}
public function getMessages($userId)
{
    $messages = Message::where('user_id', $userId)->orderBy('created_at', 'asc')->get();
    $data = $messages->map(function ($message) {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'sender' => $message->sender,
            'created_at' => $message->created_at->format('H:i A'),
        ];
    });
    return response()->json(['status' => 'success', 'data' => $data]);
}
public function loadEarlierMessages(Request $request)
{
    $userId = $request->input('user_id');
    $lastMessageTimestamp = $request->input('last_message_time'); // Pass the timestamp of the oldest message currently displayed.

    $messages = Message::where('user_id', $userId)
        ->where('created_at', '<', $lastMessageTimestamp)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->reverse(); // Reverse to show oldest first

    return response()->json([
        'status' => 'success',
        'data' => $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender' => $message->sender,
                'created_at' => $message->created_at->format('H:i A'),
            ];
        }),
    ]);
}
public function newMessagesforAdmin(Request $request)
{
    $userId = $request->input('user_id');
    $lastMessageTimestamp = $request->input('last_message_time');

    $messages = Message::where('user_id', $userId)->where('sender', 'user')
        ->where('created_at', '>', $lastMessageTimestamp)
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender' => $message->sender,
                'created_at' => $message->created_at->format('H:i A'),
                'formated_time'=> $message->created_at->format('h:i A')

            ];
        }),
    ]);
}



}
