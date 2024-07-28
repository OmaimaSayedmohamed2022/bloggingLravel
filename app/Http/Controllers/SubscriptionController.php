<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function userSubscribeAuthor(Request $request)
    {
        try {
            $validated = $request->validate([
                'userId' => 'required|exists:users,id',
                'authorId' => 'required|exists:users,id',
            ]);

            $user = User::find($validated['userId']);
            $author = User::find($validated['authorId']);

            if (!$user) {
                return response()->json(['User not found'], 404);
            }

            if (!$author) {
                return response()->json(['Author not found'], 404);
            }

            Subscription::create([
                'user_id' => $validated['userId'],
                'author_id' => $validated['authorId']
            ]);

            return response()->json([ 'User subscribed to author successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in userSubscribeAuthor: ' . $e->getMessage());
            return response()->json(['Error in user Subscribe Author'], 500);
        }
    }

    public function userUnsubscribeAuthor(Request $request)
    {
        try {
            $validated = $request->validate([
                'userId' => 'required|exists:subscriptions,user_id',
                'authorId' => 'required|exists:subscriptions,author_id',
            ]);

            $subscription = Subscription::where([
                ['user_id', '=', $validated['userId']],
                ['author_id', '=', $validated['authorId']]
            ])->first();

            if (!$subscription) {
                return response()->json(['message' => 'Subscription not found'], 404);
            }

            $subscription->delete();

            return response()->json(['status' => 1, 'message' => 'User unsubscribed from author successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in userUnsubscribeAuthor: ' . $e->getMessage());
            return response()->json(['message' => 'Error in userUnsubscribeAuthor'], 500);
        }
    }

    public function getAllSubscribedAuthors(Request $request)
    {
        try {
            $validated = $request->validate([
                'authorId' => 'required|exists:users,id',
            ]);

            // Retrieve the subscriptions for the author
            $subscriptions = Subscription::where('author_id', $validated['authorId'])->get();

            // Count the number of subscriptions
            $subscriptionCount = $subscriptions->count();

            return response()->json([
                'subscriptions' => $subscriptions,
                'subscriptionCount' => $subscriptionCount
            ], 200);
        } catch (\Exception $e) {
            Log::error('Cannot get authors: ' . $e->getMessage());
            return response()->json(['message' => 'Cannot get authors'], 500);
        }
    }
}
