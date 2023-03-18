<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}