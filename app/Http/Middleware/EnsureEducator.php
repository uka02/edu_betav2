<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEducator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isEducator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('lessons.educator_only'),
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, __('lessons.educator_only'));
        }

        return $next($request);
    }
}
