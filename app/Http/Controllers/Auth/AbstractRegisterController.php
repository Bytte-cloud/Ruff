<?php

namespace Ruff\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Ruff\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Failed;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Event;
use Ruff\Events\Auth\DirectLogin;
use Ruff\Exceptions\DisplayException;
use Ruff\Http\Controllers\Controller;
use Ruff\Services\Users\UserCreationService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

abstract class AbstractRegisterController extends Controller
{
    use AuthenticatesUsers;

    protected AuthManager $auth;

    /**
     * Lockout time for failed register requests.
     */
    protected int $lockoutTime;

    /**
     * After how many attempts should registrations be throttled and locked.
     */
    protected int $maxRegisterAttempts;

    /**
     * Where to redirect users after login / registration.
     */
    protected string $redirectTo = '/';

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->lockoutTime = config('auth.lockout.time');
        $this->maxRegisterAttempts = config('auth.lockout.attempts');
        $this->auth = Container::getInstance()->make(AuthManager::class);
    }

    /**
     * Get the failed register response instance.
     *
     * @throws DisplayException
     */
    protected function sendFailedRegisterResponse(Request $request, ?Authenticatable $user = null, ?string $message = null)
    {
        $this->incrementLoginAttempts($request);
        $this->fireFailedRegisterEvent($user, [
            $this->getField($request->input('user')) => $request->input('user'),
        ]);

        throw new DisplayException(trans('auth.failed'));
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendRegisterResponse(Request $request): JsonResponse
    {
        $connection = app(\Illuminate\Database\ConnectionInterface::class);
        $hasher = app(\Illuminate\Contracts\Hashing\Hasher::class);
        $passwordBroker = app(\Illuminate\Contracts\Auth\PasswordBroker::class);
        $repository = app(\Ruff\Contracts\Repository\UserRepositoryInterface::class);

        $service = new UserCreationService($connection, $hasher, $passwordBroker, $repository);

        $service->handle([
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'name_first' => $request->input('first_name'),
            'name_last' => $request->input('last_name'),
            'password' => $request->input('password'),
        ]);

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'intended' => '/auth/login?register=true',
            ],
        ]);
    }

    /**
     * Fire a failed register event.
     */
    protected function fireFailedRegisterEvent(?Authenticatable $user = null, array $credentials = [])
    {
        Event::dispatch(new Failed('auth', $user, $credentials));
    }
}
