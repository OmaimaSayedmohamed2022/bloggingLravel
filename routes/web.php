<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostActionsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ImageUploadController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');



   // users routers
    Route::middleware('auth:api')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/update/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);


    // post routers
    Route::post('/posts', [PostController::class, 'createPost']);
    Route::put('/posts/publish', [PostController::class, 'publishPost']);
    Route::put('/posts/unpublish', [PostController::class, 'unpublishPost']);
    Route::put('/posts/update', [PostController::class, 'updatePost']);
    Route::post('/posts/save', [PostController::class, 'savePostsForUser']);
    Route::delete('/posts/delete', [PostController::class, 'deletePost']);
    Route::get('/posts/author', [PostController::class, 'getAuthorPosts']);


    // post action routers
    Route::post('/subscribe', [SubscriptionController::class, 'userSubscribeAuthor']);
    Route::post('/unsubscribe', [SubscriptionController::class, 'userUnsubscribeAuthor']);
    Route::get('/subscribed-authors', [SubscriptionController::class, 'getAllSubscribedAuthors']);



     Route::post('/create-action', [PostActionsController::class, 'createAction']);
     Route::delete('/delete-action', [PostActionsController::class, 'deleteAction']);
     Route::post('/createComment', [PostActionsController::class, 'createComment']);
     Route::delete('/deleteComment', [PostActionsController::class, 'deleteComment']);
     Route::get('/getCommentReplies', [PostActionsController::class, 'getCommentReplies']);
     Route::post('/replyonComment', [PostActionsController::class, 'replyOnComment']);
     Route::post('/likeComment', [PostActionsController::class, 'likeComment']);
     Route::post('/hideComment', [PostActionsController::class, 'hideComment']);



    Route::post('/uploadImage', [ImageUploadController::class, 'uploadImage']);

   });


Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});


