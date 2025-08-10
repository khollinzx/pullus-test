<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\JsonResponseAPI;
use Closure;
use Illuminate\Http\Request;

class ManageUsersAccess
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if(!$request->hasHeader('authorization'))
            return JsonResponseAPI::errorResponse("Access denied! No Authorization header was defined.", JsonResponseAPI::HTTP_UNAUTHORIZED);

        /**
         *
         * Switch among the guard requested and set the provider
         * accordingly using passport authentication means
         */
        OauthAccessToken::setAuthProvider('users');

        return $next($request);
    }
}
