<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::has('account')->with('account')->orderBy('id', 'desc')->paginate(10);
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->account->firstName . ' ' . $user->account->lastName,
                'profile_picture' => $user->account->profile_picture ? asset('storage/' . $user->account->profile_picture) : '',
                'email' => $user->email
            ];
        });
        //get the id of the first user from array
        $firstUserId = $users->first()['id'];
        $firstUser = User::with('account')->find($firstUserId);
        $messages = Message::where('user_id', $firstUserId)->with('user')->orderBy('created_at', 'asc')->paginate(10);
        return view('chat.index', compact('users', 'messages', 'firstUser'));
    }
    public function show($id)
    {
        $users = User::has('account')->with('account')->orderBy('id', 'desc')->paginate(10);
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->account->firstName . ' ' . $user->account->lastName,
                'profile_picture' => $user->account->profilePicture ? asset('storage/' . $user->account->profilePicture) : null,
                'email' => $user->email
            ];
        });
        //get the id of the first user from array
        $firstUserId = $id;
        $firstUser = User::with('account')->find($firstUserId);
        $messages = Message::where('user_id', $firstUserId)
            ->with('user')
            ->orderBy('created_at', 'desc') // Fetch the latest messages
            ->take(10) // Limit to the latest 10 messages
            ->get()
            ->reverse(); // Reverse the order for display (oldest first on top)

        return view('chat.index', compact('users', 'messages', 'firstUser'));
    }
}
