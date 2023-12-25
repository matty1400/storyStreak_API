<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dummyApi;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// GET REQUESTS

Route::get('user', [DeviceController::class, 'getUser']);
Route::get('user/all', [DeviceController::class, 'getAllUsers']);
Route::get('user/id/{id?}', [DeviceController::class, 'getUserById']);
Route::get('user/username/{username?}', [DeviceController::class, 'getUserByName']);
Route::get('stories/{userId?}', [DeviceController::class, 'getStoryByUserId']);
Route::get('likes/{storyId?}', [DeviceController::class, 'getLikesByStoryId']);
Route::get('comments/{storyId?}', [DeviceController::class, 'getCommentsByStoryId']);
Route::get('follows/{userId?}', [DeviceController::class, 'getFollowsByUserId']);
Route::get('topics/id/{topicId?}', [DeviceController::class, 'getTopicById']);
Route::get('topics', [DeviceController::class, 'getTopics']);
Route::get('topics/current', [DeviceController::class, 'getCurrentTopic']);
Route::get('followerstories/{userId?}', [DeviceController::class, 'getFollowedStories']);
Route::get('follower/{userId?}', [DeviceController::class, 'getFollowingUsers']);
Route::get('followernames/{userId?}', [DeviceController::class, 'getFollowerNames']);

Route::put("delete", [DeviceController::class, 'deleteFriend']);

Route::get('codecheck', [DeviceController::class, 'checkActivationCode']);

Route::get('mail', [DeviceController::class, 'sendWelcomeEmail']);

Route::get('sendmail', [DeviceController::class, 'sendWelcomeEmail']);










//POST REQUESTS
Route::post('user', [DeviceController::class, 'postUser']);
Route::post('stories', [DeviceController::class, 'postStory']);
Route::post('likes', [DeviceController::class, 'postLike']);
Route::post('comments', [DeviceController::class, 'postComment']);
Route::post('follows', [DeviceController::class, 'postFollow']);
Route::post('topics/current', [DeviceController::class, 'postCurrentTopic']);








