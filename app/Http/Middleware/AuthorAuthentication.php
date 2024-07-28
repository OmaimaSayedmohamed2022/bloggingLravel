<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthorAuthentication
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
            $id = $request->input('id');
            $authorId = $request->input('authorId');

            $author = DB::table('users')->where('id', $authorId)->first();
            $comment = DB::table('comments')->where('id', $id)->first();

            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], 404);
            }

            if (!$author) {
                return response()->json(['message' => 'Author not found'], 404);
            }

            if ($author->id !== $comment->user_id) {
                return response()->json(['message' => 'You are not authorized to delete this comment'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error authenticating author: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
