<?php

namespace Ruff\Http\Controllers\Auth;

use Ruff\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterController extends AbstractRegisterController
{
    /**
     * Handle all incoming requests for the authentication routes and render the
     * base authentication view component. React will take over at this point and
     * turn the Register area into an SPA.
     */
    public function index(): View
    {
        return view('templates/auth.core');
    }

    public function register(Request $request): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        try {
            $user = User::where('email', $request->input('email'))->orWhere('username', $request->input('username'))->first();

            if ($user) {
                return response()->json(['error' => 'The email or username is already taken.'], 400);
            }
        } catch (ModelNotFoundException) {
            $this->sendFailedRegisterResponse($request);
        }

        return $this->sendRegisterResponse($request);
    }
}
