<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostAuthentication
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
            $postId = $request->input('postId');
            $post = DB::table('posts')->where('id', $postId)->first();

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error authenticating post: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
