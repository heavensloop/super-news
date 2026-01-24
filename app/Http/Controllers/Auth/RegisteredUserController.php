<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Psl\Type;

class RegisteredUserController extends Controller
{
    public function store(SignupRequest $request)
    {
        $user = Type\instance_of(User::class)->assert(
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ])
        );

        event(new Registered($user));

        return response()->json([
            'created' => true,
        ], 201);
    }
}
