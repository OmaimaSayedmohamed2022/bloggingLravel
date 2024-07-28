<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\SavedPost;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;



class PostController extends Controller
{
    public function createPost(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'cover_image' => 'nullable|string',
                'published' => 'boolean',
                'categories' => 'nullable|string',
                'summary' => 'nullable|string',
                'draft' => 'boolean',
                'author_id' => 'required|exists:users,id',

            ]);

            $post = Post::create($validated);
            $subscriptions = Subscription::where('author_id', $request->author_id)->get();

            // Send notifications to users subscribed to the author
            foreach ($subscriptions as $subscription) {
                $user = User::find($subscription->user_id);
                if ($user) {
                    $this->sendEmail([
                        'to' => $user->email,
                        'subject' => 'New Post Notification',
                        'text' => "A new post \"{$post->title}\" has been published by the author. You can read it here: " . env('APP_URL') . "/posts/{$post->id}"
                    ]);
                }
            }
            return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating post', 'error' => $e->getMessage()], 500);

        }
    }
    //publish post
    public function publishPost(Request $request)
    {
        $validated = $request->validate(['post_id' => 'required|exists:posts,id']);
        $post = Post::find($validated['post_id']);
        $post->published = true;
        $post->save();

        return response()->json(['message' => 'Post published successfully']);
    }

    public function unpublishPost(Request $request)
    {
        $validated = $request->validate(['post_id' => 'required|exists:posts,id']);

        $post = Post::find($validated['post_id']);
        $post->published = false;
        $post->save();

        return response()->json(['message' => 'Post unpublished successfully']);
    }

    public function updatePost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable',
        ]);

        $post = Post::find($validated['post_id']);
        $post->title = $request->title ?? $post->title;
        $post->content = $request->content ?? $post->content;
        $post->save();

        return response()->json(['message' => 'Post updated successfully', 'updatedPost' => $post]);
    }

    public function deletePost(Request $request)
    {
        $validated = $request->validate(['post_id' => 'required|exists:posts,id']);

        $post = Post::find($validated['post_id']);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /// git all author posts
    public function getAuthorPosts(Request $request)
    {
        $validated = $request->validate([
            'author_id' => 'required|exists:users,id',
            'page' => 'nullable|integer',
            'pageSize' => 'nullable|integer',
        ]);

        $page = $validated['page'] ?? 1;
        $pageSize = $validated['pageSize'] ?? 10;

        $posts = Post::where('author_id', $validated['author_id'])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page);

        return response()->json($posts);
    }
      //save posts for user
    public function savePostsForUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        SavedPost::create($validated);

        return response()->json(['message' => 'Post saved successfully for user']);
    }

    public function getSavedPostsForUser(Request $request)
    {
        $validated = $request->validate(['user_id' => 'required|exists:users,id']);

        $savedPosts = SavedPost::where('user_id', $validated['user_id'])->get();

        return response()->json($savedPosts);
    }

    // remove saved post
    public function removeSavedPostForUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        $savedPost = SavedPost::where('user_id', $validated['user_id'])
            ->where('post_id', $validated['post_id'])
            ->first();

        if (!$savedPost) {
            return response()->json(['message' => 'Saved post not found for the user'], 404);
        }

        $savedPost->delete();

        return response()->json(['message' => 'Saved post removed successfully']);
    }

    private function sendEmail($emailContent)
    {
        try {
            Mail::raw($emailContent['text'], function ($message) use ($emailContent) {
                $message->to($emailContent['to'])
                    ->subject($emailContent['subject']);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return false;
        }
    }
}




