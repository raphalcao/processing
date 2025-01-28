<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Contracts\Repositories\UserRepositoryInterface;

class CheckTokenExpiration
{

    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $user = $this->userRepositoryInterface->findBytoken($token);

        if (!$user) return response()->json([
            'error' => 'User not found.'
        ], 404, ['Content-Type' => 'application/json']);

        if ($user && $user->expired_date->isPast()) {
            $expiredDate = Carbon::parse($user->expired_date);
            if ($expiredDate->isPast()) {
                throw new HttpException(404, 'The token is expired.');
            }
        }

        return $next($request);
    }
}
