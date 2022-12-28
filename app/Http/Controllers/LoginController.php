<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, ['email' => 'required|exists:users,email', 'password' => 'required']);

        $user = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['data' => $user, 'token' => $user->createLoginToken(), 'message' => 'Updated']);
        }

        return response()->json(['error' => true, 'message' => 'Invalid credentials']);
    }
}
