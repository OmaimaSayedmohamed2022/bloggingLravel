<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentAuthentication
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
            $userId = $request->input('userId');

            $comment = DB::table('comments')->where('id', $id)->first();
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], 404);
            }

            if ($user->id !== $comment->user_id) {
                return response()->json(['message' => 'You are not authorized to delete this comment'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error authenticating comment: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}



