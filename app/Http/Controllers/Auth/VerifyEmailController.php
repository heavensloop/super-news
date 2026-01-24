<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\VerifyEmailRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class VerifyEmailController extends Controller
{
    public function __invoke(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json(['message' => 'Email verified'], 200);
    }
}
