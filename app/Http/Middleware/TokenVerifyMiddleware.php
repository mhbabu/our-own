<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Exceptions\OAuthServerException;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueOAuthServerException;


class TokenVerifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if the token is present and valid
            $request->user(); // This will validate the token

        } catch (OAuthServerException $e) {
            // Handle specific OAuth exceptions
            if ($e->getCode() === 10) {
                return new JsonResponse(['status' => 'Authorization Token is missing. Please provide a valid token in the Authorization header.'], 401);
            } elseif ($e->getCode() === 6) {
                return new JsonResponse(['status' => 'Token has expired. Please refresh your token or log in again.'], 401);
            } elseif ($e->getCode() === 7) {
                return new JsonResponse(['status' => 'Token is invalid. Please provide a valid token.'], 401);
            } else {
                return new JsonResponse(['status' => 'Authorization error.'], 401);
            }
        } catch (\Exception $e) {
            // Handle any other server errors
            return new JsonResponse(['status' => 'An unexpected error occurred. Please try again later.'], 500);
        }

        return $next($request);
    }
}
