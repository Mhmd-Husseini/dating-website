<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FavoriteBlock;
use App\Models\Message;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentUser = Auth::user();
        $gender = $currentUser->gender == 'male' ? 'female' : 'male';

        $query = User::where('gender', $gender)
            ->where('id', '<>', $currentUser->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('min_age')) {
            $query->where('age', '>=', $request->input('min_age'));
        }

        if ($request->has('max_age')) {
            $query->where('age', '<=', $request->input('max_age'));
        }

        if ($request->has('location')) {
            $query->where('location', $request->input('location'));
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $users = $query->get();

        return response()->json(['users' => $users]);
    }

    public function favorite(Request $request)
    {
        $currentUser = Auth::user();
        $otherUser = User::find($request->input('user_id'));

        $existingRecord = user_user::where('user1_id', $currentUser->id)
            ->where('user2_id', $otherUser->id)
            ->first();

        if ($existingRecord) {
            $existingRecord->favorite_block = 1;
            $existingRecord->save();
        } else {
            $record = new user_user();
            $record->user_id_action = $currentUser->id;
            $record->user2_id = $otherUser->id;
            $record->favorite_block = 1;
            $record->save();
        }

        return response()->json(['message' => 'User favorited successfully.']);
    }

    public function block(Request $request)
    {
        $currentUser = Auth::user();
        $otherUser = User::find($request->input('user_id'));

        $existingRecord = user_user::where('user1_id', $currentUser->id)
            ->where('user2_id', $otherUser->id)
            ->first();

        if ($existingRecord) {
            $existingRecord->favorite_block = 0;
            $existingRecord->save();
        } else {
            $record = new user_user();
            $record->user_id_action = $currentUser->id;
            $record->user2_id = $otherUser->id;
            $record->favorite_block = 0;
            $record->save();
        }

        return response()->json(['message' => 'User blocked successfully.']);
    }

    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');
        $messageText = $request->input('message_text');

        $blocked = user_user::where('user1_id', $receiverId)
            ->where('user2_id', $senderId)
            ->where('favorite_block', 0)
            ->exists();

        if ($blocked) {
            return response()->json(['error' => 'You are blocked by the recipient'], 400);
        }

        $message = new Message();
        $message->user_id = $senderId;
        $message->user2_id = $receiverId;
        $message->msg = $messageText;
        $message->save();

        return response()->json(['message' => 'Message sent successfully']);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'username' => 'required|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8',
            'gender' => 'required',
            'age' => 'required|integer',
            'bio' => 'required',
            'picture' => 'required',
            'location_id' => 'required|integer',
        ]);
    
        $user->update([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'gender' => $validatedData['gender'],
            'age' => $validatedData['age'],
            'bio' => $validatedData['bio'],
            'picture' => $validatedData['picture'],
            'location_id' => $validatedData['location_id'],
        ]);
    
        if(isset($validatedData['password'])){
            $user->update(['password' => Hash::make($validatedData['password'])]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
    
}
           
