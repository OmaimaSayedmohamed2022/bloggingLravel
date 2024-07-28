<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $userId = $request->input('userId');
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if ($user->roles === 'author') {
                return response()->json(['message' => 'Author cannot make comment'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error authenticating user: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
