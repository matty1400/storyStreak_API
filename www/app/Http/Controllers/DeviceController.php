<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\comments;
use App\Models\topics;
use App\Models\follows;
use App\Models\stories;
use App\Models\users;
use App\Models\likes;
use App\Models\currentTopic;



use Illuminate\Http\Request;



use Exception;
use App\Mail\WelcomeMail;
use App\Models\codes;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Mail;
use User;

$salt = "letsuprise";



class DeviceController extends Controller
{
    public $salt = "letsuprise";
    
    // GET REQUESTS
    public function getUser(Request $request){

        $username = $request->query('username'); // Accessing the 'email' header
        $password = $request->query('password'); // Accessing the 'password' header

        if (!$username || !$password) {
            // If header parameters are not provided, check query parameters
            $username = $request->query('username');
            $password = $request->query('password');
        }
        $password = hash("sha256", $password.$this->salt);
        $data = users::where('username', $username)
                     ->where('password', $password)
                     ->where('is_active', 1)
                     ->first(['id', 'mail']);

                     

        if ($data) {

            // $this->sendWelcomeEmail($data->mail,$username);
            return response()->json($data);
        } else {
            return response()->json("user not found");
        }
    }

    public function getUserById(Request $request){

        $user_id = $request->header('userId'); // Accessing the 'user_id' header

        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = users::all()->where('id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getUserByName(Request $request){

        $username = $request->header('username'); // Accessing the 'username' header

        if (!$username) {
            // If header parameters are not provided, check query parameters
            $username = $request->query('username');
        }
        if ($username) {
            $data = users::all()->where('username', $username)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getStoryByUserId(request $request){
        $user_id = $request->header('userId'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = stories::all()->where('user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "story not found";
            }
        }
        else{
            $data = "story not found";
        }

        return response()->json($data);
    }

    public function getLikesByStoryId(request $request){
        $story_id = $request->header('storyId'); // Accessing the 'story_id' header
        if (!$story_id) {
            // If header parameters are not provided, check query parameters
            $story_id = $request->query('storyId');
        }
        if ($story_id) {
            $data = likes::all()->where('liked_story', $story_id)->where('is_active', 1)->count();
            if($data =  0) {
                $data = " no likes";
            }
        }
        else{
            $data = "story not found";
        }

        return response()->$data;

        


    }

    public function getFollowsByUserId(request $request){
        $user_id = $request->header('userId'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = follows::all('followed_user_id')->where('following_user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getCommentsByStoryId(Request $request){

        $story_id = $request->header('storyId'); // Accessing the 'story_id' header

        if (!$story_id) {
            // If header parameters are not provided, check query parameters
            $story_id = $request->query('storyId');
        }
        if ($story_id) {
            $data = comments::all()->where('story_id', $story_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "story not found";
            }
        }
        else{
            $data = "story not found";
        }
        return response()->json($data);

    }

    public function getTopicById(Request $request){

        $topic_id = $request->header('topicId'); // Accessing the 'topic_id' header

        if (!$topic_id) {
            // If header parameters are not provided, check query parameters
            $topic_id = $request->query('topicId');
        }
        if ($topic_id) {
            $data = topics::all()->where('id', $topic_id);
            if($data->isEmpty()) {
                $data = "topic not found";
            }
        }
        else{
            $data = "topic not found";
        }
        return response()->json($data);

    }

    public function getTopics( Request $request){

        $data = topics::all();
        if($data->isEmpty()) {
            $data = "topics not found";
        }
        return response()->json($data);
    }

    public function getCurrentTopic( Request $request){

        $data = CurrentTopic::orderBy('id', 'desc')->first();
    if (!$data) {
        $data = "Topic not found";
    }
    return response()->json($data);
    }

    public function getAllUsers( Request $request){

        $data = users::all();
        if($data->isEmpty()) {
            $data = "users not found";
        }
        return response()->json($data);
    }
    public function getFollowingUsers(Request $request) {
        $user_id = $request->header('userId') ?? $request->query('userId');
    
        if (!$user_id) {
            return response()->json(["error" => "User ID not provided"], 400);
        }
    
        // Get the list of followed user IDs
        $followingUserIds = Follows::where('following_user_id', $user_id)
            ->where('is_active', 1)
            ->pluck('followed_user_id');
    
        return response()->json($followingUserIds);
    }
    public function getFollowerNames(Request $request){
        $user_id = $request->header('userId') ?? $request->query('userId');
    
        if (!$user_id) {
            return response()->json(["error" => "User ID not provided"], 400);
        }
    
        // Get the list of followed user IDs
        $followedUserIds = Follows::where('following_user_id', $user_id)
            ->where('is_active', 1)
            ->pluck('followed_user_id');

        if ($followedUserIds->isEmpty()) {
            return response()->json(["message" => "No followed users found"], 200);
        }

        $names = Users::whereIn('id', $followedUserIds)
        ->where('is_active', 1)
        ->get('username'); // Replace 'column1', 'column2' with the actual columns you need
    
        return response()->json($names);

        
        

        
    
    }
    
    
    public function getFollowedStories(Request $request) {
        $user_id = $request->header('userId') ?? $request->query('userId');
        
        if (!$user_id) {
            return response()->json(["error" => "User not found"], 404);
        }
    
        // Get the list of followed user IDs
        $followedUserIds = Follows::where('following_user_id', $user_id)
            ->where('is_active', 1)
            ->pluck('followed_user_id');
    
        if ($followedUserIds->isEmpty()) {
            return response()->json(["message" => "No followed users found"], 200);
        }
    
        // Get stories of the followed users without using get()
        $stories = Stories::whereIn('user_id', $followedUserIds)
            ->where('is_active', 1)
            ->get(['title', 'content','user_id']); // Replace 'column1', 'column2' with the actual columns you need
    
        return response()->json($stories);
    }
    
    




    
 

   


    

    // POST REQUESTS


   

   
    public function postUser(Request $request){

        // Check if parameters are in the query string
        $username = $request->query('username');
        $password = $request->query('password');
        $mail = $request->query('mail');
    
        // If parameters are not in the query string, check the request body (JSON or form data)
        if (empty($username) || empty($password) || empty($mail)) {
            $username = $request->input('username');
            $password = $request->input('password');
            $mail = $request->input('mail');
        }
    
        // Check if parameters are still not available, return an error response
        if (empty($username) || empty($password) || empty($mail)) {
            return response()->json(['error' => 'Missing or invalid parameters'], 400);
        }
    
        $data = new users;
    
        $data->username = $username;
        $data->mail = $mail;
        $data->password = hash("sha256", $password.$this->salt);
        $data->is_admin = 0;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
    
        $data->save();
        // $this->sendWelcomeEmail($mail,$username);
    
        return response()->json(['message' => 'Data added successfully']);
    }

    public function postStory(Request $request){

        $title = $request->query('title');
        $body = $request->query('content');
        $user_id = $request->query('userId');
        $topic_id = $request->query('topicId');
       
        $data = new stories;

        $data->title = $title;
        $data->content = $body;
        $data->user_id = $user_id;
        $data->topic_id = $topic_id;
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;

        $data->save();

        return response()->json(['message' => 'Data added successfully']);
    }

    public function postComment(Request $request){

        $message = $request->query('message');
        $author_id = $request->query('authorId');
        $author_name = $request->query('authorName');
        $story_id = $request->query('storyId');
       
        $data = new comments;

        $data->message = $message;
        $data->author_id = $author_id;
        $data->author_name = $author_name;
        $data->story_id = $story_id;
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
        $data->save();
        return response()->json(['message' => 'Data added successfully']);

    }

    public function postLike(Request $request){

       
        $liked_story = $request->query('likedStory');
       
        $data = new likes;

        $data->liked_story = $liked_story;
        
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
        $data->save();

        return response()->json(['message' => 'Data added successfully']);
    }

    public function postFollow(request $request){
            
            $following_user_id = $request->query('followingUserId');
            $followed_user_id = $request->query('followedUserId');
        
            $data = new follows;
    
            $data->following_user_id = $following_user_id;
            $data->followed_user_id = $followed_user_id;
            
            
            $data->created_at = now();
            $data->updated_at = now();
            $data->is_active = 1;
            $data->save();
    
            return response()->json(['message' => 'Data added successfully']);
    }

    public function postCurrentTopic(Request $request){
        $topic_id = $request->query('topicId');
        $data = new currentTopic;
        $data->topicId = $topic_id;
        $data->save();
        return response()->json(['message' => 'Data added successfully']);
    }

    public function checkActivationCode(Request $request)
{
    $activation_code = $request->query('code'); // Assuming activation_code is sent in the request body

    // Check if the activation code exists in the codes table
    $codeExists = codes::where('code', $activation_code)->exists();

    if ($codeExists) {
        // Activation code exists in the table
        return response()->json(['message' => 'Activation code is valid']);
    } else {
        // Activation code does not exist in the table
        return response()->json(['message' => 'Invalid activation code']);
    }
}



    //DElete LEADS

    
    public function sendWelcomeEmail(Request $request)
    {
        $email = $request->query('mail');
        $name = $request->query('name');
        
        $activation = Codes::inRandomOrder()->pluck('code')->first();
        $data = [
            'name' => $name,
            'activation'=> $activation
        ];

        Mail::to($email)->send(new WelcomeMail($data));

        return response()->json(['message' => 'mail sent']);
    }

public function deleteFriend(Request $request){
    $user_id =$request->query('id');
    
    $name = $request->query('name');
    
    
   
    $user = users::where('username',$name)->pluck('id')->first();
    follows::where('followed_user_id', $user)
    ->where('following_user_id', $user_id)
    ->update(['is_active' => 0]);

return response()->json(['message' => 'follow deleted']);

}   

}
