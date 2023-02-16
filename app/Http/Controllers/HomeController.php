<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $post;
    private $user;


    public function __construct(Post $post, User $user)
    {
       $this->post = $post;
       $this->user = $user;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $all_posts = $this->post->latest()->get();
        $suggested_users = $this->getSuggestedUsers();

        $home_posts = [];

        foreach($all_posts as $post){
            if($post->user->isFollowed() || Auth::user()->id === $post->user->id){
                $home_posts[] = $post;
            }
        }

        return view('users.home')
            ->with('home_posts', $home_posts)
            ->with('suggested_users', $suggested_users);
    }

    private function getSuggestedUsers(){
        $all_users = $this->user->all()->except(Auth::user()->id);
        $suggested_users = [];
        
        foreach($all_users as $user){
            if(!$user->isFollowed()){
                $suggested_users[] = $user;
            }
        }

        return array_slice($suggested_users, 0, 5);
    }

    public function search(Request $request){
        $users = $this->user->where('name', 'like', '%' . $request->search . '%')->get();
        return view('users.search')->with('users', $users)->with('search', $request->search);
        
    }   
}
