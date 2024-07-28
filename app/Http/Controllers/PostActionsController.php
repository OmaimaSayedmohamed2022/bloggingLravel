<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostActions;
use App\Models\User;
use App\Models\Post;
use App\Models\Comments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PostActionsController extends Controller
{
    public function createAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:like,dislike,love,care',
                'user_id' => 'required|exists:users,id',
                'post_id' => 'required|exists:posts,id',
                'type' => 'required|string|in:like,dislike,love,care'
            ]);

            $newAction = PostActions::create($validated);

            $actionsCount = PostActions::select('type', DB::raw('COUNT(type) as count'))
                ->whereIn('type', ['like', 'dislike', 'love', 'care'])
                ->groupBy('type')
                ->get();

            return response()->json([
                'message' => 'Action created successfully',
                'newAction' => $newAction,
                'actionsCount' => $actionsCount
            ], 201);
        } catch (\Exception $e) {
            Log::error('Creating Action Error: ' . $e->getMessage());
            return response()->json(['message' => 'Creating Action Error'. $e->getMessage()], 500);
        }
    }

    public function deleteAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:post_actions,id'
            ]);

            PostActions::destroy($validated['id']);

            return response()->json(['message' => 'Action deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Deleting Action Error: ' . $e->getMessage());
            return response()->json(['message' => 'Deleting Action Error'], 500);
        }
    }

    public function createComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'post_id' => 'required|exists:posts,id',
                'parent_id' => 'nullable|exists:comments,id'
            ]);

            $newComment = Comments::create($validated);
            $commentCount = Comments::count();

            return response()->json([
                'message' => 'Comment created successfully',
                'newComment' => $newComment,
                'commentCount' => $commentCount
            ], 201);
        } catch (\Exception $e) {
            Log::error('Creating Comment Error: ' . $e->getMessage());
            return response()->json(['message' => 'Creating Comment Error'. $e->getMessage()], 500);
        }
    }

    public function deleteComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:comments,id'
            ]);

            Comments::destroy($validated['id']);

            return response()->json(['message' => 'Comment deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Deleting Comment Error: ' . $e->getMessage());
            return response()->json(['message' => 'Deleting Comment Error'], 500);
        }
    }


    public function replyOnComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'commentId' => 'required|exists:comments,id',
                'content' => 'required|string',
                'user_id' => 'required|exists:users,id'
            ]);

            $parentComment = Comments::find($validated['commentId']);

            if (!$parentComment) {
                return response()->json(['message' => 'Parent comment not found'], 404);
            }
            $reply = Comments::create([
                'content' => $validated['content'],
                'user_id' => $validated['user_id'],
                'post_id' => $parentComment->post_id,
                'parent_id' => $validated['commentId']
            ]);

            return response()->json(['Reply created successfully', 'reply' => $reply], 201);
        } catch (\Exception $e) {
            Log::error('Error creating reply' . $e->getMessage());
            return response()->json(['Error creating reply'. $e->getMessage()], 500);
        }
    }

    public function getCommentReplies(Request $request)
    {
        try {
            $validated = $request->validate([
                'comment_id' => 'required|exists:comments,id'
            ]);

            $commentId = $validated['comment_id'];

            $comment = Comments::find($commentId);

            // if (!$comment) {
            //     return response()->json(['message' => 'Comment not found'], 404);
            // }

            $replies = Comments::where('parent_id', 'like', '%' . $commentId)->get();

            return response()->json(['comment' => $comment, 'replies' => $replies], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving comment and replies: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }


    //comment//non
    public function likeComment(Request $request)
{
    try {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'like' => 'required|boolean'
        ]);

        $newLike = Comments::create($validated);

        return response()->json(['like created successfully', 'newLike' => $newLike], 201);
    } catch (\Exception $e) {
        Log::error('Error in liking a comment: ' . $e->getMessage());
        return response()->json(['message' => 'Like a Comment Error: ' . $e->getMessage()], 500);
    }
}


    public function hideComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:comments,id',
                'author_id' => 'required|exists:users,id'
            ]);

            $comment = Comments::find($validated['id']);
            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], 400);
            }

            $author = User::find($validated['author_id']);
            if (!$author) {
                return response()->json(['message' => 'Author not found'], 400);
            }

            $comment->update(['hidden' => true]);

            return response()->json(['message' => 'Comment hidden successfully', 'hiddenComment' => $comment], 200);
        } catch (\Exception $e) {
            Log::error('Error hiding comment: ' . $e->getMessage());
            return response()->json(['message' => 'Error hiding comment'], 500);
        }
    }
}
